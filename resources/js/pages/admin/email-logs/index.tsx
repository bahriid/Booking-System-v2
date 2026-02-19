import { router } from '@inertiajs/react';
import { type FormEvent } from 'react';
import { Mail, Search, X, Eye, Filter } from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Pagination } from '@/components/pagination';
import { formatDateTime } from '@/lib/utils';
import { type PaginatedData, type EmailLog } from '@/types';

interface EmailLogIndexProps {
    emailLogs: PaginatedData<EmailLog>;
    eventTypes: Record<string, string>;
    filters: {
        event_type?: string;
        status?: string;
        booking_code?: string;
        email?: string;
        date_from?: string;
        date_to?: string;
    };
}

const eventTypeColors: Record<string, string> = {
    booking_confirmed: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    overbooking_requested: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    overbooking_approved: 'bg-blue-50 text-blue-700 ring-blue-600/20',
    overbooking_rejected: 'bg-red-50 text-red-700 ring-red-600/20',
    booking_cancelled: 'bg-slate-50 text-slate-700 ring-slate-600/20',
};

export default function EmailLogIndex({ emailLogs, eventTypes, filters }: EmailLogIndexProps) {
    function handleFilter(e: FormEvent<HTMLFormElement>) {
        e.preventDefault();
        const fd = new FormData(e.currentTarget);
        const params: Record<string, string> = {};
        fd.forEach((v, k) => { if (v) params[k] = v as string; });
        router.get('/admin/email-logs', params, { preserveState: true });
    }

    function clearFilters() {
        router.get('/admin/email-logs');
    }

    const hasFilters = Object.values(filters).some(Boolean);

    return (
        <AdminLayout pageTitle="Email Logs" breadcrumbs={[{ label: 'System' }, { label: 'Email Logs' }]}>
            {/* Filter Section */}
            <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
                    <div className="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <Filter className="h-4 w-4 text-slate-400" />
                        Filters
                    </div>
                    <div className="h-6 w-px bg-slate-200" />
                    <select
                        name="event_type"
                        defaultValue={filters.event_type ?? ''}
                        className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                    >
                        <option value="">All Types</option>
                        {Object.entries(eventTypes).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                    </select>
                    <select
                        name="status"
                        defaultValue={filters.status ?? ''}
                        className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                    >
                        <option value="">All Status</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                    </select>
                    <Input name="booking_code" placeholder="Booking code" defaultValue={filters.booking_code ?? ''} className="h-9 w-40 border-slate-200" />
                    <Input name="email" placeholder="Email" defaultValue={filters.email ?? ''} className="h-9 w-48 border-slate-200" />
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
                {emailLogs.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-16">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                            <Mail className="h-6 w-6 text-slate-400" />
                        </div>
                        <p className="mt-3 text-sm font-medium text-slate-900">No email logs found</p>
                        <p className="mt-1 text-sm text-slate-500">Try adjusting your search or filter criteria.</p>
                    </div>
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="bg-slate-50">
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Sent At</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Recipient</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Subject</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Type</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Booking</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                                        <th className="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {emailLogs.data.map((log) => (
                                        <tr key={log.id} className="border-b border-slate-100 transition-colors hover:bg-slate-50/50">
                                            <td className="whitespace-nowrap px-4 py-3.5 text-sm text-slate-600">
                                                {formatDateTime(log.sent_at)}
                                            </td>
                                            <td className="px-4 py-3.5 text-sm text-slate-900">{log.to_email}</td>
                                            <td className="max-w-48 truncate px-4 py-3.5 text-sm text-slate-600">{log.subject}</td>
                                            <td className="px-4 py-3.5">
                                                <Badge className={`ring-1 ring-inset ${eventTypeColors[log.event_type] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20'}`}>
                                                    {eventTypes[log.event_type] ?? log.event_type}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-3.5">
                                                {log.booking ? (
                                                    <a href={`/admin/bookings/${log.booking.id}`} className="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">
                                                        {log.booking.booking_code}
                                                    </a>
                                                ) : (
                                                    <span className="text-sm text-slate-400">&mdash;</span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3.5">
                                                {log.success ? (
                                                    <Badge className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 hover:bg-emerald-50">
                                                        Sent
                                                    </Badge>
                                                ) : (
                                                    <Badge className="bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-50">
                                                        Failed
                                                    </Badge>
                                                )}
                                            </td>
                                            <td className="px-4 py-3.5 text-right">
                                                <Button variant="ghost" size="sm" asChild className="text-slate-500 hover:text-slate-700">
                                                    <a href={`/admin/email-logs/${log.id}`}>
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
                            <Pagination links={emailLogs.links} from={emailLogs.from} to={emailLogs.to} total={emailLogs.total} />
                        </div>
                    </>
                )}
            </div>
        </AdminLayout>
    );
}
