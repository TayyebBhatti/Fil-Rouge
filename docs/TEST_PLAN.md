# Plan de tests (CP9)

## Objectif
Valider les parcours critiques de l’application et les exigences de sécurité (accès, CSRF, authentification).

## Environnement de test
- Symfony: `APP_ENV=test`
- Base: MySQL `filrouge_test` (suffix `_test` en env=test)
- Variables: définies via `phpunit.xml` (DATABASE_URL, APP_SECRET, DEFAULT_URI)
- Préparation base:
  - `php bin/console doctrine:database:drop --force --env=test`
  - `php bin/console doctrine:database:create --env=test`
  - `php bin/console doctrine:migrations:migrate -n --env=test`

## Exécution
- `php bin/console cache:clear --env=test`
- `php bin/phpunit`

## Cas de test (automatisés)
| ID | Type | Objectif | Étapes | Résultat attendu |
|----|------|----------|--------|------------------|
| SEC-01 | Fonctionnel | Accès admin protégé | GET `/admin/evenement/` sans login | Redirection vers `/login` |
| SEC-02 | Fonctionnel | Action protégée sans login | POST `/evenement/1/inscription` | Refus ou redirection |
| SEC-03 | Fonctionnel | CSRF inscription | POST `/inscription` sans `_csrf_token` | Refus + redirection |

## Résultats
- Dernière exécution: OK (3 tests, 4 assertions)
