<?php

namespace App\Http\Controllers;

use App\Models\AccessFasyankes;
use App\Models\Fasyankes;
use App\Models\FasyankesWarehouse;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FasyankesController extends Controller
{
    public function getFasyankes()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $fasyankes = Fasyankes::where('bisnis_owner_id', $bo->id)
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
            'username' => 'required|unique:access_fasyankes,username',
            'package_plan' => 'required',
            'warehouse_id' => 'required',
            'name' => 'required',
            'address' => 'required',
            'pic' => 'required',
            'pic_number' => 'required|numeric',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[!@#$%^&*(),.?":{}|<>_]/', 'regex:/[0-9]/'
            ],
            'password_confirmation' => 'required',
        ], [
            'type.required' => 'Type of Fasyankes is required',
            'username.required' => 'Username is required',
            'username.unique' => 'Username is registered',
            'package_plan.required' => 'Package Plan is required',
            'warehouse_id.required' => 'Warehouse is required',
            'name.required' => 'Name Fasyankes is required',
            'address.required' => 'Address is required',
            'pic.required' => 'PIC is required',
            'pic_number.required' => 'PIC Phone Number is required',
            'pic_number.numeric' => 'PIC Phone Number field must be a number.',
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
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

        DB::beginTransaction();

        try {
            $fasyankes = Fasyankes::updateOrCreate([
                'fasyankesId' => $request->fasyankesId
            ], [
                'fasyankesId' => $request->fasyankesId ?: rand(100000, 999999),
                'type' => $request->type,
                'warehouse_id' => $request->warehouse_id,
                'name' => $request->name,
                'address' => $request->address,
                'pic' => $request->pic,
                'pic_number' => $request->pic_number,
                'email' => $request->email,
                'is_active' => 1,
                'bisnis_owner_id' => $bo->id,
            ]);

            $accessFasyankes = AccessFasyankes::updateOrCreate([
                'fasyankes_id' => $fasyankes->fasyankesId,
            ], [
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'is_active' => 1,
                'created_by' => $bo->name,
            ]);

            $countFasyankesWarehouse = FasyankesWarehouse::count();
            $fasyankesWarehouse = FasyankesWarehouse::create([
                'wfid' => 'WFID' . date('Y') . date('m') . '0000' . $countFasyankesWarehouse + 1,
                'fasyankes_id' => $fasyankes->fasyankesId,
                'warehouse_id' => $request->warehouse_id
            ]);

            $subscriptionPlan = SubscriptionPlan::updateOrCreate([
                'fasyankes_id' => $fasyankes->fasyankesId,
            ], [
                'price' => (int) str_replace('.', '', $request->price),
                'duration' => $request->duration,
                'package_plan' => $request->package_plan,
                'start_date' => Carbon::now(),
                'end_date' => $request->duration === 'Monthly' ? Carbon::now()->addMonth() : Carbon::now()->addYear(),
            ]);
            DB::commit();
            Log::info('Berhasil');
            Log::info($fasyankes);
            Log::info($accessFasyankes);
            Log::info($fasyankesWarehouse);
            Log::info($subscriptionPlan);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollback();
        }
        if ($fasyankes) {
            return response()->json([
                'status' => true,
                'message' => 'Success Updated Fasyankes',
                'data' => $fasyankes,
                'subscription' => $subscriptionPlan,
            ], 200);
        }
    }
}
