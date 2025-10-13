<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InscriptionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private UtilisateurRepository $utilisateurRepository
    ) {}

    #[Route('/inscription', name: 'default_inscription', methods: ['GET', 'POST'])]
    public function inscription(Request $request): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('security/inscription.html.twig');
        }

        $email   = trim((string) $request->request->get('email', ''));
        $prenom  = trim((string) $request->request->get('prenom', ''));
        $nom     = trim((string) $request->request->get('nom', ''));
        $plain   = (string) $request->request->get('password', '');

        if ($email === '' || $plain === '') {
            $this->addFlash('error', 'Email et mot de passe sont obligatoires.');
            return $this->redirectToRoute('default_inscription');
        }

        if ($this->utilisateurRepository->findOneBy(['email' => $email])) {
            $this->addFlash('error', 'Un compte existe déjà avec cet email.');
            return $this->redirectToRoute('default_inscription');
        }

        $user = new Utilisateur();
        $user->setEmail($email);
        $user->setPrenom($prenom ?: null);
        $user->setNom($nom ?: null);
        $user->setRoles(['ROLE_USER']);

        $hashed = $this->passwordHasher->hashPassword($user, $plain);
        $user->setPassword($hashed);

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', 'Compte créé.');
        return $this->redirectToRoute('default_login'); 
    }
}
