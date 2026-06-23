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
        $this->productServiceUrl = config('services.product_service.url');
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
        // Note: In a production environment, you might want to optimize this 
        // using DataLoader to avoid N+1 issues with external HTTP calls.
        
        $token = request()->bearerToken();

        // Get Product Info
        $productResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get("{$this->productServiceUrl}/obat/{$order->product_id}");
        $order->product = $productResponse->json()['data'] ?? null;

        // Get User Info
        $userResponse = Http::get("{$this->userServiceUrl}/users/{$order->user_id}");
        $order->user = $userResponse->json()['data'] ?? null;

        return $order;
    }
}
