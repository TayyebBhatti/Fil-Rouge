# EventFlow (Fil Rouge)

Application web Symfony de gestion d’événements.

## Stack
- Symfony
- Doctrine ORM
- MySQL (Docker)
- Mailpit (Docker)

## Prérequis
- PHP >= 8.2
- Composer
- Docker + Docker Compose (recommandé)

## Installation (Docker recommandé)
```bash
git clone <repo>
cd Fil-Rouge-main

cp .env.example .env
composer install

docker compose up -d

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n

symfony serve -d
# ou: php -S 127.0.0.1:8000 -t public
