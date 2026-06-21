<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final readonly class OrderMutations
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    /**
     * Create a new order.
     * Logic sama persis dengan OrderController::store()
     */
    public function create($root, array $args, \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context)
    {
        $input = $args['input'];
        $request = $context->request();
        $token = $request->bearerToken();

        // Ambil user_id dari auth_user (di-set oleh middleware VerifyUserLogin)
        $userId = $request->auth_user['id'] ?? null;

        // Fetch User Data dari User Service
        $userData = $this->fetchUserData($userId);

        if (!$userData) {
            throw new \GraphQL\Error\Error('User not found');
        }

        // Fetch Product Data dari Product Service via GraphQL
        $productData = $this->fetchProductData($input['product_id'], $token);

        if (!$productData) {
            throw new \GraphQL\Error\Error('Product not found');
        }

        // Check stock
        if (($productData['stock'] ?? 0) < $input['quantity']) {
            throw new \GraphQL\Error\Error('Stok obat tidak mencukupi. Stok saat ini: ' . ($productData['stock'] ?? 0));
        }

        $order = Order::create([
            'order_code' => $this->generateOrderCode($userData, $productData),
            'user_id' => $userId,
            'customer_name' => $userData['name'] ?? ($userData['username'] ?? 'Unknown'),
            'customer_email' => $userData['email'] ?? '-',
            'product_id' => $input['product_id'],
            'quantity' => $input['quantity'],
            'total_price' => ($productData['price'] ?? 0) * $input['quantity'],
            'status' => 'pending',
        ]);

        // Kirim ke RabbitMQ untuk mengurangi stok produk
        \App\Jobs\UpdateProductStock::dispatch($input['product_id'], $input['quantity'], 'subtract')
            ->onQueue('product_stock_queue');

        return $order;
    }

    /**
     * Update an existing order.
     * Logic sama persis dengan OrderController::update()
     */
    public function update($root, array $args, \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context)
    {
        $input = $args['input'];
        $request = $context->request();
        $token = $request->bearerToken();

        $order = Order::find($input['id']);
        if (!$order) {
            throw new \GraphQL\Error\Error('Order not found');
        }

        // Get Product Info for price
        $productData = $this->fetchProductData($input['product_id'], $token);

        if (!$productData) {
            throw new \GraphQL\Error\Error('Product not found');
        }

        // Hitung perbedaan quantity dan update stok via RabbitMQ
        if ($order->quantity > $input['quantity']) {
            $qtt = $order->quantity - $input['quantity'];
            // Kirim ke RabbitMQ untuk menambah kembali stok
            \App\Jobs\UpdateProductStock::dispatch($input['product_id'], $qtt, 'add')
                ->onQueue('product_stock_queue');
        } else {
            $qtt = $input['quantity'] - $order->quantity;

            if (($productData['stock'] ?? 0) < $qtt) {
                throw new \GraphQL\Error\Error('Stok obat tidak mencukupi untuk penambahan jumlah. Stok saat ini: ' . ($productData['stock'] ?? 0));
            }

            // Kirim ke RabbitMQ untuk mengurangi stok
            \App\Jobs\UpdateProductStock::dispatch($input['product_id'], $qtt, 'subtract')
                ->onQueue('product_stock_queue');
        }

        $order->update([
            'product_id' => $input['product_id'],
            'quantity' => $input['quantity'],
            'total_price' => ($productData['price'] ?? 0) * $input['quantity'],
            'status' => $input['status'],
        ]);

        return $order;
    }

    /**
     * Update order status.
     * Logic sama persis dengan OrderController::updateStatus()
     */
    public function updateStatus($root, array $args)
    {
        $order = Order::find($args['id']);
        if (!$order) {
            throw new \GraphQL\Error\Error('Order not found');
        }

        $order->update(['status' => $args['status']]);
        return $order;
    }

    /**
     * Delete an order.
     * Logic sama persis dengan OrderController::destroy()
     */
    public function delete($root, array $args)
    {
        $order = Order::find($args['id']);
        if (!$order) {
            throw new \GraphQL\Error\Error('Order not found');
        }

        // Kirim ke RabbitMQ untuk mengembalikan stok
        \App\Jobs\UpdateProductStock::dispatch($order->product_id, $order->quantity, 'add')
            ->onQueue('product_stock_queue');

        $order->delete();
        return $order;
    }

    /**
     * Fetch user data dari User Service.
     * Sama dengan OrderController::fetchUserData()
     */
    private function fetchUserData($id)
    {
        if (!$id) return null;

        $userServiceUrl = config('services.user_service.url');
        $response = Http::timeout(5)->get("{$userServiceUrl}/users/{$id}");
        return $response->successful() ? ($response->json()['data'] ?? $response->json()) : null;
    }

    /**
     * Fetch product data dari Product Service via GraphQL.
     * Sama dengan OrderController::fetchProductData()
     */
    private function fetchProductData($id, $token)
    {
        $productServiceUrl = config('services.product_service.url');

        $query = <<<'GRAPHQL'
        query GetObat($id: ID!) {
            obat(id: $id) {
                id
                name
                category
                price
                stock
                description
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post("{$productServiceUrl}/graphql", [
            'query' => $query,
            'variables' => ['id' => $id]
        ]);

        return $response->json()['data']['obat'] ?? null;
    }

    /**
     * Generate order code.
     * Sama dengan OrderController::generateOrderCode()
     */
    private function generateOrderCode($user, $product)
    {
        $name = Str::slug($user['name'] ?? ($user['username'] ?? 'user'));
        $item = Str::slug($product['name'] ?? ($product['nama_obat'] ?? 'item'));
        return strtoupper("ORD-{$name}-{$item}-" . rand(1000, 9999));
    }
}
