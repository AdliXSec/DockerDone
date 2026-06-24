<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('api_token')) {
            return redirect('/login')->withErrors(['login_error' => 'Sesi berakhir, silakan login.']);
        }

        $token = session('api_token');
        
        $totalObat = 0;
        $totalPesanan = 0;
        $totalUser = 0;

        // 1. Fetch Total Obat (Product Service via GraphQL)
        try {
            $productGraphqlUrl = rtrim(env('PRODUCT_SERVICE_URL'), '/') . '/graphql';

            $query = '
                query {
                    obats {
                        id
                    }
                }
            ';

            $productResponse = Http::withToken($token)->timeout(60)->withOptions(['force_ip_resolve' => 'v4'])->post($productGraphqlUrl, [
                'query' => $query,
            ]);

            if ($productResponse->successful()) {
                $json = $productResponse->json();
                $data = $json['data']['obats'] ?? [];
                $totalObat = is_array($data) ? count($data) : 0;
            }
        } catch (\Exception $e) {
            session()->flash('warning_obat', 'Gagal memuat data obat.');
        }

        // 2. Fetch Total Pesanan (Order Service via GraphQL)
        try {
            $orderGraphqlUrl = rtrim(env('ORDER_SERVICE_URL'), '/') . '/graphql';
            $userId = session('user_id');
            $role = session('user_role');
            
            if ($role == 'admin') {
                $query = '
                    query {
                        orders {
                            id
                        }
                    }
                ';
                $orderResponse = Http::withToken($token)->timeout(60)->withOptions(['force_ip_resolve' => 'v4'])->post($orderGraphqlUrl, [
                    'query' => $query,
                ]);

                if ($orderResponse->successful()) {
                    $json = $orderResponse->json();
                    $data = $json['data']['orders'] ?? [];
                    $totalPesanan = is_array($data) ? count($data) : 0;
                }
            } else {
                $query = '
                    query GetOrdersByUser($user_id: ID!) {
                        ordersByUser(user_id: $user_id) {
                            id
                        }
                    }
                ';
                $orderResponse = Http::withToken($token)->timeout(60)->withOptions(['force_ip_resolve' => 'v4'])->post($orderGraphqlUrl, [
                    'query' => $query,
                    'variables' => ['user_id' => (string) $userId],
                ]);

                if ($orderResponse->successful()) {
                    $json = $orderResponse->json();
                    $data = $json['data']['ordersByUser'] ?? [];
                    $totalPesanan = is_array($data) ? count($data) : 0;
                }
            }
        } catch (\Exception $e) {
            session()->flash('warning_order', 'Gagal memuat data transaksi.');
        }

        // 3. Fetch Total User (User Service - tetap RESTful karena Flask)
        try {
            $userResponse = Http::withToken($token)->timeout(60)->withOptions(['force_ip_resolve' => 'v4'])->get('http://medtech-userservice:5000/users');
            if ($userResponse->successful()) {
                $json = $userResponse->json();
                $data = $json['data'] ?? [];
                $totalUser = is_array($data) ? count($data) : 0;
            }
        } catch (\Exception $e) {
            session()->flash('warning_user', 'Gagal memuat data pengguna.');
        }

        return view('dashboard', compact('totalObat', 'totalPesanan', 'totalUser'));
    }
}
