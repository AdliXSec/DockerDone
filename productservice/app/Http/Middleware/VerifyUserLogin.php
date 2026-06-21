<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyUserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Token tidak ditemukan, silahkan login terlebih dahulu',
                'data'    => null,
            ], 401);
        }

        try {
            $userServiceUrl = 'http://medtech-userservice:5000';
            // Forward token ke User Service endpoint /is_login
            $response = Http::timeout(5)
                ->withOptions(['force_ip_resolve' => 'v4'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$userServiceUrl}/is_login");

            if ($response->successful() && $response->json()['status'] === 'berhasil') {
                $userData = $response->json()['data'];
                // Simpan data user ke request agar bisa dipakai di middleware lain & controller
                $request->merge([
                    'auth_user' => $userData,
                    'user_role' => $userData['role'] ?? 'user'
                ]);

                return $next($request);
            }

            return response()->json([
                'status'  => 'gagal',
                'message' => 'User tidak terautentikasi, token tidak valid',
                'data'    => null,
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Gagal menghubungi User Service',
                'data'    => null,
            ], 503);
        }
    }
}

