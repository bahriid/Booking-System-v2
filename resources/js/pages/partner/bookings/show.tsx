import { router } from '@inertiajs/react';
import { ArrowLeft, Edit, XCircle, Download, Clock, Users } from 'lucide-react';
import { useState } from 'react';
import PartnerLayout from '@/layouts/partner-layout';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { StatusBadge } from '@/components/booking/status-badge';
import { PaymentBadge } from '@/components/booking/payment-badge';
import { PaxBadges } from '@/components/booking/pax-badges';
import { PassengerRow } from '@/components/booking/passenger-row';
import { formatDateTime } from '@/lib/utils';
import { type Booking } from '@/types';

interface BookingShowProps {
    booking: Booking;
}

export default function BookingShow({ booking }: BookingShowProps) {
    const [cancelOpen, setCancelOpen] = useState(false);
    const [cancelReason, setCancelReason] = useState('');
    const [cancelProcessing, setCancelProcessing] = useState(false);

    const canEdit = booking.status === 'confirmed' || booking.status === 'suspended_request';
    const canCancel = booking.status === 'confirmed' || booking.status === 'suspended_request';

    function handleCancel() {
        setCancelProcessing(true);
        router.post(`/partner/bookings/${booking.id}/cancel`, { reason: cancelReason }, {
            onFinish: () => setCancelProcessing(false),
            onSuccess: () => setCancelOpen(false),
        });
    }

    return (
        <PartnerLayout
            pageTitle={`Booking ${booking.booking_code}`}
            breadcrumbs={[
                { label: 'Bookings', href: '/partner/bookings' },
                { label: booking.booking_code },
            ]}
            toolbarActions={
                <div className="flex items-center gap-2">
                    <Button variant="outline" size="sm" asChild className="border-slate-200 text-slate-700 hover:bg-slate-50">
                        <a href={`/partner/bookings/${booking.id}/voucher`} target="_blank">
                            <Download className="mr-2 h-4 w-4" />
                            Voucher PDF
                        </a>
                    </Button>
                    {canEdit && (
                        <Button variant="outline" size="sm" asChild className="border-slate-200 text-slate-700 hover:bg-slate-50">
                            <a href={`/partner/bookings/${booking.id}/edit`}>
                                <Edit className="mr-2 h-4 w-4" />
                                Edit
                            </a>
                        </Button>
                    )}
                    {canCancel && (
                        <Button variant="destructive" size="sm" onClick={() => setCancelOpen(true)}>
                            <XCircle className="mr-2 h-4 w-4" />
                            Cancel
                        </Button>
                    )}
                </div>
            }
        >
            <div className="grid gap-6 lg:grid-cols-3">
                {/* Main Info */}
                <div className="space-y-6 lg:col-span-2">
                    {/* Booking Details */}
                    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div className="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-3">
                            <h3 className="text-sm font-semibold text-slate-900">Booking Details</h3>
                            <div className="flex items-center gap-2">
                                <StatusBadge status={booking.status} />
                                <PaymentBadge status={booking.payment_status} />
                            </div>
                        </div>
                        <div className="p-6">
                            <dl className="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <dt className="text-xs font-medium text-slate-500">Tour</dt>
                                    <dd className="mt-1.5">
                                        <p className="text-sm font-medium text-slate-900">{booking.tour_departure?.tour?.name}</p>
                                        <p className="text-xs text-slate-400">{booking.tour_departure?.tour?.code}</p>
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium text-slate-500">Departure</dt>
                                    <dd className="mt-1.5">
                                        <p className="text-sm font-medium text-slate-900">{booking.tour_departure?.date}</p>
                                        <p className="text-xs text-slate-400">at {booking.tour_departure?.time}</p>
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium text-slate-500">Passengers</dt>
                                    <dd className="mt-1.5">
                                        <PaxBadges
                                            adults={booking.adults_count}
                                            children={booking.children_count}
                                            infants={booking.infants_count}
                                        />
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-xs font-medium text-slate-500">Created</dt>
                                    <dd className="mt-1.5 text-sm text-slate-900">{formatDateTime(booking.created_at)}</dd>
                                </div>
                            </dl>

                            {booking.status === 'suspended_request' && booking.suspended_until && (
                                <div className="mt-5 flex items-center gap-2.5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                    <Clock className="h-4 w-4 shrink-0 text-amber-600" />
                                    <span>Overbooking request -- expires {formatDateTime(booking.suspended_until)}</span>
                                </div>
                            )}

                            {booking.notes && (
                                <div className="mt-5">
                                    <dt className="text-xs font-medium text-slate-500">Notes</dt>
                                    <dd className="mt-1.5 text-sm text-slate-600">{booking.notes}</dd>
                                </div>
                            )}

                            {booking.cancellation_reason && (
                                <div className="mt-5">
                                    <dt className="text-xs font-medium text-slate-500">Cancellation Reason</dt>
                                    <dd className="mt-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                                        {booking.cancellation_reason}
                                    </dd>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Passengers */}
                    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div className="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-3">
                            <div className="flex items-center gap-2">
                                <Users className="h-4 w-4 text-slate-400" />
                                <h3 className="text-sm font-semibold text-slate-900">
                                    Passengers
                                    <span className="ml-1.5 inline-flex items-center rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-700">
                                        {booking.passengers?.length ?? 0}
                                    </span>
                                </h3>
                            </div>
                            <span className="text-sm font-semibold tabular-nums text-slate-900">
                                &euro; {booking.passengers?.reduce((sum, p) => sum + Number(p.price), 0).toFixed(2) ?? '0.00'}
                            </span>
                        </div>
                        <div>
                            {booking.passengers?.map((passenger, index) => (
                                <PassengerRow key={passenger.id} passenger={passenger} index={index} />
                            ))}
                        </div>
                    </div>
                </div>

                {/* Sidebar */}
                <div className="space-y-6">
                    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-6 py-3">
                            <h3 className="text-sm font-semibold text-slate-900">Financial Summary</h3>
                        </div>
                        <div className="space-y-3 p-6">
                            <div className="flex justify-between text-sm">
                                <span className="text-slate-500">Total Amount</span>
                                <span className="font-medium text-slate-900">&euro; {Number(booking.total_amount).toFixed(2)}</span>
                            </div>
                            {booking.penalty_amount > 0 && (
                                <div className="flex justify-between text-sm">
                                    <span className="text-slate-500">Penalty</span>
                                    <span className="font-medium text-red-600">&euro; {Number(booking.penalty_amount).toFixed(2)}</span>
                                </div>
                            )}
                            <Separator className="bg-slate-100" />
                            <div className="flex justify-between text-sm">
                                <span className="text-slate-500">Amount Paid</span>
                                <span className="font-medium text-emerald-600">&euro; {Number(booking.amount_paid).toFixed(2)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-sm text-slate-500">Balance Due</span>
                                <span className="text-base font-bold text-slate-900">&euro; {Number(booking.balance_due).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>

                    <Button variant="outline" className="w-full border-slate-200 text-slate-700 hover:bg-slate-50" asChild>
                        <a href="/partner/bookings">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Bookings
                        </a>
                    </Button>
                </div>
            </div>

            {/* Cancel Dialog */}
            <Dialog open={cancelOpen} onOpenChange={setCancelOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle className="text-slate-900">Cancel Booking</DialogTitle>
                    </DialogHeader>
                    <p className="text-sm text-slate-500">
                        Are you sure you want to cancel booking <strong className="text-slate-900">{booking.booking_code}</strong>?
                        This action cannot be undone.
                    </p>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-slate-700">Reason (optional)</label>
                        <Textarea
                            value={cancelReason}
                            onChange={(e) => setCancelReason(e.target.value)}
                            placeholder="Enter cancellation reason..."
                            rows={3}
                            className="border-slate-200"
                        />
                    </div>
                    <DialogFooter>
                        <Button variant="ghost" onClick={() => setCancelOpen(false)} className="text-slate-600">
                            Keep Booking
                        </Button>
                        <Button variant="destructive" onClick={handleCancel} disabled={cancelProcessing}>
                            {cancelProcessing ? 'Cancelling...' : 'Cancel Booking'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </PartnerLayout>
    );
}
