<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ObatController extends Controller
{
    /**
     * Helper: Kirim GraphQL request ke Product Service
     */
    private function graphql($query, $variables = [], $token = null)
    {
        $url = rtrim(env('PRODUCT_SERVICE_URL'), '/') . '/graphql';

        $request = Http::timeout(60)->withOptions(['force_ip_resolve' => 'v4']);
        if ($token) {
            $request = $request->withToken($token);
        }

        return $request->post($url, [
            'query' => $query,
            'variables' => $variables,
        ]);
    }

    public function index(Request $request)
    {
        if (!session()->has('api_token')) {
            return redirect('/login')->withErrors(['login_error' => 'Sesi berakhir, silakan login.']);
        }

        // 1. Tangkap ID dari form pencarian
        $searchId = $request->input('search_id');
        $token = session('api_token');
        $obatList = [];

        try {
            if ($searchId) {
                // JIKA MENCARI ID: Gunakan query obat(id) via GraphQL
                $query = '
                    query GetObat($id: ID!) {
                        obat(id: $id) {
                            id
                            name
                            category
                            price
                            stock
                            description
                            created_at
                            updated_at
                        }
                    }
                ';
                $response = $this->graphql($query, ['id' => $searchId], $token);
            } else {
                // JIKA KOSONG: Gunakan query obats via GraphQL (Ambil Semua)
                $query = '
                    query {
                        obats {
                            id
                            name
                            category
                            price
                            stock
                            description
                            created_at
                            updated_at
                        }
                    }
                ';
                $response = $this->graphql($query, [], $token);
            }

            if ($response->successful()) {
                $json = $response->json();

                if ($searchId) {
                    // GraphQL mengembalikan single object di data.obat
                    $data = $json['data']['obat'] ?? null;
                    $obatList = $data ? [$data] : [];
                } else {
                    // GraphQL mengembalikan array di data.obats
                    $obatList = $json['data']['obats'] ?? [];
                }
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan Katalog Obat sedang tidak dapat diakses: ' . $e->getMessage());
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

        $token = session('api_token');

        // 2. Siapkan mutation GraphQL createObat
        $query = '
            mutation CreateObat($input: CreateObatInput!) {
                createObat(input: $input) {
                    id
                    name
                    category
                    price
                    stock
                    description
                }
            }
        ';

        $variables = [
            'input' => [
                'name' => $request->input('name'),
                'category' => $request->input('category'),
                'price' => (float) $request->input('price'),
                'stock' => (int) $request->input('stock'),
                'description' => $request->input('description'),
            ]
        ];

        try {
            // 3. Kirim mutation GraphQL dengan Token
            $response = $this->graphql($query, $variables, $token);

            // 4. Evaluasi Hasil
            if ($response->successful() && !isset($response->json()['errors'])) {
                session()->flash('success', 'Berhasil! Data obat baru telah tersimpan di database.');
            } else {
                $errors = $response->json()['errors'] ?? [];
                $msg = $errors[0]['message'] ?? 'Gagal menyimpan data ke API. Periksa kembali isian Anda.';
                session()->flash('warning', $msg);
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

        $token = session('api_token');

        $query = '
            mutation UpdateObat($input: UpdateObatInput!) {
                updateObat(input: $input) {
                    id
                    name
                    category
                    price
                    stock
                    description
                }
            }
        ';

        $variables = [
            'input' => [
                'id' => $id,
                'name' => $request->input('name'),
                'category' => $request->input('category'),
                'price' => (float) $request->input('price'),
                'stock' => (int) $request->input('stock'),
                'description' => $request->input('description', ''),
            ]
        ];

        try {
            // Gunakan mutation GraphQL untuk mengupdate data
            $response = $this->graphql($query, $variables, $token);

            if ($response->successful() && !isset($response->json()['errors'])) {
                session()->flash('success', 'Data obat berhasil diperbarui.');
            } else {
                $errors = $response->json()['errors'] ?? [];
                $msg = $errors[0]['message'] ?? 'Gagal memperbarui data. Pastikan format benar.';
                session()->flash('warning', $msg);
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

        $token = session('api_token');

        $query = '
            mutation DeleteObat($id: ID!) {
                deleteObat(id: $id) {
                    id
                    name
                }
            }
        ';

        try {
            // Gunakan mutation GraphQL untuk menghapus data
            $response = $this->graphql($query, ['id' => $id], $token);

            if ($response->successful() && !isset($response->json()['errors'])) {
                session()->flash('success', 'Data obat berhasil dihapus dari sistem.');
            } else {
                $errors = $response->json()['errors'] ?? [];
                $msg = $errors[0]['message'] ?? 'Gagal menghapus data obat.';
                session()->flash('warning', $msg);
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Layanan API Obat sedang down.');
        }

        return redirect('/obat');
    }
}
