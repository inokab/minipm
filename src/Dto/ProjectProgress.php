<?php

namespace App\Dto;

use App\Entity\Project;

final readonly class ProjectProgress
{
    public function __construct(
        public Project $project,
        public int $doneTasks,
        public int $totalTasks,
        public float $completion,
    ) {
    }
}
