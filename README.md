# EventMaster

## Prérequis

- PHP >= 8.2
- Composer
- MySQL ou MariaDB

## Installation

### 1. Cloner le projet

```bash
git clone <url-du-repo>
cd EventMaster
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configuration de l'environnement

Copier le fichier `.env` et l'adapter à votre environnement :

```bash
cp .env .env.local
```

Modifier les variables d'environnement dans `.env.local` selon vos besoins, notamment :
- `DATABASE_URL` : URL de connexion à la base de données
- `APP_ENV` : Environnement (dev, prod)
- `APP_SECRET` : Clé secrète de l'application

### 4. Lancer la base de données (avec MAMP)

```bash
Démarrer votre serveur MAMP
```

### 5. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Installer les assets (facultatif)

```bash
php bin/console importmap:install
php bin/console asset-map:compile
```

### 7. Lancer le serveur de développement

```bash
symfony server:start
```

Ou avec le serveur PHP intégré :

```bash
php -S localhost:8000 -t public/
```

L'application est maintenant accessible à l'adresse : `http://localhost:8000`

## Tests

Exécuter les tests :

```bash
php bin/phpunit
```

## Commandes utiles

- Créer une entité : `php bin/console make:entity`
- Créer une migration : `php bin/console make:migration`
- Vider le cache : `php bin/console cache:clear`
- Afficher les routes : `php bin/console debug:router`
