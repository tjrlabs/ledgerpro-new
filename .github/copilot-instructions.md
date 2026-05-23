<!-- Copilot / AI agent instructions for contributors -->

# Project snapshot for AI coding agents

This Laravel 13 application uses a controller -> repository -> Eloquent model flow with DTOs for input validation. Use these notes to be immediately productive and to follow established project patterns.

## Big picture
- Framework: Laravel 13 (PHP 8.4 in Sail). Frontend built with Vite 8 + Tailwind CSS 4 (see `package.json`).
- Primary flow: HTTP Controller -> Repository -> Model. Repositories encapsulate business logic and return `ResponseData` / `SuccessData` / `ErrorData` objects.
- Session reliance: `AppServiceProvider` listens for `Login` and loads `company_profile` into the session. Many methods use `session('company_profile.id')` as the active tenant/company context. See [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php).

## Key directories (start here)
- `app/Http/Controllers` — controller entry points (views + JSON endpoints).
- `app/Repositories` — business logic and data access; controllers call repository methods rather than querying models directly.
- `app/DTO` — DTO classes implement `BaseDTOInterface::from()` and `validate()`; they return `ErrorData` on validation failures. Example: [app/DTO/Clients/ManageClientDTO.php](app/DTO/Clients/ManageClientDTO.php).
- `app/Models` — Eloquent models (Client, Payment, Transaction, AccountBalance, etc.).

## Project-specific conventions and patterns
- DTO pattern: controllers call `XxxDTO::from($request->all())`. The `from()` returns either an `ErrorData` instance or a DTO. Controllers check `instanceof ErrorData` and return validation errors back to the user. Follow this pattern when adding new endpoints.
- Repository responses: repository methods return `ResponseData` wrappers (`SuccessData`/`ErrorData`) — callers check `instanceof ErrorData` before proceeding.
- Constructor property promotion: dependencies are injected via PHP 8 constructor property promotion in controllers and repositories (e.g., `public ClientsRepository $clientsRepository`). Register bindings in service providers only if needed — most classes rely on automatic resolution.
- Session-based tenant: Most queries filter by `company_profile_id` using `session('company_profile.id')`. Tests and scripts that exercise controllers must set this session value or seed an appropriate `CompanyProfile` record.
- Inconsistent DI: some repositories instantiate helper repositories directly (e.g., `new ActionLogRepository()` inside `EmployeeRepository`). When writing tests or refactors, be aware these are not injected and may need to be mocked/stubbed differently.
- Bootstrap auth data is created during migration execution, not through explicit seeders. The roles migration creates `admin` and `user`, and the users migration creates the default admin user `thejairaghav@gmail.com`.

## Build / run / test workflows
- Install PHP dependencies: `composer install`.
- Install JS dependencies: `npm ci` or `npm install`.
- Default local environment: Laravel Sail. Start the app with `./vendor/bin/sail up -d`.
- First-time app setup in Sail:

  - `./vendor/bin/sail artisan key:generate`
  - `./vendor/bin/sail artisan migrate`
  - `./vendor/bin/sail npm install`

- Daily development commands:

  - `./vendor/bin/sail up -d` — start Laravel, MySQL, Redis, Mailpit, and phpMyAdmin containers
  - `./vendor/bin/sail down` — stop the containers
  - `./vendor/bin/sail npm install` — install Linux-native frontend dependencies into the Sail node_modules volume
  - `./vendor/bin/sail npm run dev -- --host 0.0.0.0` — start the Vite dev server inside the container and expose it to the host
  - `./vendor/bin/sail artisan queue:listen --tries=1` — start queue listener
  - `./vendor/bin/sail artisan pail --timeout=0` — tail Laravel logs
  - `./vendor/bin/sail npm run build` — build frontend assets for production

- Tests: `./vendor/bin/sail test` inside Sail, or `php artisan test` on the host when using the sqlite in-memory PHPUnit config.
- Database: local development uses MySQL via Sail by default. The PHPUnit config intentionally uses sqlite in memory for fast host-side tests.
- Local app URL defaults to `http://localhost:8080` to avoid collisions with host web servers on port 80.
- phpMyAdmin is available at `http://localhost:8081` and connects to the Sail MySQL service.
- The Sail app container uses a dedicated Docker volume for `/var/www/html/node_modules` to avoid macOS/Linux native-module conflicts.

## Integration points & external dependencies
- Authentication + session: Laravel auth (Login event used to set `company_profile` in session).
- Background processing: Laravel queues and `php artisan pail` used for log tailing/processing in dev script.
- Frontend: Vite 8 + Tailwind CSS 4; legacy jQuery-based components exist (see `resources/js` / `resources/views`).

## Examples to follow
- Creating a client (controller + DTO + repository): see [app/Http/Controllers/Clients/ClientsController.php](app/Http/Controllers/Clients/ClientsController.php) and [app/DTO/Clients/ManageClientDTO.php](app/DTO/Clients/ManageClientDTO.php).
- Repository pattern example: [app/Repositories/ClientsRepository.php](app/Repositories/ClientsRepository.php) — observe use of `ResponseData` wrappers and `handleAccountBalance()` helper.

## Quick tips for AI edits
- Preserve DTO `from()`/`validate()` semantics and `ErrorData` checks in controllers.
- When changing repository APIs, update all controllers that call them — methods are widely used across controllers.
- Avoid changing global session keys like `company_profile` without ensuring the login listener and all consumers are updated.
- For unit tests: inject repositories where possible; for code that `new`-instantiates helpers, consider small adapters or wrapper bindings to make testing easier.
- Tailwind 4 is compiled through the Vite plugin and `resources/css/app.css` loads `tailwind.config.js` using `@config`. If you change theme or plugin configuration, keep those two files aligned.
- When debugging Vite failures involving native bindings, prefer reinstalling dependencies with `./vendor/bin/sail npm install` before changing package versions.

If any of these areas need deeper coverage (more examples, missing wiring, or test setup snippets), tell me which part to expand and I will iterate. 
