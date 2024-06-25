<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BisnisOwner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNull;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = BisnisOwner::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email Not Found',
            ], 404);
        }
        try {

            $token = Str::random(60);
            DB::table('password_reset_tokens')->insert(
                [
                    'email' => $user->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            $user->sendPasswordResetNotification($token);

            return response()->json(['message' => 'Reset password link telah dikirimkan ke email Anda']);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan'], 500); // Jika terjadi kesalahan, beri respons 500
        }
    }

    public function checkToken($token)
    {
        $tokenCheck = DB::table('password_reset_tokens')->where('token', $token)->first();
        if ($tokenCheck === null) {
            Log::info('invalid');
            return response()->json([
                'status' => false,
                'message' => 'Invalid Token',
            ], 404);
        } else {
            Log::info('valid');
            return response()->json([
                'status' => true,
                'message' => 'Valid Token',
            ], 200);
        }
    }
}
