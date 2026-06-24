<?php
$response = Illuminate\Support\Facades\Http::withHeaders([
    'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET'),
    'Content-Type' => 'application/json',
])->post(env('HASURA_URL'), [
    'query' => '{
        __schema {
            queryType { fields { name } }
            mutationType { fields { name } }
        }
    }'
]);
file_put_contents('/var/www/hasura_schema.json', json_encode($response->json(), JSON_PRETTY_PRINT));
