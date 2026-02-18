# MagShip B2B Booking by STARKWEB

> **AI Context Document** - This file contains all project context, specifications, and progress tracking. Read this file first before making any changes.

---

## Quick Reference

| Item | Value |
|------|-------|
| **Framework** | Laravel 12 |
| **Database** | MySQL |
| **UI Template** | Metronic 8.3.2 Bootstrap |
| **Local URL** | http://bookings.test |
| **Template Path** | `/project-spec/template/` |
| **Spec Document** | `/project-spec/ENG Documento progettazione.pdf` |

### Important Links
- **Login Page**: http://bookings.test/login
- **Admin Dashboard**: http://bookings.test/admin
- **Admin Tours**: http://bookings.test/admin/tours
- **Admin Tour Create**: http://bookings.test/admin/tours/create
- **Admin Calendar**: http://bookings.test/admin/calendar
- **Admin Bookings**: http://bookings.test/admin/bookings
- **Admin Booking Detail**: http://bookings.test/admin/bookings/1
- **Admin Partners**: http://bookings.test/admin/partners
- **Admin Partner Create**: http://bookings.test/admin/partners/create
- **Admin Partner Detail**: http://bookings.test/admin/partners/1
- **Admin Accounting**: http://bookings.test/admin/accounting
- **Admin Settings**: http://bookings.test/admin/settings
- **Partner Dashboard**: http://bookings.test/partner
- **Partner New Booking**: http://bookings.test/partner/bookings/create
- **Partner My Bookings**: http://bookings.test/partner/bookings
- **Partner Booking Detail**: http://bookings.test/partner/bookings/1
- **Driver Dashboard**: http://bookings.test/driver

---

## UI Development Guidelines

### Metronic Template Usage

**ALWAYS** use components from the Metronic template folder at `/project-spec/template/`.

#### Key Template Files to Reference:
```
/project-spec/template/
├── index.html                           # Main dashboard structure
├── apps/
│   └── user-management/
│       └── users/
│           └── list.html               # DataTable example
├── widgets/
│   └── tables.html                     # Table widget examples
└── utilities/
    └── modals/                         # Modal examples
```

#### CSS Class Patterns (Metronic Style):

**Tables:**
```html
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Column</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        <tr>
            <td>Data</td>
        </tr>
    </tbody>
</table>
```

**Cards:**
```html
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">Title</div>
        <div class="card-toolbar">Actions</div>
    </div>
    <div class="card-body py-4">
        Content
    </div>
</div>
```

**Forms:**
```html
<input type="text" class="form-control form-control-solid" />
<select class="form-select form-select-solid" data-control="select2">
```

**Badges:**
```html
<span class="badge badge-light-success">Active</span>
<span class="badge badge-light-warning">Pending</span>
<span class="badge badge-light-danger">Cancelled</span>
<span class="badge badge-light-primary">Info</span>
```

**Icons (Keenicons):**
```html
<i class="ki-duotone ki-document fs-2">
    <span class="path1"></span>
    <span class="path2"></span>
</i>
```

**Buttons:**
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-light-primary">Light Primary</button>
<a class="btn btn-sm btn-icon btn-light btn-active-light-primary">
    <i class="ki-duotone ki-dots-square fs-5">...</i>
</a>
```

---

### Table & Pagination Convention

**All tables with pagination MUST use server-side pagination (Laravel). Do NOT use DataTables.**
    
#### Controller Pattern:
```php
$items = Model::query()
    ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
    ->when(request('status'), fn($q, $s) => $q->where('status', $s))
    ->orderBy('name')
    ->paginate(15);
```

#### View - Search (form submission, not JS):
```blade
<form method="GET" action="{{ route('admin.items.index') }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." />
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
</form>
```

#### View - Filters (auto-submit on change):
```blade
<form method="GET" action="{{ route('admin.items.index') }}" class="d-flex gap-3">
    @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif
    <select name="status" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
    </select>
    @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.items.index') }}" class="btn btn-icon btn-light-danger">
            <i class="ki-duotone ki-cross fs-2">...</i>
        </a>
    @endif
