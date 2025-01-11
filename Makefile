.PHONY: up down build setup migrate seed test

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

setup:
	docker compose exec -it postgres_db psql -U postgres -c "CREATE DATABASE customers;"
	docker compose exec -it postgres_db psql -U postgres -c "CREATE DATABASE products;"
	docker compose exec -it postgres_db psql -U postgres -c "CREATE DATABASE orders;"

migrate:
	docker compose exec -it customer_service php artisan migrate
	docker compose exec -it product_service php artisan migrate
	docker compose exec -it order_service php artisan migrate

seed:
	docker compose exec -it customer_service php artisan db:seed
	docker compose exec -it product_service php artisan db:seed
	docker compose exec -it order_service php artisan db:seed

test:
	docker compose exec -it customer_service php artisan test
	docker compose exec -it product_service php artisan test
	docker compose exec -it order_service php artisan test
