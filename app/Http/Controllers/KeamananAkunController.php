<?php

namespace App\Http\Controllers;

use App\Models\BisnisOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KeamananAkunController extends Controller
{
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
        $email = $request->email;
        $checkBO = BisnisOwner::where('email', $email)->first();
        if (!$checkBO) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found'
            ], 404);
        }
        $getOtp = $this->getOtp($email);
        if ($getOtp['status'] === false) {
            return response()->json(['status' => false, 'message' => 'Email tidak valid, periksa kembali email yang Anda gunakan.']);
        }
        return response()->json(['status' => true, 'message' => 'Berhasil Mengirim OTP', 'otp_id' => $getOtp['data']['id']]);
    }
    public function changePassword(Request $request)
    {
        $user = Auth::guard('bisnis_owner')->user();

        $validator = Validator::make($request->all(), [
            'old_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_]/',
                'regex:/[0-9]/',
                function ($attribute, $value, $fail) use ($user) {
                    if (Hash::check($value, $user->password)) {
                        $fail('The new password cannot be the same as the old password.');
                    }
                }
            ],
            'new_password_confirmation' => ['required', 'min:8', 'regex:/[A-Z]/', 'regex:/[!@#$%^&*(),.?":{}|<>_]/', 'regex:/[0-9]/']
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }

        if (!$user || !Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid old password'
            ], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        log_activity('Melakukan Ubah Password', 'Keamanan Akun', $user->name, 1);
        return response()->json([
            'status' => true,
            'message' => 'Password Berhasil Diubah'
        ], 200);
    }
}
