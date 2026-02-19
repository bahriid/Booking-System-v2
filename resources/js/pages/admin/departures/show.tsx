import { router } from '@inertiajs/react';
import {
    ArrowLeft,
    Edit,
    Save,
    Ship,
    CalendarDays,
    Clock,
    Users,
    UserCheck,
    Tag,
    StickyNote,
    ChevronRight,
    Inbox,
} from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FormField } from '@/components/form-field';
import { StatusBadge } from '@/components/booking/status-badge';
import { PaxBadges } from '@/components/booking/pax-badges';
import { type TourDeparture, type User } from '@/types';

interface DepartureShowProps {
    departure: TourDeparture;
    drivers: User[];
}

const statusConfig: Record<string, { bg: string; text: string; ring: string }> = {
    open: { bg: 'bg-emerald-50', text: 'text-emerald-700', ring: 'ring-emerald-600/20' },
    closed: { bg: 'bg-slate-100', text: 'text-slate-600', ring: 'ring-slate-500/20' },
    cancelled: { bg: 'bg-red-50', text: 'text-red-700', ring: 'ring-red-600/20' },
};

export default function DepartureShow({ departure, drivers }: DepartureShowProps) {
    const [editing, setEditing] = useState(false);
    const [driverId, setDriverId] = useState(String(departure.driver_id ?? ''));
    const [capacity, setCapacity] = useState(String(departure.capacity));
    const [status, setStatus] = useState(departure.status);
    const [time, setTime] = useState(departure.time ?? '');
    const [notes, setNotes] = useState(departure.notes ?? '');
    const [processing, setProcessing] = useState(false);

    function handleUpdate(e: FormEvent) {
        e.preventDefault();
        setProcessing(true);
        router.put(
            `/admin/departures/${departure.id}`,
            { driver_id: driverId || null, capacity, status, time, notes },
            { onFinish: () => { setProcessing(false); setEditing(false); } },
        );
    }

    const statusStyle = statusConfig[departure.status] ?? statusConfig.closed;
    const capacityPct = departure.capacity > 0 ? (departure.booked_seats / departure.capacity) * 100 : 0;
    const isFull = capacityPct >= 100;
    const isNearFull = capacityPct >= 80 && !isFull;

    return (
        <AdminLayout
            pageTitle={`${departure.tour?.name} -- ${departure.date}`}
            breadcrumbs={[
                { label: 'Calendar', href: '/admin/calendar' },
                { label: `${departure.date} ${departure.time}` },
            ]}
            toolbarActions={
                !editing ? (
                    <Button variant="outline" size="sm" onClick={() => setEditing(true)}>
                        <Edit className="mr-2 h-4 w-4" />
                        Edit Details
                    </Button>
                ) : undefined
            }
        >
            <div className="grid gap-6 lg:grid-cols-3">
                {/* Main Content */}
                <div className="space-y-6 lg:col-span-2">
                    {/* Departure Details Card */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="pb-4">
                            <CardTitle className="flex items-center justify-between text-base">
                                <span className="flex items-center gap-2 text-slate-900">
                                    <Ship className="h-4 w-4 text-slate-400" />
                                    Departure Details
                                </span>
                                <span
                                    className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1 ring-inset ${statusStyle.bg} ${statusStyle.text} ${statusStyle.ring}`}
                                >
                                    {departure.status}
                                </span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {editing ? (
                                <form onSubmit={handleUpdate} className="space-y-5">
                                    <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-5">
                                        <div className="grid gap-5 sm:grid-cols-2">
                                            <FormField label="Status">
                                                <Select value={status} onValueChange={setStatus}>
                                                    <SelectTrigger className="w-full">
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="open">Open</SelectItem>
                                                        <SelectItem value="closed">Closed</SelectItem>
                                                        <SelectItem value="cancelled">Cancelled</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </FormField>
                                            <FormField label="Time">
                                                <Input
                                                    type="time"
                                                    value={time}
                                                    onChange={(e) => setTime(e.target.value)}
                                                />
                                            </FormField>
                                            <FormField label="Driver">
                                                <Select value={driverId || 'none'} onValueChange={(v) => setDriverId(v === 'none' ? '' : v)}>
                                                    <SelectTrigger className="w-full">
                                                        <SelectValue placeholder="No driver" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="none">No driver</SelectItem>
                                                        {drivers.map((d) => (
                                                            <SelectItem key={d.id} value={String(d.id)}>
                                                                {d.name}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </FormField>
                                            <FormField label="Capacity">
                                                <Input
                                                    type="number"
                                                    value={capacity}
                                                    onChange={(e) => setCapacity(e.target.value)}
                                                />
                                            </FormField>
                                        </div>
                                        <div className="mt-5">
                                            <FormField label="Notes">
                                                <Textarea
                                                    value={notes}
                                                    onChange={(e) => setNotes(e.target.value)}
                                                    rows={3}
                                                    placeholder="Add notes about this departure..."
                                                />
                                            </FormField>
                                        </div>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button type="submit" disabled={processing}>
                                            <Save className="mr-2 h-4 w-4" />
                                            {processing ? 'Saving...' : 'Save Changes'}
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            onClick={() => setEditing(false)}
                                        >
                                            Cancel
                                        </Button>
                                    </div>
                                </form>
                            ) : (
                                <div className="space-y-0 divide-y divide-slate-100">
                                    <div className="grid gap-x-6 gap-y-5 py-4 first:pt-0 sm:grid-cols-2">
                                        <div className="flex items-start gap-3">
                                            <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50">
                                                <Ship className="h-4 w-4 text-blue-600" />
                                            </div>
                                            <div>
                                                <dt className="text-xs font-medium text-slate-500">Tour</dt>
                                                <dd className="mt-0.5 text-sm font-medium text-slate-900">
                                                    {departure.tour?.name}
                                                </dd>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3">
                                            <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50">
                                                <CalendarDays className="h-4 w-4 text-violet-600" />
                                            </div>
                                            <div>
                                                <dt className="text-xs font-medium text-slate-500">Date & Time</dt>
                                                <dd className="mt-0.5 text-sm font-medium text-slate-900">
                                                    {departure.date}
                                                    <span className="ml-1 text-slate-500">at {departure.time}</span>
                                                </dd>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3">
                                            <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                                                <UserCheck className="h-4 w-4 text-emerald-600" />
                                            </div>
                                            <div>
                                                <dt className="text-xs font-medium text-slate-500">Driver</dt>
                                                <dd className="mt-0.5 text-sm text-slate-900">
                                                    {departure.driver?.name ?? (
                                                        <span className="text-slate-400">Not assigned</span>
                                                    )}
                                                </dd>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3">
                                            <div className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ${isFull ? 'bg-red-50' : isNearFull ? 'bg-amber-50' : 'bg-emerald-50'}`}>
                                                <Users className={`h-4 w-4 ${isFull ? 'text-red-600' : isNearFull ? 'text-amber-600' : 'text-emerald-600'}`} />
                                            </div>
                                            <div>
                                                <dt className="text-xs font-medium text-slate-500">Capacity</dt>
                                                <dd className="mt-0.5 text-sm text-slate-900">
                                                    <span className="font-medium">{departure.booked_seats}</span>
                                                    <span className="text-slate-400"> / {departure.capacity}</span>
                                                    <span className={`ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset ${
                                                        isFull
                                                            ? 'bg-red-50 text-red-700 ring-red-600/20'
                                                            : isNearFull
                                                              ? 'bg-amber-50 text-amber-700 ring-amber-600/20'
                                                              : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                                    }`}>
                                                        {departure.remaining_seats} remaining
                                                    </span>
                                                </dd>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3">
                                            <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50">
                                                <Tag className="h-4 w-4 text-amber-600" />
                                            </div>
                                            <div>
                                                <dt className="text-xs font-medium text-slate-500">Season</dt>
                                                <dd className="mt-0.5">
                                                    <span className="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium capitalize text-slate-700 ring-1 ring-inset ring-slate-200">
                                                        {departure.season}
                                                    </span>
                                                </dd>
                                            </div>
                                        </div>
                                    </div>

                                    {departure.notes && (
                                        <div className="py-4">
                                            <div className="flex items-start gap-3">
                                                <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                                    <StickyNote className="h-4 w-4 text-slate-500" />
                                                </div>
                                                <div>
                                                    <dt className="text-xs font-medium text-slate-500">Notes</dt>
                                                    <dd className="mt-0.5 text-sm text-slate-700">
                                                        {departure.notes}
                                                    </dd>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Bookings Card */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="pb-0">
                            <CardTitle className="flex items-center justify-between text-base">
                                <span className="flex items-center gap-2 text-slate-900">
                                    <Clock className="h-4 w-4 text-slate-400" />
                                    Bookings
                                    {departure.bookings && departure.bookings.length > 0 && (
                                        <span className="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                            {departure.bookings.length}
                                        </span>
                                    )}
                                </span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {!departure.bookings || departure.bookings.length === 0 ? (
                                <div className="flex flex-col items-center justify-center py-10">
                                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                        <Inbox className="h-6 w-6 text-slate-400" />
                                    </div>
                                    <p className="mt-3 text-sm text-slate-500">
                                        No bookings for this departure yet.
                                    </p>
                                </div>
                            ) : (
                                <div className="divide-y divide-slate-100">
                                    {departure.bookings.map((b) => (
                                        <a
                                            key={b.id}
                                            href={`/admin/bookings/${b.id}`}
                                            className="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-slate-50"
                                        >
                                            <div className="min-w-0 flex-1">
                                                <p className="truncate text-sm font-medium text-slate-900">
                                                    {b.booking_code}
                                                </p>
                                                <p className="text-xs text-slate-500">
                                                    {b.partner?.name}
                                                </p>
                                            </div>
                                            <div className="ml-4 flex items-center gap-2">
                                                <PaxBadges
                                                    adults={b.adults_count}
                                                    children={b.children_count}
                                                    infants={b.infants_count}
                                                />
                                                <StatusBadge status={b.status} />
                                                <ChevronRight className="h-4 w-4 text-slate-300" />
                                            </div>
                                        </a>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Sidebar */}
                <div className="space-y-4">
                    <Card className="border-slate-200 shadow-sm">
                        <CardContent className="p-5">
                            <h4 className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Quick Stats
                            </h4>
                            <div className="mt-4 space-y-4">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-slate-500">Booked</span>
                                    <span className="text-sm font-bold text-slate-900">
                                        {departure.booked_seats} pax
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-slate-500">Capacity</span>
                                    <span className="text-sm font-medium text-slate-700">
                                        {departure.capacity}
                                    </span>
                                </div>
                                <div>
                                    <div className="flex items-center justify-between text-sm">
                                        <span className="text-slate-500">Occupancy</span>
                                        <span className={`font-medium ${isFull ? 'text-red-600' : isNearFull ? 'text-amber-600' : 'text-emerald-600'}`}>
                                            {Math.round(capacityPct)}%
                                        </span>
                                    </div>
                                    <div className="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div
                                            className={`h-full rounded-full transition-all ${
                                                isFull ? 'bg-red-500' : isNearFull ? 'bg-amber-500' : 'bg-emerald-500'
                                            }`}
                                            style={{ width: `${Math.min(capacityPct, 100)}%` }}
                                        />
                                    </div>
                                </div>
                                <div className="flex items-center justify-between border-t border-slate-100 pt-4">
                                    <span className="text-sm text-slate-500">Remaining</span>
                                    <span className="text-sm font-bold text-slate-900">
                                        {departure.remaining_seats} seats
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Button variant="outline" className="w-full border-slate-200 text-slate-600 hover:bg-slate-50" asChild>
                        <a href="/admin/calendar">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Calendar
                        </a>
                    </Button>
                </div>
            </div>
        </AdminLayout>
    );
}
