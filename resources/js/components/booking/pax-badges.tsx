import { cn } from '@/lib/utils';

interface PaxBadgesProps {
    adults: number;
    children: number;
    infants: number;
    className?: string;
}

export function PaxBadges({ adults, children: childCount, infants, className }: PaxBadgesProps) {
    return (
        <div className={cn('flex items-center gap-1', className)}>
            {adults > 0 && (
                <span className="inline-flex items-center rounded-md bg-blue-50 px-1.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                    {adults}A
                </span>
            )}
            {childCount > 0 && (
                <span className="inline-flex items-center rounded-md bg-violet-50 px-1.5 py-0.5 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-600/20">
                    {childCount}C
                </span>
            )}
            {infants > 0 && (
                <span className="inline-flex items-center rounded-md bg-pink-50 px-1.5 py-0.5 text-xs font-medium text-pink-700 ring-1 ring-inset ring-pink-600/20">
                    {infants}I
                </span>
            )}
        </div>
    );
}
