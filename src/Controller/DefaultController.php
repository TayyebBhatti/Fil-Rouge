<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EvenementRepository;
use App\Repository\InscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
    /**
     * Page d'accueil publique.
     */
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function index(
        EvenementRepository $evenements,
        InscriptionRepository $inscriptions
    ): Response {
        // Événements avec jointures utiles
        $list = $evenements->createQueryBuilder('e')
            ->leftJoin('e.categorie', 'c')->addSelect('c')
            ->leftJoin('e.lieu', 'l')->addSelect('l')
            ->orderBy('e.dateDebut', 'ASC')
            ->getQuery()->getResult();

        // Id des événements où l’utilisateur courant est inscrit
        $registeredEventIds = [];
        if ($this->getUser()) {
            foreach ($inscriptions->findBy(['utilisateur' => $this->getUser()]) as $insc) {
                $ev = $insc->getEvenement();
                if ($ev) {
                    $registeredEventIds[] = $ev->getId();
                }
            }
        }

        return $this->render('default/home.html.twig', [
            'evenements'         => $list,
            'registeredEventIds' => $registeredEventIds,
        ]);
    }

    
}
