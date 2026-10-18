<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\Team;
use App\Enum\TaskStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return array<string, int>
     */
    public function countByStatusForTeam(Team $team): array
    {
        $rows = $this->createQueryBuilder('task')
            ->select('task.status AS status, COUNT(task.id) AS total')
            ->join('task.project', 'project')
            ->where('project.team = :team')
            ->setParameter('team', $team)
            ->groupBy('task.status')
            ->getQuery()
            ->getResult();

        $counts = array_fill_keys(array_column(TaskStatus::cases(), 'value'), 0);
        foreach ($rows as $row) {
            $counts[$row['status']->value] = (int) $row['total'];
        }

        return $counts;
    }

    /**
     * @return array<int, array{total: int, done: int}>
     */
    public function countProgressByProjectForTeam(Team $team): array
    {
        $rows = $this->createQueryBuilder('task')
            ->select('IDENTITY(task.project) AS projectId, COUNT(task.id) AS total')
            ->addSelect('SUM(CASE WHEN task.status = :done THEN 1 ELSE 0 END) AS done')
            ->join('task.project', 'project')
            ->where('project.team = :team')
            ->setParameter('team', $team)
            ->setParameter('done', TaskStatus::Done)
            ->groupBy('task.project')
            ->getQuery()
            ->getResult();

        $progress = [];
        foreach ($rows as $row) {
            $progress[(int) $row['projectId']] = ['total' => (int) $row['total'], 'done' => (int) $row['done']];
        }

        return $progress;
    }

    public function countOverdueForTeam(Team $team): int
    {
        return (int) $this->createQueryBuilder('task')
            ->select('COUNT(task.id)')
            ->join('task.project', 'project')
            ->where('project.team = :team')
            ->andWhere('task.dueDate < :today')
            ->andWhere('task.status != :done')
            ->setParameter('team', $team)
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->setParameter('done', TaskStatus::Done)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
