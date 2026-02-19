import {
    ArrowLeft,
    Edit,
    Ship,
    CalendarDays,
    Clock,
    Users,
    Tag,
    ChevronRight,
    Inbox,
} from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { type Tour } from '@/types';

interface TourShowProps {
    tour: Tour;
}

export default function TourShow({ tour }: TourShowProps) {
    return (
        <AdminLayout
            pageTitle={tour.name}
            breadcrumbs={[
                { label: 'Tours', href: '/admin/tours' },
                { label: tour.name },
            ]}
            toolbarActions={
                <div className="flex gap-2">
                    <Button variant="outline" size="sm" asChild>
                        <a href={`/admin/tours/${tour.id}/edit`}>
                            <Edit className="mr-2 h-4 w-4" />
                            Edit
                        </a>
                    </Button>
                </div>
            }
        >
            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    {/* Tour Details */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="pb-4">
                            <CardTitle className="flex items-center justify-between text-base">
                                <span className="flex items-center gap-2 text-slate-900">
                                    <Ship className="h-4 w-4 text-slate-400" />
                                    Tour Details
                                </span>
                                <Badge variant={tour.is_active ? 'default' : 'secondary'}>
                                    {tour.is_active ? 'Active' : 'Inactive'}
                                </Badge>
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                <div className="flex items-start gap-3">
                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50">
                                        <Tag className="h-4 w-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <dt className="text-xs font-medium text-slate-500">Code</dt>
                                        <dd className="mt-0.5 text-sm font-medium text-slate-900">{tour.code}</dd>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50">
                                        <Users className="h-4 w-4 text-violet-600" />
                                    </div>
                                    <div>
                                        <dt className="text-xs font-medium text-slate-500">Default Capacity</dt>
                                        <dd className="mt-0.5 text-sm font-medium text-slate-900">{tour.default_capacity} pax</dd>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                                        <CalendarDays className="h-4 w-4 text-emerald-600" />
                                    </div>
                                    <div>
                                        <dt className="text-xs font-medium text-slate-500">Season</dt>
                                        <dd className="mt-0.5 text-sm text-slate-900">{tour.seasonality_range}</dd>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50">
                                        <Clock className="h-4 w-4 text-amber-600" />
                                    </div>
                                    <div>
                                        <dt className="text-xs font-medium text-slate-500">Cutoff Hours</dt>
                                        <dd className="mt-0.5 text-sm text-slate-900">{tour.cutoff_hours}h before departure</dd>
                                    </div>
                                </div>
                            </div>
                            {tour.description && (
                                <div className="mt-5 border-t border-slate-100 pt-5">
                                    <dt className="text-xs font-medium text-slate-500">Description</dt>
                                    <dd className="mt-1 text-sm text-slate-700">{tour.description}</dd>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Upcoming Departures */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="pb-0">
                            <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                                <CalendarDays className="h-4 w-4 text-slate-400" />
                                Upcoming Departures
                                {tour.departures && tour.departures.length > 0 && (
                                    <span className="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                        {tour.departures.length}
                                    </span>
                                )}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {!tour.departures || tour.departures.length === 0 ? (
                                <div className="flex flex-col items-center justify-center py-10">
                                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                        <Inbox className="h-6 w-6 text-slate-400" />
                                    </div>
                                    <p className="mt-3 text-sm text-slate-500">No upcoming departures.</p>
                                </div>
                            ) : (
                                <div className="divide-y divide-slate-100">
                                    {tour.departures.map((d) => {
                                        const pct = d.capacity > 0 ? (d.booked_seats / d.capacity) * 100 : 0;
                                        const isFull = pct >= 100;
                                        const isNearFull = pct >= 80 && !isFull;
                                        return (
                                            <a
                                                key={d.id}
                                                href={`/admin/departures/${d.id}`}
                                                className="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-slate-50"
                                            >
                                                <div>
                                                    <p className="text-sm font-medium text-slate-900">
                                                        {d.date} <span className="text-slate-500">at {d.time}</span>
                                                    </p>
                                                    <p className="text-xs text-slate-500">
                                                        {d.booked_seats}/{d.capacity} booked
                                                    </p>
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize ring-1 ring-inset ${
                                                        d.status === 'open'
                                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                                            : d.status === 'closed'
                                                              ? 'bg-slate-100 text-slate-600 ring-slate-500/20'
                                                              : 'bg-red-50 text-red-700 ring-red-600/20'
                                                    }`}>
                                                        {d.status}
                                                    </span>
                                                    {(isFull || isNearFull) && (
                                                        <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset ${
                                                            isFull
                                                                ? 'bg-red-50 text-red-700 ring-red-600/20'
                                                                : 'bg-amber-50 text-amber-700 ring-amber-600/20'
                                                        }`}>
                                                            {isFull ? 'Full' : 'Near Full'}
                                                        </span>
                                                    )}
                                                    <ChevronRight className="h-4 w-4 text-slate-300" />
                                                </div>
                                            </a>
                                        );
                                    })}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Sidebar */}
                <div>
                    <Button variant="outline" className="w-full border-slate-200 text-slate-600 hover:bg-slate-50" asChild>
                        <a href="/admin/tours">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Tours
                        </a>
                    </Button>
                </div>
            </div>
        </AdminLayout>
    );
}
