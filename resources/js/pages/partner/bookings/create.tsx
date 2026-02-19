import { useForm } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, Plus, Trash2, Ship, Calendar, Users, Check, Loader2 } from 'lucide-react';
import { useState, useEffect, type FormEvent } from 'react';
import axios from 'axios';
import PartnerLayout from '@/layouts/partner-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { FormField } from '@/components/form-field';
import { cn } from '@/lib/utils';
import { type Tour, type PickupPoint } from '@/types';

interface CreateBookingProps {
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

let passengerKey = 0;

function makePassenger(type: 'adult' | 'child' | 'infant' = 'adult'): PassengerForm {
    return {
        key: ++passengerKey,
        pax_type: type,
        first_name: '',
        last_name: '',
        phone: '',
        allergies: '',
        notes: '',
        pickup_point_id: '',
    };
}

export default function CreateBooking({ tours, pickupPoints }: CreateBookingProps) {
    const [step, setStep] = useState(1);
    const [selectedTour, setSelectedTour] = useState<Tour | null>(null);
    const [departures, setDepartures] = useState<Departure[]>([]);
    const [selectedDeparture, setSelectedDeparture] = useState<Departure | null>(null);
    const [passengers, setPassengers] = useState<PassengerForm[]>([makePassenger()]);
    const [loadingDepartures, setLoadingDepartures] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        tour_departure_id: '',
        passengers: [] as Array<Omit<PassengerForm, 'key'>>,
    });

    // Load departures when a tour is selected
    useEffect(() => {
        if (!selectedTour) {
            setDepartures([]);
            return;
        }
        setLoadingDepartures(true);
        axios.get(`/partner/tours/${selectedTour.id}/departures`)
            .then((res) => setDepartures(res.data))
            .finally(() => setLoadingDepartures(false));
    }, [selectedTour]);

    function selectTour(tour: Tour) {
        setSelectedTour(tour);
        setSelectedDeparture(null);
        setStep(2);
    }

    function selectDeparture(departure: Departure) {
        setSelectedDeparture(departure);
        setData('tour_departure_id', String(departure.id));
        setStep(3);
    }

    function addPassenger(type: 'adult' | 'child' | 'infant' = 'adult') {
        setPassengers([...passengers, makePassenger(type)]);
    }

    function removePassenger(key: number) {
        if (passengers.length <= 1) return;
        setPassengers(passengers.filter((p) => p.key !== key));
    }

    function updatePassenger(key: number, field: keyof PassengerForm, value: string) {
        setPassengers(passengers.map((p) => p.key === key ? { ...p, [field]: value } : p));
    }

    function goToReview() {
        setData('passengers', passengers.map(({ key, ...rest }) => rest));
        setStep(4);
    }

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        post('/partner/bookings');
    }

    return (
        <PartnerLayout
            pageTitle="New Booking"
            breadcrumbs={[
                { label: 'Bookings', href: '/partner/bookings' },
                { label: 'New Booking' },
            ]}
        >
            {/* Stepper */}
            <div className="mb-8 flex items-center justify-center gap-2">
                {[
                    { num: 1, label: 'Tour', icon: Ship },
                    { num: 2, label: 'Departure', icon: Calendar },
                    { num: 3, label: 'Passengers', icon: Users },
                    { num: 4, label: 'Review', icon: Check },
                ].map(({ num, label, icon: Icon }) => (
                    <div key={num} className="flex items-center gap-2">
                        {num > 1 && (
                            <div className={cn('h-0.5 w-8 rounded-full transition-colors', step >= num ? 'bg-emerald-500' : 'bg-slate-200')} />
                        )}
                        <button
                            onClick={() => num < step && setStep(num)}
                            disabled={num > step}
                            className={cn(
                                'flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-medium transition-all',
                                step === num && 'bg-emerald-600 text-white shadow-sm',
                                step > num && 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200',
                                step < num && 'bg-slate-100 text-slate-400',
                            )}
                        >
                            <Icon className="h-3.5 w-3.5" />
                            <span className="hidden sm:inline">{label}</span>
                        </button>
                    </div>
                ))}
            </div>

            {/* Step 1: Select Tour */}
            {step === 1 && (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {tours.map((tour) => (
                        <button
                            key={tour.id}
                            className={cn(
                                'rounded-lg border bg-white p-5 text-left shadow-sm transition-all hover:shadow-md',
                                selectedTour?.id === tour.id
                                    ? 'border-emerald-500 ring-2 ring-emerald-500/20'
                                    : 'border-slate-200 hover:border-slate-300',
                            )}
                            onClick={() => selectTour(tour)}
                        >
                            <div className="mb-2 flex items-start justify-between">
                                <div>
                                    <h3 className="font-semibold text-slate-900">{tour.name}</h3>
                                    <span className="mt-1 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                        {tour.code}
                                    </span>
                                </div>
                                <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100">
                                    <Ship className="h-4 w-4 text-slate-500" />
                                </div>
                            </div>
                            {tour.description && (
                                <p className="mt-2 line-clamp-2 text-xs text-slate-500">{tour.description}</p>
                            )}
                        </button>
                    ))}
                    {tours.length === 0 && (
                        <div className="col-span-full flex flex-col items-center justify-center py-16">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                <Ship className="h-6 w-6 text-slate-400" />
                            </div>
                            <p className="mt-3 text-sm text-slate-500">No tours available for booking.</p>
                        </div>
                    )}
                </div>
            )}

            {/* Step 2: Select Departure */}
            {step === 2 && (
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                        <h3 className="text-sm font-semibold text-slate-900">
                            Select Departure -- {selectedTour?.name}
                        </h3>
                    </div>
                    <div className="p-6">
                        {loadingDepartures ? (
                            <div className="flex flex-col items-center justify-center py-16">
                                <Loader2 className="h-8 w-8 animate-spin text-slate-400" />
                                <p className="mt-3 text-sm text-slate-500">Loading departures...</p>
                            </div>
                        ) : departures.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-16">
                                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                                    <Calendar className="h-6 w-6 text-slate-400" />
                                </div>
                                <p className="mt-3 text-sm text-slate-500">No available departures.</p>
                            </div>
                        ) : (
                            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                {departures.map((dep) => (
                                    <button
                                        key={dep.id}
                                        onClick={() => !dep.past_cutoff && dep.remaining > 0 && selectDeparture(dep)}
                                        disabled={dep.past_cutoff || dep.remaining <= 0}
                                        className={cn(
                                            'rounded-lg border p-4 text-left transition-all',
                                            selectedDeparture?.id === dep.id
                                                ? 'border-emerald-500 ring-2 ring-emerald-500/20'
                                                : 'border-slate-200',
                                            dep.past_cutoff || dep.remaining <= 0
                                                ? 'cursor-not-allowed opacity-50'
                                                : 'hover:border-slate-300 hover:shadow-sm',
                                        )}
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm font-medium text-slate-900">{dep.date_formatted}</span>
                                            <Badge className="bg-slate-100 text-xs text-slate-700 ring-1 ring-inset ring-slate-200">
                                                {dep.time}
                                            </Badge>
                                        </div>
                                        <div className="mt-2 flex items-center gap-2">
                                            <span className={cn(
                                                'text-xs font-medium',
                                                dep.remaining <= 5 ? 'text-amber-600' : 'text-slate-500',
                                            )}>
                                                {dep.remaining} seats left
                                            </span>
                                            {dep.past_cutoff && (
                                                <Badge className="bg-slate-100 text-[10px] text-slate-500 ring-1 ring-inset ring-slate-200">
                                                    Past cutoff
                                                </Badge>
                                            )}
                                        </div>
                                    </button>
                                ))}
                            </div>
                        )}
                        <div className="mt-6">
                            <Button variant="ghost" onClick={() => setStep(1)} className="text-slate-600">
                                <ArrowLeft className="mr-2 h-4 w-4" /> Back
                            </Button>
                        </div>
                    </div>
                </div>
            )}

            {/* Step 3: Passengers */}
            {step === 3 && (
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-3">
                        <h3 className="text-sm font-semibold text-slate-900">Passenger Details</h3>
                        <div className="flex gap-1.5">
                            <Button size="sm" variant="outline" onClick={() => addPassenger('adult')} className="border-slate-200 text-xs">
                                <Plus className="mr-1 h-3 w-3" /> Adult
                            </Button>
                            <Button size="sm" variant="outline" onClick={() => addPassenger('child')} className="border-slate-200 text-xs">
                                <Plus className="mr-1 h-3 w-3" /> Child
                            </Button>
                            <Button size="sm" variant="outline" onClick={() => addPassenger('infant')} className="border-slate-200 text-xs">
                                <Plus className="mr-1 h-3 w-3" /> Infant
                            </Button>
                        </div>
                    </div>
                    <div className="p-6">
                        <div className="space-y-4">
                            {passengers.map((passenger, idx) => (
                                <div key={passenger.key} className="rounded-lg border border-slate-200 bg-slate-50/30 p-5">
                                    <div className="mb-4 flex items-center justify-between">
                                        <span className="text-sm font-medium text-slate-900">
                                            Passenger {idx + 1}
                                            <Badge className="ml-2 bg-slate-100 text-xs capitalize text-slate-600 ring-1 ring-inset ring-slate-200">
                                                {passenger.pax_type}
                                            </Badge>
                                        </span>
                                        {passengers.length > 1 && (
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                className="h-8 w-8 p-0 text-red-400 hover:bg-red-50 hover:text-red-600"
                                                onClick={() => removePassenger(passenger.key)}
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </Button>
                                        )}
                                    </div>
                                    <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        <FormField label="First Name" required>
                                            <Input
                                                value={passenger.first_name}
                                                onChange={(e) => updatePassenger(passenger.key, 'first_name', e.target.value)}
                                                placeholder="First name"
                                                className="border-slate-200"
                                            />
                                        </FormField>
                                        <FormField label="Last Name" required>
                                            <Input
                                                value={passenger.last_name}
                                                onChange={(e) => updatePassenger(passenger.key, 'last_name', e.target.value)}
                                                placeholder="Last name"
                                                className="border-slate-200"
                                            />
                                        </FormField>
                                        <FormField label="Pickup Point" required>
                                            <select
                                                value={passenger.pickup_point_id}
                                                onChange={(e) => updatePassenger(passenger.key, 'pickup_point_id', e.target.value)}
                                                className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                                            >
                                                <option value="">Select pickup...</option>
                                                {pickupPoints.map((pp) => (
                                                    <option key={pp.id} value={pp.id}>{pp.name}</option>
                                                ))}
                                            </select>
                                        </FormField>
                                        <FormField label="Phone">
                                            <Input
                                                value={passenger.phone}
                                                onChange={(e) => updatePassenger(passenger.key, 'phone', e.target.value)}
                                                placeholder="Phone number"
                                                className="border-slate-200"
                                            />
                                        </FormField>
                                        <FormField label="Allergies">
                                            <Input
                                                value={passenger.allergies}
                                                onChange={(e) => updatePassenger(passenger.key, 'allergies', e.target.value)}
                                                placeholder="Any allergies"
                                                className="border-slate-200"
                                            />
                                        </FormField>
                                        <FormField label="Notes">
                                            <Input
                                                value={passenger.notes}
                                                onChange={(e) => updatePassenger(passenger.key, 'notes', e.target.value)}
                                                placeholder="Notes"
                                                className="border-slate-200"
                                            />
                                        </FormField>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {errors.passengers && (
                            <p className="mt-3 text-sm text-red-600">{errors.passengers}</p>
                        )}

                        <div className="mt-6 flex items-center justify-between border-t border-slate-100 pt-6">
                            <Button variant="ghost" onClick={() => setStep(2)} className="text-slate-600">
                                <ArrowLeft className="mr-2 h-4 w-4" /> Back
                            </Button>
                            <Button onClick={goToReview}>
                                Review <ArrowRight className="ml-2 h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
            )}

            {/* Step 4: Review */}
            {step === 4 && (
                <form onSubmit={handleSubmit}>
                    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                            <h3 className="text-sm font-semibold text-slate-900">Review & Confirm</h3>
                        </div>
                        <div className="p-6 space-y-6">
                            <dl className="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <dt className="text-xs font-medium text-slate-500">Tour</dt>
                                    <dd className="mt-1.5 text-sm font-medium text-slate-900">{selectedTour?.name}</dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium text-slate-500">Departure</dt>
                                    <dd className="mt-1.5 text-sm font-medium text-slate-900">
                                        {selectedDeparture?.date_formatted} at {selectedDeparture?.time}
                                    </dd>
                                </div>
                            </dl>

                            <div>
                                <p className="mb-3 text-sm font-semibold text-slate-900">
                                    Passengers
                                    <span className="ml-1.5 inline-flex items-center rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-700">
                                        {passengers.length}
                                    </span>
                                </p>
                                <div className="overflow-x-auto rounded-lg border border-slate-200">
                                    <table className="w-full">
                                        <thead>
                                            <tr className="bg-slate-50">
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">#</th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Name</th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Type</th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Pickup</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {passengers.map((p, idx) => (
                                                <tr key={p.key} className="border-b border-slate-100 last:border-b-0">
                                                    <td className="px-4 py-3 text-sm text-slate-400">{idx + 1}</td>
                                                    <td className="px-4 py-3 text-sm font-medium text-slate-900">{p.first_name} {p.last_name}</td>
                                                    <td className="px-4 py-3">
                                                        <Badge className="bg-slate-100 text-xs capitalize text-slate-600 ring-1 ring-inset ring-slate-200">
                                                            {p.pax_type}
                                                        </Badge>
                                                    </td>
                                                    <td className="px-4 py-3 text-sm text-slate-600">
                                                        {pickupPoints.find((pp) => String(pp.id) === p.pickup_point_id)?.name ?? (
                                                            <span className="text-slate-400">&mdash;</span>
                                                        )}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div className="flex items-center justify-between border-t border-slate-100 pt-6">
                                <Button type="button" variant="ghost" onClick={() => setStep(3)} className="text-slate-600">
                                    <ArrowLeft className="mr-2 h-4 w-4" /> Back
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Creating...' : 'Confirm Booking'}
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            )}
        </PartnerLayout>
    );
}
