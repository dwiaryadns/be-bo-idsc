<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateGRNLetter;
use App\Models\DetailPenerimaanBarang;
use App\Models\GoodReceiptNote;
use App\Models\Pembelian;
use App\Models\PenerimaanBarang;
use App\Models\StockGudang;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PenerimaanController extends Controller
{
    public function penerimaan()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $delegate = Auth::guard('delegate_access')->user();
        $id = $bo ? $bo->id : $delegate->bisnis_owner_id;

        if (!$bo && !$delegate) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }

        $penerimaan = PenerimaanBarang::with('good_receipt_notes', 'pembelian', 'detailPending')
            ->orderBy('created_at', 'DESC')
            ->whereRelation('warehouse.bisnis_owner', 'id', $id)
            ->get();

        $data = [];
        foreach ($penerimaan as $p) {
            $data[] = [
                'penerimaan_id' => $p->penerimaan_id,
                'tanggal_penerimaan' => date('d M Y', strtotime($p->tanggal_penerimaan)),
                'po_name' => $p->pembelian->po_name,
                'status' => $p->status,
                'grn' => $p->good_receipt_notes,
                'pending' => $p->detailPending,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Success Get Penerimaan',
            'data' => $data
        ]);
    }

    public function showByPoId(Request $request)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $delegate = Auth::guard('delegate_access')->user();
        $id = $bo ? $bo->id : $delegate->bisnis_owner_id;

        if (!$bo && !$delegate) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }
        if (!$request->has('po_id') || $request->po_id == null || $request->po_id == '') {
            return response()->json([
                'status' => false,
                'message' => 'PO ID wajib diisi.'
            ], 400);
        }
        $poId = strtoupper($request->po_id);
        $pembelian = Pembelian::whereRelation('warehouse.bisnis_owner', 'id', $id)
            ->where('po_id', $poId)
            ->first();

        if (!$pembelian) {
            return response()->json([
                'status' => false,
                'message' => 'PO ID tidak ditemukan.'
            ], 404);
        }
        if ($pembelian->status === 'Received') {
            return response()->json([
                'status' => false,
                'message' => 'PO sudah diterima'
            ], 422);
        }

        $data = [
            'po_id' => $poId,
            'tanggal_po' => date('d M Y', strtotime($pembelian->tanggal_po)),
            'supplier' => $pembelian->supplier->nama_supplier,
            'warehouse' => $pembelian->warehouse->name,
            'warehouse_id' => $pembelian->warehouse->id,
            'barangs' => []
        ];

        foreach ($pembelian->detail_pembelians as $detail) {
            $data['barangs'][] = [
                'barang_id' => $detail->barang->barang_id,
                'nama' => $detail->barang->nama_barang,
                'qty' => $detail->jumlah,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil',
            'data' => $data
        ]);
    }

    public function save(Request $request)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $delegate = Auth::guard('delegate_access')->user();

        if (!$bo && !$delegate) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'penerima' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'pengecek' => 'required|string|max:255',
            'supplier_invoice' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'note' => 'nullable|string',
            'barangs' => 'required|array',
            'barangs.*.nama' => 'required|string|max:255',
            'barangs.*.qty' => 'required|integer|min:0',
            'barangs.*.barangDatang' => 'required|integer|min:0',
            'barangs.*.jml_kekurangan' => 'required|integer|min:0',
            // 'barangs.*.status' => 'required|string|in:Received,Retur',
            'barangs.*.kondisi' => 'required|string',
        ], [
            'penerima.required' => 'Nama Penerima wajib diisi.',
            'pengirim.required' => 'Nama Pengirim wajib diisi.',
            'pengecek.required' => 'Nama Pengecek wajib diisi.',
            'supplier_invoice.required' => 'Supplier Invoice wajib diisi.',
            'tanggal.required' => 'Tanggal Penerimaan wajib diisi.',
            // 'barangs.*.status.required' => 'Status Barang wajib diisi.',
            'barangs.*.kondisi.required' => 'Kondisi Barang wajib diisi.',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            Log::error('Validation Errors:', $errors->toArray());
            return response()->json(['status' => false, 'errors' => $errors, 'message' => 'Gagal'], 422);
        }

        try {
            DB::beginTransaction();
            $getWarehouse = Warehouse::where('id', $request->warehouse_id)->first();
            $countPenerimaan = PenerimaanBarang::count();
            $countGrn = GoodReceiptNote::count();
            Log::info('Request Data:', $request->all());

            $penerimaan = PenerimaanBarang::create([
                'penerimaan_id' => 'PEN-' . date('Y') . date('m') . str_pad($countPenerimaan + 1, 4, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'po_id' => $request->po_id,
                'supplier_invoice' => $request->supplier_invoice,
                'warehouse_id' => $getWarehouse->id,
                'tanggal_penerimaan' => $request->tanggal,
                'status' => 'Pending',
                'penerima' => $request->penerima,
                'pengecek' => $request->pengecek,
                'pengirim' => $request->pengirim,
                'catatan' => $request->note,
            ]);

            $detailPenerimaan = [];
            $barangIds = array_column($request->barangs, 'barang_id');
            $countStockBarang = StockGudang::count();
            $stockBarang = StockGudang::where('warehouse_id', $getWarehouse->id)
                ->whereIn('barang_id', $barangIds)
                ->get()
                ->keyBy('barang_id');

            foreach ($request->barangs as $barang) {
                Log::info($request->barangs);
                $status = $barang['jml_kekurangan'] > 0 ? 'Retur' : 'Received';
                $barang['status'] = $status; // Add status key to the barang array

                if (isset($stockBarang[$barang['barang_id']])) {
                    $stockBarang[$barang['barang_id']]->stok += $barang['barangDatang'];
                } else {
                    $stockBarang[$barang['barang_id']] = new StockGudang([
                        'stock_gudang_id' => 'SGID-' . date('Y') . date('m') . str_pad($countStockBarang + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                        'warehouse_id' => $getWarehouse->id,
                        'barang_id' => $barang['barang_id'],
                        'stok' => $barang['barangDatang'],
                    ]);
                }

                $detailPenerimaan[] = [
                    'detil_penerimaan_id' => 'DETIL-PEN-' . date('Y') . date('m') . str_pad($countPenerimaan + 1, 4, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                    'penerimaan_id' => $penerimaan->penerimaan_id,
                    'barang_id' => $barang['barang_id'],
                    'jumlah' => $barang['qty'],
                    'jml_datang' => $barang['barangDatang'],
                    'jml_kurang' => $barang['jml_kekurangan'],
                    'kondisi' => $barang['kondisi'],
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            foreach ($stockBarang as $barang) {
                $barang->save();
            }
            DetailPenerimaanBarang::insert($detailPenerimaan);
            $getPembelian = Pembelian::where('po_id', $request->po_id)->first();
            $getPembelian->update(['status' => 'Received']);
            $grn = GoodReceiptNote::create([
                'grn_id' => 'GRN-' . date('Y') . date('m') . str_pad($countGrn + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'penerimaan_id' => $penerimaan->penerimaan_id,
                'url_file' => '',
            ]);
            Log::info("Good Receipt Note Created:" . $grn);
            $barangsWithStatus = $request->barangs;
            foreach ($barangsWithStatus as &$barang) {
                $barang['status'] = $barang['jml_kekurangan'] > 0 ? 'Retur' : 'Received';
            }

            $grnData = [
                'grn_id' => $grn->grn_id,
                'tanggal_penerimaan' => $penerimaan->tanggal_penerimaan,
                'barangs' => $barangsWithStatus,
                'penerima' => $request->penerima,
                'pengecek' => $request->pengecek,
                'notes' => $request->note
            ];
            $isPending = false;
            foreach ($detailPenerimaan as $detail) {
                if ($detail['status'] === 'Retur') {
                    $isPending = true;
                    break;
                }
            }
            $penerimaan->update(['status' => $isPending ? 'Pending' : 'Received']);
            DB::commit();
            GenerateGRNLetter::dispatch($grnData);
            log_activity("Penerimaan Barang untuk $getWarehouse->name", "Penerimaan Barang", $bo ? $bo->name : $delegate->name, 1);
            return response()->json([
                'status' => true,
                'message' => 'Berhasil',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Exception Caught:', ['error' => $th->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Gagal',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateStockPenerimaan(Request $request)
    {
        Log::info($request->all());
        try {
            DB::beginTransaction();
            $penerimaan = PenerimaanBarang::where('penerimaan_id', $request->barangs[0]['penerimaan_id'])->first();
            $barangs = [];
            $stockBarang = StockGudang::where('warehouse_id', $penerimaan->warehouse_id)
                ->whereIn('barang_id', array_column($request->barangs, 'barang_id'))
                ->get()
                ->keyBy('barang_id');

            foreach ($request->barangs as $barang) {
                $detail = DetailPenerimaanBarang::where('detil_penerimaan_id', $barang['detil_penerimaan_id'])->first();
                $status = $barang['jml_kurang'] > 0 ? 'Retur' : 'Received';

                $perubahanKurang = $detail->jml_kurang - $barang['jml_kurang'];
                if (isset($stockBarang[$barang['barang_id']])) {
                    $stockBarang[$barang['barang_id']]->stok += $perubahanKurang;
                } else {
                    $stockBarang[$barang['barang_id']] = new StockGudang([
                        'stock_gudang_id' => 'SGID-' . date('Y') . date('m') . str_pad(StockGudang::count() + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                        'warehouse_id' => $penerimaan->warehouse_id,
                        'barang_id' => $barang['barang_id'],
                        'stok' => $perubahanKurang,
                    ]);
                }

                $detail->update([
                    'jml_datang' => $barang['jml_datang'],
                    'jml_kurang' => $barang['jml_kurang'],
                    'status' => $status,
                    'kondisi' => $barang['kondisi']
                ]);
                $barangs[] = [
                    'nama' => $detail->barang->nama_barang,
                    'qty' => $detail->jumlah,
                    'barangDatang' => $perubahanKurang,
                    'jml_kekurangan' => $detail->jml_kurang,
                    'kondisi' => $detail->kondisi,
                    'status' => $status,
                ];
            }

            foreach ($stockBarang as $barang) {
                $barang->save();
            }

            $isCompleted = true;
            foreach ($barangs as $barang) {
                if ($barang['status'] == 'Retur') {
                    $isCompleted = false;
                    break;
                }
            }
            $penerimaan->update(['status' => $isCompleted ? 'Completed' : 'Pending'],);

            $countGrn = GoodReceiptNote::count();
            $grn = GoodReceiptNote::create([
                'grn_id' => 'GRN-' . date('Y') . date('m') . str_pad($countGrn + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'penerimaan_id' => $penerimaan->penerimaan_id,
                'url_file' => '',
            ]);

            $grnData = [
                'grn_id' => $grn->grn_id,
                'tanggal_penerimaan' => $penerimaan->tanggal_penerimaan,
                'barangs' => $barangs,
                'penerima' => $penerimaan->penerima,
                'pengecek' => $penerimaan->pengecek,
                'notes' => $penerimaan->catatan,
            ];
            DB::commit();
            GenerateGRNLetter::dispatch($grnData);
            return response()->json([
                'status' => true,
                'message' => 'Berhasil memperbarui status',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Exception Caught:', ['error' => $th->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
