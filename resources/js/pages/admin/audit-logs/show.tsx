import { ArrowLeft, Globe, Monitor, User, Hash } from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { formatDateTime } from '@/lib/utils';
import { type AuditLog } from '@/types';

interface AuditLogShowProps { auditLog: AuditLog; }

const actionColors: Record<string, string> = {
    created: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    updated: 'bg-blue-50 text-blue-700 ring-blue-600/20',
    deleted: 'bg-red-50 text-red-700 ring-red-600/20',
    restored: 'bg-violet-50 text-violet-700 ring-violet-600/20',
};

function formatValue(val: unknown): string {
    if (val === null || val === undefined) return '\u2014';
    if (typeof val === 'boolean') return val ? 'Yes' : 'No';
    return String(val);
}

export default function AuditLogShow({ auditLog }: AuditLogShowProps) {
    const changedKeys = auditLog.action === 'updated' && auditLog.new_values
        ? Object.keys(auditLog.new_values)
        : auditLog.action === 'created' && auditLog.new_values
            ? Object.keys(auditLog.new_values)
            : auditLog.action === 'deleted' && auditLog.old_values
                ? Object.keys(auditLog.old_values)
                : [];

    return (
        <AdminLayout pageTitle="Audit Log Detail" breadcrumbs={[{ label: 'System' }, { label: 'Audit Logs', href: '/admin/audit-logs' }, { label: `#${auditLog.id}` }]}>
            <div className="mx-auto max-w-3xl space-y-6">
                {/* Header with action badge */}
                <div className="flex items-center gap-3">
                    <div className={`flex h-10 w-10 items-center justify-center rounded-xl ${
                        auditLog.action === 'created' ? 'bg-emerald-100' :
                        auditLog.action === 'updated' ? 'bg-blue-100' :
                        auditLog.action === 'deleted' ? 'bg-red-100' : 'bg-violet-100'
                    }`}>
                        <Hash className={`h-5 w-5 ${
                            auditLog.action === 'created' ? 'text-emerald-600' :
                            auditLog.action === 'updated' ? 'text-blue-600' :
                            auditLog.action === 'deleted' ? 'text-red-600' : 'text-violet-600'
                        }`} />
                    </div>
                    <div>
                        <div className="flex items-center gap-2">
                            <h2 className="text-lg font-semibold text-slate-900">
                                {auditLog.entity_type.split('\\').pop()} #{auditLog.entity_id}
                            </h2>
                            <Badge className={`ring-1 ring-inset ${actionColors[auditLog.action] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20'}`}>
                                {auditLog.action.charAt(0).toUpperCase() + auditLog.action.slice(1)}
                            </Badge>
                        </div>
                        <p className="text-sm text-slate-500">{formatDateTime(auditLog.created_at)}</p>
                    </div>
                </div>

                {/* Event Information */}
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                        <h3 className="text-sm font-semibold text-slate-900">Event Information</h3>
                    </div>
                    <div className="p-6">
                        <dl className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <dt className="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                    <User className="h-3.5 w-3.5" />
                                    User
                                </dt>
                                <dd className="mt-1.5 text-sm text-slate-900">{auditLog.user?.name ?? 'System'}</dd>
                            </div>
                            <div>
                                <dt className="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                    <Hash className="h-3.5 w-3.5" />
                                    Entity
                                </dt>
                                <dd className="mt-1.5 text-sm text-slate-900">
                                    {auditLog.entity_type.split('\\').pop()} #{auditLog.entity_id}
                                </dd>
                            </div>
                            {auditLog.ip_address && (
                                <div>
                                    <dt className="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                        <Globe className="h-3.5 w-3.5" />
                                        IP Address
                                    </dt>
                                    <dd className="mt-1.5 font-mono text-sm text-slate-900">{auditLog.ip_address}</dd>
                                </div>
                            )}
                            {auditLog.user_agent && (
                                <div className="sm:col-span-2">
                                    <dt className="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                        <Monitor className="h-3.5 w-3.5" />
                                        User Agent
                                    </dt>
                                    <dd className="mt-1.5 truncate text-xs text-slate-500">{auditLog.user_agent}</dd>
                                </div>
                            )}
                        </dl>
                    </div>
                </div>

                {/* Changes Table */}
                {changedKeys.length > 0 && (
                    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                            <h3 className="text-sm font-semibold text-slate-900">
                                Changes
                                <span className="ml-2 inline-flex items-center rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-700">
                                    {changedKeys.length}
                                </span>
                            </h3>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="bg-slate-50/50">
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Field</th>
                                        {auditLog.action === 'updated' && (
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Old Value</th>
                                        )}
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                            {auditLog.action === 'deleted' ? 'Value' : 'New Value'}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {changedKeys.map((key) => (
                                        <tr key={key} className="border-b border-slate-100 last:border-b-0">
                                            <td className="px-4 py-3 text-sm font-medium text-slate-900">{key}</td>
                                            {auditLog.action === 'updated' && (
                                                <td className="px-4 py-3">
                                                    <span className="inline-flex rounded-md bg-red-50 px-2 py-0.5 text-sm text-red-600">
                                                        {formatValue(auditLog.old_values?.[key])}
                                                    </span>
                                                </td>
                                            )}
                                            <td className="px-4 py-3">
                                                <span className="inline-flex rounded-md bg-emerald-50 px-2 py-0.5 text-sm text-emerald-600">
                                                    {auditLog.action === 'deleted'
                                                        ? formatValue(auditLog.old_values?.[key])
                                                        : formatValue(auditLog.new_values?.[key])}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}

                {/* Back Button */}
                <div>
                    <Button variant="outline" asChild className="border-slate-200 text-slate-700 hover:bg-slate-50">
                        <a href="/admin/audit-logs">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Audit Logs
                        </a>
                    </Button>
                </div>
            </div>
        </AdminLayout>
    );
}
