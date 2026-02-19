import { router } from '@inertiajs/react';
import { PlusCircle, Pencil, ToggleLeft, ToggleRight, MapPin, Clock } from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Pagination } from '@/components/pagination';
import { type PaginatedData, type PickupPoint } from '@/types';

interface PickupPointIndexProps { pickupPoints: PaginatedData<PickupPoint>; }

export default function PickupPointIndex({ pickupPoints }: PickupPointIndexProps) {
    return (
        <AdminLayout
            pageTitle="Pickup Points"
            breadcrumbs={[{ label: 'Pickup Points' }]}
            toolbarActions={
                <Button asChild>
                    <a href="/admin/pickup-points/create">
                        <PlusCircle className="mr-2 h-4 w-4" />
                        New Pickup Point
                    </a>
                </Button>
            }
        >
            <Card className="border-slate-200 shadow-sm">
                <CardHeader className="border-b border-slate-100">
                    <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                        <MapPin className="h-5 w-5 text-slate-400" />
                        All Pickup Points
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    {pickupPoints.data.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-16">
                            <div className="rounded-full bg-slate-100 p-3">
                                <MapPin className="h-6 w-6 text-slate-400" />
                            </div>
                            <p className="mt-3 text-sm text-slate-500">No pickup points yet.</p>
                            <Button asChild size="sm" className="mt-4">
                                <a href="/admin/pickup-points/create">
                                    <PlusCircle className="mr-2 h-4 w-4" />
                                    Create First Pickup Point
                                </a>
                            </Button>
                        </div>
                    ) : (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="bg-slate-50">
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                Name
                                            </th>
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                Location
                                            </th>
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                Default Time
                                            </th>
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                Order
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
                                        {pickupPoints.data.map((pp) => (
                                            <tr
                                                key={pp.id}
                                                className="border-b border-slate-100 transition-colors hover:bg-slate-50/50"
                                            >
                                                <td className="px-4 py-3.5 text-sm font-medium text-slate-900">
                                                    {pp.name}
                                                </td>
                                                <td className="px-4 py-3.5 text-sm text-slate-600">
                                                    {pp.location ? (
                                                        <span className="flex items-center gap-1.5">
                                                            <MapPin className="h-3.5 w-3.5 text-slate-400" />
                                                            {pp.location}
                                                        </span>
                                                    ) : (
                                                        <span className="text-slate-400">&mdash;</span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-3.5 text-sm text-slate-600">
                                                    {pp.default_time ? (
                                                        <span className="flex items-center gap-1.5">
                                                            <Clock className="h-3.5 w-3.5 text-slate-400" />
                                                            {pp.default_time}
                                                        </span>
                                                    ) : (
                                                        <span className="text-slate-400">&mdash;</span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    <span className="inline-flex h-6 w-6 items-center justify-center rounded bg-slate-100 text-xs font-medium text-slate-600">
                                                        {pp.sort_order}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    {pp.is_active ? (
                                                        <Badge className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 hover:bg-emerald-50">
                                                            Active
                                                        </Badge>
                                                    ) : (
                                                        <Badge className="bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-500/10 hover:bg-slate-50">
                                                            Inactive
                                                        </Badge>
                                                    )}
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    <div className="flex items-center justify-end gap-0.5">
                                                        <Button
                                                            variant="ghost"
                                                            size="icon-sm"
                                                            asChild
                                                            className="text-slate-500 hover:text-slate-700"
                                                        >
                                                            <a
                                                                href={`/admin/pickup-points/${pp.id}/edit`}
                                                                title="Edit pickup point"
                                                            >
                                                                <Pencil className="h-4 w-4" />
                                                            </a>
                                                        </Button>
                                                        <Button
                                                            variant="ghost"
                                                            size="icon-sm"
                                                            className={
                                                                pp.is_active
                                                                    ? 'text-emerald-600 hover:text-emerald-700'
                                                                    : 'text-slate-400 hover:text-slate-600'
                                                            }
                                                            onClick={() =>
                                                                router.post(
                                                                    `/admin/pickup-points/${pp.id}/toggle-active`,
                                                                )
                                                            }
                                                            title={pp.is_active ? 'Deactivate' : 'Activate'}
                                                        >
                                                            {pp.is_active ? (
                                                                <ToggleRight className="h-4 w-4" />
                                                            ) : (
                                                                <ToggleLeft className="h-4 w-4" />
                                                            )}
                                                        </Button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <div className="px-4 pb-2">
                                <Pagination
                                    links={pickupPoints.links}
                                    from={pickupPoints.from}
                                    to={pickupPoints.to}
                                    total={pickupPoints.total}
                                />
                            </div>
                        </>
                    )}
                </CardContent>
            </Card>
        </AdminLayout>
    );
}
