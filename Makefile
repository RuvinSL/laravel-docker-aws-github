.PHONY: help build up down restart logs shell test deploy

help:
	@echo "Available commands:"
	@echo "  make build    - Build Docker containers"
	@echo "  make up       - Start containers"
	@echo "  make down     - Stop containers"
	@echo "  make restart  - Restart containers"
	@echo "  make logs     - View container logs"
	@echo "  make shell    - Access app container shell"
	@echo "  make test     - Run tests"
	@echo "  make deploy   - Deploy to production"

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

restart: down up

logs:
	docker-compose logs -f

shell:
	docker-compose exec app bash

test:
	docker-compose exec app php artisan test

migrate:
	docker-compose exec app php artisan migrate

seed:
	docker-compose exec app php artisan db:seed

fresh:
	docker-compose exec app php artisan migrate:fresh --seed

deploy:
	./scripts/deploy.sh