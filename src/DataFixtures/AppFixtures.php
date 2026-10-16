<?php

namespace App\DataFixtures;

use App\Enum\UserRole;
use App\Factory\ProjectFactory;
use App\Factory\TaskFactory;
use App\Factory\TeamFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'admin', 'roles' => [UserRole::Admin->value]]);
        UserFactory::createOne(['name' => 'Member', 'email' => 'member@example.com', 'password' => 'member']);
        UserFactory::createMany(8);

        foreach (TeamFactory::createMany(3, fn () => ['members' => UserFactory::randomRange(3, 6)]) as $team) {
            foreach (ProjectFactory::createMany(faker()->numberBetween(2, 4), ['team' => $team]) as $project) {
                TaskFactory::createMany(faker()->numberBetween(5, 15), fn () => [
                    'project' => $project,
                    'assignee' => faker()->optional(0.8)->randomElement($team->getMembers()->toArray()),
                ]);
            }
        }
    }
}
