<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AccessControlTest extends WebTestCase
{
    public function testAnonymousUserIsRedirectedToLogin(): void
    {
        $client = AccessControlTest::createClient();
        $client->request('GET', '/admin');

        self::assertResponseRedirects('/admin/login');
    }

    public function testMemberCannotAccessUsers(): void
    {
        $client = $this->loginAs('member@example.com');
        $client->request('GET', '/admin/user');

        self::assertResponseStatusCodeSame(403);
    }

    public function testAdminCanAccessUsers(): void
    {
        $client = $this->loginAs('admin@example.com');
        $client->request('GET', '/admin/user');

        self::assertResponseIsSuccessful();
    }

    private function loginAs(string $email): KernelBrowser
    {
        $client = AccessControlTest::createClient();
        $user = AccessControlTest::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        self::assertNotNull($user);
        $client->loginUser($user);

        return $client;
    }
}
