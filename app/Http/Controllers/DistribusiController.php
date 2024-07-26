<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
