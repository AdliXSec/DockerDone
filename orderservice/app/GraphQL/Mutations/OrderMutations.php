<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Jobs\UpdateProductStock;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OrderMutations
{
    private string $userServiceUrl;
    private string $productServiceUrl;

    public function __construct()
    {
        $this->userServiceUrl = config('services.user_service.url');
        $this->productServiceUrl = env('PRODUCT_SERVICE_URL'); // Langsung menunjuk Hasura
    }

    /**
     * Create a new order.
     */
    public function create($rootValue, array $args)
    {
        $input = $args['input'];
        $token = request()->bearerToken();

        // Fetch User Data
        $userResponse = Http::get("{$this->userServiceUrl}/users/{$input['user_id']}");
        $userData = $userResponse->json()['data'] ?? $userResponse->json();

        if (!$userData) {
            throw new \Exception("User not found");
        }

        // Fetch Product Data dari Hasura
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

        $productResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET', 'admin123')
        ])->post($this->productServiceUrl, [
            'query' => $queryObat,
            'variables' => [
                'id' => (int) $input['product_id']
            ]
        ]);

        $productData = $productResponse->json()['data']['obat_by_pk'] ?? null;

        if (!$productData) {
            throw new \Exception("Product not found di Hasura");
        }

        // Check stock
        if (($productData['stock'] ?? 0) < $input['quantity']) {
            throw new \Exception("Stok obat tidak mencukupi. Stok saat ini: " . ($productData['stock'] ?? 0));
        }

        $order = Order::create([
            'order_code' => $this->generateOrderCode($userData, $productData),
            'user_id' => $input['user_id'],
            'customer_name' => $userData['name'] ?? ($userData['username'] ?? 'Unknown'),
            'customer_email' => $userData['email'] ?? '-',
            'product_id' => $input['product_id'],
            'quantity' => $input['quantity'],
            'total_price' => ($productData['price'] ?? 0) * $input['quantity'],
            'status' => 'pending',
        ]);

        // Trigger RabbitMQ Job
        UpdateProductStock::dispatch($input['product_id'], $input['quantity'], 'subtract')
            ->onQueue('product_stock_queue');

        return $order;
    }

    /**
     * Update an existing order.
     */
    public function update($rootValue, array $args)
    {
        $input = $args['input'];
        $order = Order::findOrFail($input['id']);
        $token = request()->bearerToken();

        $productId = $input['product_id'] ?? $order->product_id;
        $quantity = $input['quantity'] ?? $order->quantity;

        // Fetch Product Info dari Hasura
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

        $productResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET', 'admin123')
        ])->post($this->productServiceUrl, [
            'query' => $queryObat,
            'variables' => [
                'id' => (int) $productId
            ]
        ]);

        $productData = $productResponse->json()['data']['obat_by_pk'] ?? null;

        if (!$productData) {
            throw new \Exception("Product not found di Hasura");
        }

        // Stock Adjustment Logic
        if ($order->quantity != $quantity) {
            if ($order->quantity > $quantity) {
                $diff = $order->quantity - $quantity;
                UpdateProductStock::dispatch($productId, $diff, 'add')->onQueue('product_stock_queue');
            } else {
                $diff = $quantity - $order->quantity;
                if (($productData['stock'] ?? 0) < $diff) {
                    throw new \Exception("Stok obat tidak mencukupi untuk penambahan.");
                }
                UpdateProductStock::dispatch($productId, $diff, 'subtract')->onQueue('product_stock_queue');
            }
        }

        $order->update([
            'product_id' => $productId,
            'quantity' => $quantity,
            'total_price' => ($productData['price'] ?? 0) * $quantity,
            'status' => $input['status'] ?? $order->status,
        ]);

        return $order;
    }

    /**
     * Update only the status of an order.
     */
    public function updateStatus($rootValue, array $args)
    {
        $input = $args['input'];
        $order = Order::findOrFail($input['id']);
        
        $order->update(['status' => $input['status']]);
        
        return $order;
    }

    /**
     * Delete an order.
     */
    public function delete($rootValue, array $args)
    {
        $order = Order::findOrFail($args['id']);

        // Return stock to product service
        UpdateProductStock::dispatch($order->product_id, $order->quantity, 'add')
            ->onQueue('product_stock_queue');

        $order->delete();

        return $order;
    }

    /**
     * Helper to generate order code.
     */
    private function generateOrderCode($user, $product)
    {
        $name = Str::slug($user['name'] ?? ($user['username'] ?? 'user'));
        $item = Str::slug($product['nama_obat'] ?? ($product['name'] ?? 'item'));
        return strtoupper("ORD-{$name}-{$item}-" . rand(1000, 9999));
    }
}