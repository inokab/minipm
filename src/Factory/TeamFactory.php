<?php

namespace App\Factory;

use App\Entity\Team;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Team>
 */
final class TeamFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Team::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->unique()->company(),
            'description' => self::faker()->realText(200),
        ];
    }
}
