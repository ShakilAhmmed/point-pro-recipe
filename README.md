# RECIPE BACKEND API

API platform for managing **users, authentication, and recipes**.

---

## Installation

```bash
# Clone the repository
git clone https://github.com/ShakilAhmmed/recipe-backend-api.git
cd recipe-backend-api
```

## Copy environment file

```bash
cp .env.example .env
```

#### Write your NGINX and PHPMYADMIN PORT in .env and Set username and password for database

##### Do not use @ in the DB_PASSWORD [ex: "!!2083!!"]

## Start containers

```bash
sudo docker-compose up -d
```

# Access application container

```bash
sudo docker exec -it recipe-backend bash
```

## Install dependencies

```bash
composer install
```

## Generate application key

```bash
php artisan key:generate
```

## Install Passport keys

```bash
php artisan passport:install
```

#### Write your PASSPORT_PASSWORD_CLIENT_ID and PASSPORT_PASSWORD_SECRET PORT in .env.

## Run migrations and seeders

```bash
php artisan migrate --seed
```

## API Documentation in

```bash
http://.........../docs/api
```

## Run Tests (Pest)

```bash
sudo docker exec -it recipe-backend bash
php artisan test
```

## Static Analysis & Linting

```bash
sudo docker exec -it recipe-backend bash
vendor/bin/phpstan analyse --memory-limit=1G
```

## Code style check

```bash
sudo docker exec -it recipe-backend bash
vendor/bin/pint --test
```

## Code quality report

```bash
sudo docker exec -it recipe-backend bash
php artisan insights
```

## Developer Tools

```
    - Laravel Pint
    - Larastan
    - PestPHP
    - Symfony Dump Server
    - PHP Insights
```
## TODO
```bash
    - Observability & Monitoring - Integrate OpenTelemetry
    - Add Caching Layer
    - Rate Limiting Strategy
    - Laravel Pint as PreCommit Hook
```
