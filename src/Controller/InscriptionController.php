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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InscriptionController extends AbstractController
{
    public function __construct(
    private EntityManagerInterface $em,
    private UserPasswordHasherInterface $passwordHasher,
    private UtilisateurRepository $utilisateurRepository,
    private ValidatorInterface $validator
) {}

    #[Route('/inscription', name: 'default_inscription', methods: ['GET', 'POST'])]
    public function inscription(Request $request): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('security/inscription.html.twig');
        }
        if (!$this->isCsrfTokenValid('register', (string) $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('default_inscription');
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

        $user->setPlainPassword($plain);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
            $this->addFlash('error', $error->getMessage());
        }
        return $this->redirectToRoute('default_inscription');  
        }

        $hashed = $this->passwordHasher->hashPassword($user, (string) $user->getPlainPassword());
        $user->setPassword($hashed);
        $user->eraseCredentials();

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', 'Compte créé.');
        return $this->redirectToRoute('default_login');
    }
}
