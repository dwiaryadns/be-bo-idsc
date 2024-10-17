<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\FasyankesWarehouse;
use App\Models\KategoriBarangApotek;
use App\Models\Penjualan;
use App\Models\StockBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{

    public function master_kategori(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'page' => 'numeric',
                'per_page' => 'numeric',
                'search' => ['nullable', 'string', 'regex:/^[^%_\\\\\'\";]*$/'],
            ]
        );
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $query = KategoriBarangApotek::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }
        $barangs = $query->select('kategori_id', 'nama')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Success Get Master Kategori',
            'data' => $barangs
        ], 200);
    }
    public function master_barang(Request $request)
    {
        if (!$request->has('wfid')) {
            return response()->json([
                'status' => false,
                'message' => 'WFID is required'
            ], 400);
        }

        $checkWfid = FasyankesWarehouse::where('wfid', $request->wfid)->first();
        if (!$checkWfid) {
            return response()->json([
                'status' => false,
                'message' => 'WFID not found'
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'page' => 'numeric',
                'per_page' => 'numeric',
                'search' => ['nullable', 'string', 'regex:/^[^%_\\\\\'\";]*$/'],
            ]
        );

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $wfid = $request->get('wfid');

        $query = StockBarang::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(stok_barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhere('stok', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('barang', function ($query) use ($search) {
                        $query->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%'])
                            ->orWhereRaw('LOWER(deskripsi) LIKE ?', ['%' . strtolower($search) . '%'])
                            ->orWhereHas('kategori_barang', function ($query) use ($search) {
                                $query->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($search) . '%']);
                            });
                    });
            });
        }

        $barangs = $query->with(
            [
                'barang',
                'barang.kategori_barang',
                'barang.kfa_poa.masterKfaPov.masterKfa',
                'diskon' => function ($q) {
                    $q->select('stok_barang_id', 'type', 'percent_disc', 'amount_disc', 'expired_disc');
                },
            ]
        )
            ->select('stok_barang_id', 'fasyankes_warehouse_id', 'barang_id', 'stok', 'harga_jual')
            ->where('fasyankes_warehouse_id', $wfid)
            ->paginate($perPage, ['*'], 'page', $page);
        $barangs->getCollection()->transform(function ($item) {
            $item->barang->harga_jual = $item->harga_jual;
            unset($item->harga_jual);
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Success Get Master Barang',
            'data' => $barangs
        ], 200);
    }


    public function decreaseStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang' => 'required|array',
            'barang.*.barang_id' => 'required|string',
            'barang.*.qty' => 'required|numeric',
            'wfid' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }

        $barangIds = collect($request->barang)->pluck('barang_id');
        if ($barangIds->count() !== $barangIds->unique()->count()) {
            return response()->json([
                'status' => false,
                'message' => 'Duplicate barang_id found in the request'
            ], 422);
        }

        $wfid = $request->wfid;
        $checkWfid = FasyankesWarehouse::where('wfid', $wfid)->first();
        if (!$checkWfid) {
            return response()->json([
                'status' => false,
                'message' => 'WFID not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $barangList = $request->barang;

            $stockBarangs = StockBarang::where('fasyankes_warehouse_id', $wfid)
                ->whereIn('barang_id', $barangIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('barang_id');

            $totalPenjualan = 0;

            foreach ($barangList as $barangData) {
                $barangID = $barangData['barang_id'];
                $jumlah = $barangData['qty'];

                if (!isset($stockBarangs[$barangID])) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Barang dengan ID $barangID tidak ditemukan di WFID $wfid"
                    ], 404);
                }

                $barang = $stockBarangs[$barangID];

                if ($barang->stok < $jumlah) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Stok barang dengan ID $barangID tidak mencukupi"
                    ], 422);
                }

                $barang->stok -= $jumlah;

                $totalHargaItem = $jumlah * $barang->harga_jual;
                $totalPenjualan += $totalHargaItem;
            }

            foreach ($stockBarangs as $barang) {
                $barang->save();
            }

            $countPenjualan = Penjualan::count();
            $penjualan = Penjualan::create([
                'penjualan_id' => 'PJL-' . date('Y') . date('m') . str_pad($countPenjualan + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'total' => $totalPenjualan,
                'fasyankes_warehouse_id' => $wfid,
            ]);

            $countDetailPenjualan = DetailPenjualan::count();
            foreach ($barangList as $barangData) {
                $barangID = $barangData['barang_id'];
                $jumlah = $barangData['qty'];
                $hargaSatuan = $stockBarangs[$barangID]->harga_jual;
                $totalHargaItem = $jumlah * $hargaSatuan;

                DetailPenjualan::create([
                    'detail_penjualan_id' => 'DPJL-' . date('Y') . date('m') . str_pad($countDetailPenjualan + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                    'barang_id' => $barangID,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'total_harga' => $totalHargaItem,
                    'penjualan_id' => $penjualan->penjualan_id,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Penjualan berhasil disimpan dan stok barang dikurangi',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan penjualan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
