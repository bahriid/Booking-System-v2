import { Head, useForm, Link } from '@inertiajs/react';
import { Ship, Mail, Lock, Eye, EyeOff, Check } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { FormField } from '@/components/form-field';

interface LoginProps {
    status?: string;
}

const demoAccounts = [
    { role: 'Admin', email: 'admin@magship.test', password: 'password', accent: 'bg-blue-50 text-blue-700 border-blue-200' },
    { role: 'Partner', email: 'bookings+staff@excelsiorvittoria.com', password: 'password', accent: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { role: 'Driver', email: 'driver@magship.test', password: 'password', accent: 'bg-amber-50 text-amber-700 border-amber-200' },
];

const features = [
    'Manage tour bookings efficiently',
    'Real-time availability calendar',
    'Multi-partner B2B management',
    'Automated PDF vouchers & manifests',
];

export default function Login({ status }: LoginProps) {
    const [showPassword, setShowPassword] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false as boolean,
    });

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        post('/login');
    }

    function fillDemo(email: string, password: string) {
        setData({ email, password, remember: false });
    }

    return (
        <>
            <Head title="Sign In" />

            <div className="flex min-h-screen bg-slate-50" style={{ fontFamily: "'Inter', sans-serif" }}>
                {/* Left Side -- Form */}
                <div className="relative flex w-full flex-col justify-center px-6 py-12 lg:w-[55%] lg:px-12 xl:px-20">
                    <div className="mx-auto w-full max-w-[440px]">
                        {/* Logo */}
                        <div className="mb-8 flex items-center gap-2.5">
                            <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 shadow-md shadow-blue-600/20">
                                <Ship className="h-5 w-5 text-white" />
                            </div>
                            <span className="text-xl font-bold tracking-tight text-slate-900">MagShip</span>
                        </div>

                        {/* Heading */}
                        <h1 className="text-[28px] font-bold leading-tight tracking-tight text-slate-900">
                            Welcome back
                        </h1>
                        <p className="mt-2 text-[15px] text-slate-500">
                            Sign in to your booking management account
                        </p>

                        {/* Status message */}
                        {status && (
                            <div className="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                                {status}
                            </div>
                        )}

                        {/* General auth error (e.g. throttle) */}
                        {errors.email && !data.email && (
                            <div className="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                                {errors.email}
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

                            <FormField label="Password" htmlFor="password" error={errors.password} required>
                                <div className="relative">
                                    <Lock className="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" />
                                    <Input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Enter your password"
                                        className="h-11 rounded-lg border-slate-200 bg-white pl-11 pr-11 text-[15px] shadow-sm transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500/20"
                                        autoComplete="current-password"
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

                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <Checkbox
                                        id="remember"
                                        checked={data.remember}
                                        onCheckedChange={(checked) => setData('remember', checked === true)}
                                        className="border-slate-300 data-[state=checked]:border-blue-600 data-[state=checked]:bg-blue-600"
                                    />
                                    <Label htmlFor="remember" className="cursor-pointer text-sm font-normal text-slate-600">
                                        Remember me
                                    </Label>
                                </div>
                                <Link
                                    href="/forgot-password"
                                    className="text-sm font-medium text-blue-600 transition-colors hover:text-blue-700"
                                >
                                    Forgot password?
                                </Link>
                            </div>

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
                                        Signing in...
                                    </span>
                                ) : (
                                    'Sign In'
                                )}
                            </Button>
                        </form>

                        {/* Demo Accounts */}
                        <div className="mt-10">
                            <div className="relative">
                                <div className="absolute inset-0 flex items-center">
                                    <div className="w-full border-t border-slate-200" />
                                </div>
                                <div className="relative flex justify-center">
                                    <span className="bg-slate-50 px-3 text-xs font-medium uppercase tracking-wider text-slate-400">
                                        Demo Accounts
                                    </span>
                                </div>
                            </div>

                            <div className="mt-5 space-y-2">
                                {demoAccounts.map((account) => (
                                    <button
                                        key={account.email}
                                        type="button"
                                        onClick={() => fillDemo(account.email, account.password)}
                                        className="group flex w-full items-center gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 text-left shadow-sm transition-all hover:border-slate-300 hover:shadow"
                                    >
                                        <span className={`inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold ${account.accent}`}>
                                            {account.role}
                                        </span>
                                        <span className="text-sm text-slate-600 transition-colors group-hover:text-slate-900">
                                            {account.email}
                                        </span>
                                        <svg className="ml-auto h-4 w-4 text-slate-300 transition-colors group-hover:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Side -- Brand Panel */}
                <div className="relative hidden overflow-hidden lg:flex lg:w-[45%]">
                    {/* Gradient background */}
                    <div className="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700" />

                    {/* Decorative circles */}
                    <div className="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/5" />
                    <div className="absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-white/5" />
                    <div className="absolute right-16 top-1/3 h-48 w-48 rounded-full bg-white/5" />

                    {/* Content */}
                    <div className="relative z-10 flex flex-col items-center justify-center px-12 xl:px-16">
                        <div className="max-w-md">
                            {/* Icon */}
                            <div className="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 shadow-lg backdrop-blur-sm">
                                <Ship className="h-8 w-8 text-white" />
                            </div>

                            <h2 className="text-[32px] font-bold leading-tight tracking-tight text-white">
                                B2B Booking<br />Management
                            </h2>
                            <p className="mt-4 text-[16px] leading-relaxed text-blue-100/90">
                                Streamline your tour ticket bookings with partners, hotels, and tour operators in one unified platform.
                            </p>

                            {/* Features */}
                            <div className="mt-10 space-y-4">
                                {features.map((feature) => (
                                    <div key={feature} className="flex items-center gap-3.5">
                                        <div className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/20">
                                            <Check className="h-3.5 w-3.5 text-white" />
                                        </div>
                                        <span className="text-[15px] font-medium text-blue-50/95">{feature}</span>
                                    </div>
                                ))}
                            </div>

                            {/* Subtle bottom branding */}
                            <div className="mt-16 flex items-center gap-2 border-t border-white/10 pt-6">
                                <Ship className="h-4 w-4 text-white/40" />
                                <span className="text-sm text-white/40">MagShip Platform</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
