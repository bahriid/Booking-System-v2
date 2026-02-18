<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AuditLog;
use App\Observers\AuditObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait to enable automatic audit logging for a model.
 *
 * Add this trait to any model to automatically log create, update, and delete events.
 *
 * @mixin Model
 */
trait Auditable
{
    /**
     * Boot the auditable trait.
     */
    public static function bootAuditable(): void
    {
        static::observe(AuditObserver::class);
    }

    /**
     * Get the attributes that should be excluded from audit logs.
     *
     * Override this method in your model to customize.
     *
     * @return array<string>
     */
    public function getAuditExclude(): array
    {
        return ['password', 'remember_token', 'updated_at', 'created_at'];
    }

    /**
     * Get a human-readable name for the model.
     *
     * Override this method in your model to customize.
     */
    public function getAuditName(): string
    {
        return class_basename($this);
    }

    /**
     * Get a display label for the audited record.
     *
     * Override this method in your model to customize.
     */
    public function getAuditLabel(): string
    {
        if (property_exists($this, 'name') || isset($this->name)) {
            return $this->name;
        }

        if (property_exists($this, 'booking_code') || isset($this->booking_code)) {
            return $this->booking_code;
        }

        return "#{$this->getKey()}";
    }

    /**
     * Get the audit logs for this model.
     */
    public function auditLogs()
    {
        return AuditLog::forEntity(static::class, $this->getKey())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
