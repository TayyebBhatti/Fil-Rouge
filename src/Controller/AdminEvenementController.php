<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Utilisateur;
use App\Entity\Lieu;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\LieuRepository;
use App\Exception\DomainException;
use App\Service\LieuService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/evenement')]
final class AdminEvenementController extends AbstractController
{
    #[Route('/', name: 'admin_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $repo): Response
    {
        return $this->render('admin/evenement/index.html.twig', [
            'evenements' => $repo->findBy([], ['dateDebut' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'admin_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, LieuRepository $lieux, LieuService $lieuService): Response
    {
        $evenement = new Evenement();

        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if ($user instanceof Utilisateur) {
            $evenement->setCreateur($user);
        }

        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $lieu = $evenement->getLieu();
                if ($lieu instanceof Lieu) {
                    $evenement->setLieu($lieuService->normalizeAndDeduplicate($lieu));
                }

                $em->persist($evenement);
                $em->flush();
            } catch (DomainException $e) {
                $this->addFlash('danger', $e->getMessage());

                [$rues, $codes, $villes, $pays] = $this->distinctLieuValues($lieux);

                return $this->render('admin/evenement/new.html.twig', [
                    'form'   => $form->createView(),
                    'rues'   => $rues,
                    'codes'  => $codes,
                    'villes' => $villes,
                    'pays'   => $pays,
                ]);
            }

            $this->addFlash('success', 'Événement créé.');
            return $this->redirectToRoute('admin_evenement_index');
        }

        [$rues, $codes, $villes, $pays] = $this->distinctLieuValues($lieux);

        return $this->render('admin/evenement/new.html.twig', [
            'form'   => $form->createView(),
            'rues'   => $rues,
            'codes'  => $codes,
            'villes' => $villes,
            'pays'   => $pays,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $em, LieuRepository $lieux, LieuService $lieuService): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $lieu = $evenement->getLieu();
                if ($lieu instanceof Lieu) {
                    $evenement->setLieu($lieuService->normalizeAndDeduplicate($lieu));
                }
                $em->flush();
            } catch (DomainException $e) {
                $this->addFlash('danger', $e->getMessage());

                [$rues, $codes, $villes, $pays] = $this->distinctLieuValues($lieux);

                return $this->render('admin/evenement/edit.html.twig', [
                    'evenement' => $evenement,
                    'form'      => $form->createView(),
                    'rues'      => $rues,
                    'codes'     => $codes,
                    'villes'    => $villes,
                    'pays'      => $pays,
                ]);
            }

            $this->addFlash('success', 'Événement modifié.');
            return $this->redirectToRoute('admin_evenement_index');
        }

        [$rues, $codes, $villes, $pays] = $this->distinctLieuValues($lieux);

        return $this->render('admin/evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form'      => $form->createView(),
            'rues'      => $rues,
            'codes'     => $codes,
            'villes'    => $villes,
            'pays'      => $pays,
        ]);
    }

    #[Route('/{id}', name: 'admin_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), (string) $request->request->get('_token'))) {
            $em->remove($evenement);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé.');
        }
        return $this->redirectToRoute('admin_evenement_index');
    }

    /** @return array{0: string[], 1: string[], 2: string[], 3: string[]} */
    private function distinctLieuValues(LieuRepository $lieux): array
    {
        $fetch = function (string $field) use ($lieux): array {
            $rows = $lieux->createQueryBuilder('l')
                ->select("DISTINCT l.$field AS v")
                ->where("l.$field IS NOT NULL")
                ->andWhere("l.$field <> ''")
                ->orderBy("l.$field", 'ASC')
                ->getQuery()
                ->getScalarResult();
            return array_map(static fn(array $r) => (string) $r['v'], $rows);
        };

        $rues   = $fetch('rue');
        $codes  = $fetch('codePostal'); 
        $villes = $fetch('ville');
        $pays   = $fetch('pays');

        return [$rues, $codes, $villes, $pays];
    }
}
