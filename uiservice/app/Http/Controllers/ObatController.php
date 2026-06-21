<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('api_token')) {
            return redirect('/login')
                ->withErrors([
                    'login_error' => 'Sesi berakhir, silakan login.'
                ]);
        }
        $searchId = $request->input('search_id');
        $obatList = [];
        try {
            if ($searchId) {
                $payload = [
                    'query' => '
                        query GetObat($id: bigint!) {
                            obats_by_pk(id: $id) {
                                id
                                name
                                category
                                price
                                stock
                                description
                            }
                        }
                    ',
                    'variables' => [
                        'id' => (int) $searchId
                    ]
                ];
                $response = Http::withHeaders([
                    'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET'),
                    'Content-Type' => 'application/json',
                ])->post(env('HASURA_URL'), $payload);
                if ($response->successful()) {
                    $data = $response->json()['data']['obats_by_pk'] ?? null;
                    if ($data) {
                        $obatList = [$data];
                    }
                }
            } else {
                $payload = [
                    'query' => '
                        query {
                            obats(order_by: {id: asc}) {
                                id
                                name
                                category
                                price
                                stock
                                description
                            }
                        }
                    '
                ];
                $response = Http::withHeaders([
                    'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET'),
                    'Content-Type' => 'application/json',
                ])->post(env('HASURA_URL'), $payload);
                if ($response->successful()) {
                    $obatList =
                        $response->json()['data']['obats']
                        ?? [];
                }
            }
        } catch (\Exception $e) {
            session()->flash(
                'warning',
                'Layanan katalog obat tidak dapat diakses.'
            );
        }
        return view('obat', compact('obatList'));
    }

    public function store(Request $request)
{
    if (session('user_role') != 'admin') {
        return redirect('/obat')
            ->withErrors([
                'login_error' => 'Akses ditolak!'
            ]);
    }

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
            'name' => $request->name,
            'category' => $request->category,
            'price' => (int) $request->price,
            'stock' => (int) $request->stock,
            'description' => $request->description ?? ''
        ]
    ];

    try {
        $response = Http::withHeaders([
            'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET'),
            'Content-Type' => 'application/json',
        ])->post(
            env('HASURA_URL'),
            $payload
        );
        // dd([ // 'status' => $response->status(),
        //  'body' => $response->body(), 
        // 'json' => $response->json() 
        // ]);
        if (
            $response->successful() &&
            !isset($response->json()['errors'])
        ) {
            session()->flash(
                'success',
                'Obat berhasil ditambahkan.'
            );
        } else {
            session()->flash(
                'warning',
                'Gagal menambahkan obat.'
            );
        }
    } catch (\Exception $e) {
        session()->flash(
            'warning',
            'Layanan sedang bermasalah.'
        );
    }
    return redirect('/obat');
}
    public function update(Request $request, $id)
    {
        if (session('user_role') != 'admin') {
            return redirect('/obat')
                ->withErrors([
                    'login_error' => 'Akses ditolak!'
                ]);
        }
        $payload = [
            'query' => '
                mutation UpdateObat(
                    $id: bigint!,
                    $name: String!,
                    $category: String!,
                    $price: Int!,
                    $stock: Int!,
                    $description: String!
                ) {
                    update_obats_by_pk(
                        pk_columns: {
                            id: $id
                        },
                        _set: {
                            name: $name,
                            category: $category,
                            price: $price,
                            stock: $stock,
                            description: $description
                        }
                    ) {
                        id
                        name
                    }
                }
            ',
            'variables' => [
                'id' => (int) $id,
                'name' => $request->input('name'),
                'category' => $request->input('category'),
                'price' => (int) $request->input('price'),
                'stock' => (int) $request->input('stock'),
                'description' => $request->input('description', '')
            ]
        ];
        try {
            $response = Http::withHeaders([
                'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET'),
                'Content-Type' => 'application/json',
            ])->post(
                env('HASURA_URL'),
                $payload
            );
            if (
                $response->successful() &&
                isset($response->json()['data']['update_obats_by_pk'])
            ) {
                session()->flash(
                    'success',
                    'Data obat berhasil diperbarui.'
                );
            } else {
                session()->flash(
                    'warning',
                    'Gagal memperbarui data obat.'
                );
            }
        } catch (\Exception $e) {
            session()->flash(
                'warning',
                'Layanan Hasura sedang down.'
            );
        }
        return redirect('/obat');
    }

    public function destroy($id)
    {
        if (session('user_role') != 'admin') {
            return redirect('/obat')
                ->withErrors([
                    'login_error' => 'Akses ditolak!'
                ]);
        }
        $payload = [
            'query' => '
                mutation DeleteObat($id: bigint!) {
                    delete_obats_by_pk(id: $id) {
                        id
                        name
                    }
                }
            ',
            'variables' => [
                'id' => (int) $id
            ]
        ];
        try {
            $response = Http::withHeaders([
                'x-hasura-admin-secret' => env('HASURA_ADMIN_SECRET'),
                'Content-Type' => 'application/json',
            ])->post(
                env('HASURA_URL'),
                $payload
            );
            if (
                $response->successful() &&
                isset($response->json()['data']['delete_obats_by_pk'])
            ) {
                session()->flash(
                    'success',
                    'Data obat berhasil dihapus.'
                );
            } else {
                session()->flash(
                    'warning',
                    'Gagal menghapus data obat.'
                );
            }
        } catch (\Exception $e) {
            session()->flash(
                'warning',
                'Layanan Hasura sedang tidak tersedia.'
            );
        }
        return redirect('/obat');
    }
    }