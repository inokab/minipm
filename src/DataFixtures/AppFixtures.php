<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\ProjectStatus;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Enum\UserRole;
use App\Factory\ProjectFactory;
use App\Factory\TaskFactory;
use App\Factory\TeamFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Determinisztikus demo adat: a tesztek fix értékekre építhetnek.
 * A határidők a mai naphoz relatívak, így mindig van lejárt és közelgő feladat.
 */
class AppFixtures extends Fixture
{
    private const array USERS = [
        'admin@example.com' => ['Kovács Anna', UserRole::Admin],
        'member@example.com' => ['Nagy Péter', UserRole::Member],
        'eszter.szabo@example.com' => ['Szabó Eszter', UserRole::Member],
        'gabor.toth@example.com' => ['Tóth Gábor', UserRole::Member],
        'lilla.horvath@example.com' => ['Horváth Lilla', UserRole::Admin],
        'bence.varga@example.com' => ['Varga Bence', UserRole::Member],
        'dora.kiss@example.com' => ['Kiss Dóra', UserRole::Member],
    ];

    private const array TEAMS = [
        'Webfejlesztés' => [
            'description' => 'Az ügyfélportál és a webshop frontend és backend fejlesztése.',
            'members' => ['admin@example.com', 'member@example.com', 'eszter.szabo@example.com', 'gabor.toth@example.com'],
            'projects' => [
                'Webshop újratervezés' => [ProjectStatus::Active, 'Új termékoldal, kosár és fizetési folyamat.', [
                    ['Drótvázak elkészítése', TaskStatus::Done, TaskPriority::High, 'admin@example.com', -20],
                    ['Termékoldal komponensek', TaskStatus::Done, TaskPriority::Medium, 'member@example.com', -10],
                    ['Kosár API végpontok', TaskStatus::InProgress, TaskPriority::High, 'member@example.com', 3],
                    ['Fizetési szolgáltató integráció', TaskStatus::InProgress, TaskPriority::High, 'gabor.toth@example.com', -2],
                    ['Akadálymentességi audit', TaskStatus::Todo, TaskPriority::Medium, 'eszter.szabo@example.com', 14],
                    ['Teljesítménymérés (Lighthouse)', TaskStatus::Todo, TaskPriority::Low, null, null],
                ]],
                'Ügyfélportál 2.0' => [ProjectStatus::Active, 'Számlák, rendelések és profil kezelése egy helyen.', [
                    ['SSO bejelentkezés', TaskStatus::Done, TaskPriority::High, 'admin@example.com', -30],
                    ['Számlalista lapozással', TaskStatus::InProgress, TaskPriority::Medium, 'eszter.szabo@example.com', 5],
                    ['PDF számla letöltés', TaskStatus::Todo, TaskPriority::Medium, 'gabor.toth@example.com', 10],
                    ['Értesítési beállítások', TaskStatus::Todo, TaskPriority::Low, 'member@example.com', -1],
                ]],
                'Design system' => [ProjectStatus::Completed, 'Közös Bootstrap alapú komponenskönyvtár.', [
                    ['Színpaletta és tipográfia', TaskStatus::Done, TaskPriority::Medium, 'eszter.szabo@example.com', -60],
                    ['Gomb és űrlap komponensek', TaskStatus::Done, TaskPriority::Medium, 'member@example.com', -45],
                    ['Dokumentációs oldal', TaskStatus::Done, TaskPriority::Low, 'admin@example.com', -40],
                ]],
                'Blog migráció' => [ProjectStatus::Planned, 'A régi WordPress blog átköltöztetése.', [
                    ['Tartalom export', TaskStatus::Todo, TaskPriority::Low, null, 30],
                ]],
            ],
        ],
        'Mobil' => [
            'description' => 'iOS és Android alkalmazások.',
            'members' => ['gabor.toth@example.com', 'lilla.horvath@example.com', 'bence.varga@example.com'],
            'projects' => [
                'Mobil app v3' => [ProjectStatus::Active, 'Új navigáció és offline mód.', [
                    ['Offline szinkronizáció', TaskStatus::InProgress, TaskPriority::High, 'bence.varga@example.com', 7],
                    ['Push értesítések', TaskStatus::Todo, TaskPriority::Medium, 'lilla.horvath@example.com', 12],
                    ['Sötét téma', TaskStatus::Done, TaskPriority::Low, 'bence.varga@example.com', -5],
                    ['Crash riportok javítása', TaskStatus::Todo, TaskPriority::High, 'gabor.toth@example.com', -3],
                ]],
                'App Store optimalizálás' => [ProjectStatus::Planned, 'Képernyőképek, leírások, kulcsszavak.', []],
            ],
        ],
        'Adat & BI' => [
            'description' => 'Riportok, adattárház és üzleti intelligencia.',
            'members' => ['lilla.horvath@example.com', 'dora.kiss@example.com'],
            'projects' => [
                'Értékesítési dashboard' => [ProjectStatus::Completed, 'Havi értékesítési KPI-ok vezetőknek.', [
                    ['Adatmodell', TaskStatus::Done, TaskPriority::High, 'dora.kiss@example.com', -50],
                    ['Vizualizációk', TaskStatus::Done, TaskPriority::Medium, 'lilla.horvath@example.com', -35],
                ]],
                'Adattárház migráció' => [ProjectStatus::Active, 'Átállás felhő alapú adattárházra.', [
                    ['ETL folyamatok felmérése', TaskStatus::Done, TaskPriority::Medium, 'dora.kiss@example.com', -15],
                    ['Séma tervezés', TaskStatus::InProgress, TaskPriority::High, 'dora.kiss@example.com', 2],
                    ['Jogosultságkezelés', TaskStatus::Todo, TaskPriority::Medium, 'lilla.horvath@example.com', 20],
                ]],
            ],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $today = new \DateTimeImmutable('today');

        /** @var array<string, User> $users */
        $users = [];
        foreach (self::USERS as $email => [$name, $role]) {
            $users[$email] = UserFactory::createOne(['name' => $name, 'email' => $email, 'roles' => [$role->value]]);
        }

        foreach (self::TEAMS as $teamName => $teamData) {
            $team = TeamFactory::createOne([
                'name' => $teamName,
                'description' => $teamData['description'],
                'members' => array_map(fn (string $email) => $users[$email], $teamData['members']),
            ]);

            foreach ($teamData['projects'] as $projectTitle => [$projectStatus, $projectDescription, $tasks]) {
                $project = ProjectFactory::createOne([
                    'title' => $projectTitle,
                    'status' => $projectStatus,
                    'description' => $projectDescription,
                    'team' => $team,
                ]);

                foreach ($tasks as [$taskTitle, $taskStatus, $priority, $assigneeEmail, $dueInDays]) {
                    TaskFactory::createOne([
                        'title' => $taskTitle,
                        'description' => null,
                        'status' => $taskStatus,
                        'priority' => $priority,
                        'assignee' => null === $assigneeEmail ? null : $users[$assigneeEmail],
                        'dueDate' => null === $dueInDays ? null : $today->modify(\sprintf('%+d days', $dueInDays)),
                        'project' => $project,
                    ]);
                }
            }
        }
    }
}