</form>
```

#### View - Pagination:
```blade
{{ $items->withQueryString()->links() }}
```

#### Custom Pagination Views:
Located in `resources/views/vendor/pagination/`:
- `bootstrap-5.blade.php` - Full pagination with page numbers (Metronic styled)
- `simple-bootstrap-5.blade.php` - Simple prev/next only

**AppServiceProvider** uses `Paginator::useBootstrapFive()` for consistent styling.

---

## Architecture & Code Standards

### Design Patterns

| Pattern | Usage |
|---------|-------|
| **Action Pattern** | Single-responsibility classes for business operations |
| **Form Requests** | Validation logic separated from controllers |
| **Policies** | Authorization checks |
| **Observers** | Model events (audit logging) |
| **Notifications** | Decoupled email sending |
| **Enums** | Type-safe status constants (PHP 8.1+) |
| **Blade Components** | Reusable UI elements (DRY) |

### Code Standards

1. **Strict Types** - All PHP files must declare `declare(strict_types=1);`
2. **Type Hints** - All function parameters and return types must be typed
3. **DocBlocks** - Every function must have a descriptive comment
4. **Form Requests** - Always use Request classes for validation
5. **Error Handling** - Use try-catch with specific exceptions
6. **No Magic** - Avoid magic methods, use explicit code

### Directory Structure

```
app/
├── Actions/                    # Single-purpose business actions
│   ├── Booking/
│   │   ├── CreateBookingAction.php
│   │   ├── CancelBookingAction.php
│   │   ├── ApproveOverbookingAction.php
│   │   └── RejectOverbookingAction.php
│   ├── Partner/
│   │   ├── CreatePartnerAction.php
│   │   └── UpdatePartnerPriceListAction.php
│   ├── Tour/
│   │   ├── CreateTourAction.php
│   │   └── BulkCreateDeparturesAction.php
│   └── Payment/
│       └── RecordPaymentAction.php
├── Enums/
│   ├── BookingStatus.php
│   ├── PaymentStatus.php
│   ├── UserRole.php
│   ├── PartnerType.php
│   └── PaxType.php
├── Exceptions/
│   ├── BookingException.php
│   ├── OverbookingException.php
│   └── CutoffException.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Partner/
│   │   └── Driver/
│   ├── Requests/
│   │   ├── Admin/
│   │   │   ├── StoreTourRequest.php
│   │   │   ├── StorePartnerRequest.php
│   │   │   └── StoreBookingRequest.php
│   │   └── Partner/
│   │       └── StoreBookingRequest.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
├── Notifications/
│   ├── BookingConfirmedNotification.php
│   ├── OverbookingRequestedNotification.php
│   └── BookingCancelledNotification.php
├── Observers/
│   ├── BookingObserver.php
│   └── PaymentObserver.php
├── Policies/
│   ├── BookingPolicy.php
│   └── PartnerPolicy.php
└── View/
    └── Components/
        ├── Forms/
        └── Ui/
```

### Blade Components (Implemented)

```
resources/views/components/
├── forms/
│   ├── input.blade.php          ✅ Text input with icon, error handling
│   ├── select.blade.php         ✅ Dropdown with Select2 support
│   ├── textarea.blade.php       ✅ Multi-line text input
│   ├── checkbox.blade.php       ✅ Single checkbox with label
│   ├── toggle.blade.php         ✅ Switch toggle for boolean options
│   ├── date-picker.blade.php    ✅ Flatpickr date input
│   └── price-input.blade.php    ✅ Currency input with symbol prefix
├── ui/
│   ├── icon.blade.php           ✅ Keenicons with dynamic path count
│   ├── badge.blade.php          ✅ Status/category badges
│   ├── button.blade.php         ✅ Buttons with icons and variants
│   ├── card.blade.php           ✅ Card with header/toolbar slots
│   ├── modal.blade.php          ✅ Bootstrap modal with sizing
│   └── alert.blade.php          ✅ Notice/alert boxes
├── booking/
│   ├── status-badge.blade.php   ✅ Booking status with enum color
│   ├── pax-badges.blade.php     ✅ Adult/Child/Infant count display
│   └── passenger-row.blade.php  ✅ Editable passenger form row
└── partner/
    └── avatar.blade.php         ✅ Avatar with initials fallback
```

#### Component Usage Examples

```blade
{{-- Form Input with icon --}}
<x-forms.input name="email" label="Email" type="email" icon="sms" required />

{{-- Select dropdown --}}
<x-forms.select name="partner_id" label="Partner" :options="$partners" placeholder="Select partner..." />

{{-- Date picker --}}
<x-forms.date-picker name="date" label="Tour Date" format="d/m/Y" :minDate="today()" />

{{-- Price input --}}
<x-forms.price-input name="price" label="Adult Price" currency="€" :min="0" required />

{{-- Toggle switch --}}
<x-forms.toggle name="active" label="Active" :checked="true" />

