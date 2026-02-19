<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User model with role-based access control.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property UserRole $role
 * @property int|null $partner_id
 * @property bool $is_active
 * @property \Carbon\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'partner_id',
        'is_active',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime:Y-m-d H:i:s',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the partner associated with this user.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the bookings created by this user.
     */
    public function createdBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'created_by');
    }

    /**
     * Get the payments created by this user.
     */
    public function createdPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    /**
     * Get the audit logs for this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the tour departures assigned to this driver.
     */
    public function assignedDepartures(): HasMany
    {
        return $this->hasMany(TourDeparture::class, 'driver_id');
    }

    /**
     * Scope to filter active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by role.
     */
    public function scopeWithRole($query, UserRole $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if the user is a partner.
     */
    public function isPartner(): bool
    {
        return $this->role === UserRole::PARTNER;
    }

    /**
     * Check if the user is a driver.
     */
    public function isDriver(): bool
    {
        return $this->role === UserRole::DRIVER;
    }

    /**
     * Check if the user can manage bookings.
     */
    public function canManageBookings(): bool
    {
        return $this->role->canManageBookings();
    }

    /**
     * Check if the user can view prices.
     */
    public function canViewPrices(): bool
    {
        return $this->role->canViewPrices();
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            if (strlen($word) > 0) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }

        return $initials ?: '?';
    }
}
