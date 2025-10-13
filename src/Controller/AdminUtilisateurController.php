<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/utilisateurs')]
final class AdminUtilisateurController extends AbstractController
{
    #[Route('/', name: 'admin_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurs): Response
    {
        $liste = $utilisateurs->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC', 'email' => 'ASC']);

        return $this->render('admin/utilisateur/index.html.twig', [
            'utilisateurs' => $liste,
        ]);
    }

    #[Route('/{id}/promote', name: 'admin_utilisateur_promote', methods: ['POST'])]
    public function promote(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('promote_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Le jeton de sécurité a expiré, veuillez réessayer.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        if ($utilisateur === $this->getUser()) {
            $this->addFlash('error', "Vous ne pouvez pas modifier vos propres privilèges d'administrateur.");

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        $roles = $this->normalizeRoles($utilisateur->getRoles());

        if (!\in_array('ROLE_ADMIN', $roles, true)) {
            $roles[] = 'ROLE_ADMIN';
            $utilisateur->setRoles($this->normalizeRoles($roles));
            $entityManager->flush();

            $this->addFlash('success', sprintf('Le compte %s est désormais administrateur.', $utilisateur->getEmail() ?? 'inconnu'));
        } else {
            $this->addFlash('info', sprintf('Le compte %s est déjà administrateur.', $utilisateur->getEmail() ?? 'inconnu'));
        }

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/{id}/demote', name: 'admin_utilisateur_demote', methods: ['POST'])]
    public function demote(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('demote_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Le jeton de sécurité a expiré, veuillez réessayer.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        if ($utilisateur === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas retirer vos propres droits administrateur.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        $roles = \array_values(\array_diff($this->normalizeRoles($utilisateur->getRoles()), ['ROLE_ADMIN']));
        $utilisateur->setRoles($this->normalizeRoles($roles));
        $entityManager->flush();

        $this->addFlash('success', sprintf('Le compte %s est maintenant simple utilisateur.', $utilisateur->getEmail() ?? 'inconnu'));

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/{id}/ban', name: 'admin_utilisateur_ban', methods: ['POST'])]
    public function ban(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('ban_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Le jeton de sécurité a expiré, veuillez réessayer.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        if ($utilisateur === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas bannir votre propre compte.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        $roles = $this->normalizeRoles($utilisateur->getRoles());

        if (!\in_array('ROLE_BANNED', $roles, true)) {
            $roles = \array_values(\array_diff($roles, ['ROLE_ADMIN']));
            $roles[] = 'ROLE_BANNED';
            $utilisateur->setRoles($this->normalizeRoles($roles));
            $entityManager->flush();

            $this->addFlash('success', sprintf('Le compte %s a été banni.', $utilisateur->getEmail() ?? 'inconnu'));
        } else {
            $this->addFlash('info', sprintf('Le compte %s est déjà banni.', $utilisateur->getEmail() ?? 'inconnu'));
        }

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/{id}/unban', name: 'admin_utilisateur_unban', methods: ['POST'])]
    public function unban(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('unban_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Le jeton de sécurité a expiré, veuillez réessayer.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        $roles = \array_values(\array_diff($this->normalizeRoles($utilisateur->getRoles()), ['ROLE_BANNED']));
        $utilisateur->setRoles($this->normalizeRoles($roles));
        $entityManager->flush();

        $this->addFlash('success', sprintf('Le compte %s a été rétabli.', $utilisateur->getEmail() ?? 'inconnu'));

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    /**
     * Supprime les doublons et le rôle implicite ROLE_USER avant persistance.
     *
     * @param array<int, string> $roles
     *
     * @return array<int, string>
     */
    private function normalizeRoles(array $roles): array
    {
        $roles = \array_values(\array_unique($roles));

        return \array_values(\array_filter($roles, static fn (string $role): bool => $role !== 'ROLE_USER'));
    }
}
