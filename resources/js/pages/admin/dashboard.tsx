import {
    FileText,
    Users,
    AlertTriangle,
    Wallet,
    Calendar,
    Clock,
    ChevronRight,
    CreditCard,
    Inbox,
    Anchor,
} from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Pagination } from '@/components/pagination';
import { StatusBadge } from '@/components/booking/status-badge';
import { PaxBadges } from '@/components/booking/pax-badges';
import { formatTime } from '@/lib/utils';
import { type PaginatedData, type Booking, type TourDeparture, type Partner } from '@/types';

interface DashboardProps {
    todaysBookingsCount: number;
    weeklyPassengersCount: number;
    pendingRequestsCount: number;
    totalOutstanding: number;
    pendingRequests: PaginatedData<Booking>;
    todaysDepartures: PaginatedData<TourDeparture>;
    recentBookings: PaginatedData<Booking>;
    partnerOutstanding: PaginatedData<Partner>;
}

function StatCard({
    icon: Icon,
    label,
    value,
    iconBg,
    iconColor,
}: {
    icon: React.ComponentType<{ className?: string }>;
    label: string;
    value: string | number;
    iconBg: string;
    iconColor: string;
}) {
    return (
        <Card className="overflow-hidden border-slate-200 shadow-sm">
            <CardContent className="p-0">
                <div className="flex items-center gap-4 p-5">
                    <div
                        className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-xl ${iconBg}`}
                    >
                        <Icon className={`h-6 w-6 ${iconColor}`} />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-slate-500">{label}</p>
                        <p className="text-2xl font-bold tracking-tight text-slate-900">{value}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}

function EmptyState({ icon: Icon, message }: { icon: React.ComponentType<{ className?: string }>; message: string }) {
    return (
        <div className="flex flex-col items-center justify-center py-10">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                <Icon className="h-6 w-6 text-slate-400" />
            </div>
            <p className="mt-3 text-sm text-slate-500">{message}</p>
        </div>
    );
}

function SectionHeader({
    icon: Icon,
    title,
    count,
    viewAllHref,
    iconColor = 'text-slate-400',
}: {
    icon: React.ComponentType<{ className?: string }>;
    title: string;
    count?: number;
    viewAllHref?: string;
    iconColor?: string;
}) {
    return (
        <CardHeader className="pb-0">
            <CardTitle className="flex items-center justify-between text-base">
                <span className="flex items-center gap-2">
                    <Icon className={`h-4 w-4 ${iconColor}`} />
                    <span className="text-slate-900">{title}</span>
                    {count !== undefined && count > 0 && (
                        <span className="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                            {count}
                        </span>
                    )}
                </span>
                {viewAllHref && (
                    <Button variant="ghost" size="sm" className="text-slate-500 hover:text-slate-700" asChild>
                        <a href={viewAllHref}>
                            View All
                            <ChevronRight className="ml-1 h-3.5 w-3.5" />
                        </a>
                    </Button>
                )}
            </CardTitle>
        </CardHeader>
    );
}

export default function AdminDashboard({
    todaysBookingsCount,
    weeklyPassengersCount,
    pendingRequestsCount,
    totalOutstanding,
    pendingRequests,
    todaysDepartures,
    recentBookings,
    partnerOutstanding,
}: DashboardProps) {
    return (
        <AdminLayout pageTitle="Dashboard">
            {/* Stats Row */}
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    icon={FileText}
                    label="Today's Bookings"
                    value={todaysBookingsCount}
                    iconBg="bg-blue-50"
                    iconColor="text-blue-600"
                />
                <StatCard
                    icon={Users}
                    label="Weekly Passengers"
                    value={weeklyPassengersCount}
                    iconBg="bg-emerald-50"
                    iconColor="text-emerald-600"
                />
                <StatCard
                    icon={AlertTriangle}
                    label="Pending Requests"
                    value={pendingRequestsCount}
                    iconBg="bg-amber-50"
                    iconColor="text-amber-600"
                />
                <StatCard
                    icon={Wallet}
                    label="Outstanding"
                    value={`\u20AC ${Number(totalOutstanding).toFixed(2)}`}
                    iconBg="bg-red-50"
                    iconColor="text-red-600"
                />
            </div>

            {/* Main Content Grid */}
            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                {/* Left Column */}
                <div className="flex flex-col gap-6">
                    {/* Overbooking Requests (only shown when data exists) */}
                    {pendingRequests.data.length > 0 && (
                        <Card className="border-amber-200/60 shadow-sm">
                            <SectionHeader
                                icon={AlertTriangle}
                                title="Overbooking Requests"
                                count={pendingRequests.total}
                                iconColor="text-amber-500"
                                viewAllHref="/admin/bookings?status=suspended_request"
                            />
                            <CardContent>
                                <div className="divide-y divide-amber-100">
                                    {pendingRequests.data.map((booking) => (
                                        <a
                                            key={booking.id}
                                            href={`/admin/bookings/${booking.id}`}
                                            className="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-amber-50/60"
                                        >
                                            <div className="min-w-0 flex-1">
                                                <p className="truncate text-sm font-medium text-slate-900">
                                                    {booking.booking_code}
                                                </p>
                                                <p className="text-xs text-slate-500">
                                                    {booking.partner?.name} &middot; {booking.total_passengers} pax
                                                </p>
                                            </div>
                                            <div className="ml-4 flex items-center gap-2">
                                                {booking.suspended_until && (
                                                    <span className="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                        <Clock className="mr-1 h-3 w-3" />
                                                        {formatTime(booking.suspended_until)}
                                                    </span>
                                                )}
                                                <ChevronRight className="h-4 w-4 text-slate-300" />
                                            </div>
                                        </a>
                                    ))}
                                </div>
                                <Pagination
                                    links={pendingRequests.links}
                                    from={pendingRequests.from}
                                    to={pendingRequests.to}
                                    total={pendingRequests.total}
                                />
                            </CardContent>
                        </Card>
                    )}

                    {/* Recent Bookings */}
                    <Card className="border-slate-200 shadow-sm">
                        <SectionHeader
                            icon={FileText}
                            title="Recent Bookings"
                            viewAllHref="/admin/bookings"
                        />
                        <CardContent>
                            {recentBookings.data.length === 0 ? (
                                <EmptyState icon={Inbox} message="No recent bookings." />
                            ) : (
                                <div className="divide-y divide-slate-100">
                                    {recentBookings.data.map((booking) => (
                                        <a
                                            key={booking.id}
                                            href={`/admin/bookings/${booking.id}`}
                                            className="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-slate-50"
                                        >
                                            <div className="min-w-0 flex-1">
                                                <p className="truncate text-sm font-medium text-slate-900">
                                                    {booking.booking_code}
                                                </p>
                                                <p className="text-xs text-slate-500">
                                                    {booking.partner?.name}
                                                    {booking.tour_departure?.tour?.name && (
                                                        <> &middot; {booking.tour_departure.tour.name}</>
                                                    )}
                                                </p>
                                            </div>
                                            <div className="ml-4 flex items-center gap-2">
                                                <PaxBadges
                                                    adults={booking.adults_count}
                                                    children={booking.children_count}
                                                    infants={booking.infants_count}
                                                />
                                                <StatusBadge status={booking.status} />
                                                <ChevronRight className="h-4 w-4 text-slate-300" />
                                            </div>
                                        </a>
                                    ))}
                                </div>
                            )}
                            <Pagination
                                links={recentBookings.links}
                                from={recentBookings.from}
                                to={recentBookings.to}
                                total={recentBookings.total}
                            />
                        </CardContent>
                    </Card>
                </div>

                {/* Right Column */}
                <div className="flex flex-col gap-6">
                    {/* Today's Departures */}
                    <Card className="border-slate-200 shadow-sm">
                        <SectionHeader
                            icon={Calendar}
                            title="Today's Departures"
                            viewAllHref="/admin/calendar"
                        />
                        <CardContent>
                            {todaysDepartures.data.length === 0 ? (
                                <EmptyState icon={Anchor} message="No departures scheduled for today." />
                            ) : (
                                <div className="divide-y divide-slate-100">
                                    {todaysDepartures.data.map((dep) => {
                                        const capacityPct = dep.capacity > 0 ? (dep.booked_seats / dep.capacity) * 100 : 0;
                                        const isFull = capacityPct >= 100;
                                        const isNearFull = capacityPct >= 80 && !isFull;

                                        return (
                                            <a
                                                key={dep.id}
                                                href={`/admin/departures/${dep.id}`}
                                                className="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-slate-50"
                                            >
                                                <div className="min-w-0 flex-1">
                                                    <p className="truncate text-sm font-medium text-slate-900">
                                                        {dep.tour?.name}
                                                    </p>
                                                    <p className="text-xs text-slate-500">
                                                        {dep.time} &middot; {dep.driver?.name ?? 'No driver assigned'}
                                                    </p>
                                                </div>
                                                <div className="ml-4 flex items-center gap-2">
                                                    <span
                                                        className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset ${
                                                            isFull
                                                                ? 'bg-red-50 text-red-700 ring-red-600/20'
                                                                : isNearFull
                                                                  ? 'bg-amber-50 text-amber-700 ring-amber-600/20'
                                                                  : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                                        }`}
                                                    >
                                                        {dep.booked_seats}/{dep.capacity}
                                                    </span>
                                                    <ChevronRight className="h-4 w-4 text-slate-300" />
                                                </div>
                                            </a>
                                        );
                                    })}
                                </div>
                            )}
                            <Pagination
                                links={todaysDepartures.links}
                                from={todaysDepartures.from}
                                to={todaysDepartures.to}
                                total={todaysDepartures.total}
                            />
                        </CardContent>
                    </Card>

                    {/* Partner Balances */}
                    <Card className="border-slate-200 shadow-sm">
                        <SectionHeader
                            icon={CreditCard}
                            title="Partner Balances"
                            viewAllHref="/admin/accounting"
                        />
                        <CardContent>
                            {partnerOutstanding.data.length === 0 ? (
                                <EmptyState icon={Wallet} message="All partners are settled." />
                            ) : (
                                <div className="divide-y divide-slate-100">
                                    {partnerOutstanding.data.map((partner) => (
                                        <a
                                            key={partner.id}
                                            href={`/admin/partners/${partner.id}`}
                                            className="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-slate-50"
                                        >
                                            <div className="min-w-0 flex-1">
                                                <div className="flex items-center gap-2">
                                                    <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-600">
                                                        {partner.name
                                                            .split(' ')
                                                            .map((w) => w[0])
                                                            .join('')
                                                            .slice(0, 2)
                                                            .toUpperCase()}
                                                    </div>
                                                    <div className="min-w-0">
                                                        <p className="truncate text-sm font-medium text-slate-900">
                                                            {partner.name}
                                                        </p>
                                                        <p className="text-xs capitalize text-slate-500">
                                                            {partner.type?.replace('_', ' ')}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="ml-4 flex items-center gap-2">
                                                <span className="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">
                                                    &euro; {Number(partner.outstanding_balance).toFixed(2)}
                                                </span>
                                                <ChevronRight className="h-4 w-4 text-slate-300" />
                                            </div>
                                        </a>
                                    ))}
                                </div>
                            )}
                            <Pagination
                                links={partnerOutstanding.links}
                                from={partnerOutstanding.from}
                                to={partnerOutstanding.to}
                                total={partnerOutstanding.total}
                            />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AdminLayout>
    );
}
