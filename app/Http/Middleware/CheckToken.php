<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = 'IDsM4RtcaR32024*@>';
        if ($request->header('Authorization') != $token) {
            return response()->json([
                'status' => false,
                'message' => 'Token is Invalid'
            ], 401);
        }

        return $next($request);
    }
}
