<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPembelian;
use App\Models\FasyankesWarehouse;
use App\Models\Pembelian;
use App\Models\StockBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PembelianController extends Controller
{
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang' => 'required|array',
            'barang.*.barang_id' => 'required|string',
            'barang.*.qty' => 'required|numeric',
            'wfid' => 'required|string',
            'supplier_id' => 'required|string',
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
        $supplierId = $request->supplier_id;

        $stockBarangs = StockBarang::where('fasyankes_warehouse_id', $wfid)
            ->whereIn('barang_id', $barangIds)
            ->get()
            ->keyBy('barang_id');

        $barangDetails = Barang::whereIn('barang_id', $barangIds)->get()->keyBy('barang_id');

        $totalHarga = 0;
        $updateStockBarangs = [];

        foreach ($barangList as $barangData) {
            $barangID = $barangData['barang_id'];
            $jumlah = $barangData['qty'];

            $barang = $stockBarangs->get($barangID);
            if (!$barang) {
                return response()->json([
                    'status' => false,
                    'message' => "Barang dengan ID $barangID tidak ditemukan di WFID $wfid"
                ], 404);
            }

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

            $updateStockBarangs[] = $barang;
        }
        $countPembelian = Pembelian::count();
        $pembelian = Pembelian::create([
            'po_id' => 'PO-' . date('Y') . date('m') . str_pad($countPembelian + 1, 3, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
            'supplier_id' => $supplierId,
            'fasyankes_warehouse_id' => $wfid,
            'tanggal_po' => now(),
            'status' => 'order',
            'total_harga' => $totalHarga,
            'catatan' => $request->notes
        ]);

        $detailPembelianData = [];
        foreach ($barangList as $barangData) {
            $barangID = $barangData['barang_id'];
            $jumlah = $barangData['qty'];
            $barangDetail = $barangDetails->get($barangID);
            $hargaSatuan = $barangDetail->harga_beli;
            $totalHargaBarang = $hargaSatuan * $jumlah;

            $detailPembelianData[] = [
                'detil_po_id' => Str::uuid()->toString(),
                'po_id' => $pembelian->po_id,
                'barang_id' => $barangID,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHargaBarang
            ];
        }
        DetailPembelian::insert($detailPembelianData);

        foreach ($updateStockBarangs as $barang) {
            $barang->save();
        }

        

        return response()->json([
            'status' => true,
            'message' => 'Purchase Successfully',
        ], 200);
    }
}
