import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { type PaginationLink } from '@/types';
import { cn } from '@/lib/utils';

interface PaginationProps {
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
}

export function Pagination({ links, from, to, total }: PaginationProps) {
    if (links.length <= 3) return null;

    return (
        <div className="flex flex-col items-center justify-between gap-4 pt-4 sm:flex-row">
            <p className="text-sm text-slate-500">
                Showing <span className="font-medium text-slate-700">{from}</span> to{' '}
                <span className="font-medium text-slate-700">{to}</span> of{' '}
                <span className="font-medium text-slate-700">{total}</span> results
            </p>
            <nav className="flex items-center gap-1">
                {links.map((link, index) => {
                    const isFirst = index === 0;
                    const isLast = index === links.length - 1;

                    if (!link.url) {
                        return (
                            <span
                                key={index}
                                className="inline-flex h-8 items-center justify-center rounded-md px-2.5 text-sm text-slate-300"
                            >
                                {isFirst ? <ChevronLeft className="h-4 w-4" /> : isLast ? <ChevronRight className="h-4 w-4" /> : <span dangerouslySetInnerHTML={{ __html: link.label }} />}
                            </span>
                        );
                    }

                    return (
                        <Link
                            key={index}
                            href={link.url}
                            className={cn(
                                'inline-flex h-8 min-w-8 items-center justify-center rounded-md px-2.5 text-sm font-medium transition-colors',
                                link.active
                                    ? 'bg-blue-600 text-white shadow-sm'
                                    : 'text-slate-600 hover:bg-slate-100',
                            )}
                            preserveScroll
                        >
                            {isFirst ? <ChevronLeft className="h-4 w-4" /> : isLast ? <ChevronRight className="h-4 w-4" /> : null}
                            {!isFirst && !isLast && <span dangerouslySetInnerHTML={{ __html: link.label }} />}
                        </Link>
                    );
                })}
            </nav>
        </div>
    );
}
