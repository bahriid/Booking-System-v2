import { ArrowLeft, Mail, CheckCircle, XCircle, ExternalLink } from 'lucide-react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { formatDateTime } from '@/lib/utils';
import { type EmailLog } from '@/types';

interface EmailLogShowProps { emailLog: EmailLog; }

const eventTypeLabels: Record<string, string> = {
    booking_confirmed: 'Booking Confirmed',
    overbooking_requested: 'Overbooking Requested',
    overbooking_approved: 'Overbooking Approved',
    overbooking_rejected: 'Overbooking Rejected',
    booking_cancelled: 'Booking Cancelled',
};

export default function EmailLogShow({ emailLog }: EmailLogShowProps) {
    return (
        <AdminLayout pageTitle="Email Log Detail" breadcrumbs={[{ label: 'System' }, { label: 'Email Logs', href: '/admin/email-logs' }, { label: `#${emailLog.id}` }]}>
            <div className="mx-auto max-w-3xl space-y-6">
                {/* Status Header */}
                <div className="flex items-center gap-3">
                    <div className={`flex h-10 w-10 items-center justify-center rounded-xl ${emailLog.success ? 'bg-emerald-100' : 'bg-red-100'}`}>
                        {emailLog.success
                            ? <CheckCircle className="h-5 w-5 text-emerald-600" />
                            : <XCircle className="h-5 w-5 text-red-600" />
                        }
                    </div>
                    <div>
                        <h2 className="text-lg font-semibold text-slate-900">
                            {emailLog.success ? 'Email Sent Successfully' : 'Email Failed'}
                        </h2>
                        <p className="text-sm text-slate-500">
                            {formatDateTime(emailLog.sent_at)}
                        </p>
                    </div>
                </div>

                {/* Email Details */}
                <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                        <h3 className="text-sm font-semibold text-slate-900">Email Details</h3>
                    </div>
                    <div className="p-6">
                        <dl className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <dt className="text-xs font-medium text-slate-500">Status</dt>
                                <dd className="mt-1.5">
                                    {emailLog.success ? (
                                        <Badge className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Sent</Badge>
                                    ) : (
                                        <Badge className="bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">Failed</Badge>
                                    )}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-xs font-medium text-slate-500">Sent At</dt>
                                <dd className="mt-1.5 text-sm text-slate-900">{formatDateTime(emailLog.sent_at)}</dd>
                            </div>
                            <div>
                                <dt className="text-xs font-medium text-slate-500">Recipient</dt>
                                <dd className="mt-1.5 text-sm text-slate-900">
                                    {emailLog.to_name ? `${emailLog.to_name} <${emailLog.to_email}>` : emailLog.to_email}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-xs font-medium text-slate-500">Event Type</dt>
                                <dd className="mt-1.5 text-sm text-slate-900">{eventTypeLabels[emailLog.event_type] ?? emailLog.event_type}</dd>
                            </div>
                            <div className="sm:col-span-2">
                                <dt className="text-xs font-medium text-slate-500">Subject</dt>
                                <dd className="mt-1.5 text-sm font-medium text-slate-900">{emailLog.subject}</dd>
                            </div>
                            {!emailLog.success && emailLog.error_message && (
                                <div className="sm:col-span-2">
                                    <dt className="text-xs font-medium text-slate-500">Error Message</dt>
                                    <dd className="mt-1.5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                        {emailLog.error_message}
                                    </dd>
                                </div>
                            )}
                        </dl>
                    </div>
                </div>

                {/* Related Booking */}
                {emailLog.booking && (
                    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                            <h3 className="text-sm font-semibold text-slate-900">Related Booking</h3>
                        </div>
                        <div className="p-6">
                            <dl className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <dt className="text-xs font-medium text-slate-500">Booking Code</dt>
                                    <dd className="mt-1.5">
                                        <a
                                            href={`/admin/bookings/${emailLog.booking.id}`}
                                            className="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline"
                                        >
                                            {emailLog.booking.booking_code}
                                            <ExternalLink className="h-3 w-3" />
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium text-slate-500">Status</dt>
                                    <dd className="mt-1.5 text-sm capitalize text-slate-900">{emailLog.booking.status?.replace('_', ' ')}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                )}

                {/* Back Button */}
                <div>
                    <Button variant="outline" asChild className="border-slate-200 text-slate-700 hover:bg-slate-50">
                        <a href="/admin/email-logs">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Email Logs
                        </a>
                    </Button>
                </div>
            </div>
        </AdminLayout>
    );
}
