<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        // 1. Cek apakah session 'api_token' ada nilainya
        if (!session()->has('api_token')) {
            return redirect('/login')->withErrors(['login_error' => 'Sesi berakhir, silakan login kembali.']);
        }

        // 2. Jika ada, ambil tokennya untuk digunakan
        $token = session('api_token');

        // 3. Gunakan token untuk mengambil data dari Flask
        $response = Http::withToken($token)->get('http://medtech-userservice:5000/is_login');

        if ($response->successful()) {
            $data = $response->json();
            return view('dashboard', compact('data'));
        }

        return redirect('/login')->withErrors(['login_error' => 'Token tidak valid.']);
    }

    // Logout function
    public function logout(Request $request)
    {
        $token = session('api_token');

        try {
            // Call Flask Logout
            Http::withToken($token)->post('http://medtech-userservice:5000/logout');
        } catch (\Exception $e) {
            // Silence error if flask is down
        }

        // Clear Laravel Session
        session()->forget(['api_token', 'user_id', 'user_name', 'user_role']);
        session()->flush();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function register(Request $request)
    {
        // 1. Validasi input dari form UI Blade
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Kirim data ke API Python Flask
        // (Pastikan URL ini sesuai dengan URL Flask kamu yang sedang berjalan)
        $flaskUrl = 'http://medtech-userservice:5000/register';

        try {
            // Tembak API Flask dengan format JSON eksplisit
            $response = Http::post($flaskUrl, [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role
            ]);

            // 3. Cek respon dari Flask
            if ($response->successful()) {
                // Jika Flask berhasil menyimpan ke databasenya, alihkan user ke halaman Login
                return redirect('/login')->with('success', 'Akun berhasil dibuat di Flask! Silakan login.');
            } else {
                // Jika Flask menolak (misal: email sudah ada di database Flask)
                $pesanError = $response->json('message') ?? 'Gagal mendaftar di server Flask.';
                return back()->withErrors(['api_error' => $pesanError])->withInput();
            }
        } catch (\Exception $e) {
            // Jika server Flask mati atau tidak bisa dihubungi
            return back()->withErrors(['api_error' => 'Koneksi ke User Service (Flask) terputus.'])->withInput();
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $flaskUrl = 'http://medtech-userservice:5000/login';

        try {
            $response = Http::post($flaskUrl, [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                // 1. Ambil data JSON dari respons Flask
                $data = $response->json()['data'];

                // 2. Simpan token ke dalam Session Laravel
                $token = $data['access_token'];
                session(['api_token' => $token]);

                // Simpan data user lainnya
                session(['user_id' => $data['user']['id'] ?? null]);
                session(['user_name' => $data['user']['name'] ?? 'Admin']);
                session(['user_role' => $data['user']['role'] ?? 'user']);

                return redirect('/dashboard');
            }

            $errorMessage = $response->json('message') ?? 'Email atau password salah.';
            return back()->withErrors(['login_error' => $errorMessage])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors(['api_error' => 'Koneksi ke server gagal.'])->withInput();
        }
    }

    // ... fungsi login dan logout biarkan saja dulu ...
}

