# LedgerPro

LedgerPro is a Laravel 13 application that now uses Laravel Sail as the default local development environment.

## Stack

- PHP 8.4
- Laravel 13
- Laravel Sail
- MySQL 8.4
- Redis
- Mailpit
- phpMyAdmin
- Vite 8
- Tailwind CSS 4

## First-time setup

1. Install dependencies:

	```bash
	composer install
	```

2. Copy the environment file if needed:

	```bash
	cp .env.example .env
	```

3. Start the Sail stack:

	```bash
	./vendor/bin/sail up -d
	```

4. Prepare the application:

	```bash
	./vendor/bin/sail artisan key:generate
	./vendor/bin/sail artisan migrate
	./vendor/bin/sail npm install
	```

The migration flow also bootstraps the default auth data automatically:

- Roles: `admin`, `user`
- Admin user: `Jai Raghav`
- Email: `thejairaghav@gmail.com`
- Password: `pass@111`

## Daily development

Start the containers:

```bash
./vendor/bin/sail up -d
```

Stop the containers:

```bash
./vendor/bin/sail down
```

Run the Vite dev server inside Sail:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev -- --host 0.0.0.0
```

Run `./vendor/bin/sail npm install` again only when frontend dependencies change or the container-side `node_modules` volume needs to be refreshed.

Run the queue worker inside Sail:

```bash
./vendor/bin/sail artisan queue:listen --tries=1
```

Tail Laravel logs with Pail inside Sail:

```bash
./vendor/bin/sail artisan pail --timeout=0
```

## Common commands

Run tests:

```bash
./vendor/bin/sail test
```

Run Artisan commands:

```bash
./vendor/bin/sail artisan about
```

Create a local gzipped database backup in `storage/db-backups`:

```bash
./vendor/bin/sail artisan db:backup-local
```

Build frontend assets:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Reset the local database and recreate the default roles and admin user:

```bash
./vendor/bin/sail artisan migrate:fresh
```

Open a shell in the app container:

```bash
./vendor/bin/sail shell
```

Start phpMyAdmin after changing compose services:

```bash
./vendor/bin/sail up -d
```

## Node modules in Sail

The Sail container now uses a dedicated Docker volume for `/var/www/html/node_modules` so Linux-native packages are installed inside the container instead of reusing macOS host binaries.

If Vite fails after dependency changes, refresh the container-side install with:

```bash
./vendor/bin/sail npm install
```

## Local service ports

- App: http://localhost:8080
- Vite: http://localhost:5173
- Mailpit UI: http://localhost:8025
- phpMyAdmin: http://localhost:8081
- MySQL: localhost:3306 by default via `.env.example`

The committed `.env` in this workspace uses app port `8080` and forwards MySQL to port `8889` to avoid common local port conflicts while still using container-internal port `3306` for the application connection.
