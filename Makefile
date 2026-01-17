install:
	composer install

migrate:
	php bin/console doctrine:migrations:migrate -n

cache-clear:
	php bin/console cache:clear

test-db:
	php bin/console doctrine:database:drop --force --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:migrations:migrate -n --env=test

test:
	php bin/phpunit
