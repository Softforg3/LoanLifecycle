.PHONY: help build up down restart logs shell composer migrate seed test

help: ## Wyświetl pomoc
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Zbuduj kontenery Docker
	docker compose build

up: ## Uruchom kontenery
	docker compose up -d

down: ## Zatrzymaj kontenery
	docker compose down

restart: down up ## Restart kontenerów

logs: ## Pokaż logi
	docker compose logs -f

shell: ## Wejdź do kontenera app
	docker compose exec app bash

composer-install: ## Zainstaluj zależności Composer
	docker compose exec app composer install

migrate: ## Uruchom migracje
	docker compose exec app php artisan migrate

seed: ## Uruchom seedery
	docker compose exec app php artisan db:seed

migrate-fresh: ## Świeże migracje z seederami
	docker compose exec app php artisan migrate:fresh --seed

test: ## Uruchom testy
	docker compose exec app ./vendor/bin/phpunit

setup: build up composer-install ## Pełna konfiguracja projektu
	@echo "Kopiowanie .env..."
	@docker compose exec app cp .env.example .env || true
	@echo "Generowanie klucza aplikacji..."
	@docker compose exec app php artisan key:generate || true
	@echo "Uruchamianie migracji..."
	@docker compose exec app php artisan migrate --seed || true
	@echo "✅ Aplikacja gotowa na http://localhost:8080"
