# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MagShip B2B Booking — a Laravel 12 web application for managing tour ticket bookings between an admin (tour provider), B2B partners (hotels/tour operators), and drivers/captains. Uses Metronic 8.3.2 Bootstrap theme for UI, MySQL for database, and DomPDF for PDF generation.

**Key docs to read first:** `PROJECT.md` (specs, UI guidelines, code standards), `SYSTEM-FLOW.md` (routes, booking lifecycle, edge cases), `TECHNICAL.md` (deployment/ops).

## Commands

```bash
# Setup (full): install deps, .env, key, migrate, build assets
composer setup

# Development: runs artisan serve + queue:listen + pail + vite dev concurrently
composer dev

# Tests (Pest, SQLite in-memory)
composer test                          # all tests
php artisan test --filter=TestName     # single test

# Lint (Laravel Pint, PSR-12/Laravel style)
vendor/bin/pint

# Build frontend assets
npm run build
```

## Architecture

### Three-Role System
Routes are grouped by role prefix with middleware (`role:admin`, `role:partner`, `role:driver`):
- `/admin/*` — Full management (tours, bookings, partners, accounting, calendar, settings)
- `/partner/*` — Booking CRUD for their own clients, dashboard
- `/driver/*` — View assigned shifts and manifests

### Action Pattern
All business logic lives in single-responsibility Action classes in `app/Actions/` with a single `execute()` method. Controllers are thin — they validate via Form Requests, delegate to Actions, and return responses.

### Key Directories
- `app/Actions/` — Business logic (Booking/, Partner/, Payment/, Tour/)
- `app/Enums/` — PHP 8.1 backed enums with helper methods (label(), color()) for BookingStatus, UserRole, PaxType, Season, etc.
- `app/Http/Requests/` — Form Request validation classes (Admin/, Partner/)
- `app/Services/EmailService.php` — Centralized email sending with automatic logging to `email_logs` table
- `app/Traits/Auditable.php` + `app/Observers/AuditObserver.php` — Automatic audit logging on models
- `app/Models/Setting.php` — Key-value settings stored in DB with group support and caching

### Blade Components
Reusable components in `resources/views/components/`:
- `forms/` — input, select, textarea, checkbox, toggle, date-picker, price-input
- `ui/` — icon, badge, button, card, modal, alert
- `booking/` — status-badge, pax-badges, passenger-row
- `partner/` — avatar

Usage: `<x-forms.input name="email" label="Email" icon="sms" required />`

### Layouts
Three separate layouts: `resources/views/layouts/admin.blade.php`, `partner.blade.php`, `driver.blade.php`

## Code Standards

- **All PHP files**: `declare(strict_types=1);`
- **All functions**: typed parameters, return types, and DocBlock comments
- **Models**: declared as `final class`, use `@property` PHPDoc annotations
- **Validation**: always in Form Request classes, never in controllers
- **Icons**: Keenicons (`ki-duotone ki-*`) — NOT Bootstrap Icons
- **Tables**: server-side pagination with Laravel (`->paginate()`), NO DataTables
- **Search/filters**: form submission, not JavaScript
- **CSS**: Metronic Bootstrap class patterns (`form-control-solid`, `badge-light-success`, etc.)
- **When implementing on existing mockup views**: refactor inline HTML to use Blade components
- **i18n**: translation files in `lang/en/` and `lang/it/` (21 files each)

## Database

MySQL with 12 Eloquent models. Key relationships:
- Partner → hasMany Users, Bookings, PriceLists, Payments
- Tour → hasMany TourDepartures, PartnerPriceLists (soft deletes)
- TourDeparture → belongsTo Tour, Driver; hasMany Bookings
- Booking → belongsTo Partner, TourDeparture; hasMany BookingPassengers; belongsToMany Payments (soft deletes)

Tests use SQLite in-memory (configured in `phpunit.xml`).

## Test Accounts (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@magship.com | password |
| Driver | driver@magship.com | password |
| Partner | paradiso@example.com | password |
| Partner | grand@example.com | password |
