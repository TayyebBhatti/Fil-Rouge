<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{

    /**
     * Page d'accueil
     * @return Response
     */
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function home()
    {
        return $this->render('default/home.html.twig');
        # return new Response('<h1>Hello World!</h1>');
    }


     #[Route('/login', name: 'default_login', methods: ['GET'])]
    public function login()
    {
        return $this->render('default/login.html.twig');
        # return new Response('<h1>Hello World!</h1>');
    }

     #[Route('/inscription', name: 'default_inscription', methods: ['GET'])]
    public function inscription()
    {
        return $this->render('default/inscription.html.twig');
        # return new Response('<h1>Hello World!</h1>');
    }

    /**
     * Page catégorie des évènements
     * ex. https://localhost:8000/categorie/sport
     * ex. https://localhost:8000/categorie/concert
     * ex. https://localhost:8000/categorie/spectacle
     * @return Response
     */
    #[Route('/categorie/{type}', name: 'default_category', methods: ['GET'])]
    public function category($type)
    {
        return $this->render('default/category.html.twig', [
            'type' => $type,
        ]);
    }

    /**
     * Page pour afficher un évènement
     * ex. https://localhost:8000/{param:category}/{param:titre}_{param:id}
     * ex. https://localhost:8000/spectacle/week-end-raclette-a-baggersee_875456
     * @return Response
     */
    #[Route('/{category}/{title}_{id}', name: 'default_event', methods: ['GET'])]
    public function event($category, $title, $id): Response
    {
        return new Response("
            <h1>
                Catégorie : $category
                <br> Titre : $title,
                <br> ID : $id,
            </h1>
        ");
    }

    



}