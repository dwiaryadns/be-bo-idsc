<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPembelian;
use App\Models\FasyankesWarehouse;
use App\Models\Pembelian;
use App\Models\StockBarang;
use App\Models\SupplierBarang;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PembelianController extends Controller
{
    public function getPurchase()
    {
        $purchase = Pembelian::with('detail_pembelians.barang')
            ->whereRelation('warehouse.bisnis_owner', 'id', Auth::guard('bisnis_owner')->user()->id)
            ->get();

        $data = [];
        foreach ($purchase as $p) {
            $data[] = [
                'po_id' => $p->po_id,
                'tanggal_po' => date('d M Y', strtotime($p->tanggal_po)),
                'po_name' => $p->po_name,
                'status' => $p->status,
                'warehouse' => $p->warehouse->name,
                'supplier' => $p->supplier->nama_supplier,
                'total' => $p->total_harga,
                'detail' => $p->detail_pembelians
            ];
        }

        return response()->json(['status' => true, 'data' => $data], 200);
    }

    public function getFasyankesWarehouse()
    {
        try {
            $fasyankesWarehouses = FasyankesWarehouse::with('fasyankes', 'warehouse')->whereRelation('fasyankes.bisnis_owner', 'id', Auth::guard('bisnis_owner')->user()->id)->get();

            $data = [];
            foreach ($fasyankesWarehouses as $wf) {
                $data[] = [
                    'wfid' => $wf->wfid,
                    'fasyankes_name' => $wf->fasyankes->name,
                    'fasyankes_id' => $wf->fasyankes_id,
                    'warehouse_name' => $wf->warehouse->name,
                    'warehouse_id' => $wf->warehouse_id,
                ];
            }
            return response()->json([
                'status' => true,
                'message' => 'success get fasyankes warehouse',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'error get fasyankes warehouse',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getBarangSupplier(Request $request)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $query = SupplierBarang::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(supplier_barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(supplier_barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(harga) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
                    })
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->whereRaw('LOWER(nama_supplier) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        }
        $barangs = $query->with('supplier', 'barang.kfa_poa')->whereHas('supplier', function ($q) use ($bo) {
            $q->where('bisnis_owner_id', $bo->id);
        })->paginate($perPage, ['*'], 'page', $page);
        Log::info($barangs);

        return response()->json([
            'status' => true,
            'message' => 'Success Get Supplier Barang',
            'data' => $barangs
        ], 200);
    }

    public function purchase(Request $request)
    {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'barang' => 'required',
            'barang.*.barang_id' => 'required|string',
            'barang.*.qty' => 'required|numeric',
            'warehouse_id' => 'required|string',
            'supplier_id' => 'required|string',
            'po_name' => 'required'
        ], [
            'warehouse_id' => 'Gudang Wajib diisi ',
            'po_name' => 'Nama PO wajib diisi',

        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json([
                'status' => false,
                'message' => 'Pembelian Gagal',
                'errors' => $errors
            ], 422);
        }

        $barangIds = collect($request->barang)->pluck('barang_id');
        if ($barangIds->count() !== $barangIds->unique()->count()) {
            return response()->json([
                'status' => false,
                'message' => 'Duplicate barang_id found in the request'
            ], 422);
        }

        $barangList = $request->barang;
        $supplierId = $request->supplier_id;

        $barangDetails = Barang::whereIn('barang_id', $barangIds)->get()->keyBy('barang_id');

        $totalHarga = 0;

        foreach ($barangList as $barangData) {
            $barangID = $barangData['barang_id'];
            $jumlah = $barangData['qty'];
            $notes = $barangData['notes'];

            $barangDetail = $barangDetails->get($barangID);
            if (!$barangDetail) {
                return response()->json([
                    'status' => false,
                    'message' => "Barang dengan ID $barangID tidak ditemukan di tabel Barang"
                ], 404);
            }

            $hargaSatuan = $barangDetail->harga_beli;
            $totalHargaBarang = $hargaSatuan * $jumlah;
            $totalHarga += $totalHargaBarang;
        }
        $getWarehouse = Warehouse::where('id', $request->warehouse_id)->first();

        $countPembelian = Pembelian::count();
        $pembelian = Pembelian::create([
            'po_name' => $request->po_name,
            'po_id' => 'PO-' . date('Y') . date('m') . str_pad($countPembelian + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
            'supplier_id' => $supplierId,
            'warehouse_id' => $getWarehouse->id,
            'tanggal_po' => Carbon::now(),
            'status' => 'Order',
            'total_harga' => $totalHarga,
            'catatan' => $request->notes
        ]);

        $detailPembelianData = [];
        foreach ($barangList as $barangData) {
            $barangID = $barangData['barang_id'];
            $jumlah = !empty($barangData['qty']) ? $barangData['qty'] : 0;
            $barangDetail = $barangDetails->get($barangID);
            $hargaSatuan = $barangDetail->harga_beli;
            $totalHargaBarang = $hargaSatuan * $jumlah;
            $notes = $barangData['notes'];

            $detailPembelianData[] = [
                'detil_po_id' => 'DETIL-PO-' . date('Y') . date('m') . str_pad($countPembelian + 1, 3, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'po_id' => $pembelian->po_id,
                'barang_id' => $barangID,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHargaBarang,
                'notes' => $notes
            ];
        }
        DetailPembelian::insert($detailPembelianData);

        log_activity("Pemesanan Barang untuk $getWarehouse->name", "Pemesanan Barang", Auth::guard('bisnis_owner')->user()->name, 1);
        return response()->json([
            'status' => true,
            'message' => 'Purchase Successfully',
        ], 200);
    }
}
