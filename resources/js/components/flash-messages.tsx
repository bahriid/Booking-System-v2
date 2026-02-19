import { usePage } from '@inertiajs/react';
import { CheckCircle2, AlertTriangle, XCircle, X } from 'lucide-react';
import { useState, useEffect } from 'react';
import { type SharedProps } from '@/types';
import { cn } from '@/lib/utils';

const config = {
    success: {
        icon: CheckCircle2,
        containerClass: 'border-emerald-200 bg-emerald-50 text-emerald-900',
        iconClass: 'text-emerald-500',
        closeClass: 'text-emerald-400 hover:text-emerald-600',
    },
    error: {
        icon: XCircle,
        containerClass: 'border-red-200 bg-red-50 text-red-900',
        iconClass: 'text-red-500',
        closeClass: 'text-red-400 hover:text-red-600',
    },
    warning: {
        icon: AlertTriangle,
        containerClass: 'border-amber-200 bg-amber-50 text-amber-900',
        iconClass: 'text-amber-500',
        closeClass: 'text-amber-400 hover:text-amber-600',
    },
} as const;

export function FlashMessages() {
    const { flash } = usePage<SharedProps>().props;
    const [visible, setVisible] = useState({ success: false, error: false, warning: false });

    useEffect(() => {
        setVisible({
            success: !!flash.success,
            error: !!flash.error,
            warning: !!flash.warning,
        });

        // Auto-dismiss success after 5s
        if (flash.success) {
            const timer = setTimeout(() => setVisible((v) => ({ ...v, success: false })), 5000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    const entries = (['success', 'error', 'warning'] as const).filter((k) => visible[k] && flash[k]);

    if (entries.length === 0) return null;

    return (
        <div className="mb-4 space-y-2">
            {entries.map((type) => {
                const { icon: Icon, containerClass, iconClass, closeClass } = config[type];
                return (
                    <div
                        key={type}
                        className={cn(
                            'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm shadow-sm animate-in fade-in slide-in-from-top-1 duration-200',
                            containerClass,
                        )}
                    >
                        <Icon className={cn('mt-0.5 h-4 w-4 shrink-0', iconClass)} />
                        <span className="flex-1 leading-relaxed">{flash[type]}</span>
                        <button
                            onClick={() => setVisible((v) => ({ ...v, [type]: false }))}
                            className={cn('shrink-0 rounded-md p-0.5 transition-colors', closeClass)}
                        >
                            <X className="h-3.5 w-3.5" />
                        </button>
                    </div>
                );
            })}
        </div>
    );
}
