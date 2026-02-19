import { useForm } from '@inertiajs/react';
import { ArrowLeft, Save, Building2, CreditCard } from 'lucide-react';
import { type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { FormField } from '@/components/form-field';
import { type Tour } from '@/types';

interface CreatePartnerProps {
    tours: Tour[];
    partnerTypes: Array<{ value: string; label: string }>;
    seasons: Array<{ value: string; label: string }>;
    paxTypes: Array<{ value: string; label: string }>;
}

export default function CreatePartner({ tours, partnerTypes, seasons, paxTypes }: CreatePartnerProps) {
    const { data, setData, post, processing, errors } = useForm({
        name: '', type: 'hotel', email: '', phone: '', vat_number: '', sdi_pec: '', address: '', notes: '', is_active: true,
        prices: {} as Record<string, Record<string, Record<string, string>>>,
    });

    function handleSubmit(e: FormEvent) { e.preventDefault(); post('/admin/partners'); }

    function updatePrice(tourId: number, season: string, paxType: string, value: string) {
        setData('prices', { ...data.prices, [tourId]: { ...(data.prices[tourId] ?? {}), [season]: { ...(data.prices[tourId]?.[season] ?? {}), [paxType]: value } } });
    }

    return (
        <AdminLayout
            pageTitle="New Partner"
            breadcrumbs={[
                { label: 'Partners', href: '/admin/partners' },
                { label: 'New Partner' },
            ]}
        >
            <form onSubmit={handleSubmit} className="mx-auto max-w-2xl space-y-6">
                {/* Partner Information */}
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="pb-4">
                        <div className="flex items-center gap-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100">
                                <Building2 className="h-4.5 w-4.5 text-slate-500" />
                            </div>
                            <div>
                                <CardTitle className="text-sm font-semibold text-slate-900">
                                    Partner Information
                                </CardTitle>
                                <p className="mt-0.5 text-xs text-slate-400">
                                    Basic details about the partner company
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <Separator />
                    <CardContent className="pt-6">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <FormField label="Company Name" error={errors.name} required>
                                <Input
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="Enter company name"
                                />
                            </FormField>
                            <FormField label="Type" error={errors.type} required>
                                <select
                                    value={data.type}
                                    onChange={(e) => setData('type', e.target.value)}
                                    className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                                >
                                    {partnerTypes.map((t) => (
                                        <option key={t.value} value={t.value}>{t.label}</option>
                                    ))}
                                </select>
                            </FormField>
                            <FormField label="Email" error={errors.email} required>
                                <Input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    placeholder="partner@example.com"
                                />
                            </FormField>
                            <FormField label="Phone" error={errors.phone}>
                                <Input
                                    value={data.phone}
                                    onChange={(e) => setData('phone', e.target.value)}
                                    placeholder="+39 ..."
                                />
                            </FormField>
                            <FormField label="VAT Number" error={errors.vat_number}>
                                <Input
                                    value={data.vat_number}
                                    onChange={(e) => setData('vat_number', e.target.value)}
                                    placeholder="IT01234567890"
                                />
                            </FormField>
                            <FormField label="SDI/PEC" error={errors.sdi_pec}>
                                <Input
                                    value={data.sdi_pec}
                                    onChange={(e) => setData('sdi_pec', e.target.value)}
                                    placeholder="SDI code or PEC address"
                                />
                            </FormField>
                            <div className="sm:col-span-2">
                                <FormField label="Address" error={errors.address}>
                                    <Textarea
                                        value={data.address}
                                        onChange={(e) => setData('address', e.target.value)}
                                        rows={2}
                                        placeholder="Full address..."
                                    />
                                </FormField>
                            </div>
                            <div className="sm:col-span-2">
                                <FormField label="Notes" error={errors.notes} description="Internal notes about this partner">
                                    <Textarea
                                        value={data.notes}
                                        onChange={(e) => setData('notes', e.target.value)}
                                        rows={2}
                                        placeholder="Any additional notes..."
                                    />
                                </FormField>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Price List */}
                {tours.length > 0 && (
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="pb-4">
                            <div className="flex items-center gap-3">
                                <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100">
                                    <CreditCard className="h-4.5 w-4.5 text-slate-500" />
                                </div>
                                <div>
                                    <CardTitle className="text-sm font-semibold text-slate-900">
                                        Price List
                                    </CardTitle>
                                    <p className="mt-0.5 text-xs text-slate-400">
                                        Set custom prices per tour, season and passenger type
                                    </p>
                                </div>
                            </div>
                        </CardHeader>
                        <Separator />
                        <CardContent className="space-y-6 pt-6">
                            {tours.map((tour) => (
                                <div key={tour.id}>
                                    <h4 className="mb-3 text-sm font-medium text-slate-700">
                                        {tour.name}
                                        <span className="ml-2 text-xs font-normal text-slate-400">
                                            ({tour.code})
                                        </span>
                                    </h4>
                                    <div className="overflow-x-auto rounded-lg border border-slate-200">
                                        <table className="w-full text-sm">
                                            <thead>
                                                <tr className="bg-slate-50">
                                                    <th className="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                        Season
                                                    </th>
                                                    {paxTypes.map((pt) => (
                                                        <th key={pt.value} className="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                            {pt.label}
                                                        </th>
                                                    ))}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {seasons.map((s) => (
                                                    <tr key={s.value} className="border-t border-slate-100">
                                                        <td className="px-4 py-2.5 text-sm font-medium capitalize text-slate-700">
                                                            {s.label}
                                                        </td>
                                                        {paxTypes.map((pt) => (
                                                            <td key={pt.value} className="px-4 py-2.5">
                                                                <div className="relative">
                                                                    <span className="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">
                                                                        &euro;
                                                                    </span>
                                                                    <Input
                                                                        type="number"
                                                                        step="0.01"
                                                                        className="w-28 pl-7"
                                                                        placeholder="0.00"
                                                                        value={data.prices[tour.id]?.[s.value]?.[pt.value] ?? ''}
                                                                        onChange={(e) => updatePrice(tour.id, s.value, pt.value, e.target.value)}
                                                                    />
                                                                </div>
                                                            </td>
                                                        ))}
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                )}

                {/* Actions */}
                <div className="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3">
                    <Button type="button" variant="ghost" className="text-slate-600" asChild>
                        <a href="/admin/partners">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Cancel
                        </a>
                    </Button>
                    <Button type="submit" disabled={processing}>
                        <Save className="mr-2 h-4 w-4" />
                        {processing ? 'Saving...' : 'Create Partner'}
                    </Button>
                </div>
            </form>
        </AdminLayout>
    );
}
