.PHONY: up down start stop log migrate migration fixtures stan cs cs-fix lint check

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

check: cs stan lint
