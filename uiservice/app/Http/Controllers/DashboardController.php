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

        // 1. Fetch Total Obat (Product Service)
        try {
            $productResponse = Http::withToken($token)->timeout(5)->get(env('PRODUCT_SERVICE_URL') . '/obat');
            if ($productResponse->successful()) {
                $json = $productResponse->json();
                $data = $json['data'] ?? [];
                $totalObat = is_array($data) ? count($data) : 0;
            }
        } catch (\Exception $e) {
            session()->flash('warning_obat', 'Gagal memuat data obat.');
        }

        // 2. Fetch Total Pesanan (Order Service)
        try {
            $userId = session('user_id');
            $role = session('user_role');
            
            $url = ($role == 'admin') 
                ? env('ORDER_SERVICE_URL') . '/orders' 
                : env('ORDER_SERVICE_URL') . '/orders/user/' . $userId;

            $orderResponse = Http::withToken($token)->timeout(5)->get($url);
            
            if ($orderResponse->successful()) {
                $json = $orderResponse->json();
                $data = $json['data'] ?? [];
                $totalPesanan = is_array($data) ? count($data) : 0;
            }
        } catch (\Exception $e) {
            session()->flash('warning_order', 'Gagal memuat data transaksi.');
        }

        // 3. Fetch Total User (User Service)
        try {
            $userResponse = Http::withToken($token)->timeout(5)->get('http://medtech-userservice:5000/users');
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

