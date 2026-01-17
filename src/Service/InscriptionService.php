<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Entity\Utilisateur;
use App\Exception\DomainException;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

final class InscriptionService
{
    public function __construct(
        private readonly InscriptionRepository $inscriptions,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function register(Utilisateur $user, Evenement $evenement): void
    {
        $exists = $this->inscriptions->findOneBy([
            'utilisateur' => $user,
            'evenement' => $evenement,
        ]);

        if ($exists instanceof Inscription) {
            throw new DomainException('Deja inscrit.');
        }

        $max = $evenement->getCapaciteMax();
        if ($max !== null) {
            $current = (int) $this->inscriptions->count(['evenement' => $evenement]);
            if ($current >= $max) {
                throw new DomainException('Evenement complet.');
            }
        }

        $insc = new Inscription();
        $insc->setEvenement($evenement);
        $insc->setUtilisateur($user);
        $insc->setDateInscription(new \DateTimeImmutable());

        $this->em->persist($insc);
        $this->em->flush();
    }

    public function unregister(Utilisateur $user, Evenement $evenement): void
    {
        $insc = $this->inscriptions->findOneBy([
            'utilisateur' => $user,
            'evenement' => $evenement,
        ]);

        if (!$insc instanceof Inscription) {
            throw new DomainException('Aucune inscription trouvee.');
        }

        $this->em->remove($insc);
        $this->em->flush();
    }
}