{{-- UI Card with toolbar --}}
<x-ui.card title="Bookings">
    <x-slot:toolbar>
        <x-ui.button variant="primary" icon="plus">New Booking</x-ui.button>
    </x-slot:toolbar>
    Content here...
</x-ui.card>

{{-- Booking status badge (uses enum) --}}
<x-booking.status-badge :status="$booking->status" />
<x-booking.status-badge status="confirmed" />

{{-- Passenger counts --}}
<x-booking.pax-badges :adults="2" :children="1" :infants="0" />

{{-- Partner avatar --}}
<x-partner.avatar name="Hotel Paradiso" size="lg" showName />
```

#### Important: View Refactoring Rule

> **When implementing functionality for any page, ALWAYS refactor the mockup view to use Blade components.**
>
> Phase 1 mockup views use inline HTML. When building real functionality (Phase 3+), update the view to use components:
>
> ```blade
> {{-- BEFORE (mockup - inline HTML) --}}
> <i class="ki-duotone ki-plus fs-4 me-2">
>     <span class="path1"></span>
>     <span class="path2"></span>
> </i>
> <span class="badge badge-light-success">Confirmed</span>
>
> {{-- AFTER (functional - use components) --}}
> <x-ui.icon name="plus" class="fs-4 me-2" />
> <x-booking.status-badge :status="$booking->status" />
> ```
>
> This ensures DRY code and consistent styling across the application.

### Example: Action Class

```php
<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Exceptions\CutoffException;
use App\Exceptions\OverbookingException;
use App\Models\Booking;
use App\Models\TourDeparture;
use App\Notifications\BookingConfirmedNotification;
use Illuminate\Support\Facades\DB;

/**
 * Creates a new booking with passengers.
 *
 * Handles availability check, cut-off validation, and overbooking logic.
 */
final class CreateBookingAction
{
    /**
     * Execute the booking creation.
     *
     * @param TourDeparture $departure The tour departure to book
     * @param array<string, mixed> $data Validated booking data
     * @param array<int, array<string, mixed>> $passengers Passenger details
     *
     * @throws CutoffException When booking is past cut-off time
     * @throws OverbookingException When no capacity and overbooking disabled
     *
     * @return Booking The created booking
     */
    public function execute(
        TourDeparture $departure,
        array $data,
        array $passengers
    ): Booking {
        // Implementation...
    }
}
```

### Example: Form Request

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\PaxType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates booking creation request from admin panel.
 */
final class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tour_departure_id' => ['required', 'exists:tour_departures,id'],
            'partner_id' => ['required', 'exists:partners,id'],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.first_name' => ['required', 'string', 'max:100'],
            'passengers.*.last_name' => ['required', 'string', 'max:100'],
            'passengers.*.pax_type' => ['required', Rule::enum(PaxType::class)],
            'passengers.*.pickup_point_id' => ['required', 'exists:pickup_points,id'],
        ];
    }
}
```

### Example: Blade Component Usage

```blade
{{-- Using form components --}}
<x-forms.input
    name="code"
    label="Tour Code"
    placeholder="e.g. POSAMCL"
    required
/>

<x-forms.select
    name="partner_id"
    label="Partner"
    :options="$partners"
    required
/>

{{-- Using UI components --}}
<x-ui.card title="Booking Summary">
    <x-booking.status-badge :status="$booking->status" />
    <x-booking.pax-badges :passengers="$booking->passengers" />
</x-ui.card>

<x-ui.button type="submit" variant="primary" icon="check">
    Save Booking
</x-ui.button>
```

---

## Project Specification Summary

### 0) Project Scope

A B2B management webapp (without online payments) to manage tour ticket bookings between:
- **Admin** (service/tour provider)
- **B2B Partners** (Hotel/B&B/TO) who book tickets for their clients
- **Driver/Captain** who consults assigned shifts and passenger vouchers/manifests

### 1) Tech Stack

| Component | Technology |
|-----------|------------|
| Backend | Laravel 12 (PHP) |
| Database | MySQL |
| UI | Bootstrap (Metronic 8.3.2) |
| PDF | DomPDF |
| Email | SMTP (configurable) |
| Language | English |

### 2) Roles and Permissions (RBAC)

#### 2.1 Admin
- CRUD B2B partners
- CRUD users (admin/driver)
- CRUD tours (tour model, calendar, times, capacity, seasonality)
- Booking management (create/modify/cancel at will)
- Overbooking and approval management
- Accounting: prices (internal), payments, outstanding balances, penalties, refunds
- PDF voucher generation (tour + single booking)
- Access to activity log, reporting, export, configurations

