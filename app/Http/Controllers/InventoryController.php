<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarangApotek;
use App\Models\StockBarang;
use App\Models\SupplierBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        $query = StockBarang::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(stok) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
                    })
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->whereRaw('LOWER(nama_supplier) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        }
        $barangs = $query->with('barang.supplier')->whereHas('barang.supplier', function ($q) use ($bo) {
            $q->where('bisnis_owner_id', $bo->id);
        })->paginate($perPage, ['*'], 'page', $page);
        Log::info($barangs);

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
            'stok' => 'required|numeric',
            'stok_min' => 'required|numeric',
            'deskripsi' => 'required'
        ], [
            'nama_barang.required' => 'Nama Barang harus diisi',
            'kategori_id.required' => 'Kategori Barang harus dipilih',
            'supplier_id.required' => 'Supplier Barang harus dipilih',
            'harga_beli.required' => 'Harga Beli harus diisi',
            'harga_jual.required' => 'Harga Jual harus diisi',
            'satuan.required' => 'Satuan harus diisi',
            'expired_at.required' => 'Tanggal Kadaluwarsa harus diisi',
            'stok.required' => 'Stok Awal harus diisi',
            'stok_min.required' => 'Stok Minimum harus diisi',
            'deskripsi.required' => 'Deskripsi barang harus diisi',
            'stok.numeric' => 'Stok Awal harus berupa angka',
            'stok_min.numeric' => 'Stok Minimum harus berupa angka',
            'expired_at.date' => 'Tanggal kadaluwarsa harus tanggal',
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
    }
}
