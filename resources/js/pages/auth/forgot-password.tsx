import { Head, useForm, Link } from '@inertiajs/react';
import { Ship, Mail, ArrowLeft } from 'lucide-react';
import { type FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { FormField } from '@/components/form-field';

interface ForgotPasswordProps {
    status?: string;
}

export default function ForgotPassword({ status }: ForgotPasswordProps) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        post('/forgot-password');
    }

    return (
        <>
            <Head title="Forgot Password" />

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
                                <h1 className="text-[22px] font-bold tracking-tight text-slate-900">
                                    Forgot your password?
                                </h1>
                                <p className="mt-2 text-sm leading-relaxed text-slate-500">
                                    No worries. Enter the email address associated with your account and we'll send you a link to reset your password.
                                </p>
                            </div>

                            {/* Status message */}
                            {status && (
                                <div className="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-700">
                                    <div className="flex items-center justify-center gap-2">
                                        <svg className="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {status}
                                    </div>
                                </div>
                            )}

                            {/* Form */}
                            <form onSubmit={handleSubmit} className="mt-8 space-y-5">
                                <FormField label="Email address" htmlFor="email" error={errors.email} required>
                                    <div className="relative">
                                        <Mail className="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" />
                                        <Input
                                            id="email"
                                            type="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            placeholder="you@company.com"
                                            className="h-11 rounded-lg border-slate-200 bg-white pl-11 text-[15px] shadow-sm transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500/20"
                                            autoComplete="email"
                                            autoFocus
                                        />
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
                                            Sending link...
                                        </span>
                                    ) : (
                                        'Send Reset Link'
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
