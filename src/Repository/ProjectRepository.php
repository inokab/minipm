<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Team;
use App\Enum\ProjectStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function countActiveByTeam(Team $team): int
    {
        return $this->count(['team' => $team, 'status' => ProjectStatus::Active]);
    }
}
