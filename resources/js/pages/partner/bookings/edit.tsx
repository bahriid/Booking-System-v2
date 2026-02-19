import { useForm } from '@inertiajs/react';
import { ArrowLeft, Plus, Trash2, Save, Users, UserPlus } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import PartnerLayout from '@/layouts/partner-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { FormField } from '@/components/form-field';
import { type Booking, type PickupPoint } from '@/types';

interface EditBookingProps {
    booking: Booking;
    pickupPoints: PickupPoint[];
}

interface NewPassenger {
    key: number;
    pax_type: 'adult' | 'child' | 'infant';
    first_name: string;
    last_name: string;
    phone: string;
    allergies: string;
    notes: string;
    pickup_point_id: string;
}

let newKey = 0;

export default function EditBooking({ booking, pickupPoints }: EditBookingProps) {
    const existingPassengers = booking.passengers ?? [];
    const [removedIds, setRemovedIds] = useState<number[]>([]);
    const [newPassengers, setNewPassengers] = useState<NewPassenger[]>([]);

    const { data, setData, put, processing, errors } = useForm({
        notes: booking.notes ?? '',
        passengers: existingPassengers.map((p) => ({
            id: p.id,
            first_name: p.first_name,
            last_name: p.last_name,
            phone: p.phone ?? '',
            allergies: p.allergies ?? '',
            notes: p.notes ?? '',
            pickup_point_id: p.pickup_point_id ? String(p.pickup_point_id) : '',
        })),
        new_passengers: [] as Array<Omit<NewPassenger, 'key'>>,
        removed_passengers: [] as number[],
    });

    function updateExisting(id: number, field: string, value: string) {
        setData('passengers', data.passengers.map((p) =>
            p.id === id ? { ...p, [field]: value } : p,
        ));
    }

    function removeExisting(id: number) {
        setRemovedIds([...removedIds, id]);
        setData('passengers', data.passengers.filter((p) => p.id !== id));
        setData('removed_passengers', [...data.removed_passengers, id]);
    }

    function addNew(type: 'adult' | 'child' | 'infant' = 'adult') {
        setNewPassengers([...newPassengers, {
            key: ++newKey,
            pax_type: type,
            first_name: '',
            last_name: '',
            phone: '',
            allergies: '',
            notes: '',
            pickup_point_id: '',
        }]);
    }

    function removeNew(key: number) {
        setNewPassengers(newPassengers.filter((p) => p.key !== key));
    }

    function updateNew(key: number, field: keyof NewPassenger, value: string) {
        setNewPassengers(newPassengers.map((p) => p.key === key ? { ...p, [field]: value } : p));
    }

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        setData('new_passengers', newPassengers.map(({ key, ...rest }) => rest));
        put(`/partner/bookings/${booking.id}`);
    }

    // Sync new_passengers before submit
    const handleFormSubmit = (e: FormEvent) => {
        e.preventDefault();
        const formData = {
            ...data,
            new_passengers: newPassengers.map(({ key, ...rest }) => rest),
        };
        put(`/partner/bookings/${booking.id}`, formData as any);
    };

    return (
        <PartnerLayout
            pageTitle={`Edit Booking ${booking.booking_code}`}
            breadcrumbs={[
                { label: 'Bookings', href: '/partner/bookings' },
                { label: booking.booking_code, href: `/partner/bookings/${booking.id}` },
                { label: 'Edit' },
            ]}
        >
            <form onSubmit={handleFormSubmit}>
                {/* Tour Info Summary */}
                <div className="mb-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                        <h3 className="text-sm font-semibold text-slate-900">Booking Information</h3>
                    </div>
                    <div className="p-6">
                        <dl className="grid gap-5 sm:grid-cols-3">
                            <div>
                                <dt className="text-xs font-medium text-slate-500">Tour</dt>
                                <dd className="mt-1.5 text-sm font-medium text-slate-900">{booking.tour_departure?.tour?.name}</dd>
                            </div>
                            <div>
                                <dt className="text-xs font-medium text-slate-500">Departure</dt>
                                <dd className="mt-1.5 text-sm font-medium text-slate-900">
                                    {booking.tour_departure?.date} at {booking.tour_departure?.time}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-xs font-medium text-slate-500">Notes</dt>
                                <dd className="mt-1.5">
                                    <Textarea
                                        value={data.notes}
                                        onChange={(e) => setData('notes', e.target.value)}
                                        placeholder="Booking notes..."
                                        rows={2}
                                        className="border-slate-200"
                                    />
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {/* Existing Passengers */}
                <div className="mb-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-3">
                        <div className="flex items-center gap-2">
                            <Users className="h-4 w-4 text-slate-400" />
                            <h3 className="text-sm font-semibold text-slate-900">Existing Passengers</h3>
                            <span className="inline-flex items-center rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-700">
                                {data.passengers.length}
                            </span>
                        </div>
                    </div>
                    <div className="p-6 space-y-4">
                        {data.passengers.map((passenger, idx) => (
                            <div key={passenger.id} className="rounded-lg border border-slate-200 bg-slate-50/30 p-5">
                                <div className="mb-4 flex items-center justify-between">
                                    <span className="text-sm font-medium text-slate-900">
                                        Passenger {idx + 1}
                                        <Badge className="ml-2 bg-slate-100 text-xs capitalize text-slate-600 ring-1 ring-inset ring-slate-200">
                                            {existingPassengers.find((p) => p.id === passenger.id)?.pax_type}
                                        </Badge>
                                    </span>
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        className="h-8 w-8 p-0 text-red-400 hover:bg-red-50 hover:text-red-600"
                                        onClick={() => removeExisting(passenger.id)}
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </div>
                                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    <FormField label="First Name" required>
                                        <Input
                                            value={passenger.first_name}
                                            onChange={(e) => updateExisting(passenger.id, 'first_name', e.target.value)}
                                            className="border-slate-200"
                                        />
                                    </FormField>
                                    <FormField label="Last Name" required>
                                        <Input
                                            value={passenger.last_name}
                                            onChange={(e) => updateExisting(passenger.id, 'last_name', e.target.value)}
                                            className="border-slate-200"
                                        />
                                    </FormField>
                                    <FormField label="Pickup Point">
                                        <select
                                            value={passenger.pickup_point_id}
                                            onChange={(e) => updateExisting(passenger.id, 'pickup_point_id', e.target.value)}
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
                                            onChange={(e) => updateExisting(passenger.id, 'phone', e.target.value)}
                                            className="border-slate-200"
                                        />
                                    </FormField>
                                    <FormField label="Allergies">
                                        <Input
                                            value={passenger.allergies}
                                            onChange={(e) => updateExisting(passenger.id, 'allergies', e.target.value)}
                                            className="border-slate-200"
                                        />
                                    </FormField>
                                    <FormField label="Notes">
                                        <Input
                                            value={passenger.notes}
                                            onChange={(e) => updateExisting(passenger.id, 'notes', e.target.value)}
                                            className="border-slate-200"
                                        />
                                    </FormField>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* New Passengers */}
                <div className="mb-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-3">
                        <div className="flex items-center gap-2">
                            <UserPlus className="h-4 w-4 text-emerald-500" />
                            <h3 className="text-sm font-semibold text-slate-900">Add New Passengers</h3>
                        </div>
                        <div className="flex gap-1.5">
                            <Button type="button" size="sm" variant="outline" onClick={() => addNew('adult')} className="border-slate-200 text-xs">
                                <Plus className="mr-1 h-3 w-3" /> Adult
                            </Button>
                            <Button type="button" size="sm" variant="outline" onClick={() => addNew('child')} className="border-slate-200 text-xs">
                                <Plus className="mr-1 h-3 w-3" /> Child
                            </Button>
                            <Button type="button" size="sm" variant="outline" onClick={() => addNew('infant')} className="border-slate-200 text-xs">
                                <Plus className="mr-1 h-3 w-3" /> Infant
                            </Button>
                        </div>
                    </div>
                    <div className="p-6">
                        {newPassengers.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-8">
                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                                    <UserPlus className="h-5 w-5 text-slate-400" />
                                </div>
                                <p className="mt-2 text-sm text-slate-500">
                                    Click above to add new passengers.
                                </p>
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {newPassengers.map((passenger, idx) => (
                                    <div key={passenger.key} className="rounded-lg border border-dashed border-emerald-300 bg-emerald-50/30 p-5">
                                        <div className="mb-4 flex items-center justify-between">
                                            <span className="text-sm font-medium text-slate-900">
                                                New Passenger
                                                <Badge className="ml-2 bg-emerald-100 text-xs capitalize text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                    {passenger.pax_type}
                                                </Badge>
                                            </span>
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="ghost"
                                                className="h-8 w-8 p-0 text-red-400 hover:bg-red-50 hover:text-red-600"
                                                onClick={() => removeNew(passenger.key)}
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </Button>
                                        </div>
                                        <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                            <FormField label="First Name" required>
                                                <Input
                                                    value={passenger.first_name}
                                                    onChange={(e) => updateNew(passenger.key, 'first_name', e.target.value)}
                                                    className="border-slate-200"
                                                />
                                            </FormField>
                                            <FormField label="Last Name" required>
                                                <Input
                                                    value={passenger.last_name}
                                                    onChange={(e) => updateNew(passenger.key, 'last_name', e.target.value)}
                                                    className="border-slate-200"
                                                />
                                            </FormField>
                                            <FormField label="Pickup Point" required>
                                                <select
                                                    value={passenger.pickup_point_id}
                                                    onChange={(e) => updateNew(passenger.key, 'pickup_point_id', e.target.value)}
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
                                                    onChange={(e) => updateNew(passenger.key, 'phone', e.target.value)}
                                                    className="border-slate-200"
                                                />
                                            </FormField>
                                            <FormField label="Allergies">
                                                <Input
                                                    value={passenger.allergies}
                                                    onChange={(e) => updateNew(passenger.key, 'allergies', e.target.value)}
                                                    className="border-slate-200"
                                                />
                                            </FormField>
                                            <FormField label="Notes">
                                                <Input
                                                    value={passenger.notes}
                                                    onChange={(e) => updateNew(passenger.key, 'notes', e.target.value)}
                                                    className="border-slate-200"
                                                />
                                            </FormField>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                {/* Actions */}
                <div className="flex items-center justify-between">
                    <Button type="button" variant="outline" asChild className="border-slate-200 text-slate-700 hover:bg-slate-50">
                        <a href={`/partner/bookings/${booking.id}`}>
                            <ArrowLeft className="mr-2 h-4 w-4" /> Cancel
                        </a>
                    </Button>
                    <Button type="submit" disabled={processing}>
                        <Save className="mr-2 h-4 w-4" />
                        {processing ? 'Saving...' : 'Save Changes'}
                    </Button>
                </div>
            </form>
        </PartnerLayout>
    );
}
