<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Entity\Utilisateur;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function register(Evenement $evenement, Request $request, EntityManagerInterface $em, InscriptionRepository $inscriptions): Response
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

        $exists = $inscriptions->findOneBy(['utilisateur' => $user, 'evenement' => $evenement]);
        if ($exists) {
            $this->addFlash('error', 'Déjà inscrit.');
            return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
        }

        if (null !== $evenement->getCapaciteMax()
            && \count($evenement->getInscriptions()) >= $evenement->getCapaciteMax()) {
            $this->addFlash('error', 'Événement complet.');
            return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
        }

        $insc = new Inscription();
        $insc->setEvenement($evenement);
        $insc->setUtilisateur($user);
        $insc->setDateInscription(new \DateTimeImmutable());

        $em->persist($insc);
        $em->flush();

        $this->addFlash('success', 'Inscription enregistrée.');
        return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
    }

    #[Route('/evenement/{id}/desinscription', name: 'evenement_unregister', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function unregister(Evenement $evenement, Request $request, EntityManagerInterface $em, InscriptionRepository $inscriptions): Response
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

        $insc = $inscriptions->findOneBy(['utilisateur' => $user, 'evenement' => $evenement]);
        if (!$insc) {
            $this->addFlash('error', 'Aucune inscription trouvée.');
            return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
        }

        $em->remove($insc);
        $em->flush();

        $this->addFlash('success', 'Désinscription effectuée.');
        return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
    }
}
