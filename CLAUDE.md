# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Toya Amerta — sistem manajemen PDAM (air bersih) tingkat desa. Laravel 13 / PHP 8.3 backend serving both a server-rendered admin panel (Bootstrap 5 "Dusty" template) and a JSON API consumed by a separate Flutter mobile app (not in this repo). Three actors: **admin** (full access), **petugas** (field officer: input meter, konfirmasi pembayaran, lapor maintenance), **pelanggan** (customer, mobile-only: lihat tagihan & riwayat).

## Commands

```bash
# Full local dev (server + queue listener + logs + vite), all in one terminal
composer dev

# Run tests (Pest, config is cleared first)
composer test
php artisan test
php artisan test --filter=CustomerCrudTest
php artisan test tests/Feature/Admin/ZoneCrudTest.php

# Lint / format (Pint)
vendor/bin/pint
vendor/bin/pint --dirty

# Migrate + seed
php artisan migrate --seed

# Regenerate API docs (l5-swagger, annotations live in app/OpenApi)
php artisan l5-swagger:generate
```

Tests run against an in-memory SQLite DB (see `phpunit.xml`), so `php artisan test` needs no DB setup.

## Architecture

Strict Clean Architecture + Repository pattern. Layers live under `app/`:

```
app/Domain/Contracts/      Repository interfaces (e.g. CustomerRepositoryInterface)
app/Domain/DTOs/<Feature>/ Data Transfer Objects, one subfolder per feature
app/Domain/UseCases/<Feature>/  Business logic, one class per action (CreateCustomerUseCase, ListCustomersUseCase, ...)
app/Infrastructure/Repositories/  Eloquent implementations of the Contracts
app/Infrastructure/Import/  Excel/import-specific infra (phpoffice/phpspreadsheet)
app/Http/Controllers/Admin/    Web admin controllers — only call Use Cases, never query the DB directly
app/Http/Controllers/Api/<Role>/  Mobile API controllers, namespaced by actor: Api/Pelanggan, Api/Petugas, Api/Auth
app/Http/Controllers/Public/   Unauthenticated public endpoints (e.g. cek-tagihan)
app/Http/Requests/            Form Requests for validation (Admin/ subfolder for admin-side)
app/Http/Resources/           API Resources — every API response is transformed through one
```

Repository interface → implementation bindings are registered manually in `app/Providers/AppServiceProvider.php`; when adding a new repository, bind it there.

**Non-negotiable rules for this codebase** (from README + established convention):
1. Business logic only lives in Use Cases — controllers just call a Use Case and translate to a response.
2. Controllers never touch Eloquent/DB directly.
3. Repository interface in `Domain/Contracts`, implementation in `Infrastructure/Repositories`.
4. Cross-layer data passes through DTOs (`Domain/DTOs/<Feature>`), not raw arrays or Eloquent models.
5. Validation happens in Form Requests, not in controllers.
6. Every API response goes through an API Resource.
7. All `/api/*` JSON responses use the envelope `{ success, message, data, meta }` — this is enforced globally for exceptions in `bootstrap/app.php` (`withExceptions`), so new exception types thrown from API routes should be added there if they need custom envelope messages/status codes.

## Routing & Auth

- `routes/web.php` — admin panel (session auth) + public `cek-tagihan` routes (no login, reached via WhatsApp links).
- `routes/api.php` — mobile API (Sanctum bearer tokens), grouped by role prefix: `/api/auth`, `/api/pelanggan`, `/api/petugas`. Role gating uses Spatie `role:` middleware aliases (`role`, `permission`, `role_or_permission`, registered in `bootstrap/app.php`).
- Auth/roles use `spatie/laravel-permission`.
- When adding a new mobile endpoint, place the controller under `Http/Controllers/Api/<Role>/` and add it to the matching role-prefixed group in `routes/api.php`.

## Other integrations

- **WhatsApp notifications**: Fonnte API, triggered around billing/payment events (see `app/Jobs`, `app/Services`).
- **Exports**: `barryvdh/laravel-dompdf` for PDF, `phpoffice/phpspreadsheet` for Excel import/export (laporan, import pencatatan meter per bulan).
- **API docs**: `darkaonline/l5-swagger`, annotations in `app/OpenApi`.
- **Datatables**: `yajra/laravel-datatables-oracle` used for admin list views.

## Testing

Pest (`pestphp/pest`) with the Laravel plugin. Feature tests for CRUD flows live in `tests/Feature/Admin/*CrudTest.php` — follow that naming/location convention for new admin CRUD tests.
