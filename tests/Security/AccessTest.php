<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AccessTest extends WebTestCase
{
    public function testAnonymousIsRedirectedToLoginOnAdmin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/evenement/');

        $this->assertResponseRedirects('/login');
    }
}