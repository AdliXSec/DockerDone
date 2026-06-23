<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderQueries
{
    private string $userServiceUrl;
    private string $productServiceUrl;

    public function __construct()
    {
        $this->userServiceUrl = config('services.user_service.url');
        $this->productServiceUrl = env('PRODUCT_SERVICE_URL');
    }

    /**
     * Get all orders.
     */
    public function all($rootValue, array $args)
    {
        return Order::all()->map(function ($order) {
            return $this->attachExternalData($order);
        });
    }

    /**
     * Find a single order by ID.
     */
    public function find($rootValue, array $args)
    {
        $order = Order::find($args['id']);
        if (!$order) return null;

        return $this->attachExternalData($order);
    }

    /**
     * Get orders by User ID.
     */
    public function getByUser($rootValue, array $args)
    {
        return Order::where('user_id', $args['user_id'])->get()->map(function ($order) {
            return $this->attachExternalData($order);
        });
    }

    /**
     * Helper to attach external user and product data.
     */
    protected function attachExternalData(Order $order)
    {
        $token = request()->bearerToken();

        // Get Product Info dari Hasura
        $queryObat = '
            query GetObat($id: Int!) {
                obat_by_pk(id: $id) {
                    id
                    nama_obat
                    price
                    stock
                }
            }
        ';

        try {
            $productResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET', 'admin123')
            ])->post($this->productServiceUrl, [
                'query' => $queryObat,
                'variables' => [
                    'id' => (int) $order->product_id
                ]
            ]);
            $order->product = $productResponse->json()['data']['obat_by_pk'] ?? null;
        } catch (\Exception $e) {
            $order->product = null;
        }

        // Get User Info
        try {
            $userResponse = Http::get("{$this->userServiceUrl}/users/{$order->user_id}");
            $order->user = $userResponse->json()['data'] ?? null;
        } catch (\Exception $e) {
            $order->user = null;
        }

        return $order;
    }
}