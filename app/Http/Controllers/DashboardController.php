<?php

namespace App\Http\Controllers;

use App\Models\DetailPembelian;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\StockBarang;
use App\Models\StockGudang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    // Delegate Tidak Perlu disini
    public function getBo()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bisnis Owner not found',
            ], 404);
        }
        return $bo;
    }
    public function getMonthlyReport()
    {
        $bo = $this->getBo();
        try {
            $incomeData = Penjualan::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(total) as total_income')
                ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [Carbon::now()->year])
                ->groupBy('month')
                ->whereHas('fasyankesWarehouse', function ($q) use ($bo) {
                    $q->whereHas('fasyankes', function ($r) use ($bo) {
                        $r->where('bisnis_owner_id', $bo->id);
                    });
                })
                ->get();
            Log::info($incomeData);
            $outcomeData = Pembelian::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(total_harga) as total_outcome') // Sesuaikan logika outcome
                ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [Carbon::now()->year])
                ->groupBy('month')
                ->where('status', 'Received')
                ->whereHas('supplier', function ($q) use ($bo) {
                    $q->where('bisnis_owner_id', $this->getBo()->id);
                })
                ->get();
            Log::info($outcomeData);
            $monthlyData = array_fill(1, 12, [
                'income' => 0,
                'outcome' => 0,
                'net_income' => 0,
            ]);

            foreach ($incomeData as $data) {
                $monthlyData[$data->month]['income'] = (int)$data->total_income;
            }

            foreach ($outcomeData as $data) {
                $monthlyData[$data->month]['outcome'] = (int) $data->total_outcome;
            }

            foreach ($monthlyData as $month => $data) {
                $monthlyData[$month]['net_income'] = $data['income'] - $data['outcome'];
            }

            return response()->json($monthlyData);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getDailyReport()
    {
        $bo = $this->getBo();
        try {
            // Get current month and year
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            // Get the number of days in the current month
            $daysInMonth = Carbon::now()->daysInMonth;

            // Get income from detail_penjualan table grouped by day
            $incomeData = DetailPenjualan::selectRaw('DATE_PART(\'day\', created_at) as day, SUM(total_harga) as total_income')
                ->whereRaw('DATE_PART(\'month\', created_at) = ? AND DATE_PART(\'year\', created_at) = ?', [$currentMonth, $currentYear])
                ->groupBy('day')
                ->whereHas('penjualan.fasyankesWarehouse.fasyankes', function ($q) use ($bo) {
                    $q->where('bisnis_owner_id', $bo->id);
                })
                ->get();


            // Get outcome from detail_pembelian table grouped by day
            $outcomeData = DetailPembelian::selectRaw('DATE_PART(\'day\', created_at) as day, SUM(total_harga) as total_outcome')
                ->whereRaw('DATE_PART(\'month\', created_at) = ? AND DATE_PART(\'year\', created_at) = ?', [$currentMonth, $currentYear])
                ->groupBy('day')
                ->whereHas('pembelian.supplier', function ($q) use ($bo) {
                    $q->where('bisnis_owner_id', $bo->id);
                })
                ->get();

            // Initialize daily data array for the number of days in the current month
            $dailyData = array_fill(1, $daysInMonth, [
                'income' => 0,
                'outcome' => 0,
            ]);

            // Fill income data
            foreach ($incomeData as $data) {
                $dailyData[(int) $data->day]['income'] = (int) $data->total_income;
            }

            // Fill outcome data
            foreach ($outcomeData as $data) {
                $dailyData[(int) $data->day]['outcome'] = (int) $data->total_outcome;
            }

            return response()->json($dailyData);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to get daily report',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function stokGudangReport(Request $request)
    {
        $bo = $this->getBo();

        // Filter gudang jika ada request `warehouse_id`
        $stockGudangQuery = StockGudang::query();
        if ($request->has('warehouse_id')) {
            $stockGudangQuery->where('warehouse_id', $request->warehouse_id);
        }

        // Dapatkan stok gudang dengan stok kurang dari 10, limit 5
        $stockGudang = $stockGudangQuery->with('barang')->whereHas('warehouse', function ($q) use ($bo) {
            $q->where('bisnis_owner_id', $bo->id);
        })->limit(5)->get();

        return response()->json([
            'status' => 'success',
            'stockGudang' => $stockGudang,
        ], 200);
    }

    public function stokFasyankesReport(Request $request)
    {
        $bo = $this->getBo();

        $stockFasyankesQuery = StockBarang::query();
        if ($request->has('fasyankes_warehouse_id')) {
            $stockFasyankesQuery->whereHas('fasyankes_warehouse.fasyankes', function ($q) use ($request, $bo) {
                $q->where('fasyankesId', $request->fasyankes_warehouse_id)->where('bisnis_owner_id', $bo->id);
            });
        }

        $stockFasyankes = $stockFasyankesQuery->with('barang')->limit(5)->get();

        return response()->json([
            'status' => 'success',
            'stockFasyankes' => $stockFasyankes,
        ], 200);
    }
}
