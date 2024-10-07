<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockGudang;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function getWarehouses()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $delegate = Auth::guard('delegate_access')->user();
        $id = $bo ? $bo->id : $delegate->bisnis_owner_id;

        $warehouses = Warehouse::with('fasyankes')
            ->where('bisnis_owner_id', $id)
            ->get();
        return response()->json([
            'status' => true,
            'message' => 'Success get warehouse',
            'data' => $warehouses
        ], 200);
    }

    public function storeWarehouse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'pic' => 'required|string',
            'contact' => 'required|numeric'
        ], [
            'name.required' => 'Nama Gudang wajib diisi.',
            'address.required' => 'Alamat Gudang wajib diisi.',
            'pic.required' => 'Nama PIC wajib diisi.',
            'contact.required' => 'Nomor Telepon PIC wajib diisi.',
            'contact.numeric' => 'Nomor Telepon PIC harus berupa angka.',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'message' => 'Gagal Menambahkan Gudang', 'errors' => $errors], 422);
        }

        $user = Auth::guard('bisnis_owner')->user();
        if (empty($user)) {
            Log::error('Pengguna tidak terautentikasi.');
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }
        $warehouse = Warehouse::create(
            [
                'bisnis_owner_id' => $user->id,
                'name' => $request->name,
                'address' => $request->address,
                'pic' => $request->pic,
                'contact' => $request->contact,
            ]
        );
        log_activity("Menambahkan Gudang $request->name", 'Gudang', $user->name, 1);
        if ($warehouse) {
            return response()->json([
                'status' => true,
                'message' => 'Berhasil',
                'data' => $warehouse
            ], 200);
        }
    }
    public function stockGudang(Request $request)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $countWh = Warehouse::where('bisnis_owner_id', $bo->id)->get();
        if ($countWh->count() === 1) {
            $whId = $countWh[0]->id;
        } else {
            if (!$request->has('warehouse_id')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gudang wajib diisi.'
                ], 400);
            }

            $checkWh = Warehouse::where('id', $request->warehouse_id)->first();
            if (!$checkWh) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gudang not found'
                ], 404);
            }
            $whId = $request->get('warehouse_id');
        }


        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');


        $query = StockGudang::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(stock_gudang_id) LIKE ?', ['%' . strtolower($search) . '%'])
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
        $barangs = $query->with('warehouse', 'barang.supplier')->where('warehouse_id', $whId)->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'status' => true,
            'message' => 'Berhasil',
            'data' => $barangs
        ], 200);
    }

    public function updateIsJualBarang(Request $request)
    {
        $barangId = $request->stock_gudang_id;
        $barang = StockGudang::where('stock_gudang_id', $barangId)->first();
        Log::info($barangId . ' ' . $barang);

        if (!$barang) {
            return response()->json([
                'status' => false,
                'message' => 'Barang Tidak Ditemukan'
            ], 404);
        }
        $barang->update([
            'isJual' => $request->isJual
        ]);
        return response()->json([
            'status' => true,
            'data' => $barang,
            'message' => 'Berhasil'
        ], 200);
    }
}
