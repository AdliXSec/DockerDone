<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        if (!session()->has('api_token')) {
            return redirect('/login')->withErrors(['login_error' => 'Sesi berakhir, silakan login kembali.']);
        }

        $token = session('api_token');
        $userData = null;

        try {
            // Ambil data terbaru dari User Service
            $response = Http::withToken($token)->timeout(5)->get('http://medtech-userservice:5000/is_login');
            
            if ($response->successful()) {
                $userData = $response->json('data');
                
                // SINKRONISASI: Pastikan user_id ada di session (jika user tidak relogin)
                if ($userData && isset($userData['id'])) {
                    session(['user_id' => $userData['id']]);
                    session(['user_role' => $userData['role'] ?? session('user_role')]);
                }
            } else {
                return redirect('/login')->withErrors(['login_error' => 'Gagal mengambil data profil dari server.']);
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan User Service sedang down.');
        }

        return view('profile', compact('userData'));
    }

    public function update(Request $request)
    {
        if (!session()->has('api_token')) {
            return redirect('/login');
        }

        $userId = session('user_id');
        if (!$userId) {
            return back()->withErrors(['api_error' => 'ID User tidak ditemukan. Silakan logout dan login kembali.']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $token = session('api_token');

        // Siapkan payload
        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => session('user_role')
        ];

        // Jika password diisi, sertakan dalam payload. 
        // Jika tidak, kita kirim null
        $payload['password'] = $request->filled('password') ? $request->password : null;

        try {
            $response = Http::withToken($token)->timeout(10)->put("http://medtech-userservice:5000/users/{$userId}", $payload);

            if ($response->successful()) {
                session(['user_name' => $request->name]);
                return back()->with('success', 'Profil berhasil diperbarui!');
            } else {
                // Berikan pesan error spesifik dari Flask jika ada
                $error = $response->json('message') ?? 'Gagal memperbarui profil (Status: ' . $response->status() . ')';
                return back()->withErrors(['api_error' => $error])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['api_error' => 'Koneksi ke server User Service gagal: ' . $e->getMessage()])->withInput();
        }
    }
}

