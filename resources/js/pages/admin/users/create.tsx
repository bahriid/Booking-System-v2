import { useForm } from '@inertiajs/react';
import { ArrowLeft, Save, UserPlus } from 'lucide-react';
import { type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FormField } from '@/components/form-field';

interface CreateUserProps { roles: Array<{ value: string; label: string }>; }

export default function CreateUser({ roles }: CreateUserProps) {
    const { data, setData, post, processing, errors } = useForm({ name: '', email: '', password: '', password_confirmation: '', role: 'admin' });
    function handleSubmit(e: FormEvent) { e.preventDefault(); post('/admin/users'); }

    return (
        <AdminLayout
            pageTitle="New User"
            breadcrumbs={[{ label: 'Users', href: '/admin/users' }, { label: 'New User' }]}
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
                        {processing ? 'Creating...' : 'Create User'}
                    </Button>
                </div>
            }
        >
            <div className="mx-auto max-w-lg">
                <Card className="border-slate-200 shadow-sm">
                    <CardHeader className="border-b border-slate-100">
                        <CardTitle className="flex items-center gap-2 text-base text-slate-900">
                            <UserPlus className="h-5 w-5 text-slate-400" />
                            User Information
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
                                    onChange={(e) => setData('role', e.target.value)}
                                    className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                >
                                    {roles.map((r) => (
                                        <option key={r.value} value={r.value}>
                                            {r.label}
                                        </option>
                                    ))}
                                </select>
                            </FormField>

                            <div className="border-t border-slate-100 pt-5">
                                <p className="mb-4 text-xs font-medium uppercase tracking-wider text-slate-400">
                                    Security
                                </p>
                                <div className="space-y-5">
                                    <FormField label="Password" error={errors.password} required>
                                        <Input
                                            type="password"
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            placeholder="Minimum 8 characters"
                                        />
                                    </FormField>

                                    <FormField label="Confirm Password" error={errors.password_confirmation} required>
                                        <Input
                                            type="password"
                                            value={data.password_confirmation}
                                            onChange={(e) => setData('password_confirmation', e.target.value)}
                                            placeholder="Re-enter password"
                                        />
                                    </FormField>
                                </div>
                            </div>

                            <div className="flex items-center justify-between border-t border-slate-100 pt-5">
                                <Button type="button" variant="outline" asChild>
                                    <a href="/admin/users">
                                        <ArrowLeft className="mr-2 h-4 w-4" />
                                        Cancel
                                    </a>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    <Save className="mr-2 h-4 w-4" />
                                    {processing ? 'Creating...' : 'Create User'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
