# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MagShip B2B Booking — a Laravel 12 web application for managing tour ticket bookings between an admin (tour provider), B2B partners (hotels/tour operators), and drivers/captains. Uses React + Tailwind CSS + shadcn/ui via Inertia.js for the frontend, MySQL for database, and DomPDF for PDF generation.

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

# TypeScript check
npx tsc --noEmit
```

## Architecture

### Frontend Stack
- **React 18** with TypeScript (strict mode) via **Inertia.js v2**
- **Tailwind CSS v4** with `@tailwindcss/vite` plugin
- **shadcn/ui** (new-york style) — 21+ components in `resources/js/components/ui/`
- **Lucide React** icons (replaced Keenicons)
- **Vite 7** with `@vitejs/plugin-react`, `@` alias → `resources/js/`

### Inertia.js Integration
- Controllers return `Inertia::render('page/path', $data)` instead of `view()`
- POST handlers return `RedirectResponse` (Inertia handles redirects natively)
- JSON endpoints (e.g., `getDepartures()`) stay as `JsonResponse`
- `HandleInertiaRequests` middleware shares: `auth.user`, `flash`, `locale`, `translations`, `notifications`
- Page resolution: `import.meta.glob('./pages/**/*.tsx', { eager: true })` in `resources/js/app.tsx`

### Three-Role System
Routes in `routes/web.php` are grouped by role prefix with middleware (`role:admin`, `role:partner`, `role:driver`):
- `/admin/*` — Full management (tours, bookings, partners, accounting, calendar, settings, logs, reports)
- `/partner/*` — Booking CRUD for their own clients, dashboard, voucher download
- `/driver/*` — View assigned shifts and manifests

Middleware in `app/Http/Middleware/`: `EnsureUserHasRole` (RBAC gate, supports comma-separated roles), `RedirectIfAuthenticated` (role-based redirect for guests), `SetLocale` (en/it with user preference → session → browser header fallback), `HandleInertiaRequests` (shared props).

### Action Pattern
All business logic lives in single-responsibility Action classes in `app/Actions/` with a single `execute()` method. Controllers are thin — they validate via Form Requests, delegate to Actions, and return responses.

Actions are organized by domain: `Booking/` (8 actions: Create, Update, Cancel, Approve/Reject Overbooking, BulkMarkAsPaid, Export, ExportPdf), `Partner/` (Create, UpdatePriceList), `Payment/` (RecordPayment), `Tour/` (BulkCreateDepartures, BulkCloseDepartures, CancelDeparture).

### Key Directories

**Backend:**
- `app/Actions/` — Business logic (14 action classes)
- `app/Enums/` — PHP 8.1 backed enums with helper methods (`label()`, `color()`) for BookingStatus, UserRole, PaxType, Season, PaymentStatus, PartnerType, TourDepartureStatus
- `app/Http/Requests/` — Form Request validation classes (`Admin/`, `Partner/`)
- `app/Services/EmailService.php` — Centralized email sending with try-catch, automatic `EmailLog` recording
- `app/Traits/Auditable.php` + `app/Observers/AuditObserver.php` — Automatic audit logging on models
- `app/Models/Setting.php` — Key-value settings stored in DB with group support and caching
- `app/Http/Controllers/PdfController.php` — Shared PDF generation (vouchers + manifests)

**Frontend:**
- `resources/js/pages/` — React page components (35 pages), organized by role: `auth/`, `admin/`, `partner/`, `driver/`, `profile/`
- `resources/js/layouts/` — Role-based layouts: `admin-layout.tsx`, `partner-layout.tsx`, `driver-layout.tsx`, `role-layout.tsx`
- `resources/js/components/` — Shared components: `flash-messages`, `pagination`, `notification-dropdown`, `user-menu`, `language-switcher`, `form-field`
- `resources/js/components/ui/` — shadcn/ui components (button, card, dialog, badge, etc.)
- `resources/js/components/booking/` — Domain components: `status-badge`, `payment-badge`, `pax-badges`, `passenger-row`
- `resources/js/types/index.ts` — TypeScript interfaces for all models, enums, shared props, pagination
- `resources/js/hooks/use-translation.ts` — `useTranslation()` hook: `t('file.key', { replacements })`

### Controllers by Role
- `Admin/` — 13 controllers (Dashboard, Tour, TourDeparture, Booking, Partner, User, PickupPoint, Accounting, Reports, Settings, EmailLog, AuditLog, BackupLog)
- `Partner/` — 2 controllers (Dashboard, Booking)
- `Driver/` — 1 controller (Dashboard)
- Shared — PdfController, ProfileController, LanguageController

### Remaining Blade Templates
Only these Blade files remain (everything else is React):
- `resources/views/app.blade.php` — Inertia root HTML shell
- `resources/views/emails/` — 13 email templates + base layout
- `resources/views/pdf/` — 3 PDF templates (voucher, manifest, export)
- `resources/views/driver/partials/manifest.blade.php` — AJAX HTML partial for manifest modal

## Code Standards

### PHP
- **All PHP files**: `declare(strict_types=1);`
- **All functions**: typed parameters, return types, and DocBlock comments
- **Models**: declared as `final class`, use `@property` PHPDoc annotations
- **Validation**: always in Form Request classes, never in controllers
- **Controllers**: return `Inertia::render()` for pages, `RedirectResponse` for mutations, `JsonResponse` for AJAX

### Frontend
- **TypeScript strict mode** — all components are `.tsx`, types in `resources/js/types/index.ts`
- **Inertia forms**: use `useForm()` hook from `@inertiajs/react` for form state, submission, and error handling
- **Icons**: Lucide React (`lucide-react`) — import specific icons (e.g., `import { Save } from 'lucide-react'`)
- **Styling**: Tailwind CSS utility classes, shadcn/ui components. Use `cn()` from `@/lib/utils` for conditional classes
- **Tables**: server-side pagination with Laravel (`->paginate()`), rendered with `<Pagination>` component
- **Search/filters**: form submission via `router.get()` with `preserveState: true`
- **i18n**: translation files in `lang/en/` and `lang/it/` (21 files each). Use `useTranslation()` hook in React, `__()` helper in PHP

## Database

MySQL with 13 Eloquent models. Key relationships:
- Partner → hasMany Users, Bookings, PriceLists, Payments
- Tour → hasMany TourDepartures, PartnerPriceLists (soft deletes)
- TourDeparture → belongsTo Tour, Driver; hasMany Bookings
- Booking → belongsTo Partner, TourDeparture; hasMany BookingPassengers; belongsToMany Payments (soft deletes)
- Booking code format: `TOURCODE-NN-YYYYMMDD` (e.g., `POSAMCL-05-20250916`)

Tests use SQLite in-memory (configured in `phpunit.xml`, bcrypt rounds reduced to 4).

## Booking Lifecycle

Confirmed → Completed (after tour), or any status → Cancelled. Overbooking flow: capacity exceeded → `SUSPENDED_REQUEST` (2h timer) → Admin approves (→ Confirmed) / rejects (→ Cancelled) / timeout (→ Expired). Scheduled command `php artisan overbooking:expire` handles expiry.

## Test Accounts (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@magship.test | password |
| Driver | driver@magship.test | password |
| Partner | bookings+staff@excelsiorvittoria.com | password |
| Partner | tours+staff@capodimonte.it | password |
