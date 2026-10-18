<?php

namespace App\Tests\Service;

use App\Dto\ProjectProgress;
use App\Entity\Team;
use App\Factory\TeamFactory;
use App\Repository\TeamRepository;
use App\Service\TeamStatistics;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class TeamStatisticsTest extends KernelTestCase
{
    use Factories;

    public function testForTeam(): void
    {
        $stats = $this->statistics()->forTeam($this->team('Webfejlesztés'));

        self::assertSame(2, $stats->activeProjects);
        self::assertSame(4, $stats->totalProjects);
        self::assertSame(['todo' => 5, 'in_progress' => 3, 'done' => 6], $stats->countsByStatus);
        self::assertSame(14, $stats->totalTasks);
        self::assertSame(8, $stats->openTasks);
        self::assertEqualsWithDelta(6 / 14, $stats->completion, 0.0001);
        self::assertEqualsWithDelta(5 / 14, $stats->ratiosByStatus['todo'], 0.0001);
        self::assertSame(2, $stats->overdueTasks);
    }

    public function testProjectProgress(): void
    {
        $progress = $this->statistics()->projectProgress($this->team('Webfejlesztés'));

        self::assertSame(
            [
                'Blog migráció' => [0, 1],
                'Design system' => [3, 3],
                'Ügyfélportál 2.0' => [1, 4],
                'Webshop újratervezés' => [2, 6],
            ],
            array_combine(
                array_map(fn (ProjectProgress $item) => (string) $item->project->getTitle(), $progress),
                array_map(fn (ProjectProgress $item) => [$item->doneTasks, $item->totalTasks], $progress),
            ),
        );
        self::assertSame(1.0, $progress[1]->completion);
    }

    public function testTeamWithoutTasks(): void
    {
        $stats = $this->statistics()->forTeam(TeamFactory::createOne());

        self::assertSame(0, $stats->totalProjects);
        self::assertSame(['todo' => 0, 'in_progress' => 0, 'done' => 0], $stats->countsByStatus);
        self::assertSame(0, $stats->totalTasks);
        self::assertSame(0.0, $stats->completion);
        self::assertSame(0, $stats->overdueTasks);
    }

    private function statistics(): TeamStatistics
    {
        return TeamStatisticsTest::getContainer()->get(TeamStatistics::class);
    }

    private function team(string $name): Team
    {
        $team = TeamStatisticsTest::getContainer()->get(TeamRepository::class)->findOneBy(['name' => $name]);
        self::assertNotNull($team);

        return $team;
    }
}
