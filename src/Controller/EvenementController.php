<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Utilisateur;
use App\Repository\InscriptionRepository;
use App\Exception\DomainException;
use App\Service\InscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EvenementController extends AbstractController
{
    #[Route('/evenement/{id}', name: 'evenement_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Evenement $evenement, InscriptionRepository $inscriptions): Response
    {
                $dejaInscrit = false;

        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if ($user instanceof Utilisateur) {
            $dejaInscrit = (bool) $inscriptions->findOneBy([
                'utilisateur' => $user,
                'evenement'   => $evenement,
            ]);
        }

        $createur = $evenement->getCreateur();

        return $this->render('evenement/show.html.twig', [
            'evenement'   => $evenement,
            'dejaInscrit' => $dejaInscrit,
            'createur'    => $createur,
        ]);
    }

    #[Route('/evenement/{id}/inscription', name: 'evenement_register', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function register(Evenement $evenement, Request $request, InscriptionService $inscriptionService): Response
    {
                /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('default_login');
        }

        if (!$this->isCsrfTokenValid('inscrire_' . $evenement->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
        }

        try {
            $inscriptionService->register($user, $evenement);
            $this->addFlash('success', 'Inscription enregistrée.');
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
    }

    #[Route('/evenement/{id}/desinscription', name: 'evenement_unregister', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function unregister(Evenement $evenement, Request $request, InscriptionService $inscriptionService): Response
    {
                /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('default_login');
        }

        if (!$this->isCsrfTokenValid('desinscrire_' . $evenement->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
        }

        try {
            $inscriptionService->unregister($user, $evenement);
            $this->addFlash('success', 'Désinscription effectuée.');
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
    }
}