#### 2.2 B2B Partner
- View only bookable tours (dates/times/capacity/pickup)
- Create bookings and manage their own (according to policy)
- View only their own bookings and vouchers
- **Constraint: Price is NOT visible to partner**

#### 2.3 Driver/Captain
- Consult assigned shifts (tour/dates/times/pickup/passengers)
- Download/view tour voucher/manifest
- Consult customer statement (read-only, for assigned tours)
- **No access to prices/accounting/complete partner list**

### 3) Data Entities

#### 3.1 Partner
- Company Name
- Type (Hotel/B&B/Reception vs Tour Operator)
- Email (for notifications)
- Phone (optional)
- Invoicing data (VAT number, SDI/PEC, address)
- Status (active/suspended)
- Dedicated price list per partner

#### 3.2 Tour (Catalog)
- Tour Name
- Tour Code (e.g., POSAMCL)
- Short description
- Seasonality (e.g., April-October)
- Standard Cut-off time: 24h
- Price variants: Mid/High season, Adult/Child/Infant

#### 3.3 Tour Departure (Calendar Instance)
- Date
- Time
- Maximum capacity (seats)
- Status: open/closed/cancelled
- Booked seats / remaining seats

#### 3.4 Pickup Point
- Pickup Name (e.g., "HOTEL TUNNEL", "MAIN ROAD")
- Location
- Predefined time or offset

#### 3.5 Booking
- Partner reference
- Tour departure (date/time)
- Pax quantity by variants (ADU/CHD/INF)
- Booking status
- Passenger data per ticket
- Cancellation/penalty policy
- Internal price (admin only)

#### 3.6 Ticket / Passenger
- First and last name
- Phone
- Pickup point (from list)
- Allergies
- Notes

### 4) Booking Code Format

**Format:** `TOURCODE-NN-YYYYMMDD`

Example: `POSAMCL-05-20250916`
- `POSAMCL` = tour code
- `05` = progressive number of the tour on that date
- `20250916` = date

### 5) Booking Statuses

| Status | Description |
|--------|-------------|
| `CONFIRMED` | Booking confirmed, seats reserved |
| `SUSPENDED_REQUEST` | Overbooking request, awaiting admin approval (2h limit) |
| `REJECTED` | Overbooking request rejected by admin |
| `EXPIRED` | Overbooking request expired (no admin action within 2h) |
| `CANCELLED` | Booking cancelled |
| `COMPLETED` | Tour completed |

### 6) Business Rules

#### 6.1 Overbooking (2-hour suspended request)
- If partner books beyond capacity → status: `SUSPENDED_REQUEST`
- Duration: 2 hours
- Admin can: APPROVE or REJECT
- If time expires → status: `EXPIRED`
- Email notifications at each step

#### 6.2 Cut-off
- Base rule: stop sales 24 hours before departure
- Under 24h: block bookings for partners

#### 6.3 Cancellation Policy
- Free cancellation up to 48h before
- Cancellation within 24h → 100% no-show penalty

### 7) PDF Vouchers

#### 7.1 Tour Voucher (Manifest)
For driver/captain with:
- Tour name + date
- Meeting/pickup list
- Total pax (ADU/CHD/INF)
- Passenger list with: name, pickup, allergies, phone, notes
- Partner reference and booking code

#### 7.2 Single Booking Voucher
For partner/customer with:
- Booking Code
- Tour, date, time
- Partner
- Passenger list
- Pickup, allergies, phone, notes

### 8) Email Notifications

Events that trigger emails (to Admin + Partner):
1. New CONFIRMED booking
2. Booking in SUSPENDED_REQUEST (overbooking)
3. Approve/Reject suspended request
4. Cancellation (with penalty info)
5. Booking modification
6. Tour closure/cancellation
7. Voucher ready

### 9) Accounting

- No online payments
- Admin manually manages paid/outstanding
- Prices visible only to admin
- Track: payments, outstanding balances, penalties, refunds
- CSV export capability

---

## Database Schema

### Proposed Tables

