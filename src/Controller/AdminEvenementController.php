<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Lieu;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\LieuRepository;
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
    public function new(Request $request, EntityManagerInterface $em, LieuRepository $lieux): Response
    {
        $evenement = new Evenement();
        if (method_exists($evenement, 'setCreateur') && $this->getUser()) {
            $evenement->setCreateur($this->getUser());
        }

        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->normalizeAndDeduplicateLieu($evenement, $lieux, $em);
            $em->persist($evenement);
            $em->flush();

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
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $em, LieuRepository $lieux): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->normalizeAndDeduplicateLieu($evenement, $lieux, $em);
            $em->flush();

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

    private function normalizeAndDeduplicateLieu(Evenement $evenement, LieuRepository $lieux, EntityManagerInterface $em): void
    {
        $lieu = $evenement->getLieu();
        if (!$lieu instanceof Lieu) {
            return;
        }

        $rue   = trim((string) ($lieu->getRue() ?? ''));
        $code  = trim((string) ($lieu->getCodePostal() ?? ''));   // <-- camelCase
        $ville = trim((string) ($lieu->getVille() ?? ''));
        $pays  = trim((string) ($lieu->getPays() ?? ''));

        $tousVides = ($rue === '' && $code === '' && $ville === '' && $pays === '');
        if ($tousVides) {
            $evenement->setLieu(null);
            return;
        }

        $existant = $lieux->findOneBy([
            'rue'        => $rue === '' ? null : $rue,
            'codePostal' => $code === '' ? null : $code,
            'ville'      => $ville === '' ? null : $ville,
            'pays'       => $pays === '' ? null : $pays,
        ]);

        if ($existant) {
            $evenement->setLieu($existant);
            return;
        }

        $lieu->setRue($rue === '' ? null : $rue);
        $lieu->setCodePostal($code === '' ? null : $code);   
        $lieu->setVille($ville === '' ? null : $ville);
        $lieu->setPays($pays === '' ? null : $pays);
        $em->persist($lieu);
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
