import { useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FormField } from '@/components/form-field';
import { type Booking } from '@/types';

interface BookingEditProps {
    booking: Booking;
}

export default function BookingEdit({ booking }: BookingEditProps) {
    const { data, setData, put, processing, errors } = useForm({
        notes: booking.notes ?? '',
    });

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        put(`/admin/bookings/${booking.id}`);
    }

    return (
        <AdminLayout
            pageTitle={`Edit ${booking.booking_code}`}
            breadcrumbs={[
                { label: 'Bookings', href: '/admin/bookings' },
                { label: booking.booking_code, href: `/admin/bookings/${booking.id}` },
                { label: 'Edit' },
            ]}
        >
            <div className="mx-auto max-w-2xl">
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader>
                        <CardTitle className="text-base text-slate-900">Edit Booking Notes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <FormField label="Notes" error={errors.notes}>
                                <Textarea
                                    value={data.notes}
                                    onChange={(e) => setData('notes', e.target.value)}
                                    rows={5}
                                    placeholder="Add notes about this booking..."
                                />
                            </FormField>
                            <div className="flex items-center gap-3">
                                <Button type="submit" disabled={processing}>
                                    <Save className="mr-2 h-4 w-4" />
                                    {processing ? 'Saving...' : 'Save Changes'}
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <a href={`/admin/bookings/${booking.id}`}>
                                        <ArrowLeft className="mr-2 h-4 w-4" />
                                        Back
                                    </a>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
