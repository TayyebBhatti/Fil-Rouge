<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EvenementRepository;


class DefaultController extends AbstractController
{

    /**
     * Page d'accueil
     * @return Response
     */
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function index(EvenementRepository $repo): Response
    {
        $evenements = $repo->createQueryBuilder('e')
            ->leftJoin('e.categorie', 'c')->addSelect('c')
            ->leftJoin('e.lieu', 'l')->addSelect('l')
            ->getQuery()->getResult();
        return $this->render('default/home.html.twig', [
            'evenements' => $evenements,
        ]);
    }


     #[Route('/login', name: 'default_login', methods: ['GET'])]
    public function login()
    {
        return $this->render('security/login.html.twig');
        # return new Response('<h1>Hello World!</h1>');
    }

     #[Route('/details', name: 'default_details', methods: ['GET'])]
    public function details()
    {
        return $this->render('default/details.html.twig');
        # return new Response('<h1>Hello World!</h1>');
    }


}