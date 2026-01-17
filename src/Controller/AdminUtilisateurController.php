<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\AdminUtilisateurService;
use App\Exception\DomainException;
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
    public function promote(Request $request, Utilisateur $utilisateur, AdminUtilisateurService $adminService): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('promote_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Le jeton de sécurité a expiré, veuillez réessayer.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        /** @var Utilisateur|null $acteur */
        $acteur = $this->getUser();
        if (!$acteur instanceof Utilisateur) {
            return $this->redirectToRoute('default_login');
        }

        try {
            $message = $adminService->promote($acteur, $utilisateur);
            $this->addFlash(str_contains($message, 'déjà') ? 'info' : 'success', $message);
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/{id}/demote', name: 'admin_utilisateur_demote', methods: ['POST'])]
    public function demote(Request $request, Utilisateur $utilisateur, AdminUtilisateurService $adminService): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('demote_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Le jeton de sécurité a expiré, veuillez réessayer.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        /** @var Utilisateur|null $acteur */
        $acteur = $this->getUser();
        if (!$acteur instanceof Utilisateur) {
            return $this->redirectToRoute('default_login');
        }

        try {
            $message = $adminService->demote($acteur, $utilisateur);
            $this->addFlash('success', $message);
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/{id}/ban', name: 'admin_utilisateur_ban', methods: ['POST'])]
    public function ban(Request $request, Utilisateur $utilisateur, AdminUtilisateurService $adminService): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('ban_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Le jeton de sécurité a expiré, veuillez réessayer.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        /** @var Utilisateur|null $acteur */
        $acteur = $this->getUser();
        if (!$acteur instanceof Utilisateur) {
            return $this->redirectToRoute('default_login');
        }

        try {
            $message = $adminService->ban($acteur, $utilisateur);
            $this->addFlash(str_contains($message, 'déjà') ? 'info' : 'success', $message);
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/{id}/unban', name: 'admin_utilisateur_unban', methods: ['POST'])]
    public function unban(Request $request, Utilisateur $utilisateur, AdminUtilisateurService $adminService): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('unban_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Le jeton de sécurité a expiré, veuillez réessayer.');

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        $message = $adminService->unban($utilisateur);
        $this->addFlash('success', $message);

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    
}