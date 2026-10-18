<?php

namespace App\Tests\Controller;

use App\Repository\ProjectRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

final class AdminPagesTest extends WebTestCase
{
    #[DataProvider('listPages')]
    public function testListPageLoads(string $url): void
    {
        $client = $this->loginAsMember();
        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function listPages(): iterable
    {
        yield 'vezérlőpult' => ['/admin'];
        yield 'csapatok' => ['/admin/team'];
        yield 'projektek' => ['/admin/project'];
        yield 'feladatok' => ['/admin/task'];
    }

    public function testProjectDetailGroupsTasksByStatus(): void
    {
        $client = $this->loginAsMember();
        $project = self::getContainer()->get(ProjectRepository::class)->findOneBy(['title' => 'Webshop újratervezés']);
        self::assertNotNull($project);

        $crawler = $client->request('GET', '/admin/project/'.$project->getId());

        self::assertResponseIsSuccessful();
        self::assertSame(
            ['Teendő 2', 'Folyamatban 2', 'Kész 2'],
            $crawler->filter('.card-header')->each(fn (Crawler $header) => $header->text()),
        );
    }

    public function testTeamDashboardShowsStatistics(): void
    {
        $client = $this->loginAsMember();
        $team = self::getContainer()->get(TeamRepository::class)->findOneBy(['name' => 'Webfejlesztés']);
        self::assertNotNull($team);

        $client->request('GET', '/admin/team/'.$team->getId().'/dashboard');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.card-body', 'Aktív projektek');
    }

    private function loginAsMember(): KernelBrowser
    {
        $client = self::createClient();
        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'member@example.com']);
        self::assertNotNull($user);
        $client->loginUser($user);

        return $client;
    }
}
