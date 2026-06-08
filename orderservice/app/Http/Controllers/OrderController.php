<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private string $userServiceUrl;
    private string $productServiceUrl;

    private array $usersCache = [];

    public function __construct()
    {
        $this->userServiceUrl = config('services.user_service.url');
        $this->productServiceUrl = config('services.product_service.url');
    }

    protected function fetchUserData($id)
    {
        if (!$id)
            return null;
        if (isset($this->usersCache[$id]))
            return $this->usersCache[$id];

        $response = Http::timeout(5)->get("{$this->userServiceUrl}/users/{$id}");
        return $this->usersCache[$id] = $response->successful() ? ($response->json()['data'] ?? $response->json()) : null;
    }

    public function index()
    {
        $orders = Order::all();
        return new OrderResource($orders, 'berhasil', 'List of orders');
    }

    public function store(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $request->user_id ?? ($request->auth_user['id'] ?? null);
        $userData = $this->fetchUserData($userId);

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            // 'user_id' => '',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return new OrderResource(null, 'gagal', $validator->errors());
        }

        if (!$userData) {
            return new OrderResource(null, 'gagal', 'User not found');
        }

        $productResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get("{$this->productServiceUrl}/obat/{$request->product_id}");
        $productData = $productResponse->json()['data'] ?? null;

        if (!$productData) {
            return new OrderResource(null, 'gagal', 'Product not found');
        }

        // Check stock
        if (($productData['stock'] ?? 0) < $request->quantity) {
            return new OrderResource(null, 'gagal', 'Stok obat tidak mencukupi. Stok saat ini: ' . ($productData['stock'] ?? 0));
        }

        $order = Order::create([
            'order_code' => $this->generateOrderCode($userData, $productData),
            'user_id' => $userId,
            'customer_name' => $userData['name'] ?? ($userData['username'] ?? 'Unknown'),
            'customer_email' => $userData['email'] ?? '-',
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => ($productData['price'] ?? 0) * $request->quantity,
            'status' => 'pending',
        ]);

        // GANTI HTTP PATCH DENGAN RABBITMQ JOB
        \App\Jobs\UpdateProductStock::dispatch($request->product_id, $request->quantity, 'subtract')
            ->onQueue('product_stock_queue');

        return new OrderResource($order, 'berhasil', 'Order created successfully');
    }

    public function show($id)
    {
        $token = request()->bearerToken();
        $order = Order::find($id);
        if ($order) {
            $data = $order->toArray();

            // Get the product details (consume)
            $productResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get("{$this->productServiceUrl}/obat/{$order->product_id}");
            $data['product'] = $productResponse->json()['data'] ?? null;

            // Get the user details (consume)
            $userResponse = Http::get("{$this->userServiceUrl}/users/{$order->user_id}");
            $data['user'] = $userResponse->json()['data'] ?? null;

            return new OrderResource($data, 'berhasil', 'Order found');
        } else {
            return new OrderResource(null, 'gagal', 'Order not found');
        }
    }

    public function update(Request $request, $id)
    {
        $token = $request->bearerToken();
        $order = Order::find($id);
        if (!$order) {
            return new OrderResource(null, 'gagal', 'Order not found');
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return new OrderResource(null, 'gagal', $validator->errors());
        }

        // Get Product Info for price
        $productResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get("{$this->productServiceUrl}/obat/{$request->product_id}");
        $productData = $productResponse->json()['data'] ?? null;

        if (!$productData) {
            return new OrderResource(null, 'gagal', 'Product not found');
        }

        if ($order->quantity > $request->quantity) {
            $qtt = $order->quantity - $request->quantity;
            // Kirim ke RabbitMQ untuk menambah kembali stok
            \App\Jobs\UpdateProductStock::dispatch($request->product_id, $qtt, 'add')
                ->onQueue('product_stock_queue');
        } else {
            $qtt = $request->quantity - $order->quantity;

            if (($productData['stock'] ?? 0) < $qtt) {
                return new OrderResource(null, 'gagal', 'Stok obat tidak mencukupi untuk penambahan jumlah. Stok saat ini: ' . ($productData['stock'] ?? 0));
            }

            // Kirim ke RabbitMQ untuk mengurangi stok
            \App\Jobs\UpdateProductStock::dispatch($request->product_id, $qtt, 'subtract')
                ->onQueue('product_stock_queue');
        }

        $order->update([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => ($productData['price'] ?? 0) * $request->quantity,
            'status' => $request->status,
        ]);


        return new OrderResource($order, 'berhasil', 'Order updated successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return new OrderResource(null, 'gagal', 'Order not found');
        }

        $order->update(['status' => $request->status]);
        return new OrderResource($order, 'berhasil', 'Status updated successfully');
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return new OrderResource(null, 'gagal', 'Order not found');
        }

        // Kirim ke RabbitMQ untuk mengembalikan stok
        \App\Jobs\UpdateProductStock::dispatch($order->product_id, $order->quantity, 'add')
            ->onQueue('product_stock_queue');

        $order->delete();
        return new OrderResource(null, 'berhasil', 'Order deleted successfully');
    }

    private function generateOrderCode($user, $product)
    {
        $name = Str::slug($user['name'] ?? ($user['username'] ?? 'user'));
        $item = Str::slug($product['name'] ?? ($product['nama_obat'] ?? 'item'));
        return strtoupper("ORD-{$name}-{$item}-" . rand(1000, 9999));
    }

    public function getByUser($id)
    {
        $orders = Order::where('user_id', $id)->get();

        return new OrderResource($orders, 'berhasil', 'List of user orders');
    }
}