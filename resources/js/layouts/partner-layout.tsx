import { Head, Link } from '@inertiajs/react';
import { LayoutDashboard, PlusCircle, FileText, Menu, Anchor, X } from 'lucide-react';
import { useState, type ReactNode } from 'react';
import { FlashMessages } from '@/components/flash-messages';
import { UserMenu } from '@/components/user-menu';
import { type Breadcrumb } from '@/types';
import { cn } from '@/lib/utils';

interface PartnerLayoutProps {
    children: ReactNode;
    title?: string;
    pageTitle?: string;
    breadcrumbs?: Breadcrumb[];
    toolbarActions?: ReactNode;
}

const navItems = [
    { label: 'Dashboard', href: '/partner', icon: LayoutDashboard, match: '/partner', exact: true },
    { label: 'New Booking', href: '/partner/bookings/create', icon: PlusCircle, match: '/partner/bookings/create' },
    { label: 'My Bookings', href: '/partner/bookings', icon: FileText, match: '/partner/bookings' },
];

function isActive(match: string, exact?: boolean): boolean {
    const path = window.location.pathname;
    if (exact) return path === match || path === match + '/';
    if (match === '/partner/bookings') {
        return path === '/partner/bookings' || (path.startsWith('/partner/bookings/') && !path.includes('/create'));
    }
    return path.startsWith(match);
}

export default function PartnerLayout({ children, title, pageTitle, breadcrumbs, toolbarActions }: PartnerLayoutProps) {
    const [mobileOpen, setMobileOpen] = useState(false);

    return (
        <>
            <Head title={title ?? pageTitle} />

            <div className="flex min-h-screen flex-col bg-[#f1f4f9]">
                {/* ── Header ── */}
                <header className="sticky top-0 z-30 bg-[#022c22]">
                    <div className="mx-auto flex h-[52px] max-w-[1440px] items-center gap-1 px-4 lg:px-5">
                        {/* Logo */}
                        <Link href="/partner" className="mr-5 flex shrink-0 items-center gap-2.5">
                            <div className="flex h-8 w-8 items-center justify-center rounded-md bg-emerald-400">
                                <Anchor className="h-[18px] w-[18px] text-[#022c22]" />
                            </div>
                            <span className="text-[15px] font-extrabold tracking-tight text-white">
                                Mag<span className="text-emerald-400">Ship</span>
                            </span>
                            <span className="hidden rounded bg-emerald-400/15 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-emerald-400 sm:inline">
                                Partner
                            </span>
                        </Link>

                        {/* Desktop nav */}
                        <nav className="hidden h-[52px] items-center gap-0.5 lg:flex">
                            {navItems.map((item) => {
                                const Icon = item.icon;
                                const active = isActive(item.match, item.exact);
                                return (
                                    <a
                                        key={item.href}
                                        href={item.href}
                                        className={cn(
                                            'flex h-full items-center gap-1.5 border-b-2 px-3 text-[13px] font-medium tracking-wide transition-colors',
                                            active
                                                ? 'border-emerald-400 text-white'
                                                : 'border-transparent text-emerald-200/50 hover:text-emerald-100',
                                        )}
                                    >
                                        <Icon className={cn('h-4 w-4', active ? 'text-emerald-400' : 'opacity-50')} />
                                        {item.label}
                                    </a>
                                );
                            })}
                        </nav>

                        {/* Right side */}
                        <div className="ml-auto flex items-center gap-0.5">
                            <UserMenu />
                            <button
                                onClick={() => setMobileOpen(!mobileOpen)}
                                className="ml-1 rounded-md p-2 text-emerald-200/50 transition-colors hover:bg-white/10 hover:text-white lg:hidden"
                            >
                                {mobileOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                            </button>
                        </div>
                    </div>
                </header>

                {/* ── Mobile nav ── */}
                {mobileOpen && (
                    <div className="border-b border-slate-200 bg-white shadow-lg shadow-slate-900/5 lg:hidden">
                        <nav className="flex flex-col gap-1 p-3">
                            {navItems.map((item) => {
                                const Icon = item.icon;
                                const active = isActive(item.match, item.exact);
                                return (
                                    <a
                                        key={item.href}
                                        href={item.href}
                                        className={cn(
                                            'flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-[13px] font-medium transition-colors',
                                            active
                                                ? 'bg-emerald-900 text-white'
                                                : 'text-slate-600 hover:bg-slate-100',
                                        )}
                                    >
                                        <Icon className={cn('h-4 w-4', active ? 'text-emerald-400' : 'text-slate-400')} />
                                        {item.label}
                                    </a>
                                );
                            })}
                        </nav>
                    </div>
                )}

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
