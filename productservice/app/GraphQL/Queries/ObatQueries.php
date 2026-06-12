<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Obat;

final readonly class ObatQueries
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function all($root, array $args)
    {
        return Obat::all();
    }

    public function find($root, array $args)
    {
        return Obat::find($args['id']);
    }
}
