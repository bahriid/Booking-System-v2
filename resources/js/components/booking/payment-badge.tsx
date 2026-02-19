import { cn } from '@/lib/utils';
import { type PaymentStatus } from '@/types';

const paymentConfig: Record<string, { label: string; className: string }> = {
    paid: { label: 'Paid', className: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' },
    partial: { label: 'Partial', className: 'bg-amber-50 text-amber-700 ring-amber-600/20' },
    unpaid: { label: 'Unpaid', className: 'bg-red-50 text-red-700 ring-red-600/20' },
    refunded: { label: 'Refunded', className: 'bg-violet-50 text-violet-700 ring-violet-600/20' },
};

interface PaymentBadgeProps {
    status: PaymentStatus;
    className?: string;
}

export function PaymentBadge({ status, className }: PaymentBadgeProps) {
    const config = paymentConfig[status] ?? { label: status, className: 'bg-slate-50 text-slate-600 ring-slate-500/20' };

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
