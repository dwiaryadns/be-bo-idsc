<?php

namespace App\Http\Controllers;

use App\Models\DetailDistribusi;
use App\Models\Distribusi;
use App\Models\FasyankesWarehouse;
use App\Models\StockBarang;
use App\Models\StockGudang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DistribusiController extends Controller
{
    public function getDistribusi()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        $distribusis = Distribusi::with('warehouse', 'fasyankes', 'detail_distribusi')
            ->whereHas('warehouse', function ($q) use ($bo) {
                $q->where('bisnis_owner_id', $bo->id);
            })->get();

        $data = [];
        foreach ($distribusis as $distribusi) {
            $data[] = [
                'distribusi_id' => $distribusi->distribusi_id,
                'fasyankes' => $distribusi->fasyankes->name,
                'gudang' => $distribusi->warehouse->name,
                'date' => date('d M Y', strtotime($distribusi->date)),
                'detail' => $distribusi->detail_distribusi
            ];
        }
        return response()->json([
            'status' => true,
            'message' => 'Success get distribusi',
            'data' => $data
        ], 200);
    }
    public function getBarangGudang(Request $request)
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
        $warehouseId = $request->get('warehouse_id', null);
        $query = StockGudang::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(stock_gudang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(stok) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        }

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $query->whereHas('warehouse', function ($q) use ($bo) {
            $q->where('bisnis_owner_id', $bo->id);
        });

        $barangs = $query->with('barang', 'warehouse')
            ->paginate($perPage, ['*'], 'page', $page);

        Log::info($barangs);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil',
            'data' => $barangs
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'fasyankes_id' => 'required|exists:fasyankes,fasyankesId',
            'status' => 'required|string',
            'keterangan' => 'nullable|string',
            'details' => 'required|array',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json([
                'status' => false,
                'message' => 'Gagal Distribusi',
                'errors' => $errors
            ], 422);
        }

        DB::beginTransaction();
        try {
            $warehouseId = $request->warehouse_id;
            $fasyankesId = $request->fasyankes_id;
            $distribusi = Distribusi::create([
                'distribusi_id' => 'DSID-' . date('Y') . date('m') . str_pad(Distribusi::count() + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'warehouse_id' => $warehouseId,
                'fasyankes_id' => $fasyankesId,
                'date' => Carbon::now(),
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);

            $getWfid = FasyankesWarehouse::where('warehouse_id', $warehouseId)->where('fasyankes_id', $fasyankesId)->first();
            if (!$getWfid) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Fasyankes Warehouse not found'
                ], 404);
            }

            foreach ($request->details as $detail) {
                DetailDistribusi::create([
                    'detail_distribusi_id' => 'DDID-' . date('Y') . date('m') . str_pad(DetailDistribusi::count() + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                    'distribusi_id' => $distribusi->distribusi_id,
                    'barang_id' => $detail['barang_id'],
                    'jumlah' => $detail['jumlah'],
                ]);

                $stockBarang = StockBarang::where('fasyankes_warehouse_id', $getWfid->wfid)->where('barang_id', $detail['barang_id'])->first();
                $stockGudang = StockGudang::where('warehouse_id', $warehouseId)->where('barang_id', $detail['barang_id'])->first();

                $stockGudang->stok -= $detail['jumlah'];
                if (empty($stockBarang)) {
                    $stockBarang = StockBarang::create([
                        'stok_barang_id' => 'SBID-' . date('Y') . date('m') . str_pad(StockBarang::count() + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                        'fasyankes_warehouse_id' => $getWfid->wfid,
                        'barang_id' => $detail['barang_id'],
                        'stok' => $detail['jumlah'],
                    ]);
                } else {
                    $stockBarang->stok += $detail['jumlah'];
                    $stockBarang->save();
                }
                $stockGudang->save();
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Distribusi created successfully',
                'data' => $distribusi
            ], 201);
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
