# AGENTS.md

When this file is loaded as agent context, first reply exactly:

`context loaded`

## Keep This File Current

After making code, command, architecture, workflow, or convention changes, check whether this file is now stale. Update `AGENTS.md` in the same turn when the change would alter how future agents should work in this repository.

## Project Overview

LedgerPro is a Laravel business ledger application. It uses Laravel 13, PHP 8.4 in Sail, MySQL 8.4, Redis, Mailpit, phpMyAdmin, Vite 8, Tailwind CSS 4, Alpine.js, jQuery, jQuery Validation, and Font Awesome.

The main business areas are:

- Clients and opening account balances.
- Inventory/items.
- Sales, payments, expenses, and client ledger views.
- Employees, salary history, advances, attendance boards, and salary payment processing.
- Reports, especially the payments board and client-payment rows.

## Local Development

Prefer Sail for local app commands because the README defines Sail as the default development environment.

- Start containers: `./vendor/bin/sail up -d`
- Stop containers: `./vendor/bin/sail down`
- Run tests: `./vendor/bin/sail test`
- Run Artisan: `./vendor/bin/sail artisan <command>`
- Run Vite inside Sail: `./vendor/bin/sail npm run dev -- --host 0.0.0.0`
- Build assets: `./vendor/bin/sail npm run build`
- Tail logs: `./vendor/bin/sail artisan pail --timeout=0`
- Open a container shell: `./vendor/bin/sail shell`

Composer also defines:

- `composer dev` for a local combined server, queue listener, logs, and Vite workflow.
- `composer test` to clear config and run `php artisan test`.

The committed PHPUnit config uses sqlite in-memory for tests.

## Architecture Notes

- Web routes live in `routes/web.php`; API routes live in `routes/api.php`.
- Controllers are grouped by domain under `app/Http/Controllers`.
- Most business writes flow through repositories in `app/Repositories`.
- Request payload normalization and validation usually live in DTOs under `app/DTO`; DTO `from()` methods return either the DTO or `App\Classes\ErrorData`.
- Repository methods generally return `App\Classes\ResponseData`, `SuccessData`, or `ErrorData`.
- Sales, payments, expenses, and ledger behavior are centered on the unified `transactions` table and `App\Repositories\TransactionRepository`.
- Reports include payments boards and month-year P/L reports under `app/Http/Controllers/Reports`, `app/Repositories`, and `resources/views/pages/reports`.
- Do not reintroduce old separate Sales/Payment/Expense repository paths unless the task explicitly calls for restoring legacy behavior.
- Shared UI shells and form components live under `resources/views/components`; page views live under `resources/views/pages`.
- Global frontend styles and reusable Tailwind component classes live in `resources/css/app.css`.
- `App\Models\Reports\PaymentsBoard` uses `finalized_at` as the durable signal that a board has been finalized; P/L creation should check that instead of inferring from timestamps.

## Tenant Isolation Rules

This app is company-profile scoped. Treat tenant isolation as a core invariant.

- The active company source of truth is `App\Classes\CurrentCompany::id()`, which reads `session('company_profile.id')`.
- Login stores the user's default `CompanyProfile` in session via `App\Providers\AppServiceProvider`.
- All business reads, writes, updates, deletes, reports, lookups, and API responses for company-owned data must filter by `company_profile_id`.
- Avoid unscoped `find()`, `findOrFail()`, `all()`, broad relationship traversal, or direct model updates on tenant-owned models.
- When creating tenant-owned records, set `company_profile_id` from `CurrentCompany::id()`.
- When linking records, verify both sides belong to the active company.
- If adding validation rules such as `exists`, remember that raw `exists:table,id` only checks global existence. Add company-aware validation when cross-company IDs would be unsafe.

Tenant-sensitive models include at least clients, items, transactions, account balances, employees, attendance boards, employee attendance rows, employee balances, payments boards, client-payment rows, and action logs.

## Testing Guidance

- Prefer feature tests for web workflows and tenant-isolation regressions.
- Existing tenant coverage lives in `tests/Feature/TenantWorkflowRegressionTest.php` and `tests/Feature/CompanyProfileTenantIsolationTest.php`.
- Use `RefreshDatabase` for database feature tests.
- For session-scoped workflows, authenticate the user and set `company_profile` in the session.
- When changing cross-module flows, test that records from another company are not listed, edited, deleted, or used in calculations.

## Frontend Guidance

- Use Blade, Tailwind utility classes, existing Blade components, and the shared classes in `resources/css/app.css`.
- Keep UI changes consistent with the current always-dark black/purple dashboard style, compact tables, badge classes, button classes, and shared app shell unless the task asks for a redesign.
- Alpine is available globally from `resources/js/app.js`.
- The shared desktop sidebar toggle lives across `resources/views/components/layouts/app-layout.blade.php`, `resources/views/components/partials/header.blade.php`, and `resources/views/components/partials/sidebar.blade.php`, with collapsed state persisted in `localStorage` under `ledgerpro:sidebar-collapsed`.
- Payments board tables in the report views use a bounded scroll container with sticky headers; preserve that pattern when adjusting those tables.
- Font Awesome is imported globally through `resources/css/app.css`.
- Page layouts generally use `<x-layouts.app-layout>` and the shared header/sidebar/footer components.

## Data And Bootstrap Notes

- The project currently bootstraps default auth/company data through migrations rather than `DatabaseSeeder`.
- The default local admin noted in the README is `thejairaghav@gmail.com` with password `pass@111`.
- The default company profile is intended to be `Ampspark Technologies Private Limited` for the seeded admin user.

## Coding Conventions

- Match the existing Laravel style and domain structure before adding new abstractions.
- Keep controllers thin where practical; put reusable business/database behavior in repositories.
- Keep DTO validation and field mapping close to existing DTO patterns.
- Use Laravel relationships, scopes, query builders, migrations, and collections rather than ad hoc SQL or string parsing when possible.
- Preserve existing user changes in the working tree. Do not revert unrelated modifications.
- Prefer focused changes and focused tests over broad refactors.
- Use `rg` for searching.
