import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

/**
 * Format a date string for display.
 * Accepts "Y-m-d" or "Y-m-d H:i:s" strings from the backend.
 */
export function formatDate(date: string | null | undefined, options?: Intl.DateTimeFormatOptions): string {
    if (!date) return '—';
    // Replace space with T so Date constructor handles "Y-m-d H:i:s" reliably
    const d = new Date(date.replace(' ', 'T'));
    if (isNaN(d.getTime())) return date;
    return d.toLocaleDateString(undefined, options ?? { year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Format a datetime string for display (date + time).
 */
export function formatDateTime(date: string | null | undefined): string {
    if (!date) return '—';
    const d = new Date(date.replace(' ', 'T'));
    if (isNaN(d.getTime())) return date;
    return d.toLocaleString(undefined, {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}

/**
 * Format a time-only display from a datetime string.
 */
export function formatTime(date: string | null | undefined): string {
    if (!date) return '—';
    const d = new Date(date.replace(' ', 'T'));
    if (isNaN(d.getTime())) return date;
    return d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
}
