import { router } from '@inertiajs/react';
import { Search, X, PlusCircle, Eye, FileText } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import PartnerLayout from '@/layouts/partner-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/pagination';
import { StatusBadge } from '@/components/booking/status-badge';
import { PaxBadges } from '@/components/booking/pax-badges';
import { type PaginatedData, type Booking, type Tour } from '@/types';

interface BookingIndexProps {
    bookings: PaginatedData<Booking>;
    tours: Tour[];
    statuses: Array<{ value: string; label: string }>;
    filters: {
        search?: string;
        status?: string;
        tour?: string;
    };
}

export default function BookingIndex({ bookings, tours, statuses, filters }: BookingIndexProps) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const [tour, setTour] = useState(filters.tour ?? '');

    function handleFilter(e: FormEvent) {
        e.preventDefault();
        router.get('/partner/bookings', {
            ...(search && { search }),
            ...(status && { status }),
            ...(tour && { tour }),
        }, { preserveState: true });
    }

    function clearFilters() {
        setSearch('');
        setStatus('');
        setTour('');
        router.get('/partner/bookings');
    }

    const hasFilters = search || status || tour;

    return (
        <PartnerLayout
            pageTitle="My Bookings"
            breadcrumbs={[{ label: 'Bookings' }]}
            toolbarActions={
                <Button asChild>
                    <a href="/partner/bookings/create">
                        <PlusCircle className="mr-2 h-4 w-4" />
                        New Booking
                    </a>
                </Button>
            }
        >
            {/* Filters */}
            <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
                    <div className="w-full sm:w-auto">
                        <label className="mb-1.5 block text-xs font-medium text-slate-500">Search</label>
                        <div className="relative">
                            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Booking code..."
                                className="w-full border-slate-200 pl-10 sm:w-64"
                            />
                        </div>
                    </div>
                    <div className="w-full sm:w-auto">
                        <label className="mb-1.5 block text-xs font-medium text-slate-500">Status</label>
                        <select
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                            className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                        >
                            <option value="">All Statuses</option>
                            {statuses.map((s) => (
                                <option key={s.value} value={s.value}>{s.label}</option>
                            ))}
                        </select>
                    </div>
                    <div className="w-full sm:w-auto">
                        <label className="mb-1.5 block text-xs font-medium text-slate-500">Tour</label>
                        <select
                            value={tour}
                            onChange={(e) => setTour(e.target.value)}
                            className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                        >
                            <option value="">All Tours</option>
                            {tours.map((t) => (
                                <option key={t.id} value={t.id}>{t.name}</option>
                            ))}
                        </select>
                    </div>
                    <Button type="submit" size="sm">
                        <Search className="mr-1.5 h-3.5 w-3.5" />
                        Filter
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
                {bookings.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-16">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                            <FileText className="h-6 w-6 text-slate-400" />
                        </div>
                        <p className="mt-3 text-sm font-medium text-slate-900">No bookings found</p>
                        <p className="mt-1 text-sm text-slate-500">Try adjusting your filters or create a new booking.</p>
                    </div>
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="bg-slate-50">
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Code</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Tour</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Date</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Passengers</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Total</th>
                                        <th className="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {bookings.data.map((booking) => (
                                        <tr key={booking.id} className="border-b border-slate-100 transition-colors hover:bg-slate-50/50">
                                            <td className="px-4 py-3.5">
                                                <span className="text-sm font-medium text-slate-900">{booking.booking_code}</span>
                                            </td>
                                            <td className="px-4 py-3.5">
                                                <div>
                                                    <p className="text-sm text-slate-900">{booking.tour_departure?.tour?.name}</p>
                                                    <p className="text-xs text-slate-400">{booking.tour_departure?.tour?.code}</p>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3.5 text-sm text-slate-600">
                                                {booking.tour_departure?.date}
                                                <span className="ml-1 text-slate-400">{booking.tour_departure?.time}</span>
                                            </td>
                                            <td className="px-4 py-3.5">
                                                <PaxBadges
                                                    adults={booking.adults_count}
                                                    children={booking.children_count}
                                                    infants={booking.infants_count}
                                                />
                                            </td>
                                            <td className="px-4 py-3.5">
                                                <StatusBadge status={booking.status} />
                                            </td>
                                            <td className="px-4 py-3.5 text-sm font-semibold text-slate-900">
                                                &euro; {Number(booking.total_amount).toFixed(2)}
                                            </td>
                                            <td className="px-4 py-3.5 text-right">
                                                <Button variant="ghost" size="sm" asChild className="text-slate-500 hover:text-slate-700">
                                                    <a href={`/partner/bookings/${booking.id}`}>
                                                        <Eye className="mr-1 h-4 w-4" />
                                                        View
                                                    </a>
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <div className="border-t border-slate-100 px-4 py-3">
                            <Pagination
                                links={bookings.links}
                                from={bookings.from}
                                to={bookings.to}
                                total={bookings.total}
                            />
                        </div>
                    </>
                )}
            </div>
        </PartnerLayout>
    );
}
