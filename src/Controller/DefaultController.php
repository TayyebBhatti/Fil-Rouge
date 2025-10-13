<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\EvenementRepository;
use App\Repository\InscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
    /**
     * Page d'accueil publique.
     */
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function index(
        Request $request,
        EvenementRepository $evenements,
        InscriptionRepository $inscriptions,
        CategorieRepository $categories
    ): Response {
        $search = trim((string) $request->query->get('search', ''));
        $categorieId = $request->query->get('categorie');
        $dateDebut = $request->query->get('date_debut');
        $dateFin = $request->query->get('date_fin');

        $qb = $evenements->createQueryBuilder('e')
            ->leftJoin('e.categorie', 'c')->addSelect('c')
            ->leftJoin('e.lieu', 'l')->addSelect('l');

        if ($search !== '') {
            $qb->andWhere('e.titre LIKE :search OR e.description LIKE :search OR l.ville LIKE :search OR l.pays LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($categorieId) {
            $qb->andWhere('c.id = :categorieId')
               ->setParameter('categorieId', $categorieId);
        }

        if ($dateDebut) {
            $qb->andWhere('e.dateDebut >= :dateDebut')
               ->setParameter('dateDebut', new \DateTime($dateDebut));
        }

        if ($dateFin) {
            $qb->andWhere('e.dateDebut <= :dateFin')
               ->setParameter('dateFin', new \DateTime($dateFin));
        }

        $list = $qb->orderBy('e.dateDebut', 'ASC')
                   ->getQuery()
                   ->getResult();

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
            'categories'         => $categories->findAll(),
            'currentSearch'      => $search,
            'currentCategorie'   => $categorieId,
            'currentDateDebut'   => $dateDebut,
            'currentDateFin'     => $dateFin,
        ]);
    }
}