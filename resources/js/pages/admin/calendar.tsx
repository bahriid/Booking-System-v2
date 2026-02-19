import { router } from '@inertiajs/react';
import { useRef, useState, useCallback, type FormEvent } from 'react';
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import type { EventClickArg, EventContentArg } from '@fullcalendar/core';
import AdminLayout from '@/layouts/admin-layout';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { FormField } from '@/components/form-field';
import { Ship, PlusCircle, XCircle, Users, Clock, Tag, MapPin, StickyNote, ChevronRight, CalendarDays, Anchor } from 'lucide-react';
import { type Tour } from '@/types';

interface CalendarProps {
    tours: Tour[];
    selectedTourId: number | null;
}

const dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// Status colors for the legend
const statusColors = [
    { color: '#3b82f6', label: 'Available', desc: 'Seats open' },
    { color: '#22c55e', label: 'Almost Full', desc: '≤ 5 seats' },
    { color: '#eab308', label: 'Full', desc: 'No seats' },
    { color: '#7c3aed', label: 'Closed', desc: 'Not available' },
    { color: '#ef4444', label: 'Cancelled', desc: 'Removed' },
];

function renderEventContent(eventInfo: EventContentArg) {
    const props = eventInfo.event.extendedProps;
    const booked = props.booked ?? 0;
    const capacity = props.capacity ?? 0;
    const pct = capacity > 0 ? Math.round((booked / capacity) * 100) : 0;

    // Month view — compact pill
    if (eventInfo.view.type === 'dayGridMonth') {
        return (
            <div className="fc-custom-event flex w-full items-center gap-1 overflow-hidden px-1 py-0.5">
                <span className="truncate text-[10px] font-semibold leading-tight">
                    {props.tour_code ?? eventInfo.event.title.split(' ')[0]}
                </span>
                <span className="ml-auto shrink-0 text-[9px] font-medium opacity-80">
                    {booked}/{capacity}
                </span>
            </div>
        );
    }

    // Week / time grid view — richer card
    if (eventInfo.view.type.startsWith('timeGrid')) {
        return (
            <div className="fc-custom-event flex h-full flex-col justify-between overflow-hidden p-1">
                <div className="truncate text-[10px] font-bold leading-tight">
                    {props.tour_code}
                </div>
                <div className="mt-auto flex items-center gap-1">
                    <div className="h-1 flex-1 overflow-hidden rounded-full bg-white/30">
                        <div
                            className="h-full rounded-full bg-white/80"
                            style={{ width: `${Math.min(pct, 100)}%` }}
                        />
                    </div>
                    <span className="text-[9px] font-medium opacity-80">{booked}/{capacity}</span>
                </div>
            </div>
        );
    }

    // List view
    return (
        <div className="fc-custom-event flex items-center gap-3">
            <span className="font-semibold">{props.tour_name}</span>
            <span className="text-slate-500">{props.time}</span>
            <span className="ml-auto rounded bg-slate-100 px-1.5 py-0.5 text-xs font-semibold text-slate-700">
                {booked}/{capacity}
            </span>
            {props.driver_name && (
                <span className="text-xs text-slate-400">{props.driver_name}</span>
            )}
        </div>
    );
}

