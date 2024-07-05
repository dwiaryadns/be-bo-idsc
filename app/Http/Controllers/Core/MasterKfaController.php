<?php

namespace App\Http\Controllers\Core;

use App\Models\MasterKfa;
use App\Http\Controllers\Controller;

use App\Models\MasterKfaPoa;
use App\Models\MasterKfaPov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterKfaController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.token');
    }
    private function searchAndPaginate(Request $request, $model, array $searchableColumns)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $query = $model::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhereRaw("LOWER($column) LIKE ?", ['%' . strtolower($search) . '%']);
                }
            });
        }
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function index(Request $request)
    {
        $searchableColumns = [
            'kfa_code',
            'kfa_name',
            'kfa_desc'
        ];
        $masterKfas = $this->searchAndPaginate($request, MasterKfa::class, $searchableColumns);
        return response()->json($masterKfas);
    }

    public function kfa_pov(Request $request)
    {
        $searchableColumns = [
            'kfa_pov_code',
            'kfa_code',
            'kfa_pov_idsc',
            'pov_desc',
            'product_state'
        ];
        $masterKfaPov = $this->searchAndPaginate($request, MasterKfaPov::class, $searchableColumns);
        return response()->json([
            'status' => true,
            'data' => $masterKfaPov
        ]);
    }

    public function kfa_poa(Request $request)
    {
        $searchableColumns = [
            'kfa_poa_code',
            'kfa_pov_code',
            'kfa_poa_idsc',
            'poa_desc',
            'manufacture',
            'kfa_code_poak',
            'pack_type',
            'estimate_pack_price',
            'made_in'
        ];
        $masterKfaPoa = $this->searchAndPaginate($request, MasterKfaPoa::class, $searchableColumns);
        return response()->json([
            'status' => true,
            'data' => $masterKfaPoa
        ]);
    }
}
