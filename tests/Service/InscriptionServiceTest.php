<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Entity\Utilisateur;
use App\Exception\DomainException;
use App\Repository\InscriptionRepository;
use App\Service\InscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class InscriptionServiceTest extends TestCase
{
    public function testRegisterRejectsAlreadyRegistered(): void
    {
        $repo = $this->createMock(InscriptionRepository::class);
        $repo->method('findOneBy')->willReturn(new Inscription());

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist');

        $service = new InscriptionService($repo, $em);

        $this->expectException(DomainException::class);
        $service->register(new Utilisateur(), new Evenement());
    }

    public function testRegisterRejectsWhenFull(): void
    {
        $repo = $this->createMock(InscriptionRepository::class);
        $repo->method('findOneBy')->willReturn(null);
        $repo->method('count')->willReturn(10);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist');

        $service = new InscriptionService($repo, $em);

        $event = new Evenement();
        $event->setCapaciteMax(10);

        $this->expectException(DomainException::class);
        $service->register(new Utilisateur(), $event);
    }

    public function testRegisterPersistsAndFlushes(): void
    {
        $repo = $this->createMock(InscriptionRepository::class);
        $repo->method('findOneBy')->willReturn(null);
        $repo->method('count')->willReturn(0);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $service = new InscriptionService($repo, $em);

        $event = new Evenement();
        $event->setCapaciteMax(10);

        $service->register(new Utilisateur(), $event);
        $this->assertTrue(true);
    }

    public function testUnregisterRemovesAndFlushes(): void
    {
        $repo = $this->createMock(InscriptionRepository::class);
        $repo->method('findOneBy')->willReturn(new Inscription());

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $service = new InscriptionService($repo, $em);
        $service->unregister(new Utilisateur(), new Evenement());
    }
}