```sql
-- Users & Authentication
users (id, name, email, password_hash, role[admin|partner|driver], partner_id nullable, active, created_at)

-- Partners
partners (id, name, type, email, phone, vat_number, sdi_pec, address, active, created_at)

-- Tours
tours (id, code, name, description, seasonality_start, seasonality_end, cutoff_hours, active, created_at)

-- Tour Departures (Calendar)
tour_departures (id, tour_id, date, time, capacity, status, pricing_season, notes, created_at)

-- Pickup Points
pickup_points (id, name, location, pickup_time, active)

-- Partner Price Lists
partner_price_lists (id, partner_id, tour_id, season[mid|high], pax_type[adult|child|infant], price)

-- Bookings
bookings (id, booking_code, partner_id, tour_departure_id, status, total_amount, penalty_amount, payment_status, created_at, updated_at)

-- Booking Passengers
booking_passengers (id, booking_id, first_name, last_name, phone, pickup_point_id, allergies, notes, pax_type[adult|child|infant])

-- Payments
payments (id, partner_id, amount, paid_at, method, notes, created_by)
payment_booking_map (payment_id, booking_id)

-- Logs
audit_log (id, user_id, action, entity_type, entity_id, meta_json, created_at)
email_log (id, event_type, to_email, booking_id, success, response, created_at)
backup_log (id, ran_at, success, file_path, size, notes)
```

---

## Implementation Progress

### Phase 1: UI Mockups (Current)
- [x] Project setup (Laravel 12)
- [x] Metronic template integration
- [x] Admin layout
- [x] Partner layout
- [x] Driver layout
- [x] Admin Dashboard mockup
- [x] Admin Tours list mockup
- [x] Admin Calendar mockup
- [x] Admin Bookings list mockup
- [x] Admin Partners list mockup
- [x] Partner Dashboard mockup
- [x] Partner Bookings list mockup
- [x] Partner New Booking wizard mockup
- [x] Admin Tour create/edit form
- [x] Admin Partner create/edit form
- [x] Admin Booking detail view
- [x] Admin Partner detail view
- [x] Admin Accounting pages
- [x] Admin Settings page
- [x] Driver Dashboard mockup
- [x] Partner Booking detail view
- [x] Login page

### Phase 2: Database & Models ✅ COMPLETED
- [x] Database migrations (13 migration files)
- [x] Eloquent models with relationships (11 models)
- [x] Model factories (8 factories)
- [x] Database seeder with realistic test data

### Phase 3: Authentication & Authorization ✅ COMPLETED
- [x] User authentication (LoginController with login/logout)
- [x] Role-based access control (RBAC) with UserRole enum
- [x] Middleware for role protection (EnsureUserHasRole, RedirectIfAuthenticated)
- [x] Login/logout functionality with remember me
- [x] Route protection with auth and role middleware
- [x] Layout auth integration (user menu, logout forms)

### Phase 4: Core Features - Admin ✅ COMPLETED
- [x] Tour CRUD (TourController, StoreTourRequest, UpdateTourRequest)
- [x] Tour calendar management (FullCalendar integration)
- [x] Bulk availability creation (BulkCreateDeparturesAction)
- [x] Partner CRUD (PartnerController, CreatePartnerAction)
- [x] Partner price list management (UpdatePartnerPriceListAction)
- [x] Booking management (BookingController with filters/search)
- [x] Overbooking approval workflow (ApproveOverbookingAction, RejectOverbookingAction)
- [x] Server-side pagination for all tables (Tours, Partners, Bookings)

### Phase 5: Core Features - Partner ✅ COMPLETED
- [x] Tour availability viewing
- [x] Booking creation wizard (4-step wizard with AJAX)
- [x] Booking management (view, cancel with policy)
- [x] Cut-off enforcement
- [x] Overbooking flow (SUSPENDED_REQUEST status)

### Phase 6: Core Features - Driver ✅ COMPLETED
- [x] Assigned shifts view (date picker, today/upcoming)
- [x] Tour manifest access (AJAX modal)
- [x] Driver assignment in admin calendar

### Phase 7: PDF Generation ✅ COMPLETED
- [x] Single booking voucher (DomPDF)
- [x] Tour manifest (collection report)
- [x] PDF routes for admin, partner, driver roles

### Phase 8: Email Notifications ✅ COMPLETED
- [x] Email templates (5 Blade templates with base layout)
- [x] SMTP configuration (mail.php with admin_email)
- [x] Notification triggers (integrated in booking actions)
- [x] Email logging (EmailService with EmailLog model)
- [x] Admin email logs view with filters

### Phase 9: Accounting ✅ COMPLETED
- [x] Partner situation view (real data from database)
- [x] Outstanding balances (calculated from bookings/payments)
- [x] Payment recording (RecordPaymentAction with form validation)
- [x] Refunds/bad weather (credit functionality with reasons)
- [x] CSV export (transactions and partner balances)

