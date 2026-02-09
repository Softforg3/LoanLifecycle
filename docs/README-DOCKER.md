# Docker Setup for Lumen Loan Application

## Requirements
- Docker
- Docker Compose

## Quick Start

### 1. Launch the application
```bash
make setup
```

This command will:
- Build Docker images
- Start containers (app, webserver, db)
- Install Composer dependencies
- Copy .env file
- Run migrations and seeders

### 2. Application available at
```
http://localhost:8080
```

## Available Makefile Commands

```bash
make help              # Display all available commands
make build             # Build Docker images
make up                # Start containers
make down              # Stop containers
make restart           # Restart containers
make logs              # Display logs
make shell             # Enter PHP container
make composer-install  # Install dependencies
make migrate           # Run migrations
make migrate-fresh     # Fresh migrations with seeders
make test              # Run PHPUnit tests
```

## Manual Commands (without Makefile)

### Startup
```bash
# Build and start
docker compose up -d --build

# Install dependencies
docker compose exec app composer install

# Copy .env
docker compose exec app cp .env.example .env

# Run migrations
docker compose exec app php artisan migrate --seed
```

### Tests
```bash
docker compose exec app ./vendor/bin/phpunit
```

### Container Access
```bash
docker compose exec app bash
```

### Logs
```bash
docker compose logs -f app
docker compose logs -f webserver
docker compose logs -f db
```

## Container Structure

- **app** (PHP 7.4-fpm) - main Lumen application
- **webserver** (Nginx) - HTTP server on port 8080
- **db** (MySQL 5.7) - database on port 3306

## Database Configuration

```
Host: db (inside Docker) or localhost (from host)
Port: 3306
Database: lumen
Username: lumen
Password: lumen
Root Password: root
```

## Troubleshooting

### Port 8080 is occupied
Change port in `docker-compose.yml`:
```yaml
webserver:
  ports:
    - "8081:80"  # Use different port
```

### Permission issues
```bash
docker compose exec app chown -R www-data:www-data /var/www
docker compose exec app chmod -R 755 /var/www/storage
```

### Database reset
```bash
make migrate-fresh
```

### Clean reinstall
```bash
docker compose down -v  # Remove containers and volumes
make setup              # Install from scratch
```
