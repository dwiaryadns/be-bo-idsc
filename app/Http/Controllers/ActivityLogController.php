<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    public function activity_log(Request $request)
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
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $isSearch = filter_var($request->get('isSearch', ''), FILTER_VALIDATE_BOOLEAN);

        $query = ActivityLog::query();

        // ->addDay()->format('Y-m-d')
        $startDateFormat = Carbon::parse($startDate);
        $endDateFormat = Carbon::parse($endDate);
        Log::info('-------------');
        Log::info($startDate . ' ' . $startDateFormat);
        Log::info($endDate . ' ' . $endDateFormat);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(activity_by) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(menu) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(activity) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        Log::info($startDate === $endDate);
        if (($startDate === $endDate) && $isSearch) {
            Log::info('SATU TANGGAL');
            $query->whereDate('activity_at', $endDateFormat->addDay()->format('Y-m-d'));
        } else if ((!empty($startDate) && !empty($endDate)) && $isSearch) {
            Log::info('DUA TANGGAL');
            $query->whereBetween('activity_at', [$startDateFormat->subDay()->format('Y-m-d'), $endDateFormat->addDays(2)->format('Y-m-d')]);
        } else if (!$isSearch) {
            Log::info('TANPA TANGGAL');
            $query->whereMonth('activity_at', date('m'));
        }

        $logs = $query->where('activity_by', $bo->name)
            ->orderBy('activity_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Success Get Activity Log',
            'data' => $logs
        ], 200);
    }
}
