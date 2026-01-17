<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Lieu;
use App\Exception\DomainException;
use App\Repository\LieuRepository;
use App\Service\LieuService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class LieuServiceTest extends TestCase
{
    public function testRejectsIncompleteLieu(): void
    {
        $repo = $this->createMock(LieuRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $service = new LieuService($repo, $em);

        $lieu = (new Lieu())
            ->setRue('')
            ->setCodePostal('75001')
            ->setVille('Paris')
            ->setPays('France');

        $this->expectException(DomainException::class);
        $service->normalizeAndDeduplicate($lieu);
    }

    public function testReusesExistingLieu(): void
    {
        $existing = (new Lieu())
            ->setRue('12 avenue de la Paix')
            ->setCodePostal('75001')
            ->setVille('Paris')
            ->setPays('France');

        $repo = $this->createMock(LieuRepository::class);
        $repo
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'rue' => '12 avenue de la Paix',
                'codePostal' => '75001',
                'ville' => 'Paris',
                'pays' => 'France',
            ])
            ->willReturn($existing);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist');

        $service = new LieuService($repo, $em);

        $lieu = (new Lieu())
            ->setRue(' 12 avenue de la Paix ')
            ->setCodePostal('75001')
            ->setVille('Paris')
            ->setPays('France');

        $result = $service->normalizeAndDeduplicate($lieu);
        $this->assertSame($existing, $result);
    }

    public function testPersistsNewLieuWhenNotFound(): void
    {
        $repo = $this->createMock(LieuRepository::class);
        $repo
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist');

        $service = new LieuService($repo, $em);

        $lieu = (new Lieu())
            ->setRue(" 12  avenue   de la Paix ")
            ->setCodePostal('75001')
            ->setVille('Paris')
            ->setPays('France');

        $result = $service->normalizeAndDeduplicate($lieu);

        $this->assertSame($lieu, $result);
        $this->assertSame('12 avenue de la Paix', $result->getRue());
    }
}
