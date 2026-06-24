<?php
$payload = [
    'type' => 'pg_track_table',
    'args' => [
        'source' => 'default',
        'table' => 'obats'
    ]
];

$response2 = Illuminate\Support\Facades\Http::withHeaders([
    'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET'),
    'Content-Type' => 'application/json',
])->post('http://medtech-hasura:8080/v1/metadata', $payload);

echo json_encode($response2->json(), JSON_PRETTY_PRINT);
