import { FileText, Users, AlertTriangle, PlusCircle, Calendar, ArrowRight } from 'lucide-react';
import PartnerLayout from '@/layouts/partner-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Pagination } from '@/components/pagination';
import { StatusBadge } from '@/components/booking/status-badge';
import { PaxBadges } from '@/components/booking/pax-badges';
import { type PaginatedData, type Booking, type Partner } from '@/types';

interface PartnerDashboardProps {
    partner: Partner;
    bookingsThisMonth: number;
    passengersThisMonth: number;
    pendingRequests: number;
    recentBookings: PaginatedData<Booking>;
    upcomingBookings: PaginatedData<Booking>;
}

export default function PartnerDashboard({
    partner,
    bookingsThisMonth,
    passengersThisMonth,
    pendingRequests,
    recentBookings,
    upcomingBookings,
}: PartnerDashboardProps) {
    return (
        <PartnerLayout
            pageTitle="Dashboard"
            toolbarActions={
                <Button asChild>
                    <a href="/partner/bookings/create">
                        <PlusCircle className="mr-2 h-4 w-4" />
                        New Booking
                    </a>
                </Button>
            }
        >
            {/* Stats Cards */}
            <div className="grid gap-4 sm:grid-cols-3">
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100">
                            <FileText className="h-6 w-6 text-emerald-600" />
                        </div>
                        <div>
                            <p className="text-sm text-slate-500">Bookings This Month</p>
                            <p className="text-2xl font-bold text-slate-900">{bookingsThisMonth}</p>
                        </div>
                    </div>
                </div>
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100">
                            <Users className="h-6 w-6 text-blue-600" />
                        </div>
                        <div>
                            <p className="text-sm text-slate-500">Passengers This Month</p>
                            <p className="text-2xl font-bold text-slate-900">{passengersThisMonth}</p>
                        </div>
                    </div>
                </div>
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100">
                            <AlertTriangle className="h-6 w-6 text-amber-600" />
                        </div>
                        <div>
                            <p className="text-sm text-slate-500">Pending Requests</p>
                            <p className="text-2xl font-bold text-slate-900">{pendingRequests}</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Two-Column Layout */}
            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                {/* Recent Bookings */}
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-3">
                        <h3 className="text-sm font-semibold text-slate-900">Recent Bookings</h3>
                        <Button variant="ghost" size="sm" asChild className="text-slate-500 hover:text-slate-700">
                            <a href="/partner/bookings">
                                View All <ArrowRight className="ml-1 h-3.5 w-3.5" />
                            </a>
                        </Button>
                    </div>
                    <div className="p-4">
                        {recentBookings.data.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-10">
                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                                    <FileText className="h-5 w-5 text-slate-400" />
                                </div>
                                <p className="mt-2 text-sm text-slate-500">No bookings yet.</p>
                            </div>
                        ) : (
                            <div className="space-y-2">
                                {recentBookings.data.map((booking) => (
                                    <a
                                        key={booking.id}
                                        href={`/partner/bookings/${booking.id}`}
                                        className="flex items-center justify-between rounded-lg border border-slate-100 px-4 py-3 transition-colors hover:border-slate-200 hover:bg-slate-50/50"
                                    >
                                        <div className="min-w-0">
                                            <p className="text-sm font-medium text-slate-900">{booking.booking_code}</p>
                                            <p className="mt-0.5 truncate text-xs text-slate-500">
                                                {booking.tour_departure?.tour?.name} &middot; {booking.tour_departure?.date}
                                            </p>
                                        </div>
                                        <div className="ml-3 flex shrink-0 items-center gap-2">
                                            <PaxBadges
                                                adults={booking.adults_count}
                                                children={booking.children_count}
                                                infants={booking.infants_count}
                                            />
                                            <StatusBadge status={booking.status} />
                                        </div>
                                    </a>
                                ))}
                            </div>
                        )}
                        <div className="mt-3">
                            <Pagination
                                links={recentBookings.links}
                                from={recentBookings.from}
                                to={recentBookings.to}
                                total={recentBookings.total}
                            />
                        </div>
                    </div>
                </div>

                {/* Upcoming Tours */}
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center gap-2 border-b border-slate-100 bg-slate-50 px-5 py-3">
                        <Calendar className="h-4 w-4 text-emerald-600" />
                        <h3 className="text-sm font-semibold text-slate-900">Upcoming Tours</h3>
                        <span className="text-xs text-slate-400">(Next 7 days)</span>
                    </div>
                    <div className="p-4">
                        {upcomingBookings.data.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-10">
                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                                    <Calendar className="h-5 w-5 text-slate-400" />
                                </div>
                                <p className="mt-2 text-sm text-slate-500">No upcoming tours.</p>
                            </div>
                        ) : (
                            <div className="space-y-2">
                                {upcomingBookings.data.map((booking) => (
                                    <a
                                        key={booking.id}
                                        href={`/partner/bookings/${booking.id}`}
                                        className="flex items-center justify-between rounded-lg border border-slate-100 px-4 py-3 transition-colors hover:border-slate-200 hover:bg-slate-50/50"
                                    >
                                        <div className="min-w-0">
                                            <p className="text-sm font-medium text-slate-900">{booking.tour_departure?.tour?.name}</p>
                                            <p className="mt-0.5 text-xs text-slate-500">
                                                {booking.tour_departure?.date} at {booking.tour_departure?.time}
                                            </p>
                                        </div>
                                        <div className="ml-3 flex shrink-0 items-center gap-2">
                                            <Badge className="bg-slate-50 text-xs text-slate-700 ring-1 ring-inset ring-slate-600/20">
                                                {booking.booking_code}
                                            </Badge>
                                            <PaxBadges
                                                adults={booking.adults_count}
                                                children={booking.children_count}
                                                infants={booking.infants_count}
                                            />
                                        </div>
                                    </a>
                                ))}
                            </div>
                        )}
                        <div className="mt-3">
                            <Pagination
                                links={upcomingBookings.links}
                                from={upcomingBookings.from}
                                to={upcomingBookings.to}
                                total={upcomingBookings.total}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </PartnerLayout>
    );
}
