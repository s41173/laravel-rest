<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckCustomerSession
{
    public function handle($request, Closure $next)
    {
        $guard = auth('api');
        $user = $guard->user();

        // 1️⃣ JWT tidak valid
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $key = "customer_session:{$user->id}";
        $session = Redis::get($key);

        // 2️⃣ Session Redis tidak ada
        if (!$session) {
            return response()->json([
                'message' => 'Session expired'
            ], 401);
        }

        $sessionData = json_decode($session, true);

        // ambil token dari header
        $requestToken = $guard->getToken()->get();

        // 3️⃣ Token tidak cocok
        if (
            !isset($sessionData['token']) ||
            $sessionData['token'] !== $requestToken
        ) {
            return response()->json([
                'message' => 'Token mismatch'
            ], 401);
        }

        return $next($request);
    }
}