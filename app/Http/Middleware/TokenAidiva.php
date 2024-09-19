<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAidiva
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = env('TOKEN_AIDIVA');
        $secretKey = env('SECRET_KEY_AIDIVA');
        if ($request->header('Authorization') == null || $request->header('secret_key') == null) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
        if ($request->header('Authorization') != $token) {
            return response()->json([
                'status' => false,
                'message' => 'Token is Invalid'
            ], 401);
        } else if ($request->header('secret_key') != $secretKey) {
            return response()->json([
                'status' => false,
                'message' => 'Secret Key is Invalid'
            ], 401);
        }

        return $next($request);
    }
}
