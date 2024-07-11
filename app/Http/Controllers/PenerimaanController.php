<?php

namespace App\Http\Controllers;

use App\Models\DetailPenerimaanBarang;
use App\Models\GoodReceiptNote;
use App\Models\Pembelian;
use App\Models\PenerimaanBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PenerimaanController extends Controller
{
    public function penerimaan()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ]);
        }

        $penerimaan = PenerimaanBarang::with('good_receipt_note', 'pembelian')
            ->whereRelation('fasyankes_warehouse.fasyankes.bisnis_owner', 'id', Auth::guard('bisnis_owner')->user()->id)->get();

        $data = [];
        foreach ($penerimaan as $p) {
            $data[] = [
                'penerimaan_id' => $p->penerimaan_id,
                'tanggal_penerimaan' => date('d M Y', strtotime($p->tanggal_penerimaan)),
                'po_name' => $p->pembelian->po_name,
                'status' => $p->status,
                'grn_id' => $p->good_receipt_note ? $p->good_receipt_note->grn_id : null,
                'url_file' => $p->good_receipt_note ? $p->good_receipt_note->url_file : null,
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
        if (!$request->has('po_id') || $request->po_id == null || $request->po_id == '') {
            return response()->json([
                'status' => false,
                'message' => 'PO ID is required'
            ], 400);
        }
        $poId = strtoupper($request->po_id);
        $pembelian = Pembelian::whereRelation('fasyankes_warehouse.fasyankes.bisnis_owner', 'id', Auth::guard('bisnis_owner')->user()->id)
            ->where('po_id', $poId)
            ->first();

        if (!$pembelian) {
            return response()->json([
                'status' => false,
                'message' => 'PO ID not found'
            ], 404);
        }
        if ($pembelian->status === 'Received') {
            return response()->json([
                'status' => false,
                'message' => 'PO has already been received'
            ], 422);
        }

        $data = [
            'po_id' => $poId,
            'tanggal_po' => date('d M Y', strtotime($pembelian->tanggal_po)),
            'supplier' => $pembelian->supplier->nama_supplier,
            'warehouse' => $pembelian->fasyankes_warehouse->warehouse->name,
            'wfid' => $pembelian->fasyankes_warehouse->wfid,
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
            'message' => 'Success Get Penerimaan By PO ID',
            'data' => $data
        ]);
    }


    public function generateGRN($grnData)
    {
        try {
            $pdf = Pdf::loadView('grn-template', compact('grnData'));
            $pdfContent = $pdf->output();

            $fileName = $grnData['grn_id'] . '-' . Str::uuid() . '.pdf';
            Storage::disk('s3')->put($fileName, $pdfContent, 'public');
            $url = Storage::disk('s3')->url($fileName);

            GoodReceiptNote::where('grn_id', $grnData['grn_id'])->first()->update([
                'url_file' => $url
            ]);

            Log::info('PDF generated and uploaded successfully:', ['url' => $url]);

            return response()->json([
                'message' => 'PDF generated and uploaded successfully.',
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate and upload PDF:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to generate and upload PDF.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'penerima' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'pengecek' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'note' => 'nullable|string',
            'barangs' => 'required|array',
            'barangs.*.nama' => 'required|string|max:255',
            'barangs.*.qty' => 'required|integer|min:0',
            'barangs.*.barangDatang' => 'required|integer|min:0',
            'barangs.*.jml_kekurangan' => 'required|integer|min:0',
            'barangs.*.status' => 'required|string|in:Received,Retur',
            'barangs.*.kondisi' => 'required|string',
        ], [
            'penerima.required' => 'Nama Penerima is required.',
            'pengirim.required' => 'Nama Pengirim is required.',
            'pengecek.required' => 'Nama Pengecek is required.',
            'tanggal.required' => 'Tanggal Penerimaan is required.',
            'barangs.*.status.required' => 'Status Barang is required.',
            'barangs.*.kondisi.required' => 'Kondisi Barang is required.',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            Log::error('Validation Errors:', $errors->toArray());
            return response()->json(['status' => false, 'errors' => $errors, 'message' => 'Failed Create Good Receipt'], 422);
        }

        try {
            $countPenerimaan = PenerimaanBarang::count();
            $countGrn = GoodReceiptNote::count();
            Log::info('Request Data:', $request->all());

            $penerimaan = PenerimaanBarang::create([
                'penerimaan_id' => 'PEN-' . date('Y') . date('m') . str_pad($countPenerimaan + 1, 4, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'po_id' => $request->po_id,
                'fasyankes_warehouse_id' => $request->wfid,
                'tanggal_penerimaan' => $request->tanggal,
                'status' => 'Pending',
                'penerima' => $request->penerima,
                'pengecek' => $request->pengecek,
                'pengirim' => $request->pengirim,
                'catatan' => $request->note,
            ]);
            Log::info('Penerimaan Barang Created:', $penerimaan->toArray());

            $detailPenerimaan = [];
            foreach ($request->barangs as $barang) {
                Log::info('Processing Barang:', $barang);
                $detailPenerimaan[] = [
                    'detil_penerimaan_id' => 'DETIL-PEN-' . date('Y') . date('m') . str_pad($countPenerimaan + 1, 4, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                    'penerimaan_id' => $penerimaan->penerimaan_id,
                    'barang_id' => $barang['barang_id'],
                    'jumlah' => $barang['qty'],
                    'jml_datang' => $barang['barangDatang'],
                    'jml_kurang' => $barang['jml_kekurangan'],
                    'kondisi' => $barang['kondisi'],
                ];
            }
            DetailPenerimaanBarang::insert($detailPenerimaan);
            Log::info('Detail Penerimaan Barang Inserted:', $detailPenerimaan);

            $getPembelian = Pembelian::where('po_id', $request->po_id)->first();
            Log::info("Pembelian Found:" . $getPembelian);
            $getPembelian->update(['status' => 'Received']);

            $grn = GoodReceiptNote::create([
                'grn_id' => 'GRN-' . date('Y') . date('m') . str_pad($countGrn + 1, 5, "0", STR_PAD_LEFT),
                'penerimaan_id' => $penerimaan->penerimaan_id,
                'url_file' => '',
            ]);
            Log::info("Good Receipt Note Created:" . $grn);

            $grnData = [
                'grn_id' => $grn->grn_id,
                'tanggal_penerimaan' => $penerimaan->tanggal_penerimaan,
                'barangs' => $request->barangs,
                'penerima' => $request->penerima,
                'pengecek' => $request->pengecek,
                'notes' => $request->note
            ];
            $this->generateGRN($grnData);
        } catch (\Throwable $th) {
            Log::error('Exception Caught:', ['error' => $th->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to create Good Receipt.',
                'error' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Success Create Good Receipt',
            'data' => '',
        ]);
    }
}
