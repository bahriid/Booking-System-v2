import { useForm } from '@inertiajs/react';
import { ArrowLeft, Save, MapPin } from 'lucide-react';
import { type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FormField } from '@/components/form-field';
import { type PickupPoint } from '@/types';

interface CreatePickupPointProps { pickupPoint?: PickupPoint; }

export default function CreatePickupPoint({ pickupPoint }: CreatePickupPointProps) {
    const isEdit = !!pickupPoint;
    const { data, setData, post, put, processing, errors } = useForm({
        name: pickupPoint?.name ?? '', location: pickupPoint?.location ?? '', default_time: pickupPoint?.default_time ?? '',
        sort_order: String(pickupPoint?.sort_order ?? '0'), is_active: pickupPoint?.is_active ?? true,
    });
    function handleSubmit(e: FormEvent) { e.preventDefault(); isEdit ? put(`/admin/pickup-points/${pickupPoint!.id}`) : post('/admin/pickup-points'); }

    return (
        <AdminLayout
            pageTitle={isEdit ? `Edit ${pickupPoint!.name}` : 'New Pickup Point'}
            breadcrumbs={[
                { label: 'Pickup Points', href: '/admin/pickup-points' },
                { label: isEdit ? 'Edit' : 'New' },
            ]}
            toolbarActions={
                <div className="flex items-center gap-2">
                    <Button variant="outline" asChild>
                        <a href="/admin/pickup-points">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back
                        </a>
                    </Button>
                    <Button onClick={handleSubmit} disabled={processing}>
                        <Save className="mr-2 h-4 w-4" />
                        {processing ? 'Saving...' : isEdit ? 'Update' : 'Create'}
                    </Button>
                </div>
            }
        >
            <div className="mx-auto max-w-lg">
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="border-b border-slate-100">
                        <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                            <MapPin className="h-5 w-5 text-slate-400" />
                            {isEdit ? 'Edit' : 'New'} Pickup Point
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-5">
                            <FormField label="Name" error={errors.name} required>
                                <Input
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="e.g., Hotel Paradiso"
                                />
                            </FormField>

                            <FormField label="Location" error={errors.location} description="Address or area description">
                                <Input
                                    value={data.location}
                                    onChange={(e) => setData('location', e.target.value)}
                                    placeholder="e.g., Via Roma 15, Positano"
                                />
                            </FormField>

                            <div className="grid grid-cols-2 gap-4">
                                <FormField label="Default Time" error={errors.default_time}>
                                    <Input
                                        type="time"
                                        value={data.default_time}
                                        onChange={(e) => setData('default_time', e.target.value)}
                                    />
                                </FormField>

                                <FormField label="Sort Order" error={errors.sort_order}>
                                    <Input
                                        type="number"
                                        value={data.sort_order}
                                        onChange={(e) => setData('sort_order', e.target.value)}
                                        placeholder="0"
                                    />
                                </FormField>
                            </div>

                            <div className="border-t border-slate-100 pt-5">
                                <div className="flex items-center gap-3">
                                    <Checkbox
                                        checked={data.is_active}
                                        onCheckedChange={(c) => setData('is_active', c === true)}
                                        id="is_active"
                                    />
                                    <div>
                                        <label
                                            htmlFor="is_active"
                                            className="text-sm font-medium text-slate-700 cursor-pointer"
                                        >
                                            Active
                                        </label>
                                        <p className="text-xs text-slate-400">
                                            Inactive pickup points won't appear in booking forms
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="flex items-center justify-between border-t border-slate-100 pt-5">
                                <Button type="button" variant="outline" asChild>
                                    <a href="/admin/pickup-points">
                                        <ArrowLeft className="mr-2 h-4 w-4" />
                                        Cancel
                                    </a>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    <Save className="mr-2 h-4 w-4" />
                                    {processing ? 'Saving...' : isEdit ? 'Update' : 'Create'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
