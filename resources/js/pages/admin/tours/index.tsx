import { router } from '@inertiajs/react';
import { Search, X, PlusCircle, Eye, Edit, MapPin, Users, Calendar } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { Pagination } from '@/components/pagination';
import { type PaginatedData, type Tour } from '@/types';

interface TourIndexProps {
    tours: PaginatedData<Tour>;
    filters: Record<string, string>;
}

export default function TourIndex({ tours, filters }: TourIndexProps) {
    const [f, setF] = useState(filters);
    function handleFilter(e: FormEvent) { e.preventDefault(); router.get('/admin/tours', Object.fromEntries(Object.entries(f).filter(([, v]) => v)), { preserveState: true }); }

    const hasFilters = Object.values(f).some(Boolean);

    return (
        <AdminLayout
            pageTitle="Tours"
            breadcrumbs={[{ label: 'Tours' }]}
            toolbarActions={
                <Button asChild>
                    <a href="/admin/tours/create">
                        <PlusCircle className="mr-2 h-4 w-4" />
                        New Tour
                    </a>
                </Button>
            }
        >
            <div className="space-y-6">
                {/* Page header */}
                <div>
                    <h1 className="text-lg font-semibold text-slate-900">Tours</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Manage tours, capacity settings and seasonal schedules.
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
                                    placeholder="Name or code..."
                                    className="pl-10"
                                />
                            </div>
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
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={() => { setF({}); router.get('/admin/tours'); }}
                                className="text-slate-500 hover:text-slate-700"
                            >
                                <X className="mr-1 h-3.5 w-3.5" />
                                Clear
                            </Button>
                        )}
                    </form>
                </div>

                {/* Table */}
                <Card className="border-slate-200 shadow-sm">
                    <CardContent className="p-0">
                        {tours.data.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-16">
                                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                    <MapPin className="h-6 w-6 text-slate-400" />
                                </div>
                                <p className="mt-4 text-sm font-medium text-slate-900">No tours found</p>
                                <p className="mt-1 text-sm text-slate-500">
                                    {hasFilters
                                        ? 'Try adjusting your filters to find what you\'re looking for.'
                                        : 'Get started by creating your first tour.'}
                                </p>
                                {!hasFilters && (
                                    <Button asChild className="mt-4" size="sm">
                                        <a href="/admin/tours/create">
                                            <PlusCircle className="mr-2 h-4 w-4" />
                                            New Tour
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
                                                    Tour
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Code
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Season
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Capacity
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                    Departures
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
                                            {tours.data.map((t) => (
                                                <tr
                                                    key={t.id}
                                                    className="border-b border-slate-100 transition-colors last:border-b-0 hover:bg-slate-50/50"
                                                >
                                                    <td className="px-4 py-3.5">
                                                        <span className="text-sm font-medium text-slate-900">
                                                            {t.name}
                                                        </span>
                                                    </td>
                                                    <td className="px-4 py-3.5">
                                                        <span className="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-500/10">
                                                            {t.code}
                                                        </span>
                                                    </td>
                                                    <td className="px-4 py-3.5">
                                                        <div className="flex items-center gap-1.5 text-sm text-slate-600">
                                                            <Calendar className="h-3.5 w-3.5 text-slate-400" />
                                                            {t.seasonality_range}
                                                        </div>
                                                    </td>
                                                    <td className="px-4 py-3.5">
                                                        <div className="flex items-center gap-1.5 text-sm text-slate-600">
                                                            <Users className="h-3.5 w-3.5 text-slate-400" />
                                                            {t.default_capacity}
                                                        </div>
                                                    </td>
                                                    <td className="px-4 py-3.5 text-sm text-slate-600">
                                                        {t.departures_count ?? 0}
                                                    </td>
                                                    <td className="px-4 py-3.5">
                                                        {t.is_active ? (
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
                                                        <div className="flex items-center justify-end gap-0.5">
                                                            <Button variant="ghost" size="icon" className="h-8 w-8 text-slate-500 hover:text-slate-700" asChild>
                                                                <a href={`/admin/tours/${t.id}`}>
                                                                    <Eye className="h-4 w-4" />
                                                                </a>
                                                            </Button>
                                                            <Button variant="ghost" size="icon" className="h-8 w-8 text-slate-500 hover:text-slate-700" asChild>
                                                                <a href={`/admin/tours/${t.id}/edit`}>
                                                                    <Edit className="h-4 w-4" />
                                                                </a>
                                                            </Button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                                <div className="border-t border-slate-100 px-4 py-3">
                                    <Pagination links={tours.links} from={tours.from} to={tours.to} total={tours.total} />
                                </div>
                            </>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
