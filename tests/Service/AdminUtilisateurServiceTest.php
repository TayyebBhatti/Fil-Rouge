<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Utilisateur;
use App\Exception\DomainException;
use App\Service\AdminUtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class AdminUtilisateurServiceTest extends TestCase
{
    public function testPromoteRejectsSelf(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('flush');

        $service = new AdminUtilisateurService($em);

        $user = (new Utilisateur())->setEmail('a@b.c');

        $this->expectException(DomainException::class);
        $service->promote($user, $user);
    }

    public function testBanRemovesAdminAndAddsBanned(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('flush');

        $service = new AdminUtilisateurService($em);

        $acteur = (new Utilisateur())->setEmail('admin@site.tld')->setRoles(['ROLE_ADMIN']);
        $cible = (new Utilisateur())->setEmail('user@site.tld')->setRoles(['ROLE_ADMIN']);

        $service->ban($acteur, $cible);

        $roles = $cible->getRoles();
        $this->assertContains('ROLE_BANNED', $roles);
        $this->assertNotContains('ROLE_ADMIN', $roles);
    }
}
