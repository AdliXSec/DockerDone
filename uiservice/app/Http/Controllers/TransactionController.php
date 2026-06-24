<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    /**
     * Helper: Kirim GraphQL request ke Order Service
     */
    private function graphql($query, $variables = [], $token = null)
    {
        $url = rtrim(env('ORDER_SERVICE_URL'), '/') . '/graphql';

        $request = Http::timeout(60)->withOptions(['force_ip_resolve' => 'v4']);
        if ($token) {
            $request = $request->withToken($token);
        }

        return $request->post($url, [
            'query' => $query,
            'variables' => $variables,
        ]);
    }

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
            if ($role == 'admin') {
                // Admin: ambil semua order via GraphQL query orders
                $query = '
                    query {
                        orders {
                            id
                            order_code
                            user_id
                            customer_name
                            customer_email
                            product_id
                            quantity
                            total_price
                            status
                            created_at
                            updated_at
                        }
                    }
                ';
                $response = $this->graphql($query, [], $token);
            } else {
                // User: ambil order berdasarkan user_id via GraphQL query ordersByUser
                $query = '
                    query GetOrdersByUser($user_id: ID!) {
                        ordersByUser(user_id: $user_id) {
                            id
                            order_code
                            user_id
                            customer_name
                            customer_email
                            product_id
                            quantity
                            total_price
                            status
                            created_at
                            updated_at
                        }
                    }
                ';
                $response = $this->graphql($query, ['user_id' => (int) $userId], $token);
            }

            if ($response->successful()) {
                $json = $response->json();

                if (isset($json['errors'])) {
                    session()->flash('warning', $json['errors'][0]['message'] ?? 'Terjadi kesalahan.');
                } else {
                    if ($role == 'admin') {
                        $orders = $json['data']['orders'] ?? [];
                    } else {
                        $orders = $json['data']['ordersByUser'] ?? [];
                    }
                    if (!is_array($orders)) $orders = [];
                }
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

        $token = session('api_token');

        // Get user data from is_login to get the ID
        $userResponse = Http::withToken($token)->get('http://medtech-userservice:5000/is_login');
        $userData = $userResponse->json('data');

        if (!$userData) {
            return back()->with('warning', 'Sesi tidak valid, silakan login ulang.');
        }

        // Gunakan mutation GraphQL createOrder
        $query = '
            mutation CreateOrder($input: CreateOrderInput!) {
                createOrder(input: $input) {
                    id
                    order_code
                    user_id
                    customer_name
                    customer_email
                    product_id
                    quantity
                    total_price
                    status
                    created_at
                }
            }
        ';

        $variables = [
            'input' => [
                'product_id' => (int) $request->obat_id,
                'quantity' => (int) ($request->quantity ?? 1),
            ]
        ];

        try {
            $response = $this->graphql($query, $variables, $token);

            if ($response->successful() && !isset($response->json()['errors'])) {
                session()->flash('success', 'Berhasil memesan obat!');
                return redirect('/transaksi');
            } else {
                $errors = $response->json()['errors'] ?? [];
                $error = $errors[0]['message'] ?? 'Gagal memproses pesanan.';
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

        $token = session('api_token');

        // Gunakan mutation GraphQL updateOrderStatus
        $query = '
            mutation UpdateOrderStatus($id: ID!, $status: String!) {
                updateOrderStatus(id: $id, status: $status) {
                    id
                    status
                }
            }
        ';

        $variables = [
            'id' => $id,
            'status' => $request->status,
        ];

        try {
            $response = $this->graphql($query, $variables, $token);

            if ($response->successful() && !isset($response->json()['errors'])) {
                session()->flash('success', 'Status pesanan berhasil diperbarui.');
            } else {
                $errors = $response->json()['errors'] ?? [];
                $msg = $errors[0]['message'] ?? 'Gagal memperbarui status di server.';
                session()->flash('warning', $msg);
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

        $token = session('api_token');

        // Gunakan mutation GraphQL deleteOrder
        $query = '
            mutation DeleteOrder($id: ID!) {
                deleteOrder(id: $id)
            }
        ';

        try {
            $response = $this->graphql($query, ['id' => $id], $token);

            if ($response->successful() && !isset($response->json()['errors'])) {
                session()->flash('success', 'Pesanan berhasil dihapus.');
            } else {
                $errors = $response->json()['errors'] ?? [];
                $msg = $errors[0]['message'] ?? 'Gagal menghapus pesanan.';
                session()->flash('warning', $msg);
            }
        } catch (\Exception $e) {
            session()->flash('warning', 'Koneksi ke Order Service terputus.');
        }

        return redirect('/transaksi');
    }
}
