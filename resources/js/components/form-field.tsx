import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { type ReactNode } from 'react';

interface FormFieldProps {
    label?: string;
    htmlFor?: string;
    error?: string;
    required?: boolean;
    description?: string;
    className?: string;
    children: ReactNode;
}

export function FormField({ label, htmlFor, error, required, description, className, children }: FormFieldProps) {
    return (
        <div className={cn('space-y-1.5', className)}>
            {label && (
                <Label htmlFor={htmlFor} className={cn('text-sm font-medium text-slate-700', error && 'text-red-600')}>
                    {label}
                    {required && <span className="ml-0.5 text-red-500">*</span>}
                </Label>
            )}
            {children}
            {description && !error && (
                <p className="text-[13px] text-slate-400">{description}</p>
            )}
            {error && <p className="text-[13px] text-red-500">{error}</p>}
        </div>
    );
}
