<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Obat;

final readonly class ObatMutations
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function create($root, array $args)
    {
        return Obat::create($args['input']);
    }

    public function update($root, array $args)
    {
        $obat = Obat::findOrFail($args['input']['id']);
        $obat->update($args['input']);
        return $obat;
    }

    public function delete($root, array $args)
    {
        $obat = Obat::findOrFail($args['id']);
        $obat->delete();
        return $obat;
    }
}
