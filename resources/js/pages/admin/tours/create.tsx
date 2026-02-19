import { useForm } from '@inertiajs/react';
import { ArrowLeft, Save, MapPin, Settings } from 'lucide-react';
import { type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { FormField } from '@/components/form-field';
import { type Tour, type PickupPoint } from '@/types';

interface CreateTourProps {
    tour?: Tour;
    pickupPoints: PickupPoint[];
}

export default function CreateTour({ tour, pickupPoints }: CreateTourProps) {
    const isEdit = !!tour;
    const { data, setData, post, put, processing, errors } = useForm({
        name: tour?.name ?? '', code: tour?.code ?? '', description: tour?.description ?? '',
        seasonality_start: tour?.seasonality_start ?? '', seasonality_end: tour?.seasonality_end ?? '',
        cutoff_hours: String(tour?.cutoff_hours ?? '24'), default_capacity: String(tour?.default_capacity ?? '50'),
        is_active: tour?.is_active ?? true,
    });

    function handleSubmit(e: FormEvent) { e.preventDefault(); isEdit ? put(`/admin/tours/${tour!.id}`) : post('/admin/tours'); }

    return (
        <AdminLayout
            pageTitle={isEdit ? `Edit ${tour!.name}` : 'New Tour'}
            breadcrumbs={[
                { label: 'Tours', href: '/admin/tours' },
                { label: isEdit ? 'Edit' : 'New Tour' },
            ]}
        >
            <form onSubmit={handleSubmit} className="mx-auto max-w-2xl space-y-6">
                {/* Tour Information */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-4">
                        <div className="flex items-center gap-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100">
                                <MapPin className="h-4.5 w-4.5 text-slate-500" />
                            </div>
                            <div>
                                <CardTitle className="text-sm font-semibold text-slate-900">
                                    Tour Information
                                </CardTitle>
                                <p className="mt-0.5 text-xs text-slate-400">
                                    Basic details about the tour route
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <Separator />
                    <CardContent className="pt-6">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <FormField label="Tour Name" error={errors.name} required>
                                <Input
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="e.g. Positano to Amalfi Coast Loop"
                                />
                            </FormField>
                            <FormField label="Tour Code" error={errors.code} required description="Short unique identifier used in booking codes">
                                <Input
                                    value={data.code}
                                    onChange={(e) => setData('code', e.target.value)}
                                    placeholder="e.g. POSAMCL"
                                />
                            </FormField>
                            <div className="sm:col-span-2">
                                <FormField label="Description" error={errors.description}>
                                    <Textarea
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        rows={3}
                                        placeholder="Describe the tour itinerary, highlights, and any important details..."
                                    />
                                </FormField>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Season & Capacity */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-4">
                        <div className="flex items-center gap-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100">
                                <Settings className="h-4.5 w-4.5 text-slate-500" />
                            </div>
                            <div>
                                <CardTitle className="text-sm font-semibold text-slate-900">
                                    Season & Capacity
                                </CardTitle>
                                <p className="mt-0.5 text-xs text-slate-400">
                                    Configure operational period and booking limits
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <Separator />
                    <CardContent className="pt-6">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <FormField label="Season Start" error={errors.seasonality_start} required>
                                <Input
                                    type="date"
                                    value={data.seasonality_start}
                                    onChange={(e) => setData('seasonality_start', e.target.value)}
                                />
                            </FormField>
                            <FormField label="Season End" error={errors.seasonality_end} required>
                                <Input
                                    type="date"
                                    value={data.seasonality_end}
                                    onChange={(e) => setData('seasonality_end', e.target.value)}
                                />
                            </FormField>
                            <FormField label="Cutoff Hours" error={errors.cutoff_hours} required description="Minimum hours before departure for new bookings">
                                <Input
                                    type="number"
                                    value={data.cutoff_hours}
                                    onChange={(e) => setData('cutoff_hours', e.target.value)}
                                    placeholder="24"
                                />
                            </FormField>
                            <FormField label="Default Capacity" error={errors.default_capacity} required description="Maximum passengers per departure">
                                <Input
                                    type="number"
                                    value={data.default_capacity}
                                    onChange={(e) => setData('default_capacity', e.target.value)}
                                    placeholder="50"
                                />
                            </FormField>
                            <div className="sm:col-span-2">
                                <div className="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                                    <Checkbox
                                        checked={data.is_active}
                                        onCheckedChange={(c) => setData('is_active', c === true)}
                                        id="is_active"
                                    />
                                    <div>
                                        <label htmlFor="is_active" className="text-sm font-medium text-slate-900 cursor-pointer">
                                            Active Tour
                                        </label>
                                        <p className="text-xs text-slate-400">
                                            When active, partners can create bookings for this tour
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Actions */}
                <div className="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3">
                    <Button type="button" variant="ghost" className="text-slate-600" asChild>
                        <a href="/admin/tours">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Cancel
                        </a>
                    </Button>
                    <Button type="submit" disabled={processing}>
                        <Save className="mr-2 h-4 w-4" />
                        {processing ? 'Saving...' : isEdit ? 'Update Tour' : 'Create Tour'}
                    </Button>
                </div>
            </form>
        </AdminLayout>
    );
}
