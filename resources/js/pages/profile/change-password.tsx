import { useForm, usePage } from '@inertiajs/react';
import { Lock, Eye, EyeOff, ShieldCheck } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import RoleLayout from '@/layouts/role-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { FormField } from '@/components/form-field';
import { type SharedProps } from '@/types';

const dashboardRoutes: Record<string, string> = {
    admin: '/admin',
    partner: '/partner',
    driver: '/driver',
};

export default function ChangePassword() {
    const { auth } = usePage<SharedProps>().props;
    const [showCurrent, setShowCurrent] = useState(false);
    const [showNew, setShowNew] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        post('/profile/password', {
            onSuccess: () => {
                setData({ current_password: '', password: '', password_confirmation: '' });
            },
        });
    }

    const cancelHref = auth ? dashboardRoutes[auth.user.role] ?? '/admin' : '/';

    return (
        <RoleLayout pageTitle="Change Password" breadcrumbs={[{ label: 'Change Password' }]}>
            <div className="mx-auto max-w-lg">
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-100 bg-slate-50 px-6 py-4">
                        <div className="flex items-center gap-3">
                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200">
                                <ShieldCheck className="h-5 w-5 text-slate-600" />
                            </div>
                            <div>
                                <h2 className="text-base font-semibold text-slate-900">Change Password</h2>
                                <p className="text-xs text-slate-500">Update your account password for security.</p>
                            </div>
                        </div>
                    </div>
                    <div className="p-6">
                        <form onSubmit={handleSubmit} className="space-y-5">
                            <FormField label="Current Password" htmlFor="current_password" error={errors.current_password} required>
                                <div className="relative">
                                    <Input
                                        id="current_password"
                                        type={showCurrent ? 'text' : 'password'}
                                        value={data.current_password}
                                        onChange={(e) => setData('current_password', e.target.value)}
                                        placeholder="Enter your current password"
                                        className="border-slate-200 pr-10"
                                        autoComplete="current-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowCurrent(!showCurrent)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition-colors hover:text-slate-600"
                                    >
                                        {showCurrent ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                    </button>
                                </div>
                            </FormField>

                            <FormField label="New Password" htmlFor="password" error={errors.password} required>
                                <div className="relative">
                                    <Input
                                        id="password"
                                        type={showNew ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Enter new password"
                                        className="border-slate-200 pr-10"
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowNew(!showNew)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition-colors hover:text-slate-600"
                                    >
                                        {showNew ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                    </button>
                                </div>
                                <p className="mt-1.5 text-xs text-slate-400">
                                    Min 8 characters, with upper & lowercase letters and numbers
                                </p>
                            </FormField>

                            <FormField label="Confirm New Password" htmlFor="password_confirmation" error={errors.password_confirmation} required>
                                <div className="relative">
                                    <Input
                                        id="password_confirmation"
                                        type={showConfirm ? 'text' : 'password'}
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        placeholder="Confirm new password"
                                        className="border-slate-200 pr-10"
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowConfirm(!showConfirm)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition-colors hover:text-slate-600"
                                    >
                                        {showConfirm ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                    </button>
                                </div>
                            </FormField>

                            <div className="flex items-center gap-3 border-t border-slate-100 pt-5">
                                <Button type="submit" disabled={processing}>
                                    <Lock className="mr-2 h-4 w-4" />
                                    {processing ? 'Updating...' : 'Update Password'}
                                </Button>
                                <Button type="button" variant="outline" asChild className="border-slate-200 text-slate-700 hover:bg-slate-50">
                                    <a href={cancelHref}>Cancel</a>
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </RoleLayout>
    );
}
