.PHONY: bootstrap up down shell db-fresh db-load logs build install artisan test

bootstrap:
	./bin/bootstrap

up:
	docker compose up -d

down:
	docker compose down

shell:
	docker compose exec php sh

db-fresh:
	docker compose exec php php artisan migrate:fresh --seed

db-load:
	@read -p "Dump file [storage/app/dumps/production.sql]: " f; \
	docker compose exec -T postgres psql -U wordle_group -d wordle_group < $${f:-storage/app/dumps/production.sql}

db-dump:
	docker compose exec postgres pg_dump -U wordle_group wordle_group > storage/app/dumps/$$(date +%Y%m%d_%H%M%S).sql

logs:
	docker compose logs -f

build:
	docker compose build --no-cache

install:
	docker compose exec php composer install
	docker compose exec php npm install
	docker compose exec php npm run prod

artisan:
	docker compose exec php php artisan $(filter-out $@,$(MAKECMDGOALS))

test:
	docker compose exec php php artisan test

%:
	@:
