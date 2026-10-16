<?php

namespace App\Factory;

use App\Entity\Task;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Task>
 */
final class TaskFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Task::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->realTextBetween(15, 40),
            'description' => self::faker()->realText(300),
            'status' => self::faker()->randomElement(TaskStatus::cases()),
            'priority' => self::faker()->randomElement(TaskPriority::cases()),
            'dueDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month', '+2 months')),
            'project' => ProjectFactory::new(),
        ];
    }
}
