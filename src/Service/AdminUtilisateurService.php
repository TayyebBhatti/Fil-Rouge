<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Utilisateur;
use App\Exception\DomainException;
use Doctrine\ORM\EntityManagerInterface;

final class AdminUtilisateurService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function promote(Utilisateur $acteur, Utilisateur $cible): string
    {
        $this->denySelfAction($acteur, $cible, "Vous ne pouvez pas modifier vos propres privilèges d'administrateur.");

        $roles = $this->normalizeRoles($cible->getRoles());
        if (!\in_array('ROLE_ADMIN', $roles, true)) {
            $roles[] = 'ROLE_ADMIN';
            $cible->setRoles($this->normalizeRoles($roles));
            $this->em->flush();
            return sprintf('Le compte %s est désormais administrateur.', $this->displayEmail($cible));
        }

        return sprintf('Le compte %s est déjà administrateur.', $this->displayEmail($cible));
    }

    public function demote(Utilisateur $acteur, Utilisateur $cible): string
    {
        $this->denySelfAction($acteur, $cible, 'Vous ne pouvez pas retirer vos propres droits administrateur.');

        $roles = \array_values(\array_diff($this->normalizeRoles($cible->getRoles()), ['ROLE_ADMIN']));
        $cible->setRoles($this->normalizeRoles($roles));
        $this->em->flush();

        return sprintf('Le compte %s est maintenant simple utilisateur.', $this->displayEmail($cible));
    }

    public function ban(Utilisateur $acteur, Utilisateur $cible): string
    {
        $this->denySelfAction($acteur, $cible, 'Vous ne pouvez pas bannir votre propre compte.');

        $roles = $this->normalizeRoles($cible->getRoles());
        if (!\in_array('ROLE_BANNED', $roles, true)) {
            $roles = \array_values(\array_diff($roles, ['ROLE_ADMIN']));
            $roles[] = 'ROLE_BANNED';
            $cible->setRoles($this->normalizeRoles($roles));
            $this->em->flush();
            return sprintf('Le compte %s a été banni.', $this->displayEmail($cible));
        }

        return sprintf('Le compte %s est déjà banni.', $this->displayEmail($cible));
    }

    public function unban(Utilisateur $cible): string
    {
        $roles = \array_values(\array_diff($this->normalizeRoles($cible->getRoles()), ['ROLE_BANNED']));
        $cible->setRoles($this->normalizeRoles($roles));
        $this->em->flush();

        return sprintf('Le compte %s a été rétabli.', $this->displayEmail($cible));
    }

    private function denySelfAction(Utilisateur $acteur, Utilisateur $cible, string $message): void
    {
        if ($acteur === $cible) {
            throw new DomainException($message);
        }
    }

    private function displayEmail(Utilisateur $u): string
    {
        $email = trim($u->getEmail());
        return $email !== '' ? $email : 'inconnu';
    }

    /**
     * @param array<int, string> $roles
     * @return array<int, string>
     */
    private function normalizeRoles(array $roles): array
    {
        $roles = \array_values(\array_unique($roles));
        return \array_values(\array_filter($roles, static fn (string $role): bool => $role !== 'ROLE_USER'));
    }
}
