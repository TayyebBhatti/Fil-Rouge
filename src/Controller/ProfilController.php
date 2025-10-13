<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\InscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'user_profil', methods: ['GET'])]
    public function index(InscriptionRepository $inscriptions): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('default_login');
        }

        // Récupérer toutes les inscriptions de l'utilisateur
        $mesInscriptions = $inscriptions->createQueryBuilder('i')
            ->leftJoin('i.evenement', 'e')->addSelect('e')
            ->leftJoin('e.categorie', 'c')->addSelect('c')
            ->leftJoin('e.lieu', 'l')->addSelect('l')
            ->where('i.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('e.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();

        // Récupérer les événements créés par l'utilisateur (si applicable)
        $mesEvenementsCrees = $user->getEvenementsCrees()->toArray();

        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'mesInscriptions' => $mesInscriptions,
            'mesEvenementsCrees' => $mesEvenementsCrees,
        ]);
    }
}