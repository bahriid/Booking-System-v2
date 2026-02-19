import { type PageProps } from '@inertiajs/core';

// ─── Enums ───────────────────────────────────────────────────────────────────

export type BookingStatus =
    | 'confirmed'
    | 'suspended_request'
    | 'rejected'
    | 'expired'
    | 'cancelled'
    | 'completed';

export type PaymentStatus = 'unpaid' | 'partial' | 'paid' | 'refunded';

export type PaxType = 'adult' | 'child' | 'infant';

export type PartnerType = 'hotel' | 'tour_operator';

export type Season = 'mid' | 'high';

export type TourDepartureStatus = 'open' | 'closed' | 'cancelled';

export type UserRole = 'admin' | 'partner' | 'driver';

// ─── Auth ────────────────────────────────────────────────────────────────────

export interface AuthUser {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    role_label: string;
    initials: string;
    partner_id: number | null;
    locale: string | null;
}

// ─── Notifications ───────────────────────────────────────────────────────────

export interface NotificationBooking {
    id: number;
    booking_code: string;
    partner_name: string | null;
    passenger_count: number;
}

export interface Notification {
    type: 'overbooking' | 'new_booking' | 'cancellation';
    booking: NotificationBooking;
    created_at: string;
}

export interface Notifications {
    items: Notification[];
    count: number;
}

// ─── Flash ───────────────────────────────────────────────────────────────────

export interface Flash {
    success: string | null;
    error: string | null;
    warning: string | null;
}

// ─── Shared Page Props ───────────────────────────────────────────────────────

export interface SharedProps extends PageProps {
    auth: {
        user: AuthUser;
    } | null;
    flash: Flash;
    locale: string;
    translations: Record<string, Record<string, string>>;
    notifications: Notifications | null;
}

// ─── Pagination ──────────────────────────────────────────────────────────────

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
}

// ─── Models ──────────────────────────────────────────────────────────────────

export interface User {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    partner_id: number | null;
    is_active: boolean;
    email_verified_at: string | null;
    locale: string | null;
    created_at: string;
    updated_at: string;
    initials: string;
    partner?: Partner;
}

export interface Partner {
    id: number;
    name: string;
    type: PartnerType;
    email: string;
    phone: string | null;
    vat_number: string | null;
    sdi_pec: string | null;
    address: string | null;
    is_active: boolean;
    notes: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    initials: string;
    outstanding_balance: number;
    users?: User[];
    bookings?: Booking[];
    price_lists?: PartnerPriceList[];
    payments?: Payment[];
    bookings_count?: number;
    users_count?: number;
}

export interface Tour {
    id: number;
    code: string;
    name: string;
    description: string | null;
    seasonality_start: string;
    seasonality_end: string;
    cutoff_hours: number;
    default_capacity: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    seasonality_range: string;
    departures?: TourDeparture[];
    price_lists?: PartnerPriceList[];
    departures_count?: number;
}

export interface TourDeparture {
    id: number;
    tour_id: number;
    driver_id: number | null;
    date: string;
    time: string;
    capacity: number;
    status: TourDepartureStatus;
    season: Season;
    notes: string | null;
    created_at: string;
    updated_at: string;
    booked_seats: number;
    remaining_seats: number;
    tour?: Tour;
    driver?: User;
    bookings?: Booking[];
    bookings_count?: number;
}

export interface Booking {
    id: number;
    booking_code: string;
    partner_id: number;
    tour_departure_id: number;
    created_by: number | null;
    status: BookingStatus;
    total_amount: number;
    penalty_amount: number;
    payment_status: PaymentStatus;
    suspended_until: string | null;
    notes: string | null;
    cancellation_reason: string | null;
    cancelled_at: string | null;
    expired_at: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    adults_count: number;
    children_count: number;
    infants_count: number;
    total_passengers: number;
    pax_summary: string;
    amount_paid: number;
    balance_due: number;
    partner?: Partner;
    tour_departure?: TourDeparture;
    creator?: User;
    passengers?: BookingPassenger[];
    payments?: Payment[];
}

export interface BookingPassenger {
    id: number;
    booking_id: number;
    pickup_point_id: number | null;
    first_name: string;
    last_name: string;
    pax_type: PaxType;
    phone: string | null;
    allergies: string | null;
    notes: string | null;
    price: number;
    created_at: string;
    updated_at: string;
    full_name: string;
    booking?: Booking;
    pickup_point?: PickupPoint;
}

export interface Payment {
    id: number;
    partner_id: number;
    created_by: number | null;
    amount: number;
    method: string | null;
    reference: string | null;
    notes: string | null;
    paid_at: string;
    created_at: string;
    updated_at: string;
    allocated_amount: number;
    unallocated_amount: number;
    partner?: Partner;
    creator?: User;
    bookings?: Booking[];
    pivot?: {
        amount: number;
    };
}

export interface PartnerPriceList {
    id: number;
    partner_id: number;
    tour_id: number;
    season: Season;
    pax_type: PaxType;
    price: number;
    created_at: string;
    updated_at: string;
    partner?: Partner;
    tour?: Tour;
}

export interface PickupPoint {
    id: number;
    name: string;
    location: string | null;
    default_time: string | null;
    sort_order: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface Setting {
    id: number;
    group: string;
    key: string;
    value: string | null;
    type: string;
    created_at: string;
    updated_at: string;
}

export interface EmailLog {
    id: number;
    event_type: string;
    to_email: string;
    to_name: string | null;
    subject: string;
    booking_id: number | null;
    success: boolean;
    error_message: string | null;
    sent_at: string;
    booking?: Booking;
}

export interface AuditLog {
    id: number;
    user_id: number | null;
    action: string;
    entity_type: string;
    entity_id: number | null;
    old_values: Record<string, unknown> | null;
    new_values: Record<string, unknown> | null;
    ip_address: string | null;
    user_agent: string | null;
    created_at: string;
    user?: User;
}

export interface BackupLog {
    id: number;
    success: boolean;
    file_path: string | null;
    file_size: number | null;
    notes: string | null;
    error_message: string | null;
    ran_at: string;
    formatted_file_size: string;
}

// ─── Breadcrumb ──────────────────────────────────────────────────────────────

export interface Breadcrumb {
    label: string;
    href?: string;
}
