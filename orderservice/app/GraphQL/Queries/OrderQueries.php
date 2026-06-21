<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Order;

final readonly class OrderQueries
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function all($root, array $args)
    {
        return Order::all();
    }

    public function find($root, array $args)
    {
        return Order::find($args['id']);
    }

    public function findByUser($root, array $args)
    {
        return Order::where('user_id', $args['user_id'])->get();
    }
}
