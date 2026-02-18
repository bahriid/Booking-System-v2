<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * User role enum.
 *
 * Defines the three main roles in the system with their permissions.
 */
enum UserRole: string
{
    /**
     * Administrator with full access.
     */
    case ADMIN = 'admin';

    /**
     * B2B Partner (Hotel/B&B/Tour Operator).
     */
    case PARTNER = 'partner';

    /**
     * Driver/Captain with limited read-only access.
     */
    case DRIVER = 'driver';

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::PARTNER => 'Partner',
            self::DRIVER => 'Driver',
        };
    }

    /**
     * Get the badge color variant for the role.
     */
    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::PARTNER => 'success',
            self::DRIVER => 'info',
        };
    }

    /**
     * Get the dashboard route name for the role.
     */
    public function dashboardRoute(): string
    {
        return match ($this) {
            self::ADMIN => 'admin.dashboard',
            self::PARTNER => 'partner.dashboard',
            self::DRIVER => 'driver.dashboard',
        };
    }

    /**
     * Check if the role can manage bookings.
     */
    public function canManageBookings(): bool
    {
        return in_array($this, [self::ADMIN, self::PARTNER], true);
    }

    /**
     * Check if the role can view pricing.
     */
    public function canViewPricing(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if the role can manage partners.
     */
    public function canManagePartners(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if the role can approve overbooking.
     */
    public function canApproveOverbooking(): bool
    {
        return $this === self::ADMIN;
    }
}
