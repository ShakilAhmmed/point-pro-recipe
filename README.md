# RECIPE Management

Platform for managing **users, authentication, and recipes**.

---

## Installation

```bash
# Clone the repository
https://github.com/ShakilAhmmed/point-pro-recipe.git
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

## Access application container

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
php artisan storage:link
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
### Application Structure
```aiignore
This application follows a modular and action-driven architecture designed for clarity, scalability, and separation of concerns.
Below is an overview of the core structure inside the app/ directory:
app/Actions/V1:
    - All the Actions which are related to Api version 1
Enums:
    - All Application Related Constants
Http/Controllers/Api/V1:
    - All the Controller Related to Api version 1.
Http/Requests/Api/V1:
    - All the Form Requests Related to Api version 1.
Http/Resources/Api/V1:
    - All the Api Resources Related to Api version 1.
```


## Developer Tools

```
    - Laravel Pint
    - Larastan
    - PestPHP
    - Symfony Dump Server
    - PHP Insights
```

### Frontend Containers

```bash
cd recipe-frontend
cp .env.example .env
sudo docker-compose up -d
sudo docker exec -it recipe-frontend sh
npm i
```

## TODO

```bash
    - Observability & Monitoring - Integrate OpenTelemetry
    - Add Caching Layer
    - Rate Limiting Strategy
    - Structure Frontend Api Consuming with Actions
    - Laravel Pint Set As PreCommit Hook
```