### Phase 10: System Features ✅ COMPLETED
- [x] Audit log (Auditable trait, AuditObserver, admin view)
- [x] Database backup (artisan command, scheduled daily, admin view)
- [x] Backup logs view with download and manual trigger

### Phase 11: Multi-Language Support (i18n) ✅ COMPLETED
- [x] Create language files (English, Italian)
- [x] Implement language switcher (user preference)
- [x] Translate all UI labels
- [x] Translate email templates
- [x] Translate PDF vouchers
- [x] Per-user language preference (stored in users table)
- [x] SetLocale middleware for automatic language detection

### Phase 12: Additional Features ✅ COMPLETED
- [x] Admin create booking on behalf of partners (5-step wizard)
- [x] Self-service password change (all user roles)
- [x] Reports/Analytics dashboard (statistics, charts, trends)
- [x] Configurable voucher text in settings
- [x] Tour date filter in accounting
- [x] Technical documentation (TECHNICAL.md)

---

## File Structure

```
bookings/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── Partner/
│   │   │   └── Driver/
│   │   └── Middleware/
│   ├── Models/
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── admin.blade.php
│       │   ├── partner.blade.php
│       │   └── driver.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── tours/
│       │   ├── bookings/
│       │   ├── partners/
│       │   ├── calendar.blade.php
│       │   └── accounting/
│       ├── partner/
│       │   ├── dashboard.blade.php
│       │   └── bookings/
│       └── driver/
│           └── dashboard.blade.php
├── routes/
│   └── web.php
├── public/
│   └── assets/          # Metronic assets
├── project-spec/
│   ├── template/        # Metronic source template
│   └── ENG Documento progettazione.pdf
└── PROJECT.md           # This file
```

---

## Routes Reference

### Current Routes (web.php)

```php
// Authentication
Route::get('/login', ...)->name('login');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', ...)->name('dashboard');
    Route::get('/tours', ...)->name('tours.index');
    Route::get('/tours/create', ...)->name('tours.create');
    Route::get('/tours/{id}/edit', ...)->name('tours.edit');
    Route::get('/calendar', ...)->name('calendar');
    Route::get('/bookings', ...)->name('bookings.index');
    Route::get('/bookings/{id}', ...)->name('bookings.show');
    Route::get('/partners', ...)->name('partners.index');
    Route::get('/partners/create', ...)->name('partners.create');
    Route::get('/partners/{id}', ...)->name('partners.show');
    Route::get('/accounting', ...)->name('accounting.index');
    Route::get('/settings', ...)->name('settings');
});

// Partner Routes
Route::prefix('partner')->name('partner.')->group(function () {
    Route::get('/', ...)->name('dashboard');
    Route::get('/bookings', ...)->name('bookings.index');
    Route::get('/bookings/create', ...)->name('bookings.create');
    Route::get('/bookings/{id}', ...)->name('bookings.show');
});

// Driver Routes
Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/', ...)->name('dashboard');
});
```

---

## Development Notes

### Common Keenicons Used
| Icon | Code | Paths |
|------|------|-------|
| Dashboard | `ki-element-11` | 4 |
| Tours/Map | `ki-map` | 3 |
| Calendar | `ki-calendar-8` | 6 |
| Bookings/Document | `ki-document` | 2 |
| Partners/People | `ki-people` | 5 |
| Plus | `ki-plus` | 3 |
| Eye | `ki-eye` | 3 |
| Pencil | `ki-pencil` | 2 |
| Check | `ki-check` | 2 |
| Cross | `ki-cross` | 2 |
| Filter | `ki-filter` | 2 |
| Magnifier | `ki-magnifier` | 2 |
| Dots Menu | `ki-dots-square` | 4 |
| Building | `ki-building` | 3 |
| User | `ki-user` | 2 |
| Notification | `ki-notification-status` | 4 |

### Color Variants
- `primary` - Blue
- `success` - Green
- `warning` - Yellow/Orange
- `danger` - Red
- `info` - Light Blue
- `secondary` - Gray

### Badge Colors for Statuses
```html
<span class="badge badge-light-success">Confirmed</span>
<span class="badge badge-light-warning">Pending</span>
<span class="badge badge-light-danger">Cancelled</span>
<span class="badge badge-light-primary">2 ADU</span>
<span class="badge badge-light-info">1 CHD</span>
<span class="badge badge-light-secondary">Suspended</span>
```

---

## Changelog

### 2025-12-28 (Session 8)
- **Phase 12: Additional Features COMPLETED**
- Gap analysis against PDF specification identified 6 missing features
- Created admin booking creation on behalf of partners:
  - 5-step wizard (Partner → Tour → Date/Pax → Details → Review)
  - Admin/StoreBookingRequest for validation
  - AJAX tour/departure loading
