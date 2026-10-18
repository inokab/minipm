<?php

namespace App\Service;

use App\Dto\ProjectProgress;
use App\Dto\TeamStatisticsResult;
use App\Entity\Project;
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

    /**
     * @return list<ProjectProgress>
     */
    public function projectProgress(Team $team): array
    {
        $progress = $this->taskRepository->countProgressByProjectForTeam($team);
        $projects = $this->projectRepository->findBy(['team' => $team], ['title' => 'ASC']);

        return array_map(function (Project $project) use ($progress) {
            $totalTasks = $progress[$project->getId()]['total'] ?? 0;
            $doneTasks = $progress[$project->getId()]['done'] ?? 0;

            return new ProjectProgress($project, $doneTasks, $totalTasks, $this->ratio($doneTasks, $totalTasks));
        }, $projects);
    }

    private function ratio(int $count, int $total): float
    {
        return $total > 0 ? $count / $total : 0.0;
    }
}
