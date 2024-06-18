<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function getWarehouses()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $warehouses = Warehouse::where('bisnis_owner_id', $bo->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Success get warehouse',
            'data' => $warehouses
        ], 200);
    }

    public function storeWarehouse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'pic' => 'required|string',
            'contact' => 'required|numeric'
        ], [
            'name.required' => 'The Warehouse Name field is required',
            'address.required' => 'The Warehouse Address field is required',
            'pic.required' => 'The PIC Name field is required',
            'contact.required' => 'The PIC Number field is required',
            'contact.numeric' => 'The PIC Number field must be a number'
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'message' => 'Failed Create Fasyankes', 'errors' => $errors], 422);
        }

        $user = Auth::guard('bisnis_owner')->user();
        if (empty($user)) {
            Log::error('User is not authenticated');
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }
        $warehouse = Warehouse::create(
            [
                'bisnis_owner_id' => $user->id,
                'name' => $request->name,
                'address' => $request->address,
                'pic' => $request->pic,
                'contact' => $request->contact,
            ]
        );
        if ($warehouse) {
            return response()->json([
                'status' => true,
                'message' => 'Success create warehouse',
                'data' => $warehouse
            ], 200);
        }
    }
}
