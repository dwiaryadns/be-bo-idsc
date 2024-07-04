<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\StockBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function master_barang(Request $request)
    {
        if (!$request->has('wfid')) {
            return response()->json([
                'status' => false,
                'message' => 'WFID is required'
            ], 400);
        }

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $wfid = $request->get('wfid');

        $query = StockBarang::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('stok_barang_id', 'like', '%' . $search . '%')
                    ->orWhere("barang_id", 'like', '%' . $search . '%')
                    ->orWhere("stok", 'like', '%' . $search . '%');
            });
        }

        $barangs = $query->with('barang.kategori_barang')
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

        $wfid = $request->wfid;
        $barangList = $request->barang;

        foreach ($barangList as $barangData) {
            $barangID = $barangData['barang_id'];
            $jumlah = $barangData['qty'];

            $barang = StockBarang::where('barang_id', $barangID)
                ->where('fasyankes_warehouse_id', $wfid)
                ->first();

            if (!$barang) {
                return response()->json([
                    'status' => false,
                    'message' => "Barang dengan ID $barangID tidak ditemukan di WFID $wfid"
                ], 404);
            }
            if ($barang->stok < $jumlah) {
                return response()->json([
                    'status' => false,
                    'message' => "Stok barang dengan ID $barangID tidak mencukupi"
                ], 422);
            }
            $barang->stok -= $jumlah;
            $barang->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Mengurangi Stok Barang',
        ], 200);
    }
}
