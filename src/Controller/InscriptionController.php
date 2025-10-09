<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'default_inscription', methods: ['GET','POST'])]
    public function inscription(
        Request $request,
        UtilisateurRepository $users,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        if ($request->isMethod('POST')) {
            $nom    = trim((string)$request->request->get('nom'));
            $prenom = trim((string)$request->request->get('prenom'));
            $email  = trim((string)$request->request->get('email'));
            $plain  = (string)$request->request->get('password');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Format d\'email invalide.');
                return $this->render('security/inscription.html.twig', [
                    'old' => ['nom'=>$nom, 'prenom'=>$prenom, 'email'=>$email],
                ]);
            }

            if ($users->findOneBy(['email' => $email])) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('security/inscription.html.twig', [
                    'old' => ['nom'=>$nom, 'prenom'=>$prenom, 'email'=>$email],
                ]);
            }

            $user = new Utilisateur();
            $user->setNom($nom)
                 ->setPrenom($prenom)
                 ->setEmail($email)
                 ->setRoles(['ROLE_USER'])
                 ->setMotDePasse($hasher->hashPassword($user, $plain));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte créé avec succès. Connectez-vous.');
            return $this->redirectToRoute('default_login');
        }

        return $this->render('security/inscription.html.twig');
    }
}
