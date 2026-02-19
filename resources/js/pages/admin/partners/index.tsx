import { router } from '@inertiajs/react';
import { Search, X, PlusCircle, Eye, Building2, Users } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { Pagination } from '@/components/pagination';
import { PartnerAvatar } from '@/components/partner/avatar';
import { type PaginatedData, type Partner } from '@/types';

interface PartnerIndexProps {
    partners: PaginatedData<Partner>;
    filters: Record<string, string>;
}

export default function PartnerIndex({ partners, filters }: PartnerIndexProps) {
    const [f, setF] = useState(filters);
    function handleFilter(e: FormEvent) { e.preventDefault(); router.get('/admin/partners', Object.fromEntries(Object.entries(f).filter(([, v]) => v)), { preserveState: true }); }
    function clear() { setF({}); router.get('/admin/partners'); }

    const hasFilters = Object.values(f).some(Boolean);

    return (
        <AdminLayout
            pageTitle="Partners"
            breadcrumbs={[{ label: 'Partners' }]}
            toolbarActions={
                <Button asChild>
                    <a href="/admin/partners/create">
                        <PlusCircle className="mr-2 h-4 w-4" />
                        New Partner
                    </a>
                </Button>
            }
        >
            <div className="space-y-6">
                {/* Page header */}
                <div>
                    <h1 className="text-lg font-semibold text-slate-900">Partners</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Manage your B2B partners, hotels and tour operators.
                    </p>
                </div>

                {/* Filters */}
                <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                    <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
                        <div className="min-w-[240px] flex-1 sm:max-w-xs">
                            <label className="mb-1.5 block text-xs font-medium text-slate-500">
                                Search
                            </label>
                            <div className="relative">
                                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                <Input
                                    value={f.search ?? ''}
                                    onChange={(e) => setF({ ...f, search: e.target.value })}
                                    placeholder="Name or email..."
                                    className="pl-10"
                                />
                            </div>
                        </div>
                        <div>
                            <label className="mb-1.5 block text-xs font-medium text-slate-500">
                                Type
                            </label>
                            <select
                                value={f.type ?? ''}
                                onChange={(e) => setF({ ...f, type: e.target.value })}
                                className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                            >
                                <option value="">All Types</option>
                                <option value="hotel">Hotel</option>
                                <option value="tour_operator">Tour Operator</option>
                            </select>
                        </div>
                        <div>
                            <label className="mb-1.5 block text-xs font-medium text-slate-500">
                                Status
                            </label>
                            <select
                                value={f.status ?? ''}
                                onChange={(e) => setF({ ...f, status: e.target.value })}
                                className="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900"
                            >
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <Button type="submit" size="sm">
                            <Search className="mr-1.5 h-3.5 w-3.5" />
                            Filter
                        </Button>
                        {hasFilters && (
                            <Button type="button" variant="ghost" size="sm" onClick={clear} className="text-slate-500 hover:text-slate-700">
                                <X className="mr-1 h-3.5 w-3.5" />
                                Clear
                            </Button>
                        )}
                    </form>
                </div>

                {/* Table */}
                <Card className="border-slate-200 shadow-sm">
                    <CardContent className="p-0">
                        {partners.data.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-16">
                                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                    <Users className="h-6 w-6 text-slate-400" />
                                </div>
                                <p className="mt-4 text-sm font-medium text-slate-900">No partners found</p>
                                <p className="mt-1 text-sm text-slate-500">
                                    {hasFilters
                                        ? 'Try adjusting your filters to find what you\'re looking for.'
                                        : 'Get started by creating your first partner.'}
                                </p>
                                {!hasFilters && (
                                    <Button asChild className="mt-4" size="sm">
                                        <a href="/admin/partners/create">
                                            <PlusCircle className="mr-2 h-4 w-4" />
                                            New Partner
                                        </a>
                                    </Button>
                                )}
                            </div>
                        ) : (
                            <>
                                <div className="overflow-x-auto">
                                    <table className="w-full">
                                        <thead>
                                            <tr className="bg-slate-50">
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Partner
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Type
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Email
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Bookings
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Balance
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Status
                                                </th>
                                                <th className="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {partners.data.map((p) => (
                                                <tr
                                                    key={p.id}
                                                    className="border-b border-slate-100 transition-colors last:border-b-0 hover:bg-slate-50/50"
                                                >
                                                    <td className="px-4 py-3.5">
                                                        <div className="flex items-center gap-3">
                                                            <PartnerAvatar name={p.name} initials={p.initials} className="h-8 w-8" />
                                                            <span className="text-sm font-medium text-slate-900">
                                                                {p.name}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td className="px-4 py-3.5">
                                                        <div className="flex items-center gap-1.5 text-sm text-slate-600">
                                                            <Building2 className="h-3.5 w-3.5 text-slate-400" />
                                                            <span className="capitalize">{p.type?.replace('_', ' ')}</span>
                                                        </div>
                                                    </td>
                                                    <td className="px-4 py-3.5 text-sm text-slate-600">
                                                        {p.email}
                                                    </td>
                                                    <td className="px-4 py-3.5 text-sm text-slate-600">
                                                        {p.bookings_count ?? 0}
                                                    </td>
                                                    <td className="px-4 py-3.5">
                                                        <span className="text-sm font-medium text-slate-900">
                                                            &euro; {Number(p.outstanding_balance).toFixed(2)}
                                                        </span>
                                                    </td>
                                                    <td className="px-4 py-3.5">
                                                        {p.is_active ? (
                                                            <span className="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                                Active
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                                                Inactive
                                                            </span>
                                                        )}
                                                    </td>
                                                    <td className="px-4 py-3.5 text-right">
                                                        <Button variant="ghost" size="icon" className="h-8 w-8 text-slate-500 hover:text-slate-700" asChild>
                                                            <a href={`/admin/partners/${p.id}`}>
                                                                <Eye className="h-4 w-4" />
                                                            </a>
                                                        </Button>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                                <div className="border-t border-slate-100 px-4 py-3">
                                    <Pagination links={partners.links} from={partners.from} to={partners.to} total={partners.total} />
                                </div>
                            </>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
