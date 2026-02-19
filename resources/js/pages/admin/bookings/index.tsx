import { router } from '@inertiajs/react';
import { Search, X, PlusCircle, Eye, Download, FileText } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Pagination } from '@/components/pagination';
import { StatusBadge } from '@/components/booking/status-badge';
import { PaymentBadge } from '@/components/booking/payment-badge';
import { PaxBadges } from '@/components/booking/pax-badges';
import { FormField } from '@/components/form-field';
import { type PaginatedData, type Booking, type Partner, type Tour } from '@/types';

interface BookingIndexProps {
    bookings: PaginatedData<Booking>;
    partners: Partner[];
    tours: Tour[];
    statuses: Array<{ value: string; label: string }>;
    pendingCount: number;
    canCreate: boolean;
    filters: Record<string, string>;
}

export default function BookingIndex({ bookings, partners, tours, statuses, pendingCount, canCreate, filters }: BookingIndexProps) {
    const [f, setF] = useState(filters);

    function handleFilter(e: FormEvent) {
        e.preventDefault();
        router.get('/admin/bookings', Object.fromEntries(Object.entries(f).filter(([, v]) => v)), { preserveState: true });
    }

    function clearFilters() {
        setF({});
        router.get('/admin/bookings');
    }

    const hasFilters = Object.values(f).some(Boolean);

    return (
        <AdminLayout
            pageTitle="Bookings"
            breadcrumbs={[{ label: 'Bookings' }]}
            toolbarActions={
                <>
                    {pendingCount > 0 && (
                        <Badge variant="secondary" className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                            {pendingCount} pending
                        </Badge>
                    )}
                    <Button variant="outline" size="sm" className="h-9 border-slate-200 text-slate-700 hover:bg-slate-50" asChild>
                        <a href={`/admin/bookings/export?${new URLSearchParams(f).toString()}`}>
                            <Download className="mr-2 h-4 w-4 text-slate-500" />
                            Export
                        </a>
                    </Button>
                    {canCreate && (
                        <Button size="sm" className="h-9" asChild>
                            <a href="/admin/bookings/create">
                                <PlusCircle className="mr-2 h-4 w-4" />
                                New Booking
                            </a>
                        </Button>
                    )}
                </>
            }
        >
            {/* Filters */}
            <div className="mb-6 rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-4">
                    <FormField label="Search" className="min-w-[220px]">
                        <div className="relative">
                            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <Input
                                value={f.search ?? ''}
                                onChange={(e) => setF({ ...f, search: e.target.value })}
                                placeholder="Code or partner..."
                                className="h-9 border-slate-200 bg-white pl-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                    </FormField>

                    <FormField label="Partner">
                        <select
                            value={f.partner ?? ''}
                            onChange={(e) => setF({ ...f, partner: e.target.value })}
                            className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">All Partners</option>
                            {partners.map((p) => (
                                <option key={p.id} value={p.id}>{p.name}</option>
                            ))}
                        </select>
                    </FormField>

                    <FormField label="Status">
                        <select
                            value={f.status ?? ''}
                            onChange={(e) => setF({ ...f, status: e.target.value })}
                            className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            {statuses.map((s) => (
                                <option key={s.value} value={s.value}>{s.label}</option>
                            ))}
                        </select>
                    </FormField>

                    <FormField label="Tour">
                        <select
                            value={f.tour ?? ''}
                            onChange={(e) => setF({ ...f, tour: e.target.value })}
                            className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">All Tours</option>
                            {tours.map((t) => (
                                <option key={t.id} value={t.id}>{t.name}</option>
                            ))}
                        </select>
                    </FormField>

                    <FormField label="From">
                        <Input
                            type="date"
                            value={f.date_from ?? ''}
                            onChange={(e) => setF({ ...f, date_from: e.target.value })}
                            className="h-9 w-auto border-slate-200 bg-white text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        />
                    </FormField>

                    <FormField label="To">
                        <Input
                            type="date"
                            value={f.date_to ?? ''}
                            onChange={(e) => setF({ ...f, date_to: e.target.value })}
                            className="h-9 w-auto border-slate-200 bg-white text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        />
                    </FormField>

                    <div className="flex items-end gap-2">
                        <Button type="submit" size="sm" className="h-9">
                            <Search className="mr-1.5 h-3.5 w-3.5" />
                            Filter
                        </Button>
                        {hasFilters && (
                            <Button type="button" variant="ghost" size="sm" className="h-9 text-slate-500 hover:text-slate-700" onClick={clearFilters}>
                                <X className="mr-1.5 h-3.5 w-3.5" />
                                Clear
                            </Button>
                        )}
                    </div>
                </form>
            </div>

            {/* Table */}
            <Card className="border-slate-200 shadow-sm">
                <CardContent className="p-0">
                    {bookings.data.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-16">
                            <div className="mb-4 rounded-full bg-slate-100 p-3">
                                <FileText className="h-6 w-6 text-slate-400" />
                            </div>
                            <p className="text-sm font-medium text-slate-900">No bookings found</p>
                            <p className="mt-1 text-sm text-slate-400">Try adjusting your filters or create a new booking.</p>
                        </div>
                    ) : (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full border-separate border-spacing-0">
                                    <thead>
                                        <tr className="bg-slate-50">
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Code</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Partner</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Tour</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Date</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Pax</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Payment</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Total</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {bookings.data.map((booking) => (
                                            <tr key={booking.id} className="border-b border-slate-100 transition-colors hover:bg-slate-50/50">
                                                <td className="border-b border-slate-100 px-4 py-3.5">
                                                    <span className="text-sm font-semibold text-slate-900">{booking.booking_code}</span>
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3.5">
                                                    <span className="text-sm text-slate-600">{booking.partner?.name}</span>
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3.5">
                                                    <div>
                                                        <p className="text-sm font-medium text-slate-900">{booking.tour_departure?.tour?.name}</p>
                                                        <p className="mt-0.5 text-xs text-slate-400">{booking.tour_departure?.tour?.code}</p>
                                                    </div>
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3.5">
                                                    <div>
                                                        <p className="text-sm text-slate-900">{booking.tour_departure?.date}</p>
                                                        <p className="mt-0.5 text-xs text-slate-400">{booking.tour_departure?.time}</p>
                                                    </div>
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3.5">
                                                    <PaxBadges adults={booking.adults_count} children={booking.children_count} infants={booking.infants_count} />
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3.5">
                                                    <StatusBadge status={booking.status} />
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3.5">
                                                    <PaymentBadge status={booking.payment_status} />
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3.5 text-right">
                                                    <span className="text-sm font-semibold text-slate-900">&euro; {Number(booking.total_amount).toFixed(2)}</span>
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3.5 text-center">
                                                    <Button variant="ghost" size="sm" className="h-8 w-8 p-0 text-slate-400 hover:text-slate-700" asChild>
                                                        <a href={`/admin/bookings/${booking.id}`}>
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
                                <Pagination links={bookings.links} from={bookings.from} to={bookings.to} total={bookings.total} />
                            </div>
                        </>
                    )}
                </CardContent>
            </Card>
        </AdminLayout>
    );
}
