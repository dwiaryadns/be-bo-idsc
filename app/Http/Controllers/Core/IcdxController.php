<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Icdx;
use Illuminate\Http\Request;

class IcdxController extends Controller
{
    public function icdx(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        $query = Icdx::query();

        $query->selectRaw("id, category, sub_category, en_name, id_name, created_at, updated_at, 
                  CASE 
                      WHEN sub_category IS NULL OR sub_category = 'NULL' THEN category 
                      ELSE CONCAT(category, '.', sub_category) 
                  END as code");

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $search = strtolower($search);
                $q->whereRaw("LOWER(CONCAT(category, '.', sub_category)) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(en_name) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(id_name) LIKE ?', ['%' . $search . '%']);
            });
        }

        $icdx = $query->paginate($perPage, ['*'], 'page', $page);

        $icdx->getCollection()->transform(function ($item) {
            $item->code = $item->sub_category !== 'NULL' && $item->sub_category !== null ? $item->category . '.' . $item->sub_category : $item->category;
            unset($item->category, $item->sub_category);
            unset($item->created_at, $item->updated_at);
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Get Diagnosis',
            'data' => $icdx
        ], 200);
    }
}
