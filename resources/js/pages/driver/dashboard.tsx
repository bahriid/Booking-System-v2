import { router } from '@inertiajs/react';
import { Calendar, Clock, Users as UsersIcon, MapPin, FileText, Download, Loader2, Anchor } from 'lucide-react';
import { useState } from 'react';
import axios from 'axios';
import DriverLayout from '@/layouts/driver-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Pagination } from '@/components/pagination';
import { PaxBadges } from '@/components/booking/pax-badges';
import { type PaginatedData, type TourDeparture } from '@/types';

interface ShiftData extends TourDeparture {
    total_passengers: number;
    pax_counts: Record<string, number>;
    pickup_summary?: Record<string, number>;
}

interface DriverDashboardProps {
    selectedDate: string;
    todaysShifts: PaginatedData<ShiftData>;
    upcomingShifts: PaginatedData<ShiftData>;
}

function formatDate(dateStr: string): string {
    const date = new Date(dateStr + 'T00:00:00');
    return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
}

function formatShortDate(dateStr: string): string {
    const date = new Date(dateStr + 'T00:00:00');
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    const target = new Date(dateStr + 'T00:00:00');
    if (target.getTime() === tomorrow.getTime()) return 'Tomorrow';

    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
}

export default function DriverDashboard({ selectedDate, todaysShifts, upcomingShifts }: DriverDashboardProps) {
    const [manifestOpen, setManifestOpen] = useState(false);
    const [manifestHtml, setManifestHtml] = useState('');
    const [manifestLoading, setManifestLoading] = useState(false);
    const [manifestTitle, setManifestTitle] = useState('');
    const [manifestDepartureId, setManifestDepartureId] = useState<number | null>(null);

    function handleDateChange(newDate: string) {
        router.get('/driver', { date: newDate }, { preserveState: true });
    }

    async function openManifest(departureId: number, tourName: string) {
        setManifestOpen(true);
        setManifestLoading(true);
        setManifestTitle(tourName);
        setManifestDepartureId(departureId);
        setManifestHtml('');

        try {
            const response = await axios.get(`/driver/departures/${departureId}/manifest`);
            setManifestHtml(response.data);
        } catch {
            setManifestHtml('<p class="text-red-500">Failed to load manifest.</p>');
        } finally {
            setManifestLoading(false);
        }
    }

    return (
        <DriverLayout
            pageTitle="My Shifts"
            toolbarActions={
                <div className="flex items-center gap-2">
                    <Calendar className="h-4 w-4 text-slate-400" />
                    <Input
                        type="date"
                        value={selectedDate}
                        onChange={(e) => handleDateChange(e.target.value)}
                        className="w-auto border-slate-200"
                    />
                </div>
            }
        >
            {/* Today's Shifts */}
            <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div className="flex items-center gap-3 border-b border-slate-100 bg-slate-50 px-6 py-3">
                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100">
                        <Calendar className="h-4 w-4 text-sky-600" />
                    </div>
                    <div>
                        <h3 className="text-sm font-semibold text-slate-900">Today's Shifts</h3>
                        <p className="text-xs text-slate-500">{formatDate(selectedDate)}</p>
                    </div>
                </div>
                <div className="p-5">
                    {todaysShifts.data.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-12">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                <Anchor className="h-6 w-6 text-slate-400" />
                            </div>
                            <p className="mt-3 text-sm font-medium text-slate-900">No shifts assigned</p>
                            <p className="mt-1 text-sm text-slate-500">No shifts scheduled for this date.</p>
                        </div>
                    ) : (
                        <>
                            <div className="grid gap-4 sm:grid-cols-2">
                                {todaysShifts.data.map((shift) => (
                                    <div key={shift.id} className="rounded-lg border border-slate-200 bg-white p-5 transition-shadow hover:shadow-sm">
                                        <div className="mb-3 flex items-start justify-between">
                                            <div>
                                                <h4 className="font-semibold text-slate-900">{shift.tour?.name}</h4>
                                                <p className="text-xs text-slate-400">{shift.tour?.code}</p>
                                            </div>
                                            <Badge className="bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-600/20">
                                                <Clock className="mr-1 h-3 w-3" />
                                                {shift.time}
                                            </Badge>
                                        </div>

                                        <div className="mb-3 flex items-center gap-4 text-sm">
                                            <div className="flex items-center gap-1.5">
                                                <UsersIcon className="h-4 w-4 text-slate-400" />
                                                <span className="font-semibold text-slate-900">{shift.total_passengers} pax</span>
                                            </div>
                                            <PaxBadges
                                                adults={shift.pax_counts?.adult ?? 0}
                                                children={shift.pax_counts?.child ?? 0}
                                                infants={shift.pax_counts?.infant ?? 0}
                                            />
                                        </div>

                                        {shift.pickup_summary && Object.keys(shift.pickup_summary).length > 0 && (
                                            <div className="mb-4 space-y-1.5 rounded-lg bg-slate-50 p-3">
                                                {Object.entries(shift.pickup_summary).map(([point, count]) => (
                                                    <div key={point} className="flex items-center justify-between text-xs">
                                                        <span className="flex items-center gap-1.5 text-slate-600">
                                                            <MapPin className="h-3 w-3 text-slate-400" />
                                                            {point}
                                                        </span>
                                                        <span className="inline-flex items-center rounded-full bg-white px-2 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                                                            {count}
                                                        </span>
                                                    </div>
                                                ))}
                                            </div>
                                        )}

                                        <Button
                                            variant="outline"
                                            size="sm"
                                            className="w-full border-slate-200 text-slate-700 hover:bg-slate-50"
                                            onClick={() => openManifest(shift.id, shift.tour?.name ?? 'Manifest')}
                                        >
                                            <FileText className="mr-2 h-4 w-4" />
                                            View Manifest
                                        </Button>
                                    </div>
                                ))}
                            </div>
                            <div className="mt-4">
                                <Pagination
                                    links={todaysShifts.links}
                                    from={todaysShifts.from}
                                    to={todaysShifts.to}
                                    total={todaysShifts.total}
                                />
                            </div>
                        </>
                    )}
                </div>
            </div>

            {/* Upcoming Shifts */}
            <div className="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div className="flex items-center gap-3 border-b border-slate-100 bg-slate-50 px-6 py-3">
                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100">
                        <Calendar className="h-4 w-4 text-sky-600" />
                    </div>
                    <div>
                        <h3 className="text-sm font-semibold text-slate-900">Upcoming Shifts</h3>
                        <p className="text-xs text-slate-500">Next 7 days</p>
                    </div>
                </div>
                <div className="p-5">
                    {upcomingShifts.data.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-12">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                <Calendar className="h-6 w-6 text-slate-400" />
                            </div>
                            <p className="mt-3 text-sm font-medium text-slate-900">No upcoming shifts</p>
                            <p className="mt-1 text-sm text-slate-500">No shifts scheduled in the next 7 days.</p>
                        </div>
                    ) : (
                        <>
                            <div className="overflow-x-auto rounded-lg border border-slate-200">
                                <table className="w-full">
                                    <thead>
                                        <tr className="bg-slate-50">
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Date</th>
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Time</th>
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Tour</th>
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Passengers</th>
                                            <th className="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {upcomingShifts.data.map((shift) => (
                                            <tr key={shift.id} className="border-b border-slate-100 transition-colors last:border-b-0 hover:bg-slate-50/50">
                                                <td className="px-4 py-3.5 text-sm font-medium text-slate-900">
                                                    {formatShortDate(shift.date)}
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    <Badge className="bg-slate-100 text-xs text-slate-700 ring-1 ring-inset ring-slate-200">
                                                        {shift.time}
                                                    </Badge>
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    <div>
                                                        <p className="text-sm font-medium text-slate-900">{shift.tour?.name}</p>
                                                        <p className="text-xs text-slate-400">{shift.tour?.code}</p>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    <div className="flex items-center gap-2">
                                                        <span className="text-sm font-semibold text-slate-900">{shift.total_passengers}</span>
                                                        <PaxBadges
                                                            adults={shift.pax_counts?.adult ?? 0}
                                                            children={shift.pax_counts?.child ?? 0}
                                                            infants={shift.pax_counts?.infant ?? 0}
                                                        />
                                                    </div>
                                                </td>
                                                <td className="px-4 py-3.5 text-right">
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        className="text-slate-500 hover:text-slate-700"
                                                        onClick={() => openManifest(shift.id, shift.tour?.name ?? 'Manifest')}
                                                    >
                                                        <FileText className="mr-1 h-4 w-4" />
                                                        Manifest
                                                    </Button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <div className="mt-4">
                                <Pagination
                                    links={upcomingShifts.links}
                                    from={upcomingShifts.from}
                                    to={upcomingShifts.to}
                                    total={upcomingShifts.total}
                                />
                            </div>
                        </>
                    )}
                </div>
            </div>

            {/* Manifest Modal */}
            <Dialog open={manifestOpen} onOpenChange={setManifestOpen}>
                <DialogContent className="max-w-2xl">
                    <DialogHeader>
                        <DialogTitle className="text-slate-900">Manifest -- {manifestTitle}</DialogTitle>
                    </DialogHeader>
                    <div className="max-h-[60vh] overflow-y-auto">
                        {manifestLoading ? (
                            <div className="flex flex-col items-center justify-center py-12">
                                <Loader2 className="h-8 w-8 animate-spin text-slate-400" />
                                <p className="mt-3 text-sm text-slate-500">Loading manifest...</p>
                            </div>
                        ) : (
                            <div dangerouslySetInnerHTML={{ __html: manifestHtml }} />
                        )}
                    </div>
                    <DialogFooter>
                        {manifestDepartureId && (
                            <Button variant="outline" asChild className="border-slate-200 text-slate-700 hover:bg-slate-50">
                                <a href={`/driver/departures/${manifestDepartureId}/manifest/pdf`} target="_blank">
                                    <Download className="mr-2 h-4 w-4" />
                                    Download PDF
                                </a>
                            </Button>
                        )}
                        <Button variant="ghost" onClick={() => setManifestOpen(false)} className="text-slate-600">
                            Close
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </DriverLayout>
    );
}
