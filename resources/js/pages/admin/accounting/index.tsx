import { router } from '@inertiajs/react';
import {
    Wallet,
    Download,
    Search,
    X,
    TrendingUp,
    CreditCard,
    AlertCircle,
    Receipt,
    ChevronRight,
    Inbox,
    ArrowUpRight,
    ArrowDownRight,
    Filter,
} from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type Partner } from '@/types';

interface AccountingProps {
    totalRevenue: number;
    totalPayments: number;
    paymentCount: number;
    totalPenalties: number;
    penaltyCount: number;
    totalOutstanding: number;
    partnersWithBalance: number;
    partners: Partner[];
    transactions: Array<{
        type: string;
        date: string;
        partner_name: string;
        amount: number;
        description: string;
        method: string | null;
    }>;
    partnersForDropdown: Partner[];
    startDate: string;
    endDate: string;
    dateType: string;
    balanceFilter: string;
    unpaidBookings: Array<{
        id: number;
        booking_code: string;
        partner_name: string;
        total_amount: number;
        balance_due: number;
    }>;
    unpaidBookingsCount: number;
    filters: Record<string, string>;
}

function StatCard({
    icon: Icon,
    label,
    value,
    subtitle,
    iconBg,
    iconColor,
    valueColor = 'text-slate-900',
}: {
    icon: React.ComponentType<{ className?: string }>;
    label: string;
    value: string;
    subtitle?: string;
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
                    <div className="min-w-0">
                        <p className="text-sm font-medium text-slate-500">{label}</p>
                        <p className={`text-2xl font-bold tracking-tight ${valueColor}`}>{value}</p>
                        {subtitle && (
                            <p className="text-xs text-slate-400">{subtitle}</p>
                        )}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}

const typeConfig: Record<string, { bg: string; text: string; ring: string; icon: React.ComponentType<{ className?: string }> }> = {
    payment: { bg: 'bg-emerald-50', text: 'text-emerald-700', ring: 'ring-emerald-600/20', icon: ArrowDownRight },
    penalty: { bg: 'bg-amber-50', text: 'text-amber-700', ring: 'ring-amber-600/20', icon: AlertCircle },
    booking: { bg: 'bg-blue-50', text: 'text-blue-700', ring: 'ring-blue-600/20', icon: Receipt },
};

export default function AccountingIndex(props: AccountingProps) {
    const [startDate, setStartDate] = useState(props.startDate);
    const [endDate, setEndDate] = useState(props.endDate);
    const [dateType, setDateType] = useState(props.dateType);
    const [balanceFilter, setBalanceFilter] = useState(props.balanceFilter);

    function handleFilter(e: FormEvent) {
        e.preventDefault();
        router.get(
            '/admin/accounting',
            { start_date: startDate, end_date: endDate, date_type: dateType, balance_filter: balanceFilter },
            { preserveState: true },
        );
    }

    const hasFilters = startDate || endDate || dateType !== 'booking_date' || balanceFilter !== 'all';

    function clearFilters() {
        setStartDate('');
        setEndDate('');
        setDateType('booking_date');
        setBalanceFilter('all');
        router.get('/admin/accounting');
    }

    return (
        <AdminLayout
            pageTitle="Accounting"
            breadcrumbs={[{ label: 'Accounting' }]}
            toolbarActions={
                <Button variant="outline" size="sm" className="border-slate-200" asChild>
                    <a href="/admin/accounting/export">
                        <Download className="mr-2 h-4 w-4" />
                        Export
                    </a>
                </Button>
            }
        >
            {/* Stats Row */}
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    icon={TrendingUp}
                    label="Total Revenue"
                    value={`\u20AC ${Number(props.totalRevenue).toFixed(2)}`}
                    iconBg="bg-emerald-50"
                    iconColor="text-emerald-600"
                    valueColor="text-emerald-700"
                />
                <StatCard
                    icon={CreditCard}
                    label="Payments Received"
                    value={`\u20AC ${Number(props.totalPayments).toFixed(2)}`}
                    subtitle={`${props.paymentCount} payment${props.paymentCount !== 1 ? 's' : ''}`}
                    iconBg="bg-blue-50"
                    iconColor="text-blue-600"
                    valueColor="text-blue-700"
                />
                <StatCard
                    icon={AlertCircle}
                    label="Penalties"
                    value={`\u20AC ${Number(props.totalPenalties).toFixed(2)}`}
                    subtitle={`${props.penaltyCount} penalt${props.penaltyCount !== 1 ? 'ies' : 'y'}`}
                    iconBg="bg-amber-50"
                    iconColor="text-amber-600"
                    valueColor="text-amber-700"
                />
                <StatCard
                    icon={Wallet}
                    label="Outstanding"
                    value={`\u20AC ${Number(props.totalOutstanding).toFixed(2)}`}
                    subtitle={`${props.partnersWithBalance} partner${props.partnersWithBalance !== 1 ? 's' : ''}`}
                    iconBg="bg-red-50"
                    iconColor="text-red-600"
                    valueColor="text-red-700"
                />
            </div>

            {/* Filters */}
            <div className="mt-6 rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                <form onSubmit={handleFilter} className="flex flex-wrap items-center gap-3">
                    <div className="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <Filter className="h-4 w-4 text-slate-400" />
                        Filters
                    </div>
                    <div className="h-6 w-px bg-slate-200" />
                    <Input
                        type="date"
                        value={startDate}
                        onChange={(e) => setStartDate(e.target.value)}
                        className="h-9 w-36 border-slate-200 bg-white"
                        placeholder="From"
                    />
                    <Input
                        type="date"
                        value={endDate}
                        onChange={(e) => setEndDate(e.target.value)}
                        className="h-9 w-36 border-slate-200 bg-white"
                        placeholder="To"
                    />
                    <select
                        value={dateType}
                        onChange={(e) => setDateType(e.target.value)}
                        className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                    >
                        <option value="booking_date">Booking Date</option>
                        <option value="tour_date">Tour Date</option>
                    </select>
                    <select
                        value={balanceFilter}
                        onChange={(e) => setBalanceFilter(e.target.value)}
                        className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                    >
                        <option value="all">All Balances</option>
                        <option value="outstanding">Outstanding</option>
                        <option value="paid">Paid</option>
                    </select>
                    <Button type="submit" size="sm">
                        <Search className="mr-1.5 h-3.5 w-3.5" />
                        Apply
                    </Button>
                    {hasFilters && (
                        <Button type="button" variant="ghost" size="sm" onClick={clearFilters} className="text-slate-500 hover:text-slate-700">
                            <X className="mr-1 h-3.5 w-3.5" />
                            Clear
                        </Button>
                    )}
                </form>
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                {/* Partner Balances */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-0">
                        <CardTitle className="flex items-center justify-between text-base">
                            <span className="flex items-center gap-2 text-slate-900">
                                <CreditCard className="h-4 w-4 text-slate-400" />
                                Partner Balances
                                {props.partners.length > 0 && (
                                    <span className="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                        {props.partners.length}
                                    </span>
                                )}
                            </span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {props.partners.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-10">
                                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                    <Wallet className="h-6 w-6 text-slate-400" />
                                </div>
                                <p className="mt-3 text-sm text-slate-500">All partners are settled.</p>
                            </div>
                        ) : (
                            <div className="divide-y divide-slate-100">
                                {props.partners.map((p) => (
                                    <a
                                        key={p.id}
                                        href={`/admin/partners/${p.id}`}
                                        className="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-slate-50"
                                    >
                                        <div className="flex items-center gap-3">
                                            <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-600">
                                                {p.name
                                                    .split(' ')
                                                    .map((w) => w[0])
                                                    .join('')
                                                    .slice(0, 2)
                                                    .toUpperCase()}
                                            </div>
                                            <span className="text-sm font-medium text-slate-900">{p.name}</span>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <span className="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">
                                                &euro; {Number(p.outstanding_balance).toFixed(2)}
                                            </span>
                                            <ChevronRight className="h-4 w-4 text-slate-300" />
                                        </div>
                                    </a>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Unpaid Bookings */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-0">
                        <CardTitle className="flex items-center justify-between text-base">
                            <span className="flex items-center gap-2 text-slate-900">
                                <Receipt className="h-4 w-4 text-slate-400" />
                                Unpaid Bookings
                                {props.unpaidBookingsCount > 0 && (
                                    <span className="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                        {props.unpaidBookingsCount}
                                    </span>
                                )}
                            </span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {props.unpaidBookings.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-10">
                                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                    <Inbox className="h-6 w-6 text-slate-400" />
                                </div>
                                <p className="mt-3 text-sm text-slate-500">No unpaid bookings.</p>
                            </div>
                        ) : (
                            <div className="divide-y divide-slate-100">
                                {props.unpaidBookings.map((b) => (
                                    <a
                                        key={b.id}
                                        href={`/admin/bookings/${b.id}`}
                                        className="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-slate-50"
                                    >
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm font-medium text-slate-900">
                                                {b.booking_code}
                                            </p>
                                            <p className="text-xs text-slate-500">{b.partner_name}</p>
                                        </div>
                                        <div className="ml-4 flex items-center gap-2">
                                            <span className="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">
                                                &euro; {Number(b.balance_due).toFixed(2)}
                                            </span>
                                            <ChevronRight className="h-4 w-4 text-slate-300" />
                                        </div>
                                    </a>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Transactions Table */}
            <Card className="mt-6 border-slate-200 shadow-sm">
                <CardHeader className="pb-0">
                    <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                        <ArrowUpRight className="h-4 w-4 text-slate-400" />
                        Transactions
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    {props.transactions.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-10">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                <Inbox className="h-6 w-6 text-slate-400" />
                            </div>
                            <p className="mt-3 text-sm text-slate-500">No transactions in this period.</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-slate-100">
                                        <th className="bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                            Date
                                        </th>
                                        <th className="bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                            Type
                                        </th>
                                        <th className="bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                            Partner
                                        </th>
                                        <th className="bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                            Description
                                        </th>
                                        <th className="bg-slate-50 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                            Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {props.transactions.map((t, i) => {
                                        const config = typeConfig[t.type] ?? typeConfig.booking;
                                        const TypeIcon = config.icon;
                                        return (
                                            <tr key={i} className="transition-colors hover:bg-slate-50/50">
                                                <td className="whitespace-nowrap px-4 py-3.5 text-sm text-slate-600">
                                                    {t.date}
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    <span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium capitalize ring-1 ring-inset ${config.bg} ${config.text} ${config.ring}`}>
                                                        <TypeIcon className="h-3 w-3" />
                                                        {t.type}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3.5 text-sm font-medium text-slate-900">
                                                    {t.partner_name}
                                                </td>
                                                <td className="px-4 py-3.5 text-sm text-slate-500">
                                                    {t.description}
                                                </td>
                                                <td className="whitespace-nowrap px-4 py-3.5 text-right text-sm font-semibold text-slate-900">
                                                    &euro; {Number(t.amount).toFixed(2)}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    )}
                </CardContent>
            </Card>
        </AdminLayout>
    );
}
