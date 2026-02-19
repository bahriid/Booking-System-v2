import { ArrowLeft, Edit, Mail, Phone, MapPin, FileText, Hash, Building2, Calendar, TrendingUp } from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { PartnerAvatar } from '@/components/partner/avatar';
import { StatusBadge } from '@/components/booking/status-badge';
import { type Partner, type Tour } from '@/types';

interface PartnerShowProps {
    partner: Partner;
    tours: Tour[];
    priceMatrix: Record<string, Record<string, Record<string, number>>>;
}

export default function PartnerShow({ partner, tours, priceMatrix }: PartnerShowProps) {
    return (
        <AdminLayout
            pageTitle={partner.name}
            breadcrumbs={[
                { label: 'Partners', href: '/admin/partners' },
                { label: partner.name },
            ]}
            toolbarActions={
                <Button variant="outline" size="sm" asChild>
                    <a href={`/admin/partners/${partner.id}/edit`}>
                        <Edit className="mr-2 h-4 w-4" />
                        Edit
                    </a>
                </Button>
            }
        >
            <div className="grid gap-6 lg:grid-cols-3">
                {/* Main Content */}
                <div className="space-y-6 lg:col-span-2">
                    {/* Partner Header Card */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardContent className="p-6">
                            <div className="flex items-start gap-5">
                                <PartnerAvatar name={partner.name} initials={partner.initials} className="h-14 w-14 text-lg" />
                                <div className="min-w-0 flex-1">
                                    <div className="flex items-center gap-3">
                                        <h2 className="text-xl font-semibold text-slate-900">
                                            {partner.name}
                                        </h2>
                                        {partner.is_active ? (
                                            <span className="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                Active
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                                Inactive
                                            </span>
                                        )}
                                    </div>
                                    <div className="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                                        <Building2 className="h-3.5 w-3.5" />
                                        <span className="capitalize">{partner.type?.replace('_', ' ')}</span>
                                    </div>
                                </div>
                            </div>

                            <Separator className="my-5" />

                            {/* Contact Details Grid */}
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="flex items-start gap-3">
                                    <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                        <Mail className="h-4 w-4 text-slate-500" />
                                    </div>
                                    <div className="min-w-0">
                                        <p className="text-xs font-medium text-slate-400">Email</p>
                                        <p className="mt-0.5 truncate text-sm text-slate-900">{partner.email}</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                        <Phone className="h-4 w-4 text-slate-500" />
                                    </div>
                                    <div className="min-w-0">
                                        <p className="text-xs font-medium text-slate-400">Phone</p>
                                        <p className="mt-0.5 text-sm text-slate-900">{partner.phone || '---'}</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                        <Hash className="h-4 w-4 text-slate-500" />
                                    </div>
                                    <div className="min-w-0">
                                        <p className="text-xs font-medium text-slate-400">VAT Number</p>
                                        <p className="mt-0.5 text-sm text-slate-900">{partner.vat_number || '---'}</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                        <FileText className="h-4 w-4 text-slate-500" />
                                    </div>
                                    <div className="min-w-0">
                                        <p className="text-xs font-medium text-slate-400">SDI/PEC</p>
                                        <p className="mt-0.5 text-sm text-slate-900">{partner.sdi_pec || '---'}</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3 sm:col-span-2">
                                    <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                        <MapPin className="h-4 w-4 text-slate-500" />
                                    </div>
                                    <div className="min-w-0">
                                        <p className="text-xs font-medium text-slate-400">Address</p>
                                        <p className="mt-0.5 text-sm text-slate-900">{partner.address || '---'}</p>
                                    </div>
                                </div>
                            </div>

                            {partner.notes && (
                                <>
                                    <Separator className="my-5" />
                                    <div>
                                        <p className="text-xs font-medium text-slate-400">Notes</p>
                                        <p className="mt-1.5 text-sm leading-relaxed text-slate-600">{partner.notes}</p>
                                    </div>
                                </>
                            )}
                        </CardContent>
                    </Card>

                    {/* Price Matrix */}
                    {tours.length > 0 && (
                        <Card className="border-slate-200 shadow-sm">
                            <CardHeader className="pb-3">
                                <CardTitle className="text-sm font-semibold text-slate-900">
                                    Price List
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-5">
                                {tours.map((tour) => {
                                    const tourPrices = priceMatrix[tour.id];
                                    if (!tourPrices) return null;
                                    return (
                                        <div key={tour.id}>
                                            <h4 className="mb-3 text-sm font-medium text-slate-700">
                                                {tour.name}
                                            </h4>
                                            <div className="overflow-x-auto rounded-lg border border-slate-200">
                                                <table className="w-full text-sm">
                                                    <thead>
                                                        <tr className="bg-slate-50">
                                                            <th className="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                                Season
                                                            </th>
                                                            <th className="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                                Adult
                                                            </th>
                                                            <th className="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                                Child
                                                            </th>
                                                            <th className="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                                Infant
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {Object.entries(tourPrices).map(([season, prices]) => (
                                                            <tr key={season} className="border-t border-slate-100">
                                                                <td className="px-4 py-2.5 text-sm font-medium capitalize text-slate-700">
                                                                    {season}
                                                                </td>
                                                                <td className="px-4 py-2.5 text-sm text-slate-600">
                                                                    &euro; {Number(prices.adult ?? 0).toFixed(2)}
                                                                </td>
                                                                <td className="px-4 py-2.5 text-sm text-slate-600">
                                                                    &euro; {Number(prices.child ?? 0).toFixed(2)}
                                                                </td>
                                                                <td className="px-4 py-2.5 text-sm text-slate-600">
                                                                    &euro; {Number(prices.infant ?? 0).toFixed(2)}
                                                                </td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    );
                                })}
                            </CardContent>
                        </Card>
                    )}

                    {/* Recent Bookings */}
                    {partner.bookings && partner.bookings.length > 0 && (
                        <Card className="border-slate-200 shadow-sm">
                            <CardHeader className="pb-3">
                                <CardTitle className="text-sm font-semibold text-slate-900">
                                    Recent Bookings
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-2">
                                    {partner.bookings.slice(0, 10).map((b) => (
                                        <a
                                            key={b.id}
                                            href={`/admin/bookings/${b.id}`}
                                            className="group flex items-center justify-between rounded-lg border border-slate-200 p-3.5 transition-all hover:border-slate-300 hover:bg-slate-50/50 hover:shadow-sm"
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 transition-colors group-hover:bg-slate-200/70">
                                                    <Calendar className="h-4 w-4 text-slate-500" />
                                                </div>
                                                <div>
                                                    <p className="text-sm font-medium text-slate-900">
                                                        {b.booking_code}
                                                    </p>
                                                    <p className="text-xs text-slate-400">{b.created_at}</p>
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-3">
                                                <span className="text-sm font-semibold text-slate-900">
                                                    &euro; {Number(b.total_amount).toFixed(2)}
                                                </span>
                                                <StatusBadge status={b.status} />
                                            </div>
                                        </a>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* Sidebar */}
                <div className="space-y-6">
                    {/* Financial Card */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="pb-3">
                            <CardTitle className="text-sm font-semibold text-slate-900">
                                Financial Summary
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="rounded-lg bg-slate-50 p-4">
                                <p className="text-xs font-medium uppercase tracking-wider text-slate-400">
                                    Outstanding Balance
                                </p>
                                <p className="mt-1 text-2xl font-bold text-red-600">
                                    &euro; {Number(partner.outstanding_balance).toFixed(2)}
                                </p>
                            </div>
                            <Separator />
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-slate-500">Status</span>
                                    {partner.is_active ? (
                                        <span className="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            Active
                                        </span>
                                    ) : (
                                        <span className="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                            Inactive
                                        </span>
                                    )}
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-slate-500">Total Bookings</span>
                                    <span className="text-sm font-medium text-slate-900">
                                        {partner.bookings_count ?? partner.bookings?.length ?? 0}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Quick Actions */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="pb-3">
                            <CardTitle className="text-sm font-semibold text-slate-900">
                                Quick Actions
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-2">
                            <Button variant="outline" className="w-full justify-start text-slate-600" asChild>
                                <a href={`/admin/partners/${partner.id}/edit`}>
                                    <Edit className="mr-2 h-4 w-4 text-slate-400" />
                                    Edit Partner
                                </a>
                            </Button>
                            <Button variant="outline" className="w-full justify-start text-slate-600" asChild>
                                <a href="/admin/partners">
                                    <ArrowLeft className="mr-2 h-4 w-4 text-slate-400" />
                                    Back to Partners
                                </a>
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AdminLayout>
    );
}
