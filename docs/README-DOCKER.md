# Docker Setup dla Lumen Loan Application

## Wymagania
- Docker
- Docker Compose

## Szybki start

### 1. Uruchom aplikację
```bash
make setup
```

Ta komenda:
- Zbuduje obrazy Docker
- Uruchomi kontenery (app, webserver, db)
- Zainstaluje zależności Composer
- Skopiuje plik .env
- Uruchomi migracje i seedy

### 2. Aplikacja dostępna pod
```
http://localhost:8080
```

## Dostępne komendy Makefile

```bash
make help              # Wyświetl wszystkie dostępne komendy
make build             # Zbuduj obrazy Docker
make up                # Uruchom kontenery
make down              # Zatrzymaj kontenery
make restart           # Restart kontenerów
make logs              # Wyświetl logi
make shell             # Wejdź do kontenera PHP
make composer-install  # Zainstaluj zależności
make migrate           # Uruchom migracje
make migrate-fresh     # Świeże migracje z seederami
make test              # Uruchom testy PHPUnit
```

## Ręczne komendy (bez Makefile)

### Uruchomienie
```bash
# Zbuduj i uruchom
docker compose up -d --build

# Zainstaluj zależności
docker compose exec app composer install

# Skopiuj .env
docker compose exec app cp .env.example .env

# Uruchom migracje
docker compose exec app php artisan migrate --seed
```

### Testy
```bash
docker compose exec app ./vendor/bin/phpunit
```

### Dostęp do kontenera
```bash
docker compose exec app bash
```

### Logi
```bash
docker compose logs -f app
docker compose logs -f webserver
docker compose logs -f db
```

## Struktura kontenerów

- **app** (PHP 7.4-fpm) - główna aplikacja Lumen
- **webserver** (Nginx) - serwer HTTP na porcie 8080
- **db** (MySQL 5.7) - baza danych na porcie 3306

## Konfiguracja bazy danych

```
Host: db (wewnątrz Dockera) lub localhost (z hosta)
Port: 3306
Database: lumen
Username: lumen
Password: lumen
Root Password: root
```

## Rozwiązywanie problemów

### Port 8080 jest zajęty
Zmień port w `docker compose.yml`:
```yaml
webserver:
  ports:
    - "8081:80"  # Użyj innego portu
```

### Problemy z uprawnieniami
```bash
docker compose exec app chown -R www-data:www-data /var/www
docker compose exec app chmod -R 755 /var/www/storage
```

### Reset bazy danych
```bash
make migrate-fresh
```

### Reinstalacja od zera
```bash
docker compose down -v  # Usuń kontenery i wolumeny
make setup              # Zainstaluj od nowa
```
