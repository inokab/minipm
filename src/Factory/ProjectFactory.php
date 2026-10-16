<?php

namespace App\Factory;

use App\Entity\Project;
use App\Enum\ProjectStatus;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Project>
 */
final class ProjectFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Project::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->realTextBetween(15, 40),
            'description' => self::faker()->realText(300),
            'status' => self::faker()->randomElement(ProjectStatus::cases()),
            'team' => TeamFactory::new(),
        ];
    }
}
