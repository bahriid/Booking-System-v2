import { useForm } from '@inertiajs/react';
import { ArrowLeft, Save, UserCog } from 'lucide-react';
import { type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FormField } from '@/components/form-field';
import { type User } from '@/types';

interface EditUserProps { user: User; roles: Array<{ value: string; label: string }>; }

export default function EditUser({ user, roles }: EditUserProps) {
    const { data, setData, put, processing, errors } = useForm({ name: user.name, email: user.email, role: user.role });
    function handleSubmit(e: FormEvent) { e.preventDefault(); put(`/admin/users/${user.id}`); }

    return (
        <AdminLayout
            pageTitle={`Edit ${user.name}`}
            breadcrumbs={[{ label: 'Users', href: '/admin/users' }, { label: 'Edit' }]}
            toolbarActions={
                <div className="flex items-center gap-2">
                    <Button variant="outline" asChild>
                        <a href="/admin/users">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back
                        </a>
                    </Button>
                    <Button onClick={handleSubmit} disabled={processing}>
                        <Save className="mr-2 h-4 w-4" />
                        {processing ? 'Saving...' : 'Update User'}
                    </Button>
                </div>
            }
        >
            <div className="mx-auto max-w-lg">
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="border-b border-slate-100">
                        <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                            <UserCog className="h-5 w-5 text-slate-400" />
                            Edit User
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-5">
                            <FormField label="Full Name" error={errors.name} required>
                                <Input
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="Enter full name"
                                />
                            </FormField>

                            <FormField label="Email Address" error={errors.email} required>
                                <Input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    placeholder="user@example.com"
                                />
                            </FormField>

                            <FormField label="Role" error={errors.role} required>
                                <select
                                    value={data.role}
                                    onChange={(e) => setData('role', e.target.value as any)}
                                    className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                >
                                    {roles.map((r) => (
                                        <option key={r.value} value={r.value}>
                                            {r.label}
                                        </option>
                                    ))}
                                </select>
                            </FormField>

                            <div className="flex items-center justify-between border-t border-slate-100 pt-5">
                                <Button type="button" variant="outline" asChild>
                                    <a href="/admin/users">
                                        <ArrowLeft className="mr-2 h-4 w-4" />
                                        Cancel
                                    </a>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    <Save className="mr-2 h-4 w-4" />
                                    {processing ? 'Saving...' : 'Update User'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