export default function CalendarPage({ tours, selectedTourId }: CalendarProps) {
    const calendarRef = useRef<FullCalendar>(null);
    const [tourFilter, setTourFilter] = useState(String(selectedTourId ?? 'all'));

    // Bulk create modal
    const [createOpen, setCreateOpen] = useState(false);
    const [createData, setCreateData] = useState({
        tour_id: '',
        start_date: '',
        end_date: '',
        days: [1, 2, 3, 4, 5] as number[],
        time: '09:00',
        capacity: '',
        season: 'high',
        notes: '',
    });
    const [createProcessing, setCreateProcessing] = useState(false);

    // Bulk close modal
    const [closeOpen, setCloseOpen] = useState(false);
    const [closeData, setCloseData] = useState({
        tour_id: '',
        start_date: '',
        end_date: '',
        reason: '',
        notify_partners: true,
    });
    const [closeProcessing, setCloseProcessing] = useState(false);

    // Event detail
    const [selectedEvent, setSelectedEvent] = useState<{
        id: string;
        title: string;
        tour_name: string;
        tour_code: string;
        time: string;
        capacity: number;
        booked: number;
        remaining: number;
        status: string;
        status_label: string;
        season: string;
        driver_name: string | null;
        notes: string | null;
    } | null>(null);

    function handleTourChange(value: string) {
        setTourFilter(value);
        window.location.href = value !== 'all' ? `/admin/calendar?tour=${value}` : '/admin/calendar';
    }

    const handleEventClick = useCallback((info: EventClickArg) => {
        const p = info.event.extendedProps;
        setSelectedEvent({
            id: info.event.id,
            title: info.event.title,
            tour_name: p.tour_name,
            tour_code: p.tour_code,
            time: p.time,
            capacity: p.capacity,
            booked: p.booked,
            remaining: p.remaining,
            status: p.status,
            status_label: p.status_label,
            season: p.season,
            driver_name: p.driver_name,
            notes: p.notes,
        });
    }, []);

    function handleBulkCreate(e: FormEvent) {
        e.preventDefault();
        setCreateProcessing(true);
        router.post('/admin/departures/bulk', createData, {
            onFinish: () => {
                setCreateProcessing(false);
                setCreateOpen(false);
            },
        });
    }

    function handleBulkClose(e: FormEvent) {
        e.preventDefault();
        setCloseProcessing(true);
        router.post('/admin/departures/bulk-close', closeData, {
            onFinish: () => {
                setCloseProcessing(false);
                setCloseOpen(false);
            },
        });
    }

    function toggleDay(day: number) {
        setCreateData((prev) => ({
            ...prev,
            days: prev.days.includes(day) ? prev.days.filter((d) => d !== day) : [...prev.days, day],
        }));
    }

    const eventsUrl = tourFilter !== 'all'
        ? `/admin/departures/events?tour=${tourFilter}`
        : '/admin/departures/events';

    const pct = selectedEvent && selectedEvent.capacity > 0
        ? Math.round((selectedEvent.booked / selectedEvent.capacity) * 100)
        : 0;

    return (
        <AdminLayout
            pageTitle="Calendar"
            breadcrumbs={[{ label: 'Calendar' }]}
            toolbarActions={
                <div className="flex items-center gap-2">
                    <Button variant="outline" size="sm" className="border-slate-200 text-slate-700 hover:bg-slate-50" onClick={() => setCloseOpen(true)}>
                        <XCircle className="mr-1.5 h-4 w-4 text-slate-500" />
                        Bulk Close
                    </Button>
                    <Button size="sm" onClick={() => setCreateOpen(true)}>
                        <PlusCircle className="mr-1.5 h-4 w-4" />
                        Bulk Create
                    </Button>
                    <select
                        value={tourFilter}
                        onChange={(e) => handleTourChange(e.target.value)}
                        className="h-9 rounded-md border border-slate-200 bg-white px-3 pr-8 text-sm text-slate-900"
                    >
                        <option value="all">All Tours</option>
                        {tours.map((t) => (
                            <option key={t.id} value={String(t.id)}>{t.name}</option>
                        ))}
                    </select>
                </div>
            }
        >
            {/* Legend */}
            <div className="mb-4 flex flex-wrap items-center gap-4">
                <span className="text-xs font-semibold uppercase tracking-wider text-slate-400">Status</span>
                {statusColors.map((s) => (
                    <div key={s.label} className="flex items-center gap-1.5">
                        <span className="h-2.5 w-2.5 rounded-full" style={{ backgroundColor: s.color }} />
                        <span className="text-xs font-medium text-slate-600">{s.label}</span>
                    </div>
                ))}
            </div>

            <Card className="border-slate-200 shadow-sm">
                <CardContent className="p-4 lg:p-6">
                    <div className="fc-magship">
                        <FullCalendar
                            ref={calendarRef}
                            plugins={[dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin]}
                            initialView="dayGridMonth"
                            headerToolbar={{
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,listWeek',
                            }}
                            events={{
                                url: eventsUrl,
                                method: 'GET',
                            }}
                            eventContent={renderEventContent}
                            eventClick={handleEventClick}
                            height="auto"
                            contentHeight={680}
                            eventTimeFormat={{
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                            }}
                            slotMinTime="06:00:00"
                            slotMaxTime="22:00:00"
                            firstDay={1}
                            navLinks={true}
                            editable={false}
                            dayMaxEvents={4}
                            eventDisplay="block"
                            eventClassNames="!rounded-md !border-0 !shadow-sm cursor-pointer transition-opacity hover:!opacity-90"
                            dayHeaderFormat={{ weekday: 'short' }}
                            buttonText={{
                                today: 'Today',
                                month: 'Month',
                                week: 'Week',
                                list: 'List',
                            }}
                        />
                    </div>
                </CardContent>
            </Card>

            {/* Event Detail Dialog */}
            <Dialog open={!!selectedEvent} onOpenChange={() => setSelectedEvent(null)}>
                <DialogContent className="gap-0 overflow-hidden p-0 sm:max-w-md">
                    {selectedEvent && (
                        <>
                            {/* Colored header */}
                            <div
                                className="px-6 pb-4 pt-6"
                                style={{
                                    background: `linear-gradient(135deg, ${
                                        selectedEvent.status === 'cancelled' ? '#ef4444' :
                                        selectedEvent.status === 'closed' ? '#7c3aed' :
                                        selectedEvent.remaining <= 0 ? '#eab308' :
                                        selectedEvent.remaining <= 5 ? '#22c55e' : '#3b82f6'
                                    }, ${
                                        selectedEvent.status === 'cancelled' ? '#dc2626' :
                                        selectedEvent.status === 'closed' ? '#6d28d9' :
                                        selectedEvent.remaining <= 0 ? '#ca8a04' :
                                        selectedEvent.remaining <= 5 ? '#16a34a' : '#2563eb'
                                    })`,
                                }}
                            >
                                <div className="flex items-start justify-between">
                                    <div>
                                        <p className="text-xs font-semibold uppercase tracking-wider text-white/70">
                                            {selectedEvent.tour_code}
                                        </p>
                                        <h3 className="mt-1 text-lg font-bold text-white">
                                            {selectedEvent.tour_name}
                                        </h3>
                                    </div>
                                    <span className="rounded-lg bg-white/20 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-white backdrop-blur-sm">
                                        {selectedEvent.status_label}
                                    </span>
                                </div>
                                {/* Capacity bar */}
                                <div className="mt-4">
                                    <div className="mb-1.5 flex items-center justify-between text-xs text-white/80">
                                        <span>{selectedEvent.booked} booked of {selectedEvent.capacity}</span>
                                        <span className="font-bold text-white">{pct}%</span>
                                    </div>
                                    <div className="h-2 overflow-hidden rounded-full bg-white/20">
                                        <div
                                            className="h-full rounded-full bg-white transition-all"
                                            style={{ width: `${Math.min(pct, 100)}%` }}
                                        />
                                    </div>
                                    <p className="mt-1.5 text-xs text-white/70">
                                        {selectedEvent.remaining > 0
                                            ? `${selectedEvent.remaining} seat${selectedEvent.remaining !== 1 ? 's' : ''} remaining`
                                            : 'No seats available'
                                        }
                                    </p>
                                </div>
                            </div>

                            {/* Details grid */}
                            <div className="grid grid-cols-2 gap-4 px-6 py-5">
                                <div className="flex items-center gap-3">
                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                        <Clock className="h-4 w-4 text-slate-500" />
                                    </div>
                                    <div>
                                        <p className="text-[10px] font-medium uppercase tracking-wider text-slate-400">Time</p>
                                        <p className="text-sm font-semibold text-slate-900">{selectedEvent.time}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-3">
                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                        <Tag className="h-4 w-4 text-slate-500" />
                                    </div>
                                    <div>
                                        <p className="text-[10px] font-medium uppercase tracking-wider text-slate-400">Season</p>
                                        <p className="text-sm font-semibold capitalize text-slate-900">{selectedEvent.season}</p>
                                    </div>
                                </div>
                                {selectedEvent.driver_name && (
                                    <div className="col-span-2 flex items-center gap-3">
                                        <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                            <Anchor className="h-4 w-4 text-slate-500" />
                                        </div>
                                        <div>
                                            <p className="text-[10px] font-medium uppercase tracking-wider text-slate-400">Driver</p>
                                            <p className="text-sm font-semibold text-slate-900">{selectedEvent.driver_name}</p>
                                        </div>
                                    </div>
                                )}
                                {selectedEvent.notes && (
                                    <div className="col-span-2">
                                        <div className="flex items-start gap-2 rounded-lg bg-slate-50 p-3">
                                            <StickyNote className="mt-0.5 h-3.5 w-3.5 shrink-0 text-slate-400" />
                                            <p className="text-sm text-slate-600">{selectedEvent.notes}</p>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Footer */}
                            <div className="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50/50 px-6 py-3">
                                <Button variant="ghost" size="sm" onClick={() => setSelectedEvent(null)} className="text-slate-500">
                                    Close
                                </Button>
                                <Button size="sm" asChild>
                                    <a href={`/admin/departures/${selectedEvent.id}`}>
                                        View Details
                                        <ChevronRight className="ml-1 h-3.5 w-3.5" />
                                    </a>
                                </Button>
                            </div>
                        </>
                    )}
                </DialogContent>
            </Dialog>

            {/* Bulk Create Dialog */}
            <Dialog open={createOpen} onOpenChange={setCreateOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle className="flex items-center gap-2 text-base">
                            <CalendarDays className="h-4 w-4 text-slate-400" />
                            Bulk Create Departures
                        </DialogTitle>
                        <DialogDescription>Create multiple departures for a date range.</DialogDescription>
                    </DialogHeader>
                    <form onSubmit={handleBulkCreate} className="space-y-4">
                        <FormField label="Tour">
                            <Select value={createData.tour_id} onValueChange={(v) => setCreateData((p) => ({ ...p, tour_id: v }))}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Select tour" />
                                </SelectTrigger>
                                <SelectContent>
                                    {tours.map((t) => (
                                        <SelectItem key={t.id} value={String(t.id)}>
                                            {t.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </FormField>
                        <div className="grid grid-cols-2 gap-4">
                            <FormField label="Start Date">
                                <Input
                                    type="date"
                                    value={createData.start_date}
                                    onChange={(e) => setCreateData((p) => ({ ...p, start_date: e.target.value }))}
                                    required
                                />
                            </FormField>
                            <FormField label="End Date">
                                <Input
                                    type="date"
                                    value={createData.end_date}
                                    onChange={(e) => setCreateData((p) => ({ ...p, end_date: e.target.value }))}
                                    required
                                />
                            </FormField>
                        </div>
                        <FormField label="Days of Week">
                            <div className="flex flex-wrap gap-1.5">
                                {dayLabels.map((label, i) => (
                                    <button
                                        key={i}
                                        type="button"
                                        onClick={() => toggleDay(i)}
                                        className={`h-8 w-10 rounded-md border text-xs font-semibold transition-all ${
                                            createData.days.includes(i)
                                                ? 'border-blue-500 bg-blue-500 text-white shadow-sm shadow-blue-500/25'
                                                : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50'
                                        }`}
                                    >
                                        {label}
                                    </button>
                                ))}
                            </div>
                        </FormField>
                        <div className="grid grid-cols-3 gap-4">
                            <FormField label="Time">
                                <Input
                                    type="time"
                                    value={createData.time}
                                    onChange={(e) => setCreateData((p) => ({ ...p, time: e.target.value }))}
                                    required
                                />
                            </FormField>
                            <FormField label="Capacity">
                                <Input
                                    type="number"
                                    min={1}
                                    value={createData.capacity}
                                    onChange={(e) => setCreateData((p) => ({ ...p, capacity: e.target.value }))}
                                    placeholder="e.g. 50"
                                    required
                                />
                            </FormField>
                            <FormField label="Season">
                                <Select value={createData.season} onValueChange={(v) => setCreateData((p) => ({ ...p, season: v }))}>
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="high">High</SelectItem>
                                        <SelectItem value="mid">Mid</SelectItem>
                                        <SelectItem value="low">Low</SelectItem>
                                    </SelectContent>
                                </Select>
                            </FormField>
                        </div>
                        <FormField label="Notes">
                            <Textarea
                                value={createData.notes}
                                onChange={(e) => setCreateData((p) => ({ ...p, notes: e.target.value }))}
                                rows={2}
                                placeholder="Optional notes..."
                            />
                        </FormField>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setCreateOpen(false)}>
                                Cancel
                            </Button>
                            <Button type="submit" disabled={createProcessing}>
                                {createProcessing ? 'Creating...' : 'Create Departures'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Bulk Close Dialog */}
            <Dialog open={closeOpen} onOpenChange={setCloseOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle className="flex items-center gap-2 text-base">
                            <XCircle className="h-4 w-4 text-red-500" />
                            Bulk Close Departures
                        </DialogTitle>
                        <DialogDescription>Close all departures within a date range.</DialogDescription>
                    </DialogHeader>
                    <form onSubmit={handleBulkClose} className="space-y-4">
                        <FormField label="Tour (optional)">
                            <Select value={closeData.tour_id || 'all'} onValueChange={(v) => setCloseData((p) => ({ ...p, tour_id: v === 'all' ? '' : v }))}>
                                <SelectTrigger>
                                    <SelectValue placeholder="All Tours" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Tours</SelectItem>
                                    {tours.map((t) => (
                                        <SelectItem key={t.id} value={String(t.id)}>
                                            {t.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </FormField>
                        <div className="grid grid-cols-2 gap-4">
                            <FormField label="Start Date">
                                <Input
                                    type="date"
                                    value={closeData.start_date}
                                    onChange={(e) => setCloseData((p) => ({ ...p, start_date: e.target.value }))}
                                    required
                                />
                            </FormField>
                            <FormField label="End Date">
                                <Input
                                    type="date"
                                    value={closeData.end_date}
                                    onChange={(e) => setCloseData((p) => ({ ...p, end_date: e.target.value }))}
                                    required
                                />
                            </FormField>
                        </div>
                        <FormField label="Reason">
                            <Textarea
                                value={closeData.reason}
                                onChange={(e) => setCloseData((p) => ({ ...p, reason: e.target.value }))}
                                rows={2}
                                placeholder="e.g. Bad weather, maintenance..."
                            />
                        </FormField>
                        <div className="flex items-center gap-2">
                            <Checkbox
                                id="notify"
                                checked={closeData.notify_partners}
                                onCheckedChange={(checked) => setCloseData((p) => ({ ...p, notify_partners: !!checked }))}
                            />
                            <label htmlFor="notify" className="text-sm text-slate-700">
                                Notify affected partners
                            </label>
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setCloseOpen(false)}>
                                Cancel
                            </Button>
                            <Button type="submit" variant="destructive" disabled={closeProcessing}>
                                {closeProcessing ? 'Closing...' : 'Close Departures'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AdminLayout>
    );
}
