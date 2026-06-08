<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Seed dummy data untuk tabel orders.
     */
    public function run(): void
    {
        $orders = [
            [
                'order_code'     => 'ORD-Budi-Paracetamol-1234',
                'user_id'        => 1,
                'customer_name'  => 'Budi Sudarsono',
                'customer_email' => 'budi@gmail.com',
                'product_id'     => 1,
                'quantity'       => 2,
                'total_price'    => 50000,
                'status'         => 'pending',
            ],
            [
                'order_code'     => 'ORD-Ani-Amoxicillin-5678',
                'user_id'        => 2,
                'customer_name'  => 'Ani Fitriani',
                'customer_email' => 'ani@gmail.com',
                'product_id'     => 3,
                'quantity'       => 1,
                'total_price'    => 25000,
                'status'         => 'processing',
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
