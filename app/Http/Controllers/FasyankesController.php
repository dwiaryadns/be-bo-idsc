<?php

namespace App\Http\Controllers;

use App\Models\AccessFasyankes;
use App\Models\BisnisOwner;
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
use Illuminate\Validation\Rule;

class FasyankesController extends Controller
{
    public function getFasyankes()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        $delegate = Auth::guard('delegate_access')->user();
        $id = $bo ? $bo->id : $delegate->bisnis_owner_id;
        if (!$bo && !$delegate) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }
        $fasyankes = Fasyankes::with('warehouse', 'legal_doc', 'subscription_plan')
            ->where('bisnis_owner_id', $id)
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
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'username' => [
                'required',
                Rule::unique('access_fasyankes', 'username')
                    ->ignore($request->fasyankesId, 'fasyankes_id'),
            ],
            'package_plan' => 'required',
            'warehouse_id' => 'required',
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'pic' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required',
            'pic_number' => 'required|numeric',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_]/',
                'regex:/[0-9]/'
            ],
            'password_confirmation' => 'required',
        ], [
            'type.required' => 'Tipe Fasyankes wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'package_plan.required' => 'Paket Plan wajib diisi.',
            'warehouse_id.required' => 'Gudang wajib diisi.',
            'name.required' => 'Nama Fasyankes wajib diisi.',
            'latitude.required' => 'Latitude wajib diisi.',
            'longitude.required' => 'Longitude wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'pic.required' => 'PIC wajib diisi.',
            'pic_number.required' => 'Nomor Telepon PIC wajib diisi.',
            'pic_number.numeric' => 'Nomor Telepon PIC harus berupa angka.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.regex' => 'Password harus mengandung minimal 1 huruf kapital, 1 karakter khusus, dan 1 angka.',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'message' => 'Gagal', 'errors' => $errors], 422);
        }

        $bo = Auth::guard('bisnis_owner')->user();
        if (empty($bo)) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }

        DB::beginTransaction();

        try {
            $fasyankes = Fasyankes::updateOrCreate([
                'fasyankesId' => $request->fasyankesId
            ], [
                'fasyankesId' => $request->fasyankesId ?? rand(100000, 999999),
                'type' => $request->type,
                'warehouse_id' => $request->warehouse_id,
                'name' => $request->name,
                'address' => $request->address,
                'pic' => $request->pic,
                'pic_number' => $request->pic_number,
                'email' => $request->email,
                'is_active' => 0,
                'bisnis_owner_id' => $bo->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'province' => $request->province,
                'city' => $request->city,
                'subdistrict' => $request->subdistrict,
                'village' => $request->village,
            ]);

            $accessFasyankes = AccessFasyankes::updateOrCreate([
                'fasyankes_id' => $fasyankes->fasyankesId,
            ], [
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'is_active' =>  0,
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
            return response()->json([
                'status' => false,
                'message' => 'Gagal',
            ]);
        }
        log_activity("Menambahkan Fasyankes $request->type $request->name", "Fasyankes", $bo->name, 1);
        if ($fasyankes) {
            return response()->json([
                'status' => true,
                'message' => 'Berhasil',
                'data' => $fasyankes,
                'subscription' => $subscriptionPlan,
            ], 200);
        }
    }

    public function getOtp($email)
    {
        $url = 'https://api.fazpass.com/v1/otp/request';
        $headers = [
            'Authorization: Bearer ' . env('AUTHORIZATION_KEY'),
            'Content-Type: application/json',
        ];
        $data = [
            'email' => $email,
            'phone' => '',
            'gateway_key' => env('GATEWAY_KEY'),
        ];

        Log::info($email);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);
        Log::info('response : ' . $response);
        Log::info('error : ' . $error);
        if ($error) {
            return $error;
        }
        return json_decode($response, true);
    }
    public function sendOtp(Request $request)
    {
        $email  = $request->email;
        $getOtp = $this->getOtp($email);
        Log::info('email : ' . $email);
        if ($getOtp['status'] === false) {
            return response()->json(['status' => false, 'message' => 'Email tidak valid, periksa kembali email yang Anda gunakan.']);
        }
        return response()->json(['status' => true, 'message' => 'Berhasil Mengirim OTP', 'otp_id' => $getOtp['data']['id']]);
    }
}
