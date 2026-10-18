<?php

namespace App\Dto;

final readonly class TeamStatisticsResult
{
    /**
     * @param array<string, int>   $countsByStatus
     * @param array<string, float> $ratiosByStatus
     */
    public function __construct(
        public int $activeProjects,
        public int $totalProjects,
        public array $countsByStatus,
        public array $ratiosByStatus,
        public int $totalTasks,
        public int $openTasks,
        public float $completion,
        public int $overdueTasks,
    ) {
    }
}
