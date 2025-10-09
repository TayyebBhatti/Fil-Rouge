<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Lieu;
use App\Entity\Utilisateur;
use App\Entity\Evenement;
use App\Entity\Inscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // --- Catégories ---
        $categories = [];
        foreach (['Conférence','Atelier','Meetup','Webinaire','Hackathon'] as $nomCat) {
            $c = new Categorie();
            $c->setNom($nomCat);
            $manager->persist($c);
            $categories[] = $c;
        }

        // --- Lieux ---
        $lieux = [];
        $dataLieux = [
            ['rue' => '10 Rue de Paris',     'ville' => 'Strasbourg', 'cp' => 67000, 'pays' => 'France'],
            ['rue' => '25 Avenue de l’Europe','ville' => 'Schiltigheim','cp' => 67300, 'pays' => 'France'],
            ['rue' => '5 Quai des Alpes',    'ville' => 'Illkirch',   'cp' => 67400, 'pays' => 'France'],
            ['rue' => '2 Place du Marché',   'ville' => 'Colmar',     'cp' => 68000, 'pays' => 'France'],
            ['rue' => '48 Grand Rue',        'ville' => 'Mulhouse',   'cp' => 68100, 'pays' => 'France'],
        ];
        foreach ($dataLieux as $l) {
            $lieu = new Lieu();
            $lieu->setRue($l['rue']);
            $lieu->setVille($l['ville']);
            $lieu->setCodePostal($l['cp']);
            $lieu->setPays($l['pays']);
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }

        // --- Utilisateurs ---
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $u = new Utilisateur();
            $u->setNom("User$i");
            $u->setPrenom("Test$i");
            $u->setEmail("user$i@example.com");
            $u->setRoles($i === 1 ? ['ROLE_ADMIN'] : ['ROLE_USER']);
            // champ mot_de_passe => encode
            $hash = $this->hasher->hashPassword($u, 'Password123!');
            $u->setPassword($hash);
            $manager->persist($u);
            $users[] = $u;
        }

        // --- Événements ---
        $events = [];
        for ($i = 1; $i <= 8; $i++) {
            $e = new Evenement();
            $e->setTitre("Événement $i");
            // si ton entité a un champ description
            if (method_exists($e, 'setDescription')) {
                $e->setDescription("Description de l’événement $i");
            }
            // si ton entité a des dates début/fin (DateTimeImmutable recommandé)
            $start = new \DateTime("+$i day 10:00");
            if (method_exists($e, 'setDateDebut')) {
                $e->setDateDebut($start);
            }
            if (method_exists($e, 'setDateFin')) {
                $e->setDateFin($start->modify('+2 hours'));
            }
            // relations
            if (method_exists($e, 'setCategorie')) {
                $e->setCategorie($categories[array_rand($categories)]);
            }
            if (method_exists($e, 'setLieu')) {
                $e->setLieu($lieux[array_rand($lieux)]);
            }
            if (method_exists($e, 'setCreateur')) {
                $e->setCreateur($users[array_rand($users)]);
            }
            $manager->persist($e);
            $events[] = $e;
        }

        // --- Inscriptions (User ↔ Evenement) ---
        // crée 20 inscriptions uniques (sans doublons user/event)
        $pairs = [];
        $count = 0;
        while ($count < 20) {
            $user = $users[array_rand($users)];
            $event = $events[array_rand($events)];
            $key = spl_object_id($user) . '-' . spl_object_id($event);
            if (isset($pairs[$key])) {
                continue;
            }
            $pairs[$key] = true;

            $insc = new Inscription();
            $insc->setUtilisateur($user);
            $insc->setEvenement($event);
            $insc->setDateInscription(new \DateTime());
            $manager->persist($insc);

            $count++;
        }

        $manager->flush();
    }
}
