<?php

namespace App\Http\Controllers\Aidiva;

use App\Http\Controllers\Controller;
use App\Models\Fasyankes;
use App\Models\MasterPoa;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Str;

class ApiController extends Controller
{
    public function listFasyankes()
    {
        try {
            $fasyankes = Fasyankes::get();
            return response()->json([
                'status' => true,
                'data' => $fasyankes,
                'message' => 'Success get list fasyankes'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error get list fasyankes',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getBarang(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $query = MasterPoa::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(id_idsc) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhere('poa_code', 'like', "%{$search}%")
                    ->orWhereRaw('LOWER(pov) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(poa) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        $masterPoa = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Success Get Master Barang',
            'data' => $masterPoa
        ], 200);
    }
}
