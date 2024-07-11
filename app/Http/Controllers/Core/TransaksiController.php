<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\FasyankesWarehouse;
use App\Models\KategoriBarangApotek;
use App\Models\StockBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{

    public function master_kategori(Request $request)
    {
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
                                $query->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($search) . '%']);
                            });
                    });
            });
        }

        $barangs = $query->with('barang', 'barang.kategori_barang', 'barang.kfa_poa.masterKfaPov.masterKfa')
            ->select('stok_barang_id', 'fasyankes_warehouse_id', 'barang_id', 'stok')
            ->where('fasyankes_warehouse_id', $wfid)
            ->paginate($perPage, ['*'], 'page', $page);

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
        $barangList = $request->barang;

        $stockBarangs = StockBarang::where('fasyankes_warehouse_id', $wfid)
            ->whereIn('barang_id', $barangIds)
            ->get()
            ->keyBy('barang_id'); 
        foreach ($barangList as $barangData) {
            $barangID = $barangData['barang_id'];
            $jumlah = $barangData['qty'];
            if (!isset($stockBarangs[$barangID])) {
                return response()->json([
                    'status' => false,
                    'message' => "Barang dengan ID $barangID tidak ditemukan di WFID $wfid"
                ], 404);
            }
            $barang = $stockBarangs[$barangID];
            if ($barang->stok < $jumlah) {
                return response()->json([
                    'status' => false,
                    'message' => "Stok barang dengan ID $barangID tidak mencukupi"
                ], 422);
            }
            $barang->stok -= $jumlah;
        }

        foreach ($stockBarangs as $barang) {
            $barang->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Mengurangi Stok Barang',
        ], 200);
    }
}
