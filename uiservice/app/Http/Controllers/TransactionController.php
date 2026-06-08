<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    public function index()
    {
        if (!session()->has('api_token')) {
            return redirect('/login')->withErrors(['login_error' => 'Sesi berakhir, silakan login.']);
        }

        $token = session('api_token');
        $userId = session('user_id');
        $role = session('user_role');
        $orders = [];

        try {
            // Jika admin, ambil semua. Jika user, ambil berdasarkan ID user
            $url = ($role == 'admin') 
                ? env('ORDER_SERVICE_URL') . '/orders' 
                : env('ORDER_SERVICE_URL') . '/orders/user/' . $userId;

            $response = Http::withToken($token)->get($url);
            
            if ($response->successful()) {
                $json = $response->json();
                $orders = $json['data'] ?? [];
                if (!is_array($orders)) $orders = [];
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan Transaksi sedang tidak tersedia.');
        }

        return view('transaksi', compact('orders'));
    }

    public function store(Request $request)
    {
        if (!session()->has('api_token')) {
            return redirect('/login');
        }

        // Get user data from is_login to get the ID
        $userResponse = Http::withToken(session('api_token'))->get('http://medtech-userservice:5000/is_login');
        $userData = $userResponse->json('data');

        if (!$userData) {
            return back()->with('warning', 'Sesi tidak valid, silakan login ulang.');
        }

        $payload = [
            'user_id' => $userData['id'],
            'product_id' => $request->obat_id,
            'quantity' => (int) ($request->quantity ?? 1),
        ];

        try {
            $response = Http::withToken(session('api_token'))->post(env('ORDER_SERVICE_URL') . '/orders', $payload);
            
            if ($response->successful()) {
                session()->flash('success', 'Berhasil memesan obat!');
                return redirect('/transaksi');
            } else {
                $error = $response->json('message') ?? 'Gagal memproses pesanan.';
                session()->flash('warning', $error);
                return back();
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan Order Service sedang bermasalah.');
            return back();
        }
    }

    public function update(Request $request, $id)
    {
        if (session('user_role') != 'admin') {
            return back()->with('warning', 'Hanya admin yang dapat mengubah status.');
        }

        try {
            // Kita panggil endpoint PATCH/PUT status di Order Service
            $response = Http::withToken(session('api_token'))->patch(env('ORDER_SERVICE_URL') . '/orders/' . $id . '/status', [
                'status' => $request->status
            ]);

            if ($response->successful()) {
                session()->flash('success', 'Status pesanan berhasil diperbarui.');
            } else {
                session()->flash('warning', 'Gagal memperbarui status di server.');
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Koneksi ke Order Service gagal.');
        }

        return redirect('/transaksi');
    }

    public function destroy($id)
    {
        if (!session()->has('api_token')) {
            return redirect('/login');
        }

        try {
            $response = Http::withToken(session('api_token'))->delete(env('ORDER_SERVICE_URL') . '/orders/' . $id);
            if ($response->successful()) {
                session()->flash('success', 'Pesanan berhasil dihapus.');
            } else {
                session()->flash('warning', 'Gagal menghapus pesanan.');
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Koneksi ke Order Service terputus.');
        }

        return redirect('/transaksi');
    }
}

