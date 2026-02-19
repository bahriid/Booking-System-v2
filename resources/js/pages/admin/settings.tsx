import { useForm, router } from '@inertiajs/react';
import {
    Save,
    Send,
    Building2,
    Globe,
    Clock,
    CalendarDays,
    Timer,
    AlertTriangle,
    Percent,
    Server,
    Hash,
    User,
    Lock,
    Mail,
    UserCircle,
    Shield,
    FileText,
    Settings2,
} from 'lucide-react';
import { type FormEvent } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FormField } from '@/components/form-field';

interface SettingsProps {
    generalSettings: Record<string, string>;
    bookingSettings: Record<string, string>;
    emailSettings: Record<string, string>;
    languageSettings: Record<string, string>;
    voucherSettings: Record<string, string>;
    timezones: string[];
    currencies: string[];
    dateFormats: string[];
    languages: Array<{ code: string; label: string }>;
}

export default function Settings(props: SettingsProps) {
    const general = useForm(props.generalSettings);
    const booking = useForm(props.bookingSettings);
    const email = useForm(props.emailSettings);

    return (
        <AdminLayout pageTitle="Settings" breadcrumbs={[{ label: 'Settings' }]}>
            <div className="mx-auto max-w-4xl">
                <Tabs defaultValue="general">
                    <TabsList className="mb-6 w-full justify-start border-b border-slate-200 bg-transparent p-0" variant="line">
                        <TabsTrigger
                            value="general"
                            className="gap-1.5 data-[state=active]:text-slate-900"
                        >
                            <Settings2 className="h-4 w-4" />
                            General
                        </TabsTrigger>
                        <TabsTrigger
                            value="booking"
                            className="gap-1.5 data-[state=active]:text-slate-900"
                        >
                            <FileText className="h-4 w-4" />
                            Booking
                        </TabsTrigger>
                        <TabsTrigger
                            value="email"
                            className="gap-1.5 data-[state=active]:text-slate-900"
                        >
                            <Mail className="h-4 w-4" />
                            Email
                        </TabsTrigger>
                        <TabsTrigger
                            value="voucher"
                            className="gap-1.5 data-[state=active]:text-slate-900"
                        >
                            <FileText className="h-4 w-4" />
                            Voucher
                        </TabsTrigger>
                    </TabsList>

                    {/* General Settings */}
                    <TabsContent value="general">
                        <Card className="border-slate-200 shadow-sm">
                            <CardHeader>
                                <div className="flex items-center gap-3">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50">
                                        <Settings2 className="h-5 w-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <CardTitle className="text-base text-slate-900">
                                            General Settings
                                        </CardTitle>
                                        <CardDescription className="text-sm text-slate-500">
                                            Configure your company information and regional preferences.
                                        </CardDescription>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <form
                                    onSubmit={(e: FormEvent) => {
                                        e.preventDefault();
                                        router.post('/admin/settings/general', general.data);
                                    }}
                                    className="space-y-6"
                                >
                                    <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-5">
                                        <div className="grid gap-5 sm:grid-cols-2">
                                            <FormField label="Company Name">
                                                <div className="relative">
                                                    <Building2 className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                    <Input
                                                        value={general.data.company_name ?? ''}
                                                        onChange={(e) => general.setData('company_name', e.target.value)}
                                                        className="bg-white pl-10"
                                                        placeholder="Your company name"
                                                    />
                                                </div>
                                            </FormField>
                                            <FormField label="Timezone">
                                                <Select
                                                    value={general.data.timezone ?? ''}
                                                    onValueChange={(v) => general.setData('timezone', v)}
                                                >
                                                    <SelectTrigger className="w-full bg-white">
                                                        <div className="flex items-center gap-2">
                                                            <Globe className="h-4 w-4 text-slate-400" />
                                                            <SelectValue placeholder="Select timezone" />
                                                        </div>
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {props.timezones.map((tz) => (
                                                            <SelectItem key={tz} value={tz}>
                                                                {tz}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </FormField>
                                            <FormField label="Currency">
                                                <Select
                                                    value={general.data.currency ?? ''}
                                                    onValueChange={(v) => general.setData('currency', v)}
                                                >
                                                    <SelectTrigger className="w-full bg-white">
                                                        <div className="flex items-center gap-2">
                                                            <span className="text-sm text-slate-400">&euro;</span>
                                                            <SelectValue placeholder="Select currency" />
                                                        </div>
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {props.currencies.map((c) => (
                                                            <SelectItem key={c} value={c}>
                                                                {c}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </FormField>
                                            <FormField label="Date Format">
                                                <Select
                                                    value={general.data.date_format ?? ''}
                                                    onValueChange={(v) => general.setData('date_format', v)}
                                                >
                                                    <SelectTrigger className="w-full bg-white">
                                                        <div className="flex items-center gap-2">
                                                            <CalendarDays className="h-4 w-4 text-slate-400" />
                                                            <SelectValue placeholder="Select format" />
                                                        </div>
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {props.dateFormats.map((df) => (
                                                            <SelectItem key={df} value={df}>
                                                                {df}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </FormField>
                                        </div>
                                    </div>
                                    <div className="flex justify-end">
                                        <Button type="submit" disabled={general.processing}>
                                            <Save className="mr-2 h-4 w-4" />
                                            {general.processing ? 'Saving...' : 'Save Changes'}
                                        </Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    {/* Booking Settings */}
                    <TabsContent value="booking">
                        <Card className="border-slate-200 shadow-sm">
                            <CardHeader>
                                <div className="flex items-center gap-3">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-violet-50">
                                        <FileText className="h-5 w-5 text-violet-600" />
                                    </div>
                                    <div>
                                        <CardTitle className="text-base text-slate-900">
                                            Booking Settings
                                        </CardTitle>
                                        <CardDescription className="text-sm text-slate-500">
                                            Configure booking cutoff times, overbooking policies, and cancellation rules.
                                        </CardDescription>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <form
                                    onSubmit={(e: FormEvent) => {
                                        e.preventDefault();
                                        router.post('/admin/settings/booking', booking.data);
                                    }}
                                    className="space-y-6"
                                >
                                    <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-5">
                                        <div className="grid gap-5 sm:grid-cols-2">
                                            <FormField
                                                label="Default Cutoff Hours"
                                                description="Hours before departure when booking closes"
                                            >
                                                <div className="relative">
                                                    <Timer className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                    <Input
                                                        type="number"
                                                        value={booking.data.default_cutoff_hours ?? ''}
                                                        onChange={(e) => booking.setData('default_cutoff_hours', e.target.value)}
                                                        className="bg-white pl-10"
                                                        placeholder="e.g. 24"
                                                    />
                                                </div>
                                            </FormField>
                                            <FormField
                                                label="Overbooking Timeout (hours)"
                                                description="How long before overbooking requests expire"
                                            >
                                                <div className="relative">
                                                    <Clock className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                    <Input
                                                        type="number"
                                                        value={booking.data.overbooking_timeout_hours ?? ''}
                                                        onChange={(e) => booking.setData('overbooking_timeout_hours', e.target.value)}
                                                        className="bg-white pl-10"
                                                        placeholder="e.g. 2"
                                                    />
                                                </div>
                                            </FormField>
                                            <FormField
                                                label="Cancellation Penalty %"
                                                description="Percentage applied as penalty on cancellation"
                                            >
                                                <div className="relative">
                                                    <Percent className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                    <Input
                                                        type="number"
                                                        value={booking.data.cancellation_penalty_percentage ?? ''}
                                                        onChange={(e) => booking.setData('cancellation_penalty_percentage', e.target.value)}
                                                        className="bg-white pl-10"
                                                        placeholder="e.g. 30"
                                                    />
                                                </div>
                                            </FormField>
                                        </div>
                                    </div>

                                    {/* Info banner */}
                                    <div className="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4">
                                        <AlertTriangle className="mt-0.5 h-4 w-4 shrink-0 text-amber-600" />
                                        <div>
                                            <p className="text-sm font-medium text-amber-800">Important</p>
                                            <p className="mt-0.5 text-xs text-amber-700">
                                                Changes to these settings will apply to all new bookings. Existing bookings
                                                will not be affected.
                                            </p>
                                        </div>
                                    </div>

                                    <div className="flex justify-end">
                                        <Button type="submit" disabled={booking.processing}>
                                            <Save className="mr-2 h-4 w-4" />
                                            {booking.processing ? 'Saving...' : 'Save Changes'}
                                        </Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    {/* Email Settings */}
                    <TabsContent value="email">
                        <Card className="border-slate-200 shadow-sm">
                            <CardHeader>
                                <div className="flex items-center gap-3">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                                        <Mail className="h-5 w-5 text-emerald-600" />
                                    </div>
                                    <div>
                                        <CardTitle className="text-base text-slate-900">
                                            Email / SMTP Settings
                                        </CardTitle>
                                        <CardDescription className="text-sm text-slate-500">
                                            Configure your outgoing email server and sender information.
                                        </CardDescription>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <form
                                    onSubmit={(e: FormEvent) => {
                                        e.preventDefault();
                                        router.post('/admin/settings/email', email.data);
                                    }}
                                    className="space-y-6"
                                >
                                    {/* SMTP Server */}
                                    <div>
                                        <h4 className="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                                            SMTP Server
                                        </h4>
                                        <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-5">
                                            <div className="grid gap-5 sm:grid-cols-2">
                                                <FormField label="SMTP Host">
                                                    <div className="relative">
                                                        <Server className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                        <Input
                                                            value={email.data.smtp_host ?? ''}
                                                            onChange={(e) => email.setData('smtp_host', e.target.value)}
                                                            className="bg-white pl-10"
                                                            placeholder="smtp.example.com"
                                                        />
                                                    </div>
                                                </FormField>
                                                <FormField label="SMTP Port">
                                                    <div className="relative">
                                                        <Hash className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                        <Input
                                                            value={email.data.smtp_port ?? ''}
                                                            onChange={(e) => email.setData('smtp_port', e.target.value)}
                                                            className="bg-white pl-10"
                                                            placeholder="587"
                                                        />
                                                    </div>
                                                </FormField>
                                                <FormField label="SMTP Username">
                                                    <div className="relative">
                                                        <User className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                        <Input
                                                            value={email.data.smtp_username ?? ''}
                                                            onChange={(e) => email.setData('smtp_username', e.target.value)}
                                                            className="bg-white pl-10"
                                                            placeholder="user@example.com"
                                                        />
                                                    </div>
                                                </FormField>
                                                <FormField label="SMTP Password">
                                                    <div className="relative">
                                                        <Lock className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                        <Input
                                                            type="password"
                                                            value={email.data.smtp_password ?? ''}
                                                            onChange={(e) => email.setData('smtp_password', e.target.value)}
                                                            className="bg-white pl-10"
                                                            placeholder="••••••••"
                                                        />
                                                    </div>
                                                </FormField>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Sender Info */}
                                    <div>
                                        <h4 className="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                                            Sender Information
                                        </h4>
                                        <div className="rounded-lg border border-slate-200 bg-slate-50/50 p-5">
                                            <div className="grid gap-5 sm:grid-cols-2">
                                                <FormField label="From Email">
                                                    <div className="relative">
                                                        <Mail className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                        <Input
                                                            value={email.data.from_email ?? ''}
                                                            onChange={(e) => email.setData('from_email', e.target.value)}
                                                            className="bg-white pl-10"
                                                            placeholder="noreply@example.com"
                                                        />
                                                    </div>
                                                </FormField>
                                                <FormField label="From Name">
                                                    <div className="relative">
                                                        <UserCircle className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                        <Input
                                                            value={email.data.from_name ?? ''}
                                                            onChange={(e) => email.setData('from_name', e.target.value)}
                                                            className="bg-white pl-10"
                                                            placeholder="MagShip Bookings"
                                                        />
                                                    </div>
                                                </FormField>
                                                <FormField
                                                    label="Admin Email"
                                                    description="System notifications will be sent to this address"
                                                >
                                                    <div className="relative">
                                                        <Shield className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                                        <Input
                                                            value={email.data.admin_email ?? ''}
                                                            onChange={(e) => email.setData('admin_email', e.target.value)}
                                                            className="bg-white pl-10"
                                                            placeholder="admin@example.com"
                                                        />
                                                    </div>
                                                </FormField>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between border-t border-slate-100 pt-4">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            className="border-slate-200"
                                            onClick={() => router.post('/admin/settings/test-email')}
                                        >
                                            <Send className="mr-2 h-4 w-4" />
                                            Send Test Email
                                        </Button>
                                        <Button type="submit" disabled={email.processing}>
                                            <Save className="mr-2 h-4 w-4" />
                                            {email.processing ? 'Saving...' : 'Save Changes'}
                                        </Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    {/* Voucher Settings */}
                    <TabsContent value="voucher">
                        <Card className="border-slate-200 shadow-sm">
                            <CardHeader>
                                <div className="flex items-center gap-3">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50">
                                        <FileText className="h-5 w-5 text-amber-600" />
                                    </div>
                                    <div>
                                        <CardTitle className="text-base text-slate-900">
                                            Voucher Settings
                                        </CardTitle>
                                        <CardDescription className="text-sm text-slate-500">
                                            Configure voucher PDF appearance and content.
                                        </CardDescription>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="flex flex-col items-center justify-center py-12">
                                    <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                                        <FileText className="h-7 w-7 text-slate-400" />
                                    </div>
                                    <p className="mt-4 text-sm font-medium text-slate-600">
                                        Voucher configuration coming soon
                                    </p>
                                    <p className="mt-1 text-xs text-slate-400">
                                        You will be able to customize header, footer, logo, and terms.
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>
                </Tabs>
            </div>
        </AdminLayout>
    );
}
