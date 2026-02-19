import { useForm } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, Plus, Trash2, Ship, Calendar, Users, Check, Loader2, Anchor } from 'lucide-react';
import { useState, useEffect, type FormEvent } from 'react';
import axios from 'axios';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { FormField } from '@/components/form-field';
import { cn } from '@/lib/utils';
import { type Tour, type PickupPoint, type Partner } from '@/types';

interface CreateBookingProps {
    partners: Partner[];
    tours: Tour[];
    pickupPoints: PickupPoint[];
}

interface Departure {
    id: number;
    date: string;
    date_formatted: string;
    time: string;
    capacity: number;
    booked: number;
    remaining: number;
    past_cutoff: boolean;
}

interface PassengerForm {
    key: number;
    pax_type: 'adult' | 'child' | 'infant';
    first_name: string;
    last_name: string;
    phone: string;
    allergies: string;
    notes: string;
    pickup_point_id: string;
}

let pKey = 0;
function makePassenger(type: 'adult' | 'child' | 'infant' = 'adult'): PassengerForm {
    return { key: ++pKey, pax_type: type, first_name: '', last_name: '', phone: '', allergies: '', notes: '', pickup_point_id: '' };
}

const steps = [
    { n: 1, label: 'Setup', icon: Ship },
    { n: 2, label: 'Departure', icon: Calendar },
    { n: 3, label: 'Passengers', icon: Users },
    { n: 4, label: 'Review', icon: Check },
];

