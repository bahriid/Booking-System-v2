import { router } from '@inertiajs/react';
import {
    BarChart3,
    TrendingUp,
    Users,
    FileText,
    Wallet,
    XCircle,
    Ship,
    Award,
    CalendarDays,
    Inbox,
} from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface ReportsProps {
    period: string;
    stats: {
        totalBookings: number;
        totalPassengers: number;
        totalRevenue: number;
        avgBookingValue: number;
        cancellationRate: number;
    };
    bookingsByStatus: Array<{ status: string; count: number }>;
    revenueByTour: Array<{ tour_name: string; revenue: number; bookings: number }>;
    topPartners: Array<{ name: string; bookings: number; revenue: number }>;
    bookingsTrend: Array<{ date: string; count: number }>;
    upcomingCapacity: Array<{ date: string; tour_name: string; booked: number; capacity: number }>;
}

const periods = [
    { value: 'week', label: 'This Week' },
    { value: 'month', label: 'This Month' },
    { value: 'quarter', label: 'This Quarter' },
    { value: 'year', label: 'This Year' },
    { value: 'all', label: 'All Time' },
];

function StatCard({
    icon: Icon,
    label,
    value,
    iconBg,
    iconColor,
    valueColor = 'text-slate-900',
}: {
    icon: React.ComponentType<{ className?: string }>;
    label: string;
    value: string | number;
    iconBg: string;
    iconColor: string;
    valueColor?: string;
}) {
    return (
        <Card className="overflow-hidden border-slate-200 shadow-sm">
            <CardContent className="p-0">
                <div className="flex items-center gap-4 p-5">
                    <div className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-xl ${iconBg}`}>
                        <Icon className={`h-6 w-6 ${iconColor}`} />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-slate-500">{label}</p>
                        <p className={`text-2xl font-bold tracking-tight ${valueColor}`}>{value}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}

const statusColorMap: Record<string, { bg: string; text: string; ring: string }> = {
    confirmed: { bg: 'bg-emerald-50', text: 'text-emerald-700', ring: 'ring-emerald-600/20' },
    suspended_request: { bg: 'bg-amber-50', text: 'text-amber-700', ring: 'ring-amber-600/20' },
    completed: { bg: 'bg-blue-50', text: 'text-blue-700', ring: 'ring-blue-600/20' },
    cancelled: { bg: 'bg-red-50', text: 'text-red-700', ring: 'ring-red-600/20' },
    rejected: { bg: 'bg-slate-100', text: 'text-slate-600', ring: 'ring-slate-500/20' },
    expired: { bg: 'bg-slate-50', text: 'text-slate-500', ring: 'ring-slate-400/20' },
};

export default function ReportsIndex({
    period,
    stats,
    bookingsByStatus,
    revenueByTour,
    topPartners,
    bookingsTrend,
    upcomingCapacity,
}: ReportsProps) {
    const maxTrendCount = Math.max(...bookingsTrend.map((b) => b.count), 1);
    const maxRevenue = Math.max(...revenueByTour.map((t) => t.revenue), 1);

    return (
        <AdminLayout
            pageTitle="Reports"
            breadcrumbs={[{ label: 'Reports' }]}
            toolbarActions={
                <div className="flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 p-1">
                    {periods.map((p) => (
                        <button
                            key={p.value}
                            onClick={() =>
                                router.get('/admin/reports', { period: p.value }, { preserveState: true })
                            }
                            className={`rounded-md px-3 py-1.5 text-xs font-medium transition-all ${
                                period === p.value
                                    ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200'
                                    : 'text-slate-500 hover:bg-white/60 hover:text-slate-700'
                            }`}
                        >
                            {p.label}
                        </button>
                    ))}
                </div>
            }
        >
            {/* Stats Row */}
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <StatCard
                    icon={FileText}
                    label="Total Bookings"
                    value={stats.totalBookings}
                    iconBg="bg-blue-50"
                    iconColor="text-blue-600"
                />
                <StatCard
                    icon={Users}
                    label="Total Passengers"
                    value={stats.totalPassengers}
                    iconBg="bg-violet-50"
                    iconColor="text-violet-600"
                />
                <StatCard
                    icon={Wallet}
                    label="Total Revenue"
                    value={`\u20AC ${Number(stats.totalRevenue).toFixed(2)}`}
                    iconBg="bg-emerald-50"
                    iconColor="text-emerald-600"
                    valueColor="text-emerald-700"
                />
                <StatCard
                    icon={TrendingUp}
                    label="Avg Booking Value"
                    value={`\u20AC ${Number(stats.avgBookingValue).toFixed(2)}`}
                    iconBg="bg-amber-50"
                    iconColor="text-amber-600"
                />
                <StatCard
                    icon={XCircle}
                    label="Cancellation Rate"
                    value={`${Number(stats.cancellationRate).toFixed(1)}%`}
                    iconBg="bg-red-50"
                    iconColor="text-red-600"
                    valueColor={stats.cancellationRate > 10 ? 'text-red-700' : 'text-slate-900'}
                />
            </div>

            {/* Bookings Trend */}
            {bookingsTrend.length > 0 && (
                <Card className="mt-6 border-slate-200 shadow-sm">
                    <CardHeader className="pb-2">
                        <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                            <BarChart3 className="h-4 w-4 text-slate-400" />
                            Booking Trend
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-end gap-1" style={{ height: '140px' }}>
                            {bookingsTrend.map((point, i) => {
                                const heightPct = (point.count / maxTrendCount) * 100;
                                return (
                                    <div
                                        key={i}
                                        className="group relative flex flex-1 flex-col items-center"
                                        style={{ height: '100%' }}
                                    >
                                        <div className="flex w-full flex-1 items-end justify-center">
                                            <div
                                                className="w-full max-w-[32px] rounded-t-md bg-blue-500/80 transition-colors group-hover:bg-blue-600"
                                                style={{ height: `${Math.max(heightPct, 4)}%` }}
                                            />
                                        </div>
                                        <span className="mt-1.5 text-[10px] text-slate-400">
                                            {point.date.slice(-5)}
                                        </span>
                                        {/* Tooltip */}
                                        <div className="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 rounded bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                            {point.count}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </CardContent>
                </Card>
            )}

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                {/* Bookings by Status */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-2">
                        <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                            <FileText className="h-4 w-4 text-slate-400" />
                            Bookings by Status
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {bookingsByStatus.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-8">
                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                                    <Inbox className="h-5 w-5 text-slate-400" />
                                </div>
                                <p className="mt-2 text-sm text-slate-500">No data available.</p>
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {bookingsByStatus.map((s) => {
                                    const totalBookings = bookingsByStatus.reduce((sum, item) => sum + item.count, 0);
                                    const pct = totalBookings > 0 ? (s.count / totalBookings) * 100 : 0;
                                    const colors = statusColorMap[s.status] ?? statusColorMap.expired;
                                    return (
                                        <div key={s.status}>
                                            <div className="mb-1.5 flex items-center justify-between">
                                                <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize ring-1 ring-inset ${colors.bg} ${colors.text} ${colors.ring}`}>
                                                    {s.status.replace('_', ' ')}
                                                </span>
                                                <span className="text-sm font-semibold text-slate-700">
                                                    {s.count}
                                                </span>
                                            </div>
                                            <div className="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                                <div
                                                    className={`h-full rounded-full transition-all ${
                                                        s.status === 'confirmed' ? 'bg-emerald-500' :
                                                        s.status === 'suspended_request' ? 'bg-amber-500' :
                                                        s.status === 'completed' ? 'bg-blue-500' :
                                                        s.status === 'cancelled' ? 'bg-red-500' :
                                                        'bg-slate-400'
                                                    }`}
                                                    style={{ width: `${pct}%` }}
                                                />
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Revenue by Tour */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-2">
                        <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                            <Ship className="h-4 w-4 text-slate-400" />
                            Revenue by Tour
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {revenueByTour.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-8">
                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                                    <Inbox className="h-5 w-5 text-slate-400" />
                                </div>
                                <p className="mt-2 text-sm text-slate-500">No data available.</p>
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {revenueByTour.map((t) => {
                                    const pct = (t.revenue / maxRevenue) * 100;
                                    return (
                                        <div key={t.tour_name}>
                                            <div className="mb-1.5 flex items-center justify-between">
                                                <div>
                                                    <p className="text-sm font-medium text-slate-900">
                                                        {t.tour_name}
                                                    </p>
                                                    <p className="text-xs text-slate-500">
                                                        {t.bookings} booking{t.bookings !== 1 ? 's' : ''}
                                                    </p>
                                                </div>
                                                <span className="text-sm font-bold text-emerald-700">
                                                    &euro; {Number(t.revenue).toFixed(2)}
                                                </span>
                                            </div>
                                            <div className="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                                <div
                                                    className="h-full rounded-full bg-emerald-500 transition-all"
                                                    style={{ width: `${pct}%` }}
                                                />
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Top Partners */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-2">
                        <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                            <Award className="h-4 w-4 text-slate-400" />
                            Top Partners
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {topPartners.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-8">
                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                                    <Inbox className="h-5 w-5 text-slate-400" />
                                </div>
                                <p className="mt-2 text-sm text-slate-500">No data available.</p>
                            </div>
                        ) : (
                            <div className="divide-y divide-slate-100">
                                {topPartners.map((p, i) => (
                                    <div key={p.name} className="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                                        <div className={`flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold ${
                                            i === 0 ? 'bg-amber-50 text-amber-700' :
                                            i === 1 ? 'bg-slate-100 text-slate-600' :
                                            i === 2 ? 'bg-orange-50 text-orange-700' :
                                            'bg-slate-50 text-slate-500'
                                        }`}>
                                            {i + 1}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm font-medium text-slate-900">
                                                {p.name}
                                            </p>
                                            <p className="text-xs text-slate-500">
                                                {p.bookings} booking{p.bookings !== 1 ? 's' : ''}
                                            </p>
                                        </div>
                                        <span className="text-sm font-bold text-slate-900">
                                            &euro; {Number(p.revenue).toFixed(2)}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Upcoming Capacity */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-2">
                        <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                            <CalendarDays className="h-4 w-4 text-slate-400" />
                            Upcoming Capacity
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {upcomingCapacity.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-8">
                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                                    <Inbox className="h-5 w-5 text-slate-400" />
                                </div>
                                <p className="mt-2 text-sm text-slate-500">No upcoming departures.</p>
                            </div>
                        ) : (
                            <div className="divide-y divide-slate-100">
                                {upcomingCapacity.slice(0, 10).map((c, i) => {
                                    const pct = c.capacity > 0 ? (c.booked / c.capacity) * 100 : 0;
                                    const isFull = pct >= 100;
                                    const isNearFull = pct >= 80 && !isFull;
                                    return (
                                        <div key={i} className="py-3 first:pt-0 last:pb-0">
                                            <div className="flex items-center justify-between">
                                                <div className="min-w-0 flex-1">
                                                    <p className="truncate text-sm font-medium text-slate-900">
                                                        {c.tour_name}
                                                    </p>
                                                    <p className="text-xs text-slate-500">{c.date}</p>
                                                </div>
                                                <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset ${
                                                    isFull
                                                        ? 'bg-red-50 text-red-700 ring-red-600/20'
                                                        : isNearFull
                                                          ? 'bg-amber-50 text-amber-700 ring-amber-600/20'
                                                          : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                                }`}>
                                                    {c.booked}/{c.capacity}
                                                </span>
                                            </div>
                                            <div className="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                                <div
                                                    className={`h-full rounded-full transition-all ${
                                                        isFull ? 'bg-red-500' : isNearFull ? 'bg-amber-500' : 'bg-emerald-500'
                                                    }`}
                                                    style={{ width: `${Math.min(pct, 100)}%` }}
                                                />
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
