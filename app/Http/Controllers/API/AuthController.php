<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BisnisOwner;
use App\Models\DelegateAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'email' => 'required|email|max:255|unique:bisnis_owners|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'phone' => 'required',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_]/',
                'regex:/[0-9]/'
            ],
        ], [
            'password.regex' => 'Password must contain at least 1 Uppercase Word, 1 Special Character, and 1 Number',
            'email.regex' => 'Format email tidak sesuai',
            'name.regex' => 'Format nama tidak sesuai',
            'phone.required' => 'Phone Harus diisi.',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }
        Log::info($request->all());
        $getOtp = $this->getOtp($request->email);
        Log::info($getOtp);

        if ($getOtp['status'] === false) {
            return response()->json(['status' => 'false', 'message' => 'Email tidak valid, periksa kembali email yang Anda gunakan.']);
        }

        $user = BisnisOwner::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        if (!$user) {
            return response()->json(
                ['success' => false, 'message' => 'Gagal Register'],
                400
            );
        }
        $token = $user->createToken('iDsm4rtC4R3')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration Successfully',
            'user' => $user,
            'otp_id' => $getOtp['data']['id'],
            'register_id' => encrypt(rand(1000000000000000000, 999999999999999999)),
        ], 200);
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

    public function changeEmail(Request $request)
    {
        $oldEmail = $request->old_email;
        $checkBo = BisnisOwner::where('email', $oldEmail)
            ->whereNull('email_verified_at')
            ->first();

        if (!$checkBo) {
            return response()->json([
                'status' => false,
                'message' => 'Email Lama Salah'
            ], 404);
        }
        $newEmail = $request->new_email;
        $checkExistBo = BisnisOwner::where('email', $newEmail)->first();
        if ($checkExistBo) {
            return response()->json([
                'status' => false,
                'message' => 'Email Baru Sudah Terdaftar'
            ], 422);
        }
        $getOtp = $this->getOtp($newEmail);
        if ($getOtp['status'] === false) {
            return response()->json(['status' => false, 'message' => 'Email baru tidak valid, periksa kembali email yang Anda gunakan.']);
        }
        $checkBo->update(
            ['email' => $newEmail]
        );
        return response()->json([
            'status' => true,
            'message' => 'Berhasil Merubah Email',
            'otp_id' => $getOtp['data']['id']
        ], 200);
    }

    public function resendOtp(Request $request)
    {
        $email = $request->email;
        $checkBo = BisnisOwner::where('email', $email)->first();
        if (!$checkBo) {
            return response()->json([
                'status' => false,
                'message' => 'Email Tidak Ditemukan'
            ], 404);
        }
        $getOtp = $this->getOtp($email);
        if ($getOtp['status'] === false) {
            return response()->json(['status' => false, 'message' => 'Email yang Anda gunakan tidak valid, periksa kembali email yang Anda gunakan.']);
        }
        return response()->json([
            'status' => true,
            'message' => 'Berhasil Mengirim Ulang OTP',
            'otp_id' => $getOtp['data']['id']
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                    'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/'
                ],
                'password' => 'required',
            ]
        );

        $user = BisnisOwner::where('email', $request->email)->first();

        $delegate = DelegateAccess::where('email', $request->email)->first();

        if ($user) {
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            } else if ($user->email_verified_at === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not verified, Please check your email to verify'
                ], 401);
            }

            $token = $user->createToken('iDsm4rtC4R3')->plainTextToken;

            $cookie = cookie('token', $token, 60 * 24); // 1 day

            log_activity('Melakukan Login', 'Login', $user->name, 1);
            return response()->json([
                'token' => $token,
                'status' => true,
                'message' => 'Login Successfully',
                'data' => $user
            ], 200)->withCookie($cookie);
        }

        if ($delegate) {
            if (!Hash::check($request->password, $delegate->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            $token = $delegate->createToken('iDsm4rtC4R3')->plainTextToken;

            $cookie = cookie('token', $token, 60 * 24); // 1 day

            log_activity('Melakukan Login', 'Login', $delegate->name, 1);
            return response()->json([
                'token' => $token,
                'status' => true,
                'message' => 'Login Successfully',
                'data' => $delegate
            ], 200)->withCookie($cookie);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $request->user()->currentAccessToken()->delete();
        Log::info($request->user());
        return response()->json(['status' => true, 'message' => 'Logged out successfully'], 200);
    }

    public function storeOtp(Request $request)
    {
        Log::info($request->all());
        $url = 'https://api.fazpass.com/v1/otp/verify';
        $headers = [
            'Authorization: Bearer ' . $request->header('Authorization'),
            'Content-Type: application/json',
        ];

        $bo = BisnisOwner::where('email', $request->email)->first();


        $data = [
            'otp_id' => $request->input('otp_id'),
            'otp' => $request->otp,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return response()->json(['error' => $error], 500);
        }
        Log::info($response);
        if ($bo) {
            $bo->markEmailAsVerified();
        }
        return response()->json(json_decode($response, true));
    }
}
