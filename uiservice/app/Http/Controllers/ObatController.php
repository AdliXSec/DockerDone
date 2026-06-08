<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('api_token')) {
            return redirect('/login')->withErrors(['login_error' => 'Sesi berakhir, silakan login.']);
        }

        // 1. Tangkap ID dari form pencarian
        $searchId = $request->input('search_id');

        // Base URL API Obat
        $apiUrl = env('PRODUCT_SERVICE_URL') . '/obat';
        $obatList = [];

        try {
            if ($searchId) {
                // JIKA MENCARI ID: Tembak ke endpoint /api/obat/{id}
                $response = Http::withToken(session('api_token'))->get($apiUrl . '/' . $searchId);
            } else {
                // JIKA KOSONG: Tembak ke endpoint /api/obat (Ambil Semua)
                $response = Http::withToken(session('api_token'))->get($apiUrl);
            }

            if ($response->successful()) {
                $data = $response->json('data');

                // PERHATIAN: Jika mencari by ID, API hanya mengembalikan 1 Objek (bukan Array).
                // Agar @forelse di Blade tidak error, kita harus membungkus 1 objek itu ke dalam Array.
                if ($searchId && !empty($data)) {
                    $obatList = [$data];
                } else {
                    // Jika ambil semua, datanya memang sudah Array
                    $obatList = $data ?? [];
                }
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan Katalog Obat sedang tidak dapat diakses.');
        }

        return view('obat', compact('obatList'));
    }

    public function store(Request $request)
    {
        // 1. Proteksi Ekstra: Pastikan hanya admin yang bisa memproses ini
        if (session('user_role') != 'admin') {
            session()->flash('warning', 'Akses ditolak! Anda bukan admin.');
            return redirect('/obat');
        }

        // 2. Siapkan data Payload sesuai dengan struktur JSON API Product Service
        $payload = [
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'price' => (int) $request->input('price'),
            'stock' => (int) $request->input('stock'),
            'description' => $request->input('description'),
        ];

        $apiUrl = env('PRODUCT_SERVICE_URL') . '/obat';

        try {
            // 3. Tembak API menggunakan method POST dan bawa Token serta Payload
            $response = Http::withToken(session('api_token'))->post($apiUrl, $payload);

            // 4. Evaluasi Hasil
            if ($response->successful()) {
                session()->flash('success', 'Berhasil! Data obat baru telah tersimpan di database.');
            } else {
                session()->flash('warning', 'Gagal menyimpan data ke API. Periksa kembali isian Anda.');
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan API Obat sedang down. Tidak dapat menyimpan data.');
        }

        // 5. Kembalikan user ke halaman katalog obat
        return redirect('/obat');
    }

    public function update(Request $request, $id)
    {
        if (session('user_role') != 'admin') {
            return redirect('/obat')->withErrors(['login_error' => 'Akses ditolak!']);
        }

        $payload = [
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'price' => (int) $request->input('price'),
            'stock' => (int) $request->input('stock'),
            'description' => $request->input('description', ''), // Bisa kosong
        ];

        $apiUrl = env('PRODUCT_SERVICE_URL') . '/obat/' . $id;

        try {
            // Gunakan PUT untuk mengupdate data
            $response = Http::withToken(session('api_token'))->put($apiUrl, $payload);

            if ($response->successful()) {
                session()->flash('success', 'Data obat berhasil diperbarui.');
            } else {
                session()->flash('warning', 'Gagal memperbarui data. Pastikan format benar.');
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan API Obat sedang down.');
        }

        return redirect('/obat');
    }

    public function destroy($id)
    {
        if (session('user_role') != 'admin') {
            return redirect('/obat')->withErrors(['login_error' => 'Akses ditolak!']);
        }

        $apiUrl = env('PRODUCT_SERVICE_URL') . '/obat/' . $id;

        try {
            // Gunakan DELETE untuk menghapus data
            $response = Http::withToken(session('api_token'))->delete($apiUrl);

            if ($response->successful()) {
                session()->flash('success', 'Data obat berhasil dihapus dari sistem.');
            } else {
                session()->flash('warning', 'Gagal menghapus data obat.');
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan API Obat sedang down.');
        }

        return redirect('/obat');
    }
}
