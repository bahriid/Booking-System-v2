<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

final class BackupLogController extends Controller
{
    /**
     * Display a listing of backup logs.
     */
    public function index(): View
    {
        $backupLogs = BackupLog::query()
            ->orderBy('ran_at', 'desc')
            ->paginate(20);

        // Get some statistics
        $stats = [
            'total' => BackupLog::count(),
            'successful' => BackupLog::successful()->count(),
            'failed' => BackupLog::failed()->count(),
            'last_backup' => BackupLog::successful()->latest('ran_at')->first(),
        ];

        return view('admin.backup-logs.index', compact('backupLogs', 'stats'));
    }

    /**
     * Trigger a manual backup.
     */
    public function runBackup(): RedirectResponse
    {
        try {
            Artisan::call('db:backup', ['--compress' => true]);
            $output = Artisan::output();

            return redirect()
                ->route('admin.backup-logs.index')
                ->with('success', 'Backup started successfully. ' . trim($output));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.backup-logs.index')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download(BackupLog $backupLog): Response|RedirectResponse
    {
        if (!$backupLog->success || !$backupLog->file_path || !file_exists($backupLog->file_path)) {
            return redirect()
                ->route('admin.backup-logs.index')
                ->with('error', 'Backup file not found.');
        }

        return response()->download($backupLog->file_path);
    }
}
