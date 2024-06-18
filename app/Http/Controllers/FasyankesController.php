<?php

namespace App\Http\Controllers;

use App\Models\Fasyankes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FasyankesController extends Controller
{
    public function getFasyankes()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $fasyankes = Fasyankes::where('bisnis_owner_id', $bo->id)
            ->where('is_active', 1)
            ->get();

        if (!$fasyankes) {
            return response()->json([
                'status' => false,
                'message' => 'Data Fasyankes Not Found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Get Data Fasyankes',
            'data' => $fasyankes
        ], 200);
    }

    public function storeFasyankes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'sector' => 'required',
            'duration' => 'required',
            'package_plan' => 'required',
            'warehouse_id' => 'required',
            'name' => 'required',
            'address' => 'required',
            'pic' => 'required',
            'pic_number' => 'required|numeric',
            'email' => 'required|email|unique:fasyankes',
            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[!@#$%^&*(),.?":{}|<>_]/', 'regex:/[0-9]/'
            ],
            'password_confirmation' => 'required',
        ], [
            'type.required' => 'Type of Fasyankes is required',
            'sector.required' => 'Sektor Usaha is required',
            'package_plan.required' => 'Package Plan is required',
            'warehouse_id.required' => 'Warehouse is required',
            'name.required' => 'Name Fasyankes is required',
            'address.required' => 'Address is required',
            'pic.required' => 'PIC is required',
            'pic_number.required' => 'PIC Phone Number is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
            'email.unique' => 'Email already registered',
            'password.regex' => 'Password must contain at least 1 Uppercase Word, 1 Special Character, and 1 Number',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'message' => 'Failed Create Fasyankes', 'errors' => $errors], 422);
        }

        $bo = Auth::guard('bisnis_owner')->user();
        if (empty($bo)) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        $fasyankes = Fasyankes::updateOrCreate([
            'fasyankesId' => $request->fasyankesId
        ], [
            'fasyankesId' => rand(100000, 999999),
            'type' => $request->type,
            'sector' => $request->sector,
            'duration' => $request->duration,
            'package_plan' => $request->package_plan,
            'warehouse_id' => $request->warehouse_id,
            'name' => $request->name,
            'address' => $request->address,
            'pic' => $request->pic,
            'pic_number' => $request->pic_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'start_date' => Carbon::now(),
            'end_date' => $request->duration === 'Monthly' ? Carbon::now()->addMonths(3) : Carbon::now()->addYear(),
            'is_active' => 0,
            'bisnis_owner_id' => $bo->id,
        ]);

        if ($fasyankes) {
            return response()->json([
                'status' => true,
                'message' => 'Success Updated Fasyankes',
                'data' => $fasyankes
            ], 200);
        }
    }
}
