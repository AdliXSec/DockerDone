<?php
use Illuminate\Support\Facades\Http;

$payload = [
    'query' => '
        mutation InsertObat(
            $name: String!,
            $category: String!,
            $price: Int!,
            $stock: Int!,
            $description: String!
        ) {
            insert_obats_one(object: {
                name: $name,
                category: $category,
                price: $price,
                stock: $stock,
                description: $description
            }) {
                id
                name
            }
        }
    ',
    'variables' => [
        'name' => 'Test Obat',
        'category' => 'Obat Bebas',
        'price' => 10000,
        'stock' => 10,
        'description' => 'Test'
    ]
];

$response = Http::withHeaders([
    'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET'),
    'Content-Type' => 'application/json',
])->post(env('HASURA_URL'), $payload);

echo json_encode($response->json(), JSON_PRETTY_PRINT);
