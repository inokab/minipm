.PHONY: up down start migrate migration

up:
	docker compose up -d --wait

down:
	docker compose down

start: up
	symfony serve -d

migrate:
	symfony console doctrine:migrations:migrate --no-interaction

migration:
	symfony console make:migration