- Implemented self-service password change:
  - ProfileController with password validation
  - Change password view with role-based layout selection
  - Added link to all layouts (admin, partner, driver)
- Created Reports/Analytics dashboard:
  - ReportsController with statistics aggregation
  - Period filter (week, month, quarter, year, all)
  - ApexCharts for 30-day booking trends
  - Revenue by tour, top partners, capacity utilization
- Added configurable voucher text in settings:
  - New Voucher tab in settings page
  - Migration for voucher_header, voucher_notes, voucher_footer settings
  - SettingsController updateVoucher method
- Added tour date filter to accounting:
  - Date type dropdown (Booking Date vs Tour Date)
  - Modified revenue query to filter by tour departure date
- Created comprehensive technical documentation (TECHNICAL.md):
  - System requirements
  - Installation guide
  - SMTP configuration for multiple providers
  - Cron/scheduler setup
  - Database backup procedures
  - Deployment guide with Nginx/Apache configs
  - Troubleshooting guide

### 2025-12-28 (Session 7)
- **Phase 7: PDF Generation COMPLETED**
- Installed barryvdh/laravel-dompdf package
- Created PdfController with:
  - bookingVoucher: Download voucher PDF
  - bookingVoucherStream: Preview voucher in browser
  - tourManifest: Download manifest PDF
  - tourManifestStream: Preview manifest in browser
- Created PDF templates:
  - `pdf/booking-voucher.blade.php`: Professional voucher with passenger list, pickup info
  - `pdf/tour-manifest.blade.php`: Collection report with pickup schedule, allergy alerts
- Added PDF routes for all roles (admin, partner, driver)
- Wired up PDF buttons in:
  - Partner booking show view
  - Partner dashboard (recent & upcoming)
  - Admin booking show view
  - Driver dashboard manifest modal

### 2025-12-28 (Session 6)
- **Phase 5: Core Features - Partner COMPLETED**
- Created Partner BookingController with full CRUD:
  - index: Server-side search & pagination
  - create: 4-step booking wizard (Tour → Date/Pax → Details → Review)
  - store: Uses CreateBookingAction with overbooking detection
  - show: Booking details with cancellation modal
  - cancel: Respects cancellation policy
  - getDepartures: AJAX endpoint for time slot loading
- Created CreateBookingAction for partner booking flow
- Created StoreBookingRequest for validation with cut-off check
- Created Partner DashboardController with real statistics
- Updated all partner views with real data

- **Phase 6: Core Features - Driver COMPLETED**
- Added driver_id to tour_departures table (migration)
- Updated TourDeparture model with driver relationship
- Updated User model with assignedDepartures relationship
- Created Driver DashboardController:
  - Date picker for viewing any date's shifts
  - Today's shifts as cards with pickup summary
  - Upcoming shifts table (next 7 days)
  - AJAX manifest loading
- Created manifest partial view with passenger list
- Added driver assignment dropdown in admin calendar
- Updated seeder to assign driver to upcoming departures

### 2025-12-27 (Session 5)
- **Phase 3: Authentication & Authorization COMPLETED**
- Created LoginController with:
  - Login form display and validation
  - Role-based redirect after login (admin/partner/driver dashboards)
  - Logout with session invalidation
  - Remember me functionality
  - Inactive user blocking
- Created EnsureUserHasRole middleware for RBAC route protection
- Created RedirectIfAuthenticated middleware for guest routes
- Registered middleware aliases in bootstrap/app.php
- Updated routes/web.php with full protection:
  - Guest middleware on login routes
  - Auth + role middleware on admin/partner/driver routes
- Updated login.blade.php to be functional:
  - Form action with CSRF
  - Error display with alert
  - Remember me checkbox
  - Old input preservation
- Updated all layouts with auth integration:
  - Dynamic user initials, name, email display
  - Functional logout form with POST and CSRF
  - Admin: light-primary styling
  - Partner: light-success styling
  - Driver: light-info styling

### 2025-12-27 (Session 4)
- **Architecture & Components Setup**
- Added Architecture & Code Standards section to PROJECT.md
- Created PHP 8.1 Enums with helper methods:
  - `BookingStatus` - Booking lifecycle states with color/label helpers
  - `UserRole` - Admin/Partner/Driver with permission checks
  - `PaxType` - Adult/Child/Infant with shortCode and isChargeable
  - `PartnerType` - Hotel/TourOperator
  - `PaymentStatus` - Unpaid/Partial/Paid/Refunded with colors
  - `Season` - Mid/High with fromMonth() helper
  - `TourDepartureStatus` - Open/Closed/Cancelled
