.PHONY: up down start stop log migrate migration fixtures stan cs cs-fix lint test check

up:
	docker compose up -d --wait

down:
	docker compose down

start: up
	symfony serve -d

stop:
	symfony server:stop
	$(MAKE) down

log:
	symfony server:log

migrate:
	symfony console doctrine:migrations:migrate --no-interaction

migration:
	symfony console make:migration

fixtures:
	symfony console doctrine:fixtures:load --no-interaction

stan:
	symfony php vendor/bin/phpstan analyse

cs:
	symfony php vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix:
	symfony php vendor/bin/php-cs-fixer fix

lint:
	symfony console lint:yaml config translations
	symfony console lint:twig templates
	symfony console lint:container
	symfony console doctrine:schema:validate

test: export APP_ENV = test
test: export DATABASE_URL = mysql://root:root@127.0.0.1:3306/app?serverVersion=8.4&charset=utf8mb4
test:
	symfony console doctrine:database:drop --env=test --force --if-exists
	symfony console doctrine:database:create --env=test
	symfony console doctrine:migrations:migrate --env=test --no-interaction
	symfony console doctrine:fixtures:load --env=test --no-interaction
	symfony php bin/phpunit

check: cs stan lint test
