<?php

namespace App\Service;

use App\Dto\TeamStatisticsResult;
use App\Entity\Team;
use App\Enum\TaskStatus;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;

readonly class TeamStatistics
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private TaskRepository $taskRepository,
    ) {
    }

    public function forTeam(Team $team): TeamStatisticsResult
    {
        $countsByStatus = $this->taskRepository->countByStatusForTeam($team);
        $totalTasks = array_sum($countsByStatus);
        $doneTasks = $countsByStatus[TaskStatus::Done->value];

        return new TeamStatisticsResult(
            activeProjects: $this->projectRepository->countActiveByTeam($team),
            totalProjects: $team->getProjects()->count(),
            countsByStatus: $countsByStatus,
            ratiosByStatus: array_map(fn (int $count) => $this->ratio($count, $totalTasks), $countsByStatus),
            totalTasks: $totalTasks,
            openTasks: $totalTasks - $doneTasks,
            completion: $this->ratio($doneTasks, $totalTasks),
            overdueTasks: $this->taskRepository->countOverdueForTeam($team),
        );
    }

    private function ratio(int $count, int $total): float
    {
        return $total > 0 ? $count / $total : 0.0;
    }
}
