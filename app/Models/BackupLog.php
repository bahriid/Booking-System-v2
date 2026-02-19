<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * BackupLog model for tracking database backups.
 *
 * @property int $id
 * @property bool $success
 * @property string|null $file_path
 * @property int|null $file_size
 * @property string|null $notes
 * @property string|null $error_message
 * @property \Carbon\Carbon $ran_at
 */
final class BackupLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'success',
        'file_path',
        'file_size',
        'notes',
        'error_message',
        'ran_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'file_size' => 'integer',
            'ran_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * Scope to filter successful backups.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope to filter failed backups.
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Get the file size formatted for display.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->file_size;
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Log a backup operation.
     */
    public static function logBackup(
        bool $success,
        ?string $filePath = null,
        ?int $fileSize = null,
        ?string $notes = null,
        ?string $errorMessage = null
    ): self {
        return self::create([
            'success' => $success,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'notes' => $notes,
            'error_message' => $errorMessage,
            'ran_at' => now(),
        ]);
    }
}
