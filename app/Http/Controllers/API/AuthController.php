<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmailJob;
use App\Mail\SendOtpMail;
use App\Models\BisnisOwner;
use App\Models\OtpEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:bisnis_owners',
            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[!@#$%^&*(),.?":{}|<>_]/', 'regex:/[0-9]/'
            ],
        ], [
            'password.regex' => 'Password must contain at least 1 Uppercase Word, 1 Special Character, and 1 Number',
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

        try {
            SendOtpEmailJob::dispatch($request->email, $otp);
            Log::info('OTP job dispatched for email: ' . $request->email);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch OTP job: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to dispatch OTP job'], 500);
        }
        OtpEmail::create([
            'bisnis_owner_id' => $user->id,
            'otp' => $otp,
            'expired_at' => Carbon::now()->addMinutes(5)
        ]);
        return response()->json([
            'status' => true,
            'user' => $user,
            'register_id' => encrypt(rand(1000000000000000000, 999999999999999999)),
            'token_type' => 'Bearer'
        ], 200);
    }

    public function storeOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }

        $bo = BisnisOwner::where('email', $request->email)->first();
        if (!$bo) {
            return response()->json(['status' => false, 'message' => 'Email not found'], 404);
        }
        $otp = OtpEmail::where('bisnis_owner_id', $bo->id)
            ->where('otp', $request->otp)
            ->where('expired_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['status' => false, 'message' => 'OTP not valid or expired'], 422);
        }

        $bo->markEmailAsVerified();
        $bo->update([
            'is_send_email' => 1,
        ]);
        return response()->json(['status' => true, 'message' => 'Registrasi Successfully'], 200);
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
                    'required', 'email', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/'
                ],
                'password' => 'required',
            ],
        );

        $user = BisnisOwner::where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password'
            ], 401);
        } else if ($user && $user->email_verified_at === null) {
            return response()->json([
                'status' => false,
                'message' => 'User not verified, Please Cek your email to verify'
            ], 401);
        }

        $token = $user->createToken('iDsm4rtC4R3')->plainTextToken;

        return response()->json([
            'token' => $token,
            'status' => true,
            'message' => 'Login Successfully',
            'data' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => true, 'message' => 'Logged out successfully'], 200);
    }
}
