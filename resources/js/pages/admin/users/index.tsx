import { router } from '@inertiajs/react';
import { PlusCircle, Pencil, KeyRound, ToggleLeft, ToggleRight, Users, ShieldCheck } from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Pagination } from '@/components/pagination';
import { type PaginatedData, type User } from '@/types';

interface UserIndexProps { users: PaginatedData<User>; }

export default function UserIndex({ users }: UserIndexProps) {
    return (
        <AdminLayout
            pageTitle="Users"
            breadcrumbs={[{ label: 'Users' }]}
            toolbarActions={
                <Button asChild>
                    <a href="/admin/users/create">
                        <PlusCircle className="mr-2 h-4 w-4" />
                        New User
                    </a>
                </Button>
            }
        >
            <Card className="border-slate-200 shadow-sm">
                <CardHeader className="border-b border-slate-100">
                    <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                        <ShieldCheck className="h-5 w-5 text-slate-400" />
                        Admin & Driver Users
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    {users.data.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-16">
                            <div className="rounded-full bg-slate-100 p-3">
                                <Users className="h-6 w-6 text-slate-400" />
                            </div>
                            <p className="mt-3 text-sm text-slate-500">No users found.</p>
                            <Button asChild size="sm" className="mt-4">
                                <a href="/admin/users/create">
                                    <PlusCircle className="mr-2 h-4 w-4" />
                                    Create First User
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
                                                Email
                                            </th>
                                            <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                                                Role
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
                                        {users.data.map((u) => (
                                            <tr
                                                key={u.id}
                                                className="border-b border-slate-100 transition-colors hover:bg-slate-50/50"
                                            >
                                                <td className="px-4 py-3.5 text-sm font-medium text-slate-900">
                                                    {u.name}
                                                </td>
                                                <td className="px-4 py-3.5 text-sm text-slate-600">
                                                    {u.email}
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    <Badge
                                                        variant="secondary"
                                                        className="bg-slate-100 capitalize text-slate-700 ring-1 ring-inset ring-slate-200"
                                                    >
                                                        {u.role}
                                                    </Badge>
                                                </td>
                                                <td className="px-4 py-3.5">
                                                    {u.is_active ? (
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
                                                            <a href={`/admin/users/${u.id}/edit`} title="Edit user">
                                                                <Pencil className="h-4 w-4" />
                                                            </a>
                                                        </Button>
                                                        <Button
                                                            variant="ghost"
                                                            size="icon-sm"
                                                            className={
                                                                u.is_active
                                                                    ? 'text-emerald-600 hover:text-emerald-700'
                                                                    : 'text-slate-400 hover:text-slate-600'
                                                            }
                                                            onClick={() =>
                                                                router.post(`/admin/users/${u.id}/toggle-active`)
                                                            }
                                                            title={u.is_active ? 'Deactivate' : 'Activate'}
                                                        >
                                                            {u.is_active ? (
                                                                <ToggleRight className="h-4 w-4" />
                                                            ) : (
                                                                <ToggleLeft className="h-4 w-4" />
                                                            )}
                                                        </Button>
                                                        <Button
                                                            variant="ghost"
                                                            size="icon-sm"
                                                            className="text-slate-500 hover:text-amber-600"
                                                            onClick={() => {
                                                                if (confirm('Reset password?'))
                                                                    router.post(`/admin/users/${u.id}/reset-password`);
                                                            }}
                                                            title="Reset password"
                                                        >
                                                            <KeyRound className="h-4 w-4" />
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
                                    links={users.links}
                                    from={users.from}
                                    to={users.to}
                                    total={users.total}
                                />
                            </div>
                        </>
                    )}
                </CardContent>
            </Card>
        </AdminLayout>
    );
}
