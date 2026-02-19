import { router } from '@inertiajs/react';
import { Database, Play, Download, CheckCircle, XCircle, Clock, HardDrive } from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Pagination } from '@/components/pagination';
import { formatDate, formatDateTime } from '@/lib/utils';
import { type PaginatedData, type BackupLog } from '@/types';

interface BackupLogIndexProps {
    backupLogs: PaginatedData<BackupLog>;
    stats: {
        total: number;
        successful: number;
        failed: number;
        last_backup: BackupLog | null;
    };
}

export default function BackupLogIndex({ backupLogs, stats }: BackupLogIndexProps) {
    function runBackup() {
        if (confirm('Are you sure you want to run a backup now?')) {
            router.post('/admin/backup-logs/run');
        }
    }

    return (
        <AdminLayout
            pageTitle="Backup Logs"
            breadcrumbs={[{ label: 'System' }, { label: 'Backup Logs' }]}
            toolbarActions={
                <Button onClick={runBackup}>
                    <Play className="mr-2 h-4 w-4" />
                    Run Backup Now
                </Button>
            }
        >
            {/* Stats Cards */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100">
                            <Database className="h-6 w-6 text-blue-600" />
                        </div>
                        <div>
                            <p className="text-sm text-slate-500">Total Backups</p>
                            <p className="text-2xl font-bold text-slate-900">{stats.total}</p>
                        </div>
                    </div>
                </div>
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100">
                            <CheckCircle className="h-6 w-6 text-emerald-600" />
                        </div>
                        <div>
                            <p className="text-sm text-slate-500">Successful</p>
                            <p className="text-2xl font-bold text-slate-900">{stats.successful}</p>
                        </div>
                    </div>
                </div>
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100">
                            <XCircle className="h-6 w-6 text-red-600" />
                        </div>
                        <div>
                            <p className="text-sm text-slate-500">Failed</p>
                            <p className="text-2xl font-bold text-slate-900">{stats.failed}</p>
                        </div>
                    </div>
                </div>
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100">
                            <Clock className="h-6 w-6 text-amber-600" />
                        </div>
                        <div>
                            <p className="text-sm text-slate-500">Last Backup</p>
                            <p className="text-sm font-semibold text-slate-900">
                                {stats.last_backup ? formatDate(stats.last_backup.ran_at) : 'Never'}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Table */}
            <div className="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                    <h3 className="text-sm font-semibold text-slate-900">Backup History</h3>
                </div>
                {backupLogs.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-16">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                            <HardDrive className="h-6 w-6 text-slate-400" />
                        </div>
                        <p className="mt-3 text-sm font-medium text-slate-900">No backup logs yet</p>
                        <p className="mt-1 text-sm text-slate-500">Run your first backup to get started.</p>
                    </div>
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="bg-slate-50">
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Date</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">File Size</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Notes</th>
                                        <th className="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {backupLogs.data.map((log) => (
                                        <tr key={log.id} className="border-b border-slate-100 transition-colors hover:bg-slate-50/50">
                                            <td className="whitespace-nowrap px-4 py-3.5 text-sm text-slate-600">
                                                {formatDateTime(log.ran_at)}
                                            </td>
                                            <td className="px-4 py-3.5">
                                                {log.success ? (
                                                    <Badge className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 hover:bg-emerald-50">
                                                        Success
                                                    </Badge>
                                                ) : (
                                                    <Badge className="bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-50">
                                                        Failed
                                                    </Badge>
                                                )}
                                            </td>
                                            <td className="px-4 py-3.5 text-sm text-slate-600">
                                                {log.formatted_file_size ?? <span className="text-slate-400">&mdash;</span>}
                                            </td>
                                            <td className="max-w-xs truncate px-4 py-3.5 text-sm text-slate-600">
                                                {log.error_message ? (
                                                    <span className="text-red-600">{log.error_message}</span>
                                                ) : (
                                                    log.notes ?? <span className="text-slate-400">&mdash;</span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3.5 text-right">
                                                {log.success && log.file_path && (
                                                    <Button variant="ghost" size="sm" asChild className="text-slate-500 hover:text-slate-700">
                                                        <a href={`/admin/backup-logs/${log.id}/download`}>
                                                            <Download className="h-4 w-4" />
                                                        </a>
                                                    </Button>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <div className="border-t border-slate-100 px-4 py-3">
                            <Pagination links={backupLogs.links} from={backupLogs.from} to={backupLogs.to} total={backupLogs.total} />
                        </div>
                    </>
                )}
            </div>
        </AdminLayout>
    );
}