- Created Blade UI Components (DRY reusable elements):
  - Form components: Input, Select, Textarea, Checkbox, Toggle, DatePicker, PriceInput
  - UI components: Icon (with path count mapping), Badge, Button, Card, Modal, Alert
  - Booking components: StatusBadge (enum-integrated), PaxBadges, PassengerRow
  - Partner components: Avatar (with initials fallback)
- All components follow strict types, docblocks, and type hints
- **Phase 2: Database & Models COMPLETED**
- Created 13 database migrations:
  - partners, tours, pickup_points, tour_departures
  - partner_price_lists, bookings, booking_passengers
  - payments, booking_payment (pivot), audit_logs, email_logs, backup_logs
  - Modified users table (added role, partner_id, is_active)
- Created 11 Eloquent models with full relationships:
  - Partner, Tour, PickupPoint, TourDeparture, PartnerPriceList
  - Booking, BookingPassenger, Payment, AuditLog, EmailLog, BackupLog
  - Updated User model with role support
- Models include:
  - Enum casting for type-safe status fields
  - Scopes for common queries (active, forPartner, etc.)
  - Computed attributes (pax_summary, outstanding_balance, etc.)
  - Business logic helpers (isPastCutoff, hasAvailability, etc.)
- Created 8 model factories with fluent state methods
- Created comprehensive DatabaseSeeder with:
  - Admin and driver users
  - 8 realistic pickup points
  - 5 tour products (Amalfi Coast region)
  - 5 partner hotels with user accounts
  - Complete price lists (all partner/tour/season combinations)
  - 3 months of tour departures
  - Sample bookings with passengers
  - Sample payments
- **Note:** Phase 1 mockup views still use inline HTML. Added refactoring rule to PROJECT.md - when implementing functionality, update views to use new Blade components.

### 2025-12-27 (Session 3)
- **Phase 1 UI Mockups COMPLETED**
- Created Admin Accounting page with:
  - Summary cards (Revenue, Payments, Outstanding, Penalties)
  - Partner balances table with payment actions
  - Recent transactions list with tabs
  - Record payment modal
- Created Admin Settings page with tabs:
  - General settings (company, timezone, currency)
  - Booking rules (cut-off, overbooking, cancellation policy)
  - Email & notifications (SMTP config, notification events)
  - Language settings (multi-language configuration)
  - Pickup points management
  - Users & admins management
  - Backup & logs
- Added Accounting and Settings to admin navigation
- Added Phase 11: Multi-Language Support (i18n) to project plan
- Updated routes and PROJECT.md documentation

### 2025-12-27 (Session 2)
- Created Admin Tour create/edit form with Metronic patterns
- Created Admin Partner create/edit form with dedicated price list accordion
- Created Admin Booking detail view with passenger list, accounting, and cancellation modal
- Created Admin Partner detail view with tabs (Bookings, Accounting, Info)
- Created Driver layout and Dashboard with shift cards and manifest modals
- Created Partner Booking detail view with voucher download and cancellation
- Created Login page with demo accounts display
- Updated routes to include login and new views
- Updated PROJECT.md documentation

### 2025-12-27 (Session 1)
- Initial project setup with Laravel 12
- Integrated Metronic 8.3.2 template
- Created admin and partner layouts
- Built mockup pages for:
  - Admin: Dashboard, Tours, Calendar, Bookings, Partners
  - Partner: Dashboard, Bookings list, New Booking wizard
- Fixed DataTable i18n errors (removed Italian language loading)
- Fixed navigation icons
- Fixed calendar layout issues
- Standardized header styles across admin and partner areas
- Created PROJECT.md documentation

---

## AI Instructions

When continuing this project:

1. **Always read this file first** to understand the current state
2. **Follow Metronic patterns** from `/project-spec/template/`
3. **Use English** for all UI text
4. **No Italian text** anywhere in the application
5. **Use Keenicons** (`ki-duotone ki-*`) not Bootstrap icons
6. **DataTables** - initialize without i18n loading
7. **Update this file** when completing tasks or making significant changes
8. **Check the Progress section** to see what's next
9. **Refer to the PDF spec** for detailed business requirements

### Before Making Changes:
```
1. Read PROJECT.md
2. Check /project-spec/template/ for component examples
3. Review existing code patterns in layouts and views
4. Follow established CSS class conventions
```