export default function CreateBooking({ partners, tours, pickupPoints }: CreateBookingProps) {
    const [step, setStep] = useState(1);
    const [selectedPartner, setSelectedPartner] = useState('');
    const [selectedTour, setSelectedTour] = useState<Tour | null>(null);
    const [departures, setDepartures] = useState<Departure[]>([]);
    const [selectedDep, setSelectedDep] = useState<Departure | null>(null);
    const [passengers, setPassengers] = useState<PassengerForm[]>([makePassenger()]);
    const [loadingDeps, setLoadingDeps] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        partner_id: '',
        tour_departure_id: '',
        passengers: [] as Array<Omit<PassengerForm, 'key'>>,
    });

    useEffect(() => {
        if (!selectedTour) {
            setDepartures([]);
            return;
        }
        setLoadingDeps(true);
        axios
            .get(`/admin/tours/${selectedTour.id}/departures`)
            .then((r) => setDepartures(r.data))
            .finally(() => setLoadingDeps(false));
    }, [selectedTour]);

    function addP(t: 'adult' | 'child' | 'infant' = 'adult') {
        setPassengers([...passengers, makePassenger(t)]);
    }
    function removeP(key: number) {
        if (passengers.length > 1) setPassengers(passengers.filter((p) => p.key !== key));
    }
    function updateP(key: number, field: keyof PassengerForm, value: string) {
        setPassengers(passengers.map((p) => (p.key === key ? { ...p, [field]: value } : p)));
    }

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        post('/admin/bookings');
    }

    function capacityPercentage(dep: Departure) {
        if (dep.capacity === 0) return 0;
        return Math.round((dep.booked / dep.capacity) * 100);
    }

    return (
        <AdminLayout
            pageTitle="New Booking"
            breadcrumbs={[{ label: 'Bookings', href: '/admin/bookings' }, { label: 'New Booking' }]}
        >
            {/* Steps Indicator */}
            <div className="mb-8">
                <div className="flex items-center justify-center">
                    {steps.map(({ n, label, icon: Icon }, idx) => (
                        <div key={n} className="flex items-center">
                            {idx > 0 && (
                                <div
                                    className={cn(
                                        'mx-2 h-0.5 w-10 rounded-full transition-colors sm:w-16',
                                        step >= n ? 'bg-blue-500' : 'bg-slate-200',
                                    )}
                                />
                            )}
                            <button
                                type="button"
                                onClick={() => n < step && setStep(n)}
                                disabled={n > step}
                                className={cn(
                                    'flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-all',
                                    step === n && 'bg-blue-600 text-white shadow-md shadow-blue-600/25',
                                    step > n && 'cursor-pointer bg-blue-50 text-blue-700 hover:bg-blue-100',
                                    step < n && 'cursor-default bg-slate-100 text-slate-400',
                                )}
                            >
                                <div
                                    className={cn(
                                        'flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold',
                                        step === n && 'bg-white/20 text-white',
                                        step > n && 'bg-blue-600 text-white',
                                        step < n && 'bg-slate-200 text-slate-400',
                                    )}
                                >
                                    {step > n ? <Check className="h-3.5 w-3.5" /> : n}
                                </div>
                                <span className="hidden sm:inline">{label}</span>
                            </button>
                        </div>
                    ))}
                </div>
            </div>

            {/* Step 1: Partner & Tour */}
            {step === 1 && (
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="border-b border-slate-100 pb-4">
                        <CardTitle className="flex items-center gap-2 text-base font-semibold text-slate-900">
                            <Ship className="h-4 w-4 text-slate-400" />
                            Select Partner & Tour
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-6 pt-5">
                        <FormField label="Partner" required>
                            <select
                                value={selectedPartner}
                                onChange={(e) => {
                                    setSelectedPartner(e.target.value);
                                    setData('partner_id', e.target.value);
                                }}
                                className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">Select partner...</option>
                                {partners.map((p) => (
                                    <option key={p.id} value={p.id}>{p.name}</option>
                                ))}
                            </select>
                        </FormField>

                        <div>
                            <label className="mb-3 block text-sm font-medium text-slate-700">
                                Tour <span className="text-red-500">*</span>
                            </label>
                            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                {tours.map((tour) => (
                                    <button
                                        type="button"
                                        key={tour.id}
                                        className={cn(
                                            'rounded-lg border p-4 text-left transition-all',
                                            selectedTour?.id === tour.id
                                                ? 'border-blue-500 bg-blue-50/50 ring-2 ring-blue-500'
                                                : 'border-slate-200 hover:border-slate-300 hover:shadow-sm',
                                        )}
                                        onClick={() => {
                                            setSelectedTour(tour);
                                            setSelectedDep(null);
                                        }}
                                    >
                                        <div className="flex items-start justify-between">
                                            <h3 className="text-sm font-semibold text-slate-900">{tour.name}</h3>
                                            {selectedTour?.id === tour.id && (
                                                <div className="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600">
                                                    <Check className="h-3 w-3 text-white" />
                                                </div>
                                            )}
                                        </div>
                                        <Badge variant="secondary" className="mt-2 bg-slate-100 text-xs text-slate-600">
                                            {tour.code}
                                        </Badge>
                                    </button>
                                ))}
                            </div>
                        </div>

                        <div className="flex justify-end border-t border-slate-100 pt-5">
                            <Button
                                className="h-10"
                                disabled={!selectedPartner || !selectedTour}
                                onClick={() => setStep(2)}
                            >
                                Next Step
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Step 2: Select Departure */}
            {step === 2 && (
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="border-b border-slate-100 pb-4">
                        <CardTitle className="flex items-center gap-2 text-base font-semibold text-slate-900">
                            <Calendar className="h-4 w-4 text-slate-400" />
                            <span>Select Departure</span>
                            <span className="text-slate-400">--</span>
                            <span className="text-blue-600">{selectedTour?.name}</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-5">
                        {loadingDeps ? (
                            <div className="flex flex-col items-center justify-center py-16">
                                <Loader2 className="mb-3 h-6 w-6 animate-spin text-blue-500" />
                                <p className="text-sm text-slate-500">Loading departures...</p>
                            </div>
                        ) : departures.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-16">
                                <div className="mb-4 rounded-full bg-slate-100 p-3">
                                    <Anchor className="h-6 w-6 text-slate-400" />
                                </div>
                                <p className="text-sm font-medium text-slate-900">No available departures</p>
                                <p className="mt-1 text-sm text-slate-400">There are no departures available for this tour.</p>
                            </div>
                        ) : (
                            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                {departures.map((d) => {
                                    const pct = capacityPercentage(d);
                                    const isDisabled = d.past_cutoff || d.remaining <= 0;
                                    const isSelected = selectedDep?.id === d.id;

                                    return (
                                        <button
                                            type="button"
                                            key={d.id}
                                            onClick={() => {
                                                setSelectedDep(d);
                                                setData('tour_departure_id', String(d.id));
                                                setStep(3);
                                            }}
                                            disabled={isDisabled}
                                            className={cn(
                                                'rounded-lg border p-4 text-left transition-all',
                                                isSelected && 'border-blue-500 bg-blue-50/50 ring-2 ring-blue-500',
                                                isDisabled
                                                    ? 'cursor-not-allowed border-slate-100 bg-slate-50 opacity-60'
                                                    : 'border-slate-200 hover:border-slate-300 hover:shadow-sm',
                                            )}
                                        >
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm font-semibold text-slate-900">{d.date_formatted}</span>
                                                <Badge variant="outline" className="border-slate-200 text-xs text-slate-600">
                                                    {d.time}
                                                </Badge>
                                            </div>

                                            {/* Capacity Bar */}
                                            <div className="mt-3">
                                                <div className="mb-1.5 flex items-center justify-between text-xs">
                                                    <span className="text-slate-500">{d.booked} / {d.capacity} booked</span>
                                                    <span className={cn(
                                                        'font-medium',
                                                        d.remaining <= 3 ? 'text-amber-600' : 'text-slate-600',
                                                    )}>
                                                        {d.remaining} left
                                                    </span>
                                                </div>
                                                <div className="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                                    <div
                                                        className={cn(
                                                            'h-full rounded-full transition-all',
                                                            pct >= 90 ? 'bg-red-500' : pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500',
                                                        )}
                                                        style={{ width: `${Math.min(pct, 100)}%` }}
                                                    />
                                                </div>
                                            </div>

                                            {d.past_cutoff && (
                                                <p className="mt-2 text-xs text-red-500">Past cutoff</p>
                                            )}
                                        </button>
                                    );
                                })}
                            </div>
                        )}

                        <div className="mt-6 border-t border-slate-100 pt-5">
                            <Button variant="outline" className="border-slate-200 text-slate-700 hover:bg-slate-50" onClick={() => setStep(1)}>
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Step 3: Passengers */}
            {step === 3 && (
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="border-b border-slate-100 pb-4">
                        <CardTitle className="flex items-center justify-between text-base font-semibold text-slate-900">
                            <div className="flex items-center gap-2">
                                <Users className="h-4 w-4 text-slate-400" />
                                Passengers
                                <span className="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-slate-100 px-1.5 text-xs font-medium text-slate-600">
                                    {passengers.length}
                                </span>
                            </div>
                            <div className="flex gap-1.5">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    className="h-8 border-slate-200 text-xs text-slate-700 hover:bg-slate-50"
                                    onClick={() => addP('adult')}
                                >
                                    <Plus className="mr-1 h-3 w-3" />
                                    Adult
                                </Button>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    className="h-8 border-slate-200 text-xs text-slate-700 hover:bg-slate-50"
                                    onClick={() => addP('child')}
                                >
                                    <Plus className="mr-1 h-3 w-3" />
                                    Child
                                </Button>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    className="h-8 border-slate-200 text-xs text-slate-700 hover:bg-slate-50"
                                    onClick={() => addP('infant')}
                                >
                                    <Plus className="mr-1 h-3 w-3" />
                                    Infant
                                </Button>
                            </div>
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-5">
                        <div className="space-y-4">
                            {passengers.map((p, i) => (
                                <div key={p.key} className="rounded-lg border border-slate-200 bg-white p-5 transition-shadow hover:shadow-sm">
                                    <div className="mb-4 flex items-center justify-between">
                                        <div className="flex items-center gap-2.5">
                                            <div className="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                                {i + 1}
                                            </div>
                                            <span className="text-sm font-medium text-slate-900">Passenger {i + 1}</span>
                                            <Badge
                                                variant="secondary"
                                                className={cn(
                                                    'text-xs capitalize',
                                                    p.pax_type === 'adult' && 'bg-blue-50 text-blue-700',
                                                    p.pax_type === 'child' && 'bg-violet-50 text-violet-700',
                                                    p.pax_type === 'infant' && 'bg-pink-50 text-pink-700',
                                                )}
                                            >
                                                {p.pax_type}
                                            </Badge>
                                        </div>
                                        {passengers.length > 1 && (
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                className="h-8 w-8 p-0 text-slate-400 hover:bg-red-50 hover:text-red-500"
                                                onClick={() => removeP(p.key)}
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </Button>
                                        )}
                                    </div>
                                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        <FormField label="First Name" required>
                                            <Input
                                                value={p.first_name}
                                                onChange={(e) => updateP(p.key, 'first_name', e.target.value)}
                                                placeholder="Enter first name"
                                                className="h-9 border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            />
                                        </FormField>
                                        <FormField label="Last Name" required>
                                            <Input
                                                value={p.last_name}
                                                onChange={(e) => updateP(p.key, 'last_name', e.target.value)}
                                                placeholder="Enter last name"
                                                className="h-9 border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            />
                                        </FormField>
                                        <FormField label="Pickup Point" required>
                                            <select
                                                value={p.pickup_point_id}
                                                onChange={(e) => updateP(p.key, 'pickup_point_id', e.target.value)}
                                                className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            >
                                                <option value="">Select pickup...</option>
                                                {pickupPoints.map((pp) => (
                                                    <option key={pp.id} value={pp.id}>{pp.name}</option>
                                                ))}
                                            </select>
                                        </FormField>
                                        <FormField label="Phone">
                                            <Input
                                                value={p.phone}
                                                onChange={(e) => updateP(p.key, 'phone', e.target.value)}
                                                placeholder="Phone number"
                                                className="h-9 border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            />
                                        </FormField>
                                        <FormField label="Allergies">
                                            <Input
                                                value={p.allergies}
                                                onChange={(e) => updateP(p.key, 'allergies', e.target.value)}
                                                placeholder="Any allergies?"
                                                className="h-9 border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            />
                                        </FormField>
                                        <FormField label="Notes">
                                            <Input
                                                value={p.notes}
                                                onChange={(e) => updateP(p.key, 'notes', e.target.value)}
                                                placeholder="Additional notes"
                                                className="h-9 border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            />
                                        </FormField>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="mt-6 flex justify-between border-t border-slate-100 pt-5">
                            <Button variant="outline" className="border-slate-200 text-slate-700 hover:bg-slate-50" onClick={() => setStep(2)}>
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back
                            </Button>
                            <Button
                                className="h-10"
                                onClick={() => {
                                    setData('passengers', passengers.map(({ key, ...r }) => r));
                                    setStep(4);
                                }}
                            >
                                Review Booking
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Step 4: Review & Confirm */}
            {step === 4 && (
                <form onSubmit={handleSubmit}>
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="border-b border-slate-100 pb-4">
                            <CardTitle className="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <Check className="h-4 w-4 text-slate-400" />
                                Review & Confirm
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="pt-5">
                            {/* Summary Cards */}
                            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                                <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Partner</dt>
                                    <dd className="mt-1.5 text-sm font-semibold text-slate-900">
                                        {partners.find((p) => String(p.id) === selectedPartner)?.name}
                                    </dd>
                                </div>
                                <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Tour</dt>
                                    <dd className="mt-1.5 text-sm font-semibold text-slate-900">{selectedTour?.name}</dd>
                                </div>
                                <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Departure</dt>
                                    <dd className="mt-1.5 text-sm font-semibold text-slate-900">
                                        {selectedDep?.date_formatted}
                                        <span className="ml-1.5 font-normal text-slate-400">at</span>
                                        <span className="ml-1.5">{selectedDep?.time}</span>
                                    </dd>
                                </div>
                            </div>

                            {/* Passengers Table */}
                            <div className="mb-4 flex items-center gap-2">
                                <h3 className="text-sm font-semibold text-slate-900">Passengers</h3>
                                <span className="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-slate-100 px-1.5 text-xs font-medium text-slate-600">
                                    {passengers.length}
                                </span>
                            </div>

                            <div className="overflow-hidden rounded-lg border border-slate-200">
                                <table className="w-full border-separate border-spacing-0">
                                    <thead>
                                        <tr className="bg-slate-50">
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">#</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Name</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Type</th>
                                            <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Pickup</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {passengers.map((p, i) => (
                                            <tr key={p.key} className="border-b border-slate-100 transition-colors hover:bg-slate-50/50">
                                                <td className="border-b border-slate-100 px-4 py-3 text-sm text-slate-400">{i + 1}</td>
                                                <td className="border-b border-slate-100 px-4 py-3 text-sm font-medium text-slate-900">
                                                    {p.first_name} {p.last_name}
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3">
                                                    <Badge
                                                        variant="secondary"
                                                        className={cn(
                                                            'text-xs capitalize',
                                                            p.pax_type === 'adult' && 'bg-blue-50 text-blue-700',
                                                            p.pax_type === 'child' && 'bg-violet-50 text-violet-700',
                                                            p.pax_type === 'infant' && 'bg-pink-50 text-pink-700',
                                                        )}
                                                    >
                                                        {p.pax_type}
                                                    </Badge>
                                                </td>
                                                <td className="border-b border-slate-100 px-4 py-3 text-sm text-slate-600">
                                                    {pickupPoints.find((pp) => String(pp.id) === p.pickup_point_id)?.name ?? '--'}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Error display */}
                            {Object.keys(errors).length > 0 && (
                                <div className="mt-4 rounded-lg border border-red-200 bg-red-50 p-4">
                                    <p className="text-sm font-medium text-red-800">Please fix the following errors:</p>
                                    <ul className="mt-2 list-inside list-disc text-sm text-red-600">
                                        {Object.values(errors).map((error, i) => (
                                            <li key={i}>{error}</li>
                                        ))}
                                    </ul>
                                </div>
                            )}

                            <div className="mt-6 flex justify-between border-t border-slate-100 pt-5">
                                <Button
                                    type="button"
                                    variant="outline"
                                    className="border-slate-200 text-slate-700 hover:bg-slate-50"
                                    onClick={() => setStep(3)}
                                >
                                    <ArrowLeft className="mr-2 h-4 w-4" />
                                    Back
                                </Button>
                                <Button type="submit" className="h-10" disabled={processing}>
                                    {processing ? (
                                        <>
                                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                            Creating...
                                        </>
                                    ) : (
                                        'Confirm Booking'
                                    )}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            )}
        </AdminLayout>
    );
}
