import { cn } from '@/lib/utils';
import { type BookingStatus } from '@/types';

const statusConfig: Record<string, { label: string; className: string }> = {
    confirmed: { label: 'Confirmed', className: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' },
    suspended_request: { label: 'Pending', className: 'bg-amber-50 text-amber-700 ring-amber-600/20' },
    completed: { label: 'Completed', className: 'bg-blue-50 text-blue-700 ring-blue-600/20' },
    cancelled: { label: 'Cancelled', className: 'bg-red-50 text-red-700 ring-red-600/20' },
    rejected: { label: 'Rejected', className: 'bg-slate-100 text-slate-600 ring-slate-500/20' },
    expired: { label: 'Expired', className: 'bg-slate-50 text-slate-500 ring-slate-400/20' },
};

interface StatusBadgeProps {
    status: BookingStatus;
    className?: string;
}

export function StatusBadge({ status, className }: StatusBadgeProps) {
    const config = statusConfig[status] ?? { label: status, className: 'bg-slate-50 text-slate-600 ring-slate-500/20' };

    return (
        <span className={cn(
            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset',
            config.className,
            className,
        )}>
            {config.label}
        </span>
    );
}
