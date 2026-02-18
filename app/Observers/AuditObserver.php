<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Observer to automatically log model changes to audit log.
 */
final class AuditObserver
{
    /**
     * Handle the "created" event.
     */
    public function created(Model $model): void
    {
        $this->logAction($model, 'created', null, $this->getAuditableValues($model));
    }

    /**
     * Handle the "updated" event.
     */
    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $original = $model->getOriginal();

        // Filter out excluded attributes
        $excludedFields = $this->getExcludedFields($model);
        $changes = array_diff_key($changes, array_flip($excludedFields));

        if (empty($changes)) {
            return;
        }

        // Get only the original values for changed fields
        $oldValues = array_intersect_key($original, $changes);

        $this->logAction($model, 'updated', $oldValues, $changes);
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logAction($model, 'deleted', $this->getAuditableValues($model), null);
    }

    /**
     * Handle the "restored" event (for soft deletes).
     */
    public function restored(Model $model): void
    {
        $this->logAction($model, 'restored', null, ['id' => $model->getKey()]);
    }

    /**
     * Log an action to the audit log.
     */
    private function logAction(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::log(
            action: $action,
            entityType: get_class($model),
            entityId: $model->getKey(),
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Get the excluded fields for a model.
     *
     * @return array<string>
     */
    private function getExcludedFields(Model $model): array
    {
        if (method_exists($model, 'getAuditExclude')) {
            return $model->getAuditExclude();
        }

        return ['password', 'remember_token', 'updated_at', 'created_at'];
    }

    /**
     * Get auditable values from a model.
     *
     * @return array<string, mixed>
     */
    private function getAuditableValues(Model $model): array
    {
        $attributes = $model->getAttributes();
        $excludedFields = $this->getExcludedFields($model);

        return array_diff_key($attributes, array_flip($excludedFields));
    }
}
