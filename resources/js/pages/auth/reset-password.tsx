import { Head, useForm, Link } from '@inertiajs/react';
import { Ship, Lock, Eye, EyeOff, ArrowLeft, KeyRound } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { FormField } from '@/components/form-field';

interface ResetPasswordProps {
    token: string;
    email: string;
}

export default function ResetPassword({ token, email }: ResetPasswordProps) {
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        token,
        email,
        password: '',
        password_confirmation: '',
    });

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        post('/reset-password');
    }

    return (
        <>
            <Head title="Reset Password" />

            <div
                className="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12"
                style={{ fontFamily: "'Inter', sans-serif" }}
            >
                <div className="w-full max-w-[440px]">
                    {/* Logo */}
                    <div className="mb-8 flex flex-col items-center">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 shadow-lg shadow-blue-600/20">
                            <Ship className="h-6 w-6 text-white" />
                        </div>
                        <span className="mt-3 text-lg font-bold tracking-tight text-slate-900">MagShip</span>
                    </div>

                    {/* Card */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardContent className="px-8 py-8">
                            {/* Header */}
                            <div className="text-center">
                                <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-50">
                                    <KeyRound className="h-6 w-6 text-blue-600" />
                                </div>
                                <h1 className="text-[22px] font-bold tracking-tight text-slate-900">
                                    Set a new password
                                </h1>
                                <p className="mt-2 text-sm leading-relaxed text-slate-500">
                                    Create a strong password for <span className="font-medium text-slate-700">{email}</span>
                                </p>
                            </div>

                            {/* General error */}
                            {errors.email && (
                                <div className="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-center text-sm text-red-600">
                                    {errors.email}
                                </div>
                            )}

                            {/* Form */}
                            <form onSubmit={handleSubmit} className="mt-8 space-y-5">
                                {/* Hidden token + email */}
                                <input type="hidden" name="token" value={data.token} />
                                <input type="hidden" name="email" value={data.email} />

                                <FormField label="New password" htmlFor="password" error={errors.password} required description="Minimum 8 characters with letters and numbers">
                                    <div className="relative">
                                        <Lock className="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" />
                                        <Input
                                            id="password"
                                            type={showPassword ? 'text' : 'password'}
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            placeholder="Enter new password"
                                            className="h-11 rounded-lg border-slate-200 bg-white pl-11 pr-11 text-[15px] shadow-sm transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500/20"
                                            autoComplete="new-password"
                                            autoFocus
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="absolute right-3 top-1/2 -translate-y-1/2 rounded p-0.5 text-slate-400 transition-colors hover:text-slate-600"
                                            tabIndex={-1}
                                        >
                                            {showPassword ? <EyeOff className="h-[18px] w-[18px]" /> : <Eye className="h-[18px] w-[18px]" />}
                                        </button>
                                    </div>
                                </FormField>

                                <FormField label="Confirm password" htmlFor="password_confirmation" error={errors.password_confirmation} required>
                                    <div className="relative">
                                        <Lock className="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" />
                                        <Input
                                            id="password_confirmation"
                                            type={showConfirm ? 'text' : 'password'}
                                            value={data.password_confirmation}
                                            onChange={(e) => setData('password_confirmation', e.target.value)}
                                            placeholder="Confirm new password"
                                            className="h-11 rounded-lg border-slate-200 bg-white pl-11 pr-11 text-[15px] shadow-sm transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500/20"
                                            autoComplete="new-password"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowConfirm(!showConfirm)}
                                            className="absolute right-3 top-1/2 -translate-y-1/2 rounded p-0.5 text-slate-400 transition-colors hover:text-slate-600"
                                            tabIndex={-1}
                                        >
                                            {showConfirm ? <EyeOff className="h-[18px] w-[18px]" /> : <Eye className="h-[18px] w-[18px]" />}
                                        </button>
                                    </div>
                                </FormField>

                                <Button
                                    type="submit"
                                    className="h-11 w-full rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-[15px] font-semibold shadow-md shadow-blue-600/25 transition-all hover:from-blue-700 hover:to-indigo-700 hover:shadow-lg hover:shadow-blue-600/30"
                                    disabled={processing}
                                >
                                    {processing ? (
                                        <span className="flex items-center gap-2">
                                            <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                            </svg>
                                            Resetting password...
                                        </span>
                                    ) : (
                                        'Reset Password'
                                    )}
                                </Button>
                            </form>

                            {/* Back to login */}
                            <div className="mt-6 text-center">
                                <Link
                                    href="/login"
                                    className="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition-colors hover:text-slate-700"
                                >
                                    <ArrowLeft className="h-4 w-4" />
                                    Back to sign in
                                </Link>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Footer */}
                    <div className="mt-8 flex items-center justify-center gap-2">
                        <Ship className="h-3.5 w-3.5 text-slate-300" />
                        <span className="text-xs text-slate-400">MagShip B2B Booking Platform</span>
                    </div>
                </div>
            </div>
        </>
    );
}
