<?php

namespace App\Http\Controllers\Aidiva;

use App\Http\Controllers\Controller;
use App\Models\Fasyankes;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function listFasyankes() {
        try {
            $fasyankes = Fasyankes::get();
            return response()->json([
                'status'=> true,
               'data' => $fasyankes,
               'message' => 'Success get list fasyankes'
            ] );
        } catch (\Throwable $th) {
            return response()->json([
               'status'=> false,
               'message' => 'Error get list fasyankes',
               'error' => $th->getMessage()
            ]);
        }

    }
}
