# Procédure de déploiement (CP10)

## Prérequis serveur
- Linux (Debian/Ubuntu)
- PHP 8.2+ (pdo_mysql, intl, mbstring, xml, curl)
- MySQL 8
- Nginx ou Apache
- Composer

## Variables d’environnement (prod)
Configurer via `.env.local` (non versionné) ou variables système.
- `APP_ENV=prod`
- `APP_DEBUG=0`
- `APP_SECRET=...`
- `DATABASE_URL=...`

## Déploiement
1. Récupérer le code
   - `git clone …` ou déploiement par archive
2. Installer les dépendances
   - `composer install --no-dev --optimize-autoloader --no-interaction`
3. Configurer la connexion DB + secret
4. Exécuter les migrations
   - `php bin/console doctrine:migrations:migrate -n --env=prod`
5. Construire le cache prod
   - `php bin/console cache:clear --env=prod`
6. Droits
   - `var/` doit être accessible en écriture par l’utilisateur du serveur web

## Rollback (principe)
- Revenir au tag/release précédent
- Restaurer un backup DB si changement de schéma
- (Option) exécuter une migration down si elle existe et est sûre

## Vérifications post-déploiement
- Accès page d’accueil
- Connexion/déconnexion
- Accès admin protégé
- Logs applicatifs et serveur

## Tests avant prod
- CI: exécution automatique des tests + qualité
- Validation manuelle sur environnement de recette (UAT)
