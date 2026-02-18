# MagShip B2B Booking - System Flow & Documentation

> B2B Tour Booking Management System by STARKWEB

**Base URL:** `http://bookings.test`

---

## Table of Contents

1. [Overview](#overview)
2. [User Roles](#user-roles)
3. [Authentication Flow](#authentication-flow)
4. [Admin Panel](#admin-panel)
5. [Partner Portal](#partner-portal)
6. [Driver Portal](#driver-portal)
7. [Booking Lifecycle](#booking-lifecycle)
8. [Overbooking Workflow](#overbooking-workflow)
9. [Payment Flow](#payment-flow)
10. [Edge Cases](#edge-cases)

---

## Overview

MagShip is a B2B platform that connects **tour operators** with **travel partners** (agencies, hotels, resellers). Partners create bookings on behalf of their customers, and admins manage tour operations.

---

## User Roles

| Role | Description | Portal |
|------|-------------|--------|
| **Admin** | Tour operator staff - full system access | `/admin` |
| **Partner** | Travel agencies - create and manage bookings | `/partner` |
| **Driver** | Tour drivers - view assigned shifts and manifests | `/driver` |

---

## Authentication Flow

### Login
| Action | URL | Method |
|--------|-----|--------|
| Login Page | `/login` | GET |
| Submit Login | `/login` | POST |
| Logout | `/logout` | POST |

### Flow
```
User visits /login
    ↓
Enter email + password
    ↓
System checks credentials + role
    ↓
Redirect based on role:
    - Admin → /admin
    - Partner → /partner
    - Driver → /driver
```

### Edge Cases
- **Inactive user**: Cannot login, shown error message
- **Wrong credentials**: Stay on login page with error
- **Session expired**: Redirected to login

---

## Admin Panel

### Dashboard
| Action | URL | Method |
|--------|-----|--------|
| Dashboard | `/admin` | GET |

**Displays:**
- Today's bookings count
- Weekly passengers count
- Pending overbooking requests
- Total outstanding balance
- Pending overbooking requests table (with approve/reject actions)
- Today's departures
- Recent bookings
- Partner outstanding balances

---

### Tour Management

| Action | URL | Method |
|--------|-----|--------|
| List Tours | `/admin/tours` | GET |
| Create Tour Form | `/admin/tours/create` | GET |
| Store Tour | `/admin/tours` | POST |
| View Tour | `/admin/tours/{tour}` | GET |
| Edit Tour Form | `/admin/tours/{tour}/edit` | GET |
| Update Tour | `/admin/tours/{tour}` | PUT |
| Delete Tour | `/admin/tours/{tour}` | DELETE |

**Tour Properties:**
- Name, Code, Description
- Capacity (max passengers)
- Base prices (Adult, Child, Infant)
- Duration
- Active status

**Use Cases:**
1. Admin creates a new tour with pricing
2. Admin updates tour capacity for next season
3. Admin deactivates a discontinued tour

**Edge Cases:**
- Cannot delete tour with existing bookings
- Inactive tour not shown to partners

---

### Departure (Schedule) Management

| Action | URL | Method |
|--------|-----|--------|
| Calendar View | `/admin/calendar` | GET |
| Get Events (AJAX) | `/admin/departures/events` | GET |
| Create Departure | `/admin/departures` | POST |
| Bulk Create | `/admin/departures/bulk` | POST |
| View Departure | `/admin/departures/{departure}` | GET |
| Edit Departure | `/admin/departures/{departure}/edit` | GET |
| Update Departure | `/admin/departures/{departure}` | PUT |
| Delete Departure | `/admin/departures/{departure}` | DELETE |
| Cancel Departure | `/admin/departures/{departure}/cancel` | POST |
| Bulk Close | `/admin/departures/bulk-close` | POST |
| View Manifest | `/admin/departures/{departure}/manifest` | GET |
| Preview Manifest | `/admin/departures/{departure}/manifest/preview` | GET |

**Departure Properties:**
- Tour (linked)
- Date & Time
- Assigned Driver (optional)
- Status (Open, Closed, Cancelled)
- Notes

**Use Cases:**
1. Admin schedules daily departures for a tour
2. Admin bulk-creates departures for entire month
3. Admin assigns driver to departure
4. Admin cancels departure due to weather (notifies all partners)
5. Admin downloads manifest PDF for driver

**Edge Cases:**
- Cancel departure with bookings → All bookings cancelled, partners notified
- Cancel due to bad weather → Different email template, refund process
- Cannot delete departure with confirmed bookings

---

### Booking Management

| Action | URL | Method |
|--------|-----|--------|
| List Bookings | `/admin/bookings` | GET |
| View Booking | `/admin/bookings/{booking}` | GET |
| Edit Booking | `/admin/bookings/{booking}/edit` | GET |
| Update Booking | `/admin/bookings/{booking}` | PUT |
| Delete Booking | `/admin/bookings/{booking}` | DELETE |
| Cancel Booking | `/admin/bookings/{booking}/cancel` | POST |
| Approve Overbooking | `/admin/bookings/{booking}/approve` | POST |
| Reject Overbooking | `/admin/bookings/{booking}/reject` | POST |
| Download Voucher | `/admin/bookings/{booking}/voucher` | GET |
| Preview Voucher | `/admin/bookings/{booking}/voucher/preview` | GET |

**Booking Properties:**
- Booking Code (auto-generated)
- Partner (who made the booking)
- Tour Departure (date/time)
- Passengers (with details)
- Total Amount
- Status (Confirmed, Suspended Request, Cancelled, Completed, Expired)
- Payment Status (Unpaid, Partial, Paid)

**Filters:**
- By status
- By partner
- By tour
- Search by booking code

---

### Partner Management

| Action | URL | Method |
|--------|-----|--------|
| List Partners | `/admin/partners` | GET |
| Create Partner Form | `/admin/partners/create` | GET |
| Store Partner | `/admin/partners` | POST |
| View Partner | `/admin/partners/{partner}` | GET |
| Edit Partner Form | `/admin/partners/{partner}/edit` | GET |
| Update Partner | `/admin/partners/{partner}` | PUT |
| Delete Partner | `/admin/partners/{partner}` | DELETE |
| Update Partner Prices | `/admin/partners/{partner}/prices` | PUT |

**Partner Properties:**
- Name, Email, Phone
- Contact Person
- Commission Rate
- Custom Pricing (per tour, optional)
- Associated User Account
- Active Status

**Use Cases:**
1. Admin creates partner with user account for portal access
2. Admin sets custom pricing for VIP partner
3. Admin views partner's booking history and outstanding balance

**Edge Cases:**
- Cannot delete partner with existing bookings
- Deactivating partner disables their login

---

### User Management

| Action | URL | Method |
|--------|-----|--------|
| List Users | `/admin/users` | GET |
| Create User Form | `/admin/users/create` | GET |
| Store User | `/admin/users` | POST |
| Edit User Form | `/admin/users/{user}/edit` | GET |
| Update User | `/admin/users/{user}` | PUT |
| Toggle Active | `/admin/users/{user}/toggle-active` | PATCH |
| Reset Password | `/admin/users/{user}/reset-password` | PATCH |

**User Types:**
- Admin users
- Driver users

*(Partner users are created via Partner management)*

---

### Pickup Points

| Action | URL | Method |
|--------|-----|--------|
| List Pickup Points | `/admin/pickup-points` | GET |
| Create Form | `/admin/pickup-points/create` | GET |
| Store | `/admin/pickup-points` | POST |
| Edit Form | `/admin/pickup-points/{pickup_point}/edit` | GET |
| Update | `/admin/pickup-points/{pickup_point}` | PUT |
| Delete | `/admin/pickup-points/{pickup_point}` | DELETE |
| Toggle Active | `/admin/pickup-points/{pickup_point}/toggle-active` | PATCH |

**Pickup Point Properties:**
- Name (Hotel name, location)
- Location/Address
- Default Pickup Time
- Active Status

---

### Accounting

| Action | URL | Method |
|--------|-----|--------|
| Accounting Overview | `/admin/accounting` | GET |
| Record Payment | `/admin/accounting/payment` | POST |
| Record Credit | `/admin/accounting/credit` | POST |
| Bulk Mark as Paid | `/admin/accounting/bulk-mark-paid` | POST |
| Export Transactions | `/admin/accounting/export` | GET |
| Export Balances | `/admin/accounting/export-balances` | GET |

**Features:**
- View outstanding balances by partner
- Record partial or full payments
- Issue credits/refunds
- Bulk mark bookings as paid
- Export reports

---

### Logs & Audit

| Action | URL | Method |
|--------|-----|--------|
| Audit Logs | `/admin/audit-logs` | GET |
| View Audit Entry | `/admin/audit-logs/{auditLog}` | GET |
| Email Logs | `/admin/email-logs` | GET |
| View Email | `/admin/email-logs/{emailLog}` | GET |
| Backup Logs | `/admin/backup-logs` | GET |
| Run Backup | `/admin/backup-logs/run` | POST |
| Download Backup | `/admin/backup-logs/{backupLog}/download` | GET |

---

### Settings

| Action | URL | Method |
|--------|-----|--------|
| Settings Page | `/admin/settings` | GET |
| Update General | `/admin/settings/general` | PUT |
| Update Booking | `/admin/settings/booking` | PUT |
| Update Email | `/admin/settings/email` | PUT |
| Update Language | `/admin/settings/language` | PUT |
| Update Notifications | `/admin/settings/notifications` | PUT |
| Send Test Email | `/admin/settings/test-email` | POST |
| Create Backup | `/admin/settings/backup` | POST |

**Settings Tabs:**
- General (Company info, timezone, currency)
- Booking (Cutoff hours, overbooking expiry, cancellation policy)
- Email (SMTP settings)
- Notifications (Which events trigger emails)
- Pickup Points
- Users
- Backup & Logs

---

## Partner Portal

### Dashboard
| Action | URL | Method |
|--------|-----|--------|
| Dashboard | `/partner` | GET |

**Displays:**
- Bookings this month
- Passengers this month
- Pending overbooking requests
- Quick actions (New Booking, View All)
- Recent bookings
- Upcoming tours (next 7 days)

---

### Booking Management

| Action | URL | Method |
|--------|-----|--------|
| List Bookings | `/partner/bookings` | GET |
| Create Booking Form | `/partner/bookings/create` | GET |
| Store Booking | `/partner/bookings` | POST |
| View Booking | `/partner/bookings/{booking}` | GET |
| Edit Booking | `/partner/bookings/{booking}/edit` | GET |
| Update Booking | `/partner/bookings/{booking}` | PUT |
| Cancel Booking | `/partner/bookings/{booking}/cancel` | POST |
| Download Voucher | `/partner/bookings/{booking}/voucher` | GET |
| Preview Voucher | `/partner/bookings/{booking}/voucher/preview` | GET |

### Get Available Departures
| Action | URL | Method |
|--------|-----|--------|
| Tour Departures | `/partner/tours/{tour}/departures` | GET |

**Use Cases:**
1. Partner selects tour → sees available departures
2. Partner adds passengers with details (name, type, pickup point, allergies)
3. Partner submits booking
4. If confirmed → downloads voucher for customer
5. If overbooking → waits for admin approval

---

## Driver Portal

### Dashboard
| Action | URL | Method |
|--------|-----|--------|
| Dashboard | `/driver` | GET |

**Features:**
- Date picker to view different days
- Today's assigned shifts with:
  - Tour name and time
  - Passenger count by type
  - Pickup point summary
  - View Manifest button
- Upcoming shifts (next 7 days)

---

### Manifest

| Action | URL | Method |
|--------|-----|--------|
| View Manifest (AJAX) | `/driver/departures/{departure}/manifest` | GET |
| Download PDF | `/driver/departures/{departure}/manifest/pdf` | GET |
| Preview PDF | `/driver/departures/{departure}/manifest/preview` | GET |

**Manifest Contains:**
- Tour info, date, time
- Total passenger count
- Passengers with allergies (highlighted)
- Pickup schedule (time, location, count)
- Full passenger list with:
  - Name
  - Type (Adult/Child/Infant)
  - Pickup point
  - Phone
  - Partner & Booking code
  - Notes/Allergies

---

## Booking Lifecycle

```
┌─────────────────────────────────────────────────────────────────┐
│                      BOOKING LIFECYCLE                          │
└─────────────────────────────────────────────────────────────────┘

Partner creates booking
         │
         ▼
┌─────────────────┐
│ Check Capacity  │
└────────┬────────┘
         │
         ├─────── Capacity Available ─────► CONFIRMED
         │                                      │
         │                                      ▼
         │                               Tour completed?
         │                                      │
         │                          Yes ◄───────┴───────► No
         │                           │                     │
         │                           ▼                     │
         │                      COMPLETED              (stays confirmed)
         │
         └─────── No Capacity ─────► SUSPENDED_REQUEST
                                           │
                                           ▼
                                    ┌──────────────┐
                                    │ Admin Action │
                                    └──────┬───────┘
                                           │
                      ┌────────────────────┼────────────────────┐
                      │                    │                    │
                      ▼                    ▼                    ▼
                  Approved            Rejected              Timeout
                      │                    │                    │
                      ▼                    ▼                    ▼
                 CONFIRMED             CANCELLED             EXPIRED


                         ┌─────────────────┐
                         │ Any Status Can  │
                         │   Be Cancelled  │
                         └────────┬────────┘
                                  │
                                  ▼
                             CANCELLED
```

---

## Overbooking Workflow

### When It Happens
- Partner creates booking for a departure that is already at full capacity

### Process
1. Booking created with status `SUSPENDED_REQUEST`
2. `suspended_until` timestamp set (default: 2 hours from creation)
3. Admin notified via email
4. Partner notified that request is pending

### Admin Actions

**Approve:**
- Changes status to `CONFIRMED`
- Effectively increases capacity for this departure
- Partner receives confirmation email

**Reject:**
- Changes status to `CANCELLED`
- Admin can provide rejection reason
- Partner receives rejection email

### Expiry
- Scheduled command runs: `php artisan bookings:expire-overbooking`
- Checks for `SUSPENDED_REQUEST` bookings past `suspended_until`
- Changes status to `EXPIRED`
- Partner receives expiry email

### Edge Cases
- **Multiple overbooking requests**: Each handled independently
- **Admin approves after expiry**: Not possible, already expired
- **Partner cancels pending request**: Allowed, status becomes `CANCELLED`

---

## Payment Flow

```
Booking Created (total_amount set)
         │
         ▼
Payment Status: UNPAID
         │
         ▼
┌─────────────────────────────┐
│  Admin Records Payment(s)   │
│  via Accounting page        │
└──────────────┬──────────────┘
               │
               ▼
         Sum of payments
               │
    ┌──────────┴──────────┐
    │                     │
    ▼                     ▼
< total_amount       = total_amount
    │                     │
    ▼                     ▼
 PARTIAL                PAID
```

### Payment Recording
- Admin goes to `/admin/accounting`
- Selects partner → sees outstanding bookings
- Records payment amount
- Can be partial or full
- Multiple payments allowed per booking

### Credits
- Admin can issue credits (refunds)
- Applied to partner's balance
- Used for cancellation refunds

---

## Edge Cases

### Booking Edge Cases

| Scenario | Behavior |
|----------|----------|
| Partner books same passengers twice | Allowed (no duplicate check) |
| Partner edits confirmed booking | Modification email sent, may affect pricing |
| Partner cancels within free period | Full refund eligible |
| Partner cancels after cutoff | Penalty may apply |
| Booking for past departure | Not allowed (validation) |
| Booking for closed departure | Not allowed |
| Departure cancelled by admin | All bookings cancelled, emails sent |

### Capacity Edge Cases

| Scenario | Behavior |
|----------|----------|
| Exactly at capacity | Next booking becomes overbooking request |
| Approved overbooking + regular booking | Both confirmed, over capacity |
| Cancel confirmed booking | Capacity freed, pending requests NOT auto-approved |

### Payment Edge Cases

| Scenario | Behavior |
|----------|----------|
| Overpayment | Credit balance for partner |
| Payment for cancelled booking | Should be refunded as credit |
| Multiple partial payments | All summed for payment status |

### User Edge Cases

| Scenario | Behavior |
|----------|----------|
| Admin deactivates partner | Partner cannot login, bookings remain |
| Driver unassigned from departure | Still shows in calendar, no driver manifest |
| Multiple admins editing same booking | Last save wins |

### Email Edge Cases

| Scenario | Behavior |
|----------|----------|
| Email send fails | Logged in email_logs, booking still processed |
| Partner has no email | Skip email notification |
| Departure cancelled (bad weather) | Different email template with refund info |

---

## Language Switching

| Action | URL | Method |
|--------|-----|--------|
| Switch Language | `/language/{locale}` | GET |

Supported locales: `en`, `it`

---

## Quick Reference

### Status Colors

| Status | Color | Badge Class |
|--------|-------|-------------|
| Confirmed | Green | `badge-light-success` |
| Suspended Request | Warning | `badge-light-warning` |
| Cancelled | Red | `badge-light-danger` |
| Completed | Blue | `badge-light-info` |
| Expired | Gray | `badge-light-secondary` |

### Payment Status Colors

| Status | Color | Badge Class |
|--------|-------|-------------|
| Paid | Green | `badge-light-success` |
| Partial | Warning | `badge-light-warning` |
| Unpaid | Red | `badge-light-danger` |

---

## File Downloads

| Document | Who Can Access | URL Pattern |
|----------|----------------|-------------|
| Booking Voucher | Admin, Partner | `/*/bookings/{booking}/voucher` |
| Tour Manifest | Admin, Driver | `/*/departures/{departure}/manifest` |
| Backup File | Admin | `/admin/backup-logs/{backup}/download` |
| Accounting Export | Admin | `/admin/accounting/export` |
