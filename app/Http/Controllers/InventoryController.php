<?php

namespace App\Http\Controllers;

use App\Imports\BarangImport;
use App\Models\AccessFasyankes;
use App\Models\Barang;
use App\Models\KategoriBarangApotek;
use App\Models\StockBarang;
use App\Models\SupplierBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public function getKategori()
    {
        $kategories = KategoriBarangApotek::get();
        return response()->json(
            [
                'status' => true,
                'data' => $kategories,
                'message' => 'success get kategories'
            ]
        );
    }

    public function getBarang(Request $request)
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
        $query = Barang::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(satuan) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhere('harga_beli', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($r) use ($search) {
                        $r->whereRaw('LOWER(nama_supplier) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        }

        $barangs = $query->with([
            'supplier' => function ($q) {
                $q->select('supplier_id', 'nama_supplier');
            }, 'kategori_barang',
        ])->whereHas('supplier', function ($q) use ($bo) {
            $q->where('bisnis_owner_id', $bo->id);
        })->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Success Get Supplier Barang',
            'data' => $barangs
        ], 200);
    }

    public function getStockBarang(Request $request)
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
        $fasyankesId = $request->get('fasyankesId', '');
        $query = StockBarang::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
                    })
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->whereRaw('LOWER(nama_supplier) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        }
        if ($fasyankesId) {
            $query->whereHas('fasyankes_warehouse.fasyankes', function ($q) use ($fasyankesId) {
                $q->where('fasyankesId', $fasyankesId);
            });
        }

        $barangs = $query->with([
            'barang' => function ($q) {
                $q->select('barang_id', 'nama_barang', 'supplier_id', 'harga_beli', 'harga_jual', 'satuan'); // Tambahkan 'supplier_id'
            },
            'barang.supplier' => function ($q) {
                $q->select('supplier_id', 'nama_supplier');
            },
            'fasyankes_warehouse.fasyankes' => function ($q) {
                $q->select('fasyankesId', 'name');
            }
        ])->whereHas('barang.supplier', function ($q) use ($bo) {
            $q->where('bisnis_owner_id', $bo->id);
        })->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Success Get Supplier Barang',
            'data' => $barangs
        ], 200);
    }

    public function storeBarang(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required',
            'kategori_id' => 'required',
            'supplier_id' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'satuan' => 'required',
            'expired_at' => 'required|date',
            'deskripsi' => 'required'
        ], [
            'nama_barang.required' => 'Nama Barang harus diisi',
            'kategori_id.required' => 'Kategori Barang harus dipilih',
            'supplier_id.required' => 'Supplier Barang harus dipilih',
            'harga_beli.required' => 'Harga Beli harus diisi',
            'harga_jual.required' => 'Harga Jual harus diisi',
            'satuan.required' => 'Satuan harus diisi',
            'expired_at.required' => 'Tanggal Kadaluwarsa harus diisi',
            'deskripsi.required' => 'Deskripsi barang harus diisi',
            'expired_at.date' => 'Format Tanggal kadaluwarsa harus Benar',
        ]);
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed Create Barang',
                    'errors' => $errors
                ],
                422
            );
        }
        $harga_beli = str_replace('.', '', $request->harga_beli);
        $harga_jual = str_replace('.', '', $request->harga_jual);
        $barang = Barang::create([
            'barang_id' => 'BID-' . date('Y') . date('m') . str_pad(Barang::count() + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
            'nama_barang' => $request->nama_barang,
            'kategori_id' => $request->kategori_id,
            'supplier_id' => $request->supplier_id,
            'harga_beli' => str_replace('Rp', '', $harga_beli),
            'harga_jual' => str_replace('Rp', '', $harga_jual),
            'satuan' => $request->satuan,
            'expired_at' => $request->expired_at,
            'deskripsi' => $request->deskripsi
        ]);

        SupplierBarang::create([
            'supplier_barang_id' => 'SBID-' . date('Y') . date('m') . str_pad(SupplierBarang::count() + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
            'supplier_id' => $request->supplier_id,
            'barang_id' => $barang->barang_id,
            'harga' => str_replace('Rp', '', $harga_beli),
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Berhasil Menambah Barang'
        ]);
    }
    public function importBarang(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);
        try {
            $import = new BarangImport;
            Excel::import($import, $request->file('file'));
            $importedData = $import->getData();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'File berhasil diimpor',
                    'data' => $importedData
                ],
                200
            );
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal Import Barang',
                'error' => $th->getMessage()
            ], 422);
        }
    }

    public function test()
    {
        $access = AccessFasyankes::get();
        return response()->json($access);
    }
}
