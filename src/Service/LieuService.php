<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Lieu;
use App\Exception\DomainException;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;

final class LieuService
{
    public function __construct(
        private readonly LieuRepository $lieux,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Normalise et deduplique un lieu. Retourne l'entite a utiliser.
     */
    public function normalizeAndDeduplicate(Lieu $lieu): Lieu
    {
        $rue = $this->norm($lieu->getRue());
        $code = $this->norm($lieu->getCodePostal());
        $ville = $this->norm($lieu->getVille());
        $pays = $this->norm($lieu->getPays());

        if ($rue === '' || $code === '' || $ville === '' || $pays === '') {
            throw new DomainException('Le lieu doit etre complet: rue, code postal, ville, pays.');
        }

        $existing = $this->lieux->findOneBy([
            'rue' => $rue,
            'codePostal' => $code,
            'ville' => $ville,
            'pays' => $pays,
        ]);

        if ($existing instanceof Lieu) {
            return $existing;
        }

        $lieu->setRue($rue);
        $lieu->setCodePostal($code);
        $lieu->setVille($ville);
        $lieu->setPays($pays);

        $this->em->persist($lieu);

        return $lieu;
    }

    private function norm(string $v): string
    {
        $v = trim($v);
        // Normalisation legere. Pas de magic (accents, etc.).
        $v = preg_replace('/\s+/', ' ', $v) ?? $v;
        return $v;
    }
}
