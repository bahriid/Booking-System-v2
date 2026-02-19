import { Head, Link } from '@inertiajs/react';
import { LayoutDashboard, Anchor } from 'lucide-react';
import { type ReactNode } from 'react';
import { FlashMessages } from '@/components/flash-messages';
import { UserMenu } from '@/components/user-menu';
import { type Breadcrumb } from '@/types';
import { cn } from '@/lib/utils';

interface DriverLayoutProps {
    children: ReactNode;
    title?: string;
    pageTitle?: string;
    breadcrumbs?: Breadcrumb[];
    toolbarActions?: ReactNode;
}

export default function DriverLayout({ children, title, pageTitle, breadcrumbs, toolbarActions }: DriverLayoutProps) {
    const path = window.location.pathname;
    const isShiftsActive = path === '/driver' || path === '/driver/';

    return (
        <>
            <Head title={title ?? pageTitle ?? 'My Shifts'} />

            <div className="flex min-h-screen flex-col bg-[#f1f4f9]">
                {/* ── Header ── */}
                <header className="sticky top-0 z-30 bg-[#1e293b]">
                    <div className="mx-auto flex h-[52px] max-w-[1440px] items-center gap-1 px-4 lg:px-5">
                        {/* Logo */}
                        <Link href="/driver" className="mr-5 flex shrink-0 items-center gap-2.5">
                            <div className="flex h-8 w-8 items-center justify-center rounded-md bg-sky-400">
                                <Anchor className="h-[18px] w-[18px] text-[#1e293b]" />
                            </div>
                            <span className="text-[15px] font-extrabold tracking-tight text-white">
                                Mag<span className="text-sky-400">Ship</span>
                            </span>
                            <span className="hidden rounded bg-sky-400/15 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-sky-400 sm:inline">
                                Driver
                            </span>
                        </Link>

                        {/* Nav */}
                        <nav className="flex h-[52px] items-center">
                            <a
                                href="/driver"
                                className={cn(
                                    'flex h-full items-center gap-1.5 border-b-2 px-3 text-[13px] font-medium tracking-wide transition-colors',
                                    isShiftsActive
                                        ? 'border-sky-400 text-white'
                                        : 'border-transparent text-slate-400 hover:text-slate-200',
                                )}
                            >
                                <LayoutDashboard className={cn('h-4 w-4', isShiftsActive ? 'text-sky-400' : 'opacity-50')} />
                                My Shifts
                            </a>
                        </nav>

                        {/* Right side */}
                        <div className="ml-auto">
                            <UserMenu />
                        </div>
                    </div>
                </header>

                {/* ── Page title bar ── */}
                {(pageTitle || toolbarActions) && (
                    <div className="border-b border-slate-200/80 bg-white px-4 lg:px-5">
                        <div className="mx-auto flex max-w-[1440px] flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                            {pageTitle && (
                                <h1 className="text-[17px] font-bold tracking-tight text-slate-900">{pageTitle}</h1>
                            )}
                            {toolbarActions && <div className="flex items-center gap-2">{toolbarActions}</div>}
                        </div>
                    </div>
                )}

                {/* ── Content ── */}
                <main className="flex-1 px-4 py-5 lg:px-5">
                    <div className="mx-auto max-w-[1440px]">
                        <FlashMessages />
                        {children}
                    </div>
                </main>
            </div>
        </>
    );
}
