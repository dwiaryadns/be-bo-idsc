<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmailJob;
use App\Mail\SendOtpMail;
use App\Models\BisnisOwner;
use App\Models\DelegateAccess;
use App\Models\OtpEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function generateOtp()
    {
        $otp = rand(100000, 999999);
        return $otp;
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'email' => 'required|email|max:255|unique:bisnis_owners|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
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
            'email.regex' => 'Invalid format email',
            'name.regex' => 'Invalid format fullname',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }
        $user = BisnisOwner::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if (!$user) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
        $token = $user->createToken('iDsm4rtC4R3')->plainTextToken;

        $otp = $this->generateOtp();

        if ($otp === null) {
            Log::error('OTP is null for email: ' . $request->email);
            return response()->json(['status' => false, 'message' => 'Failed to generate OTP'], 500);
        }
        return response()->json([
            'status' => true,
            'message' => 'Registration Successfully',
            'user' => $user,
            'register_id' => encrypt(rand(1000000000000000000, 999999999999999999)),
        ], 200);
    }

    public function resendOtp(Request $request)
    {
        Log::info('Resend OTP request received for email: ' . $request->email);

        $bo = BisnisOwner::where('email', $request->email)->first();
        if (!$bo) {
            Log::error('Email not found: ' . $request->email);
            return response()->json(['status' => false, 'message' => 'Email not found'], 404);
        }

        $otp = $this->generateOtp();
        Log::info('Generated OTP: ' . $otp . ' for email: ' . $request->email);

        OtpEmail::create([
            'bisnis_owner_id' => $bo->id,
            'otp' => $otp,
            'expired_at' => Carbon::now()->addMinutes(5)
        ]);

        if ($otp === null) {
            Log::error('OTP is null for email: ' . $request->email);
            return response()->json(['status' => false, 'message' => 'Failed to generate OTP'], 500);
        }

        try {
            SendOtpEmailJob::dispatch($request->email, $otp);
            Log::info('OTP job dispatched for email: ' . $request->email);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch OTP job: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to dispatch OTP job'], 500);
        }

        return response()->json(['status' => true, 'message' => 'Resend OTP Successfully'], 200);
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

    public function getOtp(Request $request)
    {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'newEmail' => 'nullable|email|max:255|unique:bisnis_owners,email'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email salah',
            'email.unique' => 'Email sudah terdaftar',
            'newEmail.email' => 'Format email salah',
            'newEmail.unique' => 'Email baru sudah terdaftar',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'message' => 'Gagal Merubah Email', 'errors' => $errors], 422);
        }


        $url = 'https://api.fazpass.com/v1/otp/request';
        $headers = [
            'Authorization: Bearer ' . $request->header('Authorization'),
            'Content-Type: application/json',
        ];
        $newEmail = $request->newEmail;
        $data = [
            'email' => $newEmail ?? $request->input('email'),
            'phone' => '',
            'gateway_key' => env('GATEWAY_KEY', '3954aa72-856e-49eb-8b1c-f18d658ee067'),
        ];

        Log::info($request->all());
        if ($newEmail) {
            $checkBo = BisnisOwner::where('email', $request->email)->first();
            if ($checkBo && $checkBo->email == $newEmail) {
                return response()->json(['status' => false, 'message' => 'Email sudah terdaftar'], 422);
            }
            $checkBo->update(['email' => $newEmail]);
        }


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
        Log::info('response : ' . $response);
        return response()->json(json_decode($response, true));
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
