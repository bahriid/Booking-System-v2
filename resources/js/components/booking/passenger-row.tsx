import { MapPin, Phone, AlertTriangle, StickyNote, User } from 'lucide-react';
import { type BookingPassenger, type PaxType } from '@/types';

const paxTypeConfig: Record<PaxType, { label: string; short: string; bg: string; text: string; ring: string; icon: string }> = {
    adult: { label: 'Adult', short: 'ADU', bg: 'bg-blue-50', text: 'text-blue-700', ring: 'ring-blue-600/20', icon: 'bg-blue-100 text-blue-600' },
    child: { label: 'Child', short: 'CHD', bg: 'bg-violet-50', text: 'text-violet-700', ring: 'ring-violet-600/20', icon: 'bg-violet-100 text-violet-600' },
    infant: { label: 'Infant', short: 'INF', bg: 'bg-pink-50', text: 'text-pink-700', ring: 'ring-pink-600/20', icon: 'bg-pink-100 text-pink-600' },
};

interface PassengerRowProps {
    passenger: BookingPassenger;
    index: number;
    currency?: string;
}

export function PassengerRow({ passenger, index, currency = '€' }: PassengerRowProps) {
    const config = paxTypeConfig[passenger.pax_type];
    const hasDetails = passenger.allergies || passenger.notes;

    return (
        <div className={`group relative ${index > 0 ? 'border-t border-slate-100' : ''}`}>
            <div className="flex items-center gap-4 px-5 py-3.5 transition-colors group-hover:bg-slate-50/60">
                {/* Index + Avatar */}
                <div className="flex items-center gap-3">
                    <span className="w-5 text-center text-xs tabular-nums text-slate-400">{index + 1}</span>
                    <div className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ${config.icon}`}>
                        <User className="h-4 w-4" />
                    </div>
                </div>

                {/* Name + Type */}
                <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-2">
                        <span className="truncate text-sm font-semibold text-slate-900">
                            {passenger.full_name}
                        </span>
                        <span className={`inline-flex shrink-0 items-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide ring-1 ring-inset ${config.bg} ${config.text} ${config.ring}`}>
                            {config.short}
                        </span>
                    </div>
                    <div className="mt-0.5 flex items-center gap-3 text-xs text-slate-500">
                        {passenger.pickup_point?.name && (
                            <span className="inline-flex items-center gap-1">
                                <MapPin className="h-3 w-3 text-slate-400" />
                                {passenger.pickup_point.name}
                            </span>
                        )}
                        {passenger.phone && (
                            <span className="inline-flex items-center gap-1">
                                <Phone className="h-3 w-3 text-slate-400" />
                                {passenger.phone}
                            </span>
                        )}
                    </div>
                </div>

                {/* Price */}
                <div className="shrink-0 text-right">
                    <span className="text-sm font-semibold tabular-nums text-slate-900">
                        {currency} {Number(passenger.price).toFixed(2)}
                    </span>
                </div>
            </div>

            {/* Allergies / Notes sub-row */}
            {hasDetails && (
                <div className="flex gap-4 border-t border-dashed border-slate-100 bg-slate-50/40 px-5 py-2 pl-[4.25rem]">
                    {passenger.allergies && (
                        <span className="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/15">
                            <AlertTriangle className="h-3 w-3" />
                            {passenger.allergies}
                        </span>
                    )}
                    {passenger.notes && (
                        <span className="inline-flex items-center gap-1.5 text-xs text-slate-500">
                            <StickyNote className="h-3 w-3 text-slate-400" />
                            {passenger.notes}
                        </span>
                    )}
                </div>
            )}
        </div>
    );
}
