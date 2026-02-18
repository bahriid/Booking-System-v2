<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BackupLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Command to backup the MySQL database.
 */
final class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup
                            {--compress : Compress the backup with gzip}
                            {--keep=7 : Number of days to keep old backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to a SQL file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting database backup...');

        try {
            $filename = $this->generateFilename();
            $filePath = $this->getBackupPath($filename);

            // Ensure backup directory exists
            $this->ensureBackupDirectoryExists();

            // Run mysqldump
            $this->runMysqlDump($filePath);

            // Compress if requested
            if ($this->option('compress')) {
                $filePath = $this->compressBackup($filePath);
                $filename .= '.gz';
            }

            // Get file size
            $fileSize = filesize($filePath);

            // Log successful backup
            BackupLog::logBackup(
                success: true,
                filePath: $filePath,
                fileSize: $fileSize,
                notes: sprintf('Backup completed successfully. Size: %s', $this->formatBytes($fileSize))
            );

            $this->info("Backup created: {$filePath}");
            $this->info("File size: {$this->formatBytes($fileSize)}");

            // Clean old backups
            $this->cleanOldBackups();

            $this->info('Database backup completed successfully!');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error("Backup failed: {$e->getMessage()}");

            // Log failed backup
            BackupLog::logBackup(
                success: false,
                errorMessage: $e->getMessage()
            );

            return self::FAILURE;
        }
    }

    /**
     * Generate a unique filename for the backup.
     */
    private function generateFilename(): string
    {
        $database = config('database.connections.mysql.database');
        $timestamp = date('Y-m-d_His');

        return "{$database}_{$timestamp}.sql";
    }

    /**
     * Get the full path for the backup file.
     */
    private function getBackupPath(string $filename): string
    {
        return storage_path("app/backups/{$filename}");
    }

    /**
     * Ensure the backup directory exists.
     */
    private function ensureBackupDirectoryExists(): void
    {
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
    }

    /**
     * Run mysqldump to create the backup.
     */
    private function runMysqlDump(string $filePath): void
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = [
            'mysqldump',
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            "--password={$password}",
            '--single-transaction',
            '--routines',
            '--triggers',
            $database,
        ];

        $process = new Process($command);
        $process->setTimeout(3600); // 1 hour timeout

        $output = fopen($filePath, 'w');

        $process->run(function ($type, $buffer) use ($output) {
            if ($type === Process::OUT) {
                fwrite($output, $buffer);
            }
        });

        fclose($output);

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("mysqldump failed: " . $process->getErrorOutput());
        }
    }

    /**
     * Compress the backup file with gzip.
     */
    private function compressBackup(string $filePath): string
    {
        $gzFilePath = $filePath . '.gz';

        $process = new Process(['gzip', '-f', $filePath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("gzip compression failed: " . $process->getErrorOutput());
        }

        return $gzFilePath;
    }

    /**
     * Clean old backup files.
     */
    private function cleanOldBackups(): void
    {
        $keepDays = (int) $this->option('keep');
        $backupDir = storage_path('app/backups');
        $cutoffTime = time() - ($keepDays * 86400);

        $files = glob($backupDir . '/*.sql*');
        $deletedCount = 0;

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Cleaned {$deletedCount} old backup(s)");
        }
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
