.PHONY: up down start stop log migrate migration

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
