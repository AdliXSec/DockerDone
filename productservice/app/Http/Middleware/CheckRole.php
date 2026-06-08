<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *

     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->auth_user;

        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.',
                'data'    => null,
            ], 403);
        }
        return $next($request);    
    }
}
