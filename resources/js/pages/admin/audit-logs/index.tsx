import { router } from '@inertiajs/react';
import { type FormEvent } from 'react';
import { Search, X, Eye, Filter, ClipboardList } from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Pagination } from '@/components/pagination';
import { formatDateTime } from '@/lib/utils';
import { type PaginatedData, type AuditLog } from '@/types';

interface AuditLogIndexProps {
    auditLogs: PaginatedData<AuditLog>;
    entityTypes: Array<{ value: string; label: string }>;
    actions: Record<string, string>;
    filters: {
        action?: string;
        entity_type?: string;
        user_id?: string;
        date_from?: string;
        date_to?: string;
    };
}

const actionColors: Record<string, string> = {
    created: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    updated: 'bg-blue-50 text-blue-700 ring-blue-600/20',
    deleted: 'bg-red-50 text-red-700 ring-red-600/20',
    restored: 'bg-violet-50 text-violet-700 ring-violet-600/20',
};

function changesCount(log: AuditLog): number {
    if (log.action === 'updated' && log.old_values && log.new_values) {
        return Object.keys(log.new_values).length;
    }
    if (log.action === 'created' && log.new_values) {
        return Object.keys(log.new_values).length;
    }
    if (log.action === 'deleted' && log.old_values) {
        return Object.keys(log.old_values).length;
    }
    return 0;
}

function changesPreview(log: AuditLog): string {
    if (log.action === 'updated' && log.new_values) {
        const keys = Object.keys(log.new_values);
        return keys.slice(0, 3).join(', ') + (keys.length > 3 ? ` +${keys.length - 3} more` : '');
    }
    return '';
}

export default function AuditLogIndex({ auditLogs, entityTypes, actions, filters }: AuditLogIndexProps) {
    function handleFilter(e: FormEvent<HTMLFormElement>) {
        e.preventDefault();
        const fd = new FormData(e.currentTarget);
        const params: Record<string, string> = {};
        fd.forEach((v, k) => { if (v) params[k] = v as string; });
        router.get('/admin/audit-logs', params, { preserveState: true });
    }

    function clearFilters() {
        router.get('/admin/audit-logs');
    }

    const hasFilters = Object.values(filters).some(Boolean);

    return (
        <AdminLayout pageTitle="Audit Logs" breadcrumbs={[{ label: 'System' }, { label: 'Audit Logs' }]}>
            {/* Filter Section */}
            <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
                    <div className="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <Filter className="h-4 w-4 text-slate-400" />
                        Filters
                    </div>
                    <div className="h-6 w-px bg-slate-200" />
                    <select
                        name="action"
                        defaultValue={filters.action ?? ''}
                        className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                    >
                        <option value="">All Actions</option>
                        {Object.entries(actions).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                    </select>
                    <select
                        name="entity_type"
                        defaultValue={filters.entity_type ?? ''}
                        className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                    >
                        <option value="">All Entities</option>
                        {entityTypes.map((et) => <option key={et.value} value={et.value}>{et.label}</option>)}
                    </select>
                    <Input name="date_from" type="date" defaultValue={filters.date_from ?? ''} className="h-9 w-36 border-slate-200" />
                    <Input name="date_to" type="date" defaultValue={filters.date_to ?? ''} className="h-9 w-36 border-slate-200" />
                    <Button type="submit" size="sm">
                        <Search className="mr-1.5 h-3.5 w-3.5" />
                        Search
                    </Button>
                    {hasFilters && (
                        <Button type="button" variant="ghost" size="sm" onClick={clearFilters} className="text-slate-500 hover:text-slate-700">
                            <X className="mr-1 h-3.5 w-3.5" />
                            Clear
                        </Button>
                    )}
                </form>
            </div>

            {/* Table */}
            <div className="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                {auditLogs.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-16">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                            <ClipboardList className="h-6 w-6 text-slate-400" />
                        </div>
                        <p className="mt-3 text-sm font-medium text-slate-900">No audit logs found</p>
                        <p className="mt-1 text-sm text-slate-500">Try adjusting your search or filter criteria.</p>
                    </div>
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="bg-slate-50">
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Date</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">User</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Action</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Entity</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Changes</th>
                                        <th className="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {auditLogs.data.map((log) => (
                                        <tr key={log.id} className="border-b border-slate-100 transition-colors hover:bg-slate-50/50">
                                            <td className="whitespace-nowrap px-4 py-3.5 text-sm text-slate-600">
                                                {formatDateTime(log.created_at)}
                                            </td>
                                            <td className="px-4 py-3.5 text-sm font-medium text-slate-900">
                                                {log.user?.name ?? <span className="text-slate-400">System</span>}
                                            </td>
                                            <td className="px-4 py-3.5">
                                                <Badge className={`ring-1 ring-inset ${actionColors[log.action] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20'}`}>
                                                    {actions[log.action] ?? log.action}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-3.5">
                                                <span className="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                                    {log.entity_type.split('\\').pop()}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3.5">
                                                {changesCount(log) > 0 && (
                                                    <div>
                                                        <span className="text-xs font-medium text-slate-700">
                                                            {changesCount(log)} field{changesCount(log) !== 1 ? 's' : ''}
                                                        </span>
                                                        {changesPreview(log) && (
                                                            <p className="mt-0.5 text-xs text-slate-400">{changesPreview(log)}</p>
                                                        )}
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-4 py-3.5 text-right">
                                                <Button variant="ghost" size="sm" asChild className="text-slate-500 hover:text-slate-700">
                                                    <a href={`/admin/audit-logs/${log.id}`}>
                                                        <Eye className="h-4 w-4" />
                                                    </a>
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <div className="border-t border-slate-100 px-4 py-3">
                            <Pagination links={auditLogs.links} from={auditLogs.from} to={auditLogs.to} total={auditLogs.total} />
                        </div>
                    </>
                )}
            </div>
        </AdminLayout>
    );
}
