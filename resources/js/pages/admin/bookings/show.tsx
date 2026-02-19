import { router } from '@inertiajs/react';
import { ArrowLeft, XCircle, CheckCircle, Ban, Download, Clock, Wallet, CreditCard, FileText, Users } from 'lucide-react';
import { useState } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { StatusBadge } from '@/components/booking/status-badge';
import { PaymentBadge } from '@/components/booking/payment-badge';
import { PaxBadges } from '@/components/booking/pax-badges';
import { PassengerRow } from '@/components/booking/passenger-row';
import { FormField } from '@/components/form-field';
import { formatDate, formatDateTime } from '@/lib/utils';
import { type Booking } from '@/types';

interface BookingShowProps {
    booking: Booking;
}

export default function BookingShow({ booking }: BookingShowProps) {
    const [cancelOpen, setCancelOpen] = useState(false);
    const [cancelReason, setCancelReason] = useState('');
    const [rejectOpen, setRejectOpen] = useState(false);
    const [rejectReason, setRejectReason] = useState('');
    const [paymentOpen, setPaymentOpen] = useState(false);
    const [paymentAmount, setPaymentAmount] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('');
    const [paymentRef, setPaymentRef] = useState('');
    const [processing, setProcessing] = useState(false);

    const isSuspended = booking.status === 'suspended_request';
    const canCancel = booking.status === 'confirmed' || isSuspended;

    function handleApprove() {
        setProcessing(true);
        router.post(`/admin/bookings/${booking.id}/approve`, {}, {
            onFinish: () => setProcessing(false),
        });
    }

    function handleReject() {
        setProcessing(true);
        router.post(`/admin/bookings/${booking.id}/reject`, { reason: rejectReason }, {
            onFinish: () => setProcessing(false),
            onSuccess: () => setRejectOpen(false),
        });
    }

    function handleCancel() {
        setProcessing(true);
        router.post(`/admin/bookings/${booking.id}/cancel`, { reason: cancelReason }, {
            onFinish: () => setProcessing(false),
            onSuccess: () => setCancelOpen(false),
        });
    }

    function handlePayment() {
        setProcessing(true);
        router.post(`/admin/accounting/payments`, {
            partner_id: booking.partner_id,
            amount: paymentAmount,
            method: paymentMethod,
            paid_at: new Date().toISOString().split('T')[0],
            reference: paymentRef,
            booking_id: booking.id,
        }, {
            onFinish: () => setProcessing(false),
            onSuccess: () => setPaymentOpen(false),
        });
    }

    return (
        <AdminLayout
            pageTitle={`Booking ${booking.booking_code}`}
            breadcrumbs={[{ label: 'Bookings', href: '/admin/bookings' }, { label: booking.booking_code }]}
            toolbarActions={
                <>
                    <Button variant="outline" size="sm" className="h-9 border-slate-200 text-slate-700 hover:bg-slate-50" asChild>
                        <a href={`/admin/bookings/${booking.id}/voucher`} target="_blank">
                            <Download className="mr-2 h-4 w-4 text-slate-500" />
                            Voucher
                        </a>
                    </Button>
                    {isSuspended && (
                        <>
                            <Button size="sm" className="h-9 bg-emerald-600 hover:bg-emerald-700" onClick={handleApprove} disabled={processing}>
                                <CheckCircle className="mr-2 h-4 w-4" />
                                Approve
                            </Button>
                            <Button size="sm" className="h-9" variant="destructive" onClick={() => setRejectOpen(true)}>
                                <Ban className="mr-2 h-4 w-4" />
                                Reject
                            </Button>
                        </>
                    )}
                    {booking.status === 'confirmed' && (
                        <Button size="sm" variant="outline" className="h-9 border-slate-200 text-slate-700 hover:bg-slate-50" onClick={() => setPaymentOpen(true)}>
                            <Wallet className="mr-2 h-4 w-4 text-slate-500" />
                            Record Payment
                        </Button>
                    )}
                    {canCancel && (
                        <Button size="sm" className="h-9" variant="destructive" onClick={() => setCancelOpen(true)}>
                            <XCircle className="mr-2 h-4 w-4" />
                            Cancel
                        </Button>
                    )}
                </>
            }
        >
            <div className="grid gap-6 lg:grid-cols-3">
                {/* Left Column */}
                <div className="space-y-6 lg:col-span-2">
                    {/* Booking Details */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="border-b border-slate-100 pb-4">
                            <CardTitle className="flex items-center justify-between text-base font-semibold text-slate-900">
                                <div className="flex items-center gap-2">
                                    <FileText className="h-4 w-4 text-slate-400" />
                                    Booking Details
                                </div>
                                <div className="flex items-center gap-2">
                                    <StatusBadge status={booking.status} />
                                    <PaymentBadge status={booking.payment_status} />
                                </div>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="pt-5">
                            {isSuspended && booking.suspended_until && (
                                <div className="mb-5 flex items-center gap-2.5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                    <Clock className="h-4 w-4 shrink-0" />
                                    <span>Overbooking request -- expires {formatDateTime(booking.suspended_until)}</span>
                                </div>
                            )}

                            <div className="grid gap-x-8 gap-y-5 sm:grid-cols-2">
                                <div>
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Partner</dt>
                                    <dd className="mt-1 text-sm font-medium text-slate-900">{booking.partner?.name}</dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Tour</dt>
                                    <dd className="mt-1 text-sm font-medium text-slate-900">{booking.tour_departure?.tour?.name}</dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Departure</dt>
                                    <dd className="mt-1 text-sm font-medium text-slate-900">
                                        {booking.tour_departure?.date}
                                        <span className="ml-1.5 text-slate-400">at</span>
                                        <span className="ml-1.5">{booking.tour_departure?.time}</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Passengers</dt>
                                    <dd className="mt-1.5">
                                        <PaxBadges adults={booking.adults_count} children={booking.children_count} infants={booking.infants_count} />
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Created</dt>
                                    <dd className="mt-1 text-sm text-slate-600">{formatDateTime(booking.created_at)}</dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Created By</dt>
                                    <dd className="mt-1 text-sm text-slate-600">{booking.creator?.name ?? '--'}</dd>
                                </div>
                            </div>

                            {booking.notes && (
                                <div className="mt-5 border-t border-slate-100 pt-5">
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Notes</dt>
                                    <dd className="mt-1.5 text-sm text-slate-600">{booking.notes}</dd>
                                </div>
                            )}

                            {booking.cancellation_reason && (
                                <div className="mt-5 border-t border-slate-100 pt-5">
                                    <dt className="text-xs font-medium uppercase tracking-wider text-slate-500">Cancellation Reason</dt>
                                    <dd className="mt-1.5 text-sm font-medium text-red-600">{booking.cancellation_reason}</dd>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Passengers */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="border-b border-slate-100 pb-4">
                            <CardTitle className="flex items-center justify-between text-base font-semibold text-slate-900">
                                <div className="flex items-center gap-2">
                                    <Users className="h-4 w-4 text-slate-400" />
                                    Passengers
                                    <span className="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-slate-100 px-1.5 text-xs font-medium text-slate-600">
                                        {booking.passengers?.length ?? 0}
                                    </span>
                                </div>
                                <span className="text-sm font-semibold tabular-nums text-slate-900">
                                    &euro; {booking.passengers?.reduce((sum, p) => sum + Number(p.price), 0).toFixed(2) ?? '0.00'}
                                </span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            {booking.passengers?.map((p, i) => (
                                <PassengerRow key={p.id} passenger={p} index={i} />
                            ))}
                        </CardContent>
                    </Card>

                    {/* Payments Table */}
                    {booking.payments && booking.payments.length > 0 && (
                        <Card className="border-slate-200 shadow-sm">
                            <CardHeader className="border-b border-slate-100 pb-4">
                                <CardTitle className="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <CreditCard className="h-4 w-4 text-slate-400" />
                                    Payments
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="p-0">
                                <div className="overflow-x-auto">
                                    <table className="w-full border-separate border-spacing-0">
                                        <thead>
                                            <tr className="bg-slate-50">
                                                <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Date</th>
                                                <th className="border-b border-slate-200 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Amount</th>
                                                <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Method</th>
                                                <th className="border-b border-slate-200 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Reference</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {booking.payments.map((p) => (
                                                <tr key={p.id} className="border-b border-slate-100 transition-colors hover:bg-slate-50/50">
                                                    <td className="border-b border-slate-100 px-4 py-3 text-sm text-slate-600">{formatDate(p.paid_at)}</td>
                                                    <td className="border-b border-slate-100 px-4 py-3 text-right text-sm font-semibold text-slate-900">
                                                        &euro; {Number(p.pivot?.amount ?? p.amount).toFixed(2)}
                                                    </td>
                                                    <td className="border-b border-slate-100 px-4 py-3 text-sm text-slate-600">{p.method ?? '--'}</td>
                                                    <td className="border-b border-slate-100 px-4 py-3 text-sm text-slate-400">{p.reference ?? '--'}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* Right Column */}
                <div className="space-y-6">
                    {/* Financial Summary */}
                    <Card className="border-slate-200 shadow-sm">
                        <CardHeader className="border-b border-slate-100 pb-4">
                            <CardTitle className="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <Wallet className="h-4 w-4 text-slate-400" />
                                Financial Summary
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="pt-5">
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-slate-500">Total Amount</span>
                                    <span className="text-sm font-semibold text-slate-900">&euro; {Number(booking.total_amount).toFixed(2)}</span>
                                </div>

                                {booking.penalty_amount > 0 && (
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-slate-500">Penalty</span>
                                        <span className="text-sm font-semibold text-red-600">&euro; {Number(booking.penalty_amount).toFixed(2)}</span>
                                    </div>
                                )}

                                <Separator className="bg-slate-100" />

                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-slate-500">Amount Paid</span>
                                    <span className="text-sm font-semibold text-emerald-600">&euro; {Number(booking.amount_paid).toFixed(2)}</span>
                                </div>

                                <Separator className="bg-slate-100" />

                                <div className="flex items-center justify-between">
                                    <span className="text-sm font-medium text-slate-900">Balance Due</span>
                                    <span className="text-base font-bold text-slate-900">&euro; {Number(booking.balance_due).toFixed(2)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Back Button */}
                    <Button variant="outline" className="h-10 w-full border-slate-200 text-slate-700 hover:bg-slate-50" asChild>
                        <a href="/admin/bookings">
                            <ArrowLeft className="mr-2 h-4 w-4 text-slate-500" />
                            Back to Bookings
                        </a>
                    </Button>
                </div>
            </div>

            {/* Cancel Dialog */}
            <Dialog open={cancelOpen} onOpenChange={setCancelOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle className="text-lg font-semibold text-slate-900">Cancel Booking</DialogTitle>
                        <DialogDescription className="text-sm text-slate-500">
                            This will cancel booking <span className="font-medium text-slate-700">{booking.booking_code}</span>. This action cannot be undone.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="py-2">
                        <FormField label="Reason">
                            <Textarea
                                value={cancelReason}
                                onChange={(e) => setCancelReason(e.target.value)}
                                placeholder="Provide a reason for cancellation..."
                                rows={3}
                                className="border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </FormField>
                    </div>
                    <DialogFooter className="gap-2 sm:gap-0">
                        <Button variant="outline" className="border-slate-200 text-slate-700" onClick={() => setCancelOpen(false)}>Keep Booking</Button>
                        <Button variant="destructive" onClick={handleCancel} disabled={processing}>
                            {processing ? 'Cancelling...' : 'Cancel Booking'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Reject Dialog */}
            <Dialog open={rejectOpen} onOpenChange={setRejectOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle className="text-lg font-semibold text-slate-900">Reject Overbooking</DialogTitle>
                        <DialogDescription className="text-sm text-slate-500">
                            Reject the overbooking request for <span className="font-medium text-slate-700">{booking.booking_code}</span>. The booking will be cancelled.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="py-2">
                        <FormField label="Reason">
                            <Textarea
                                value={rejectReason}
                                onChange={(e) => setRejectReason(e.target.value)}
                                placeholder="Provide a reason for rejection..."
                                rows={3}
                                className="border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </FormField>
                    </div>
                    <DialogFooter className="gap-2 sm:gap-0">
                        <Button variant="outline" className="border-slate-200 text-slate-700" onClick={() => setRejectOpen(false)}>Cancel</Button>
                        <Button variant="destructive" onClick={handleReject} disabled={processing}>
                            {processing ? 'Rejecting...' : 'Reject Request'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Payment Dialog */}
            <Dialog open={paymentOpen} onOpenChange={setPaymentOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle className="text-lg font-semibold text-slate-900">Record Payment</DialogTitle>
                        <DialogDescription className="text-sm text-slate-500">
                            Record a payment for booking <span className="font-medium text-slate-700">{booking.booking_code}</span>.
                            Balance due: <span className="font-semibold text-slate-700">&euro; {Number(booking.balance_due).toFixed(2)}</span>
                        </DialogDescription>
                    </DialogHeader>
                    <div className="space-y-4 py-2">
                        <FormField label="Amount" required>
                            <Input
                                type="number"
                                step="0.01"
                                value={paymentAmount}
                                onChange={(e) => setPaymentAmount(e.target.value)}
                                placeholder="0.00"
                                className="border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </FormField>
                        <FormField label="Method">
                            <select
                                value={paymentMethod}
                                onChange={(e) => setPaymentMethod(e.target.value)}
                                className="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">Select method...</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="other">Other</option>
                            </select>
                        </FormField>
                        <FormField label="Reference">
                            <Input
                                value={paymentRef}
                                onChange={(e) => setPaymentRef(e.target.value)}
                                placeholder="Reference number"
                                className="border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </FormField>
                    </div>
                    <DialogFooter className="gap-2 sm:gap-0">
                        <Button variant="outline" className="border-slate-200 text-slate-700" onClick={() => setPaymentOpen(false)}>Cancel</Button>
                        <Button onClick={handlePayment} disabled={processing || !paymentAmount}>
                            {processing ? 'Recording...' : 'Record Payment'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AdminLayout>
    );
}
