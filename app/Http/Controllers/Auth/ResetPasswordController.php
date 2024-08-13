<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BisnisOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }
        Log::info('Request data:', $request->all());
        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $request->token)->first();
        if (!$tokenData) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Token Invalid or Expired"
                ],
                422
            );
        }
        $user = BisnisOwner::where('email', $tokenData->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email Not Found',
            ], 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        log_activity('Melakukan Reset Password', 'Reset Password', $user->email, 1);
        return response()->json([
            'status' => true,
            'message' => 'Successfully Reset Password'
        ]);
    }
}
