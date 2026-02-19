import { Head, Link } from '@inertiajs/react';
import {
    LayoutDashboard,
    Map,
    Calendar,
    FileText,
    Users,
    Wallet,
    BarChart3,
    Settings,
    ChevronDown,
    Mail,
    ScrollText,
    Database,
    Menu,
    Anchor,
    MapPin,
    UserCog,
    X,
} from 'lucide-react';
import { useState, type ReactNode } from 'react';
import { FlashMessages } from '@/components/flash-messages';
import { NotificationDropdown } from '@/components/notification-dropdown';
import { UserMenu } from '@/components/user-menu';
import { type Breadcrumb } from '@/types';
import { cn } from '@/lib/utils';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

interface AdminLayoutProps {
    children: ReactNode;
    title?: string;
    pageTitle?: string;
    breadcrumbs?: Breadcrumb[];
    toolbarActions?: ReactNode;
}

interface NavItemDef {
    label: string;
    href: string;
    icon: React.ComponentType<{ className?: string }>;
    match: string;
    exact?: boolean;
}

const primaryNav: NavItemDef[] = [
    { label: 'Dashboard', href: '/admin', icon: LayoutDashboard, match: '/admin', exact: true },
    { label: 'Tours', href: '/admin/tours', icon: Map, match: '/admin/tours' },
    { label: 'Calendar', href: '/admin/calendar', icon: Calendar, match: '/admin/calendar' },
    { label: 'Bookings', href: '/admin/bookings', icon: FileText, match: '/admin/bookings' },
    { label: 'Partners', href: '/admin/partners', icon: Users, match: '/admin/partners' },
];

const financeNav: NavItemDef[] = [
    { label: 'Accounting', href: '/admin/accounting', icon: Wallet, match: '/admin/accounting' },
    { label: 'Reports', href: '/admin/reports', icon: BarChart3, match: '/admin/reports' },
];

const configNav: NavItemDef[] = [
    { label: 'Users', href: '/admin/users', icon: UserCog, match: '/admin/users' },
    { label: 'Pickup Points', href: '/admin/pickup-points', icon: MapPin, match: '/admin/pickup-points' },
    { label: 'Settings', href: '/admin/settings', icon: Settings, match: '/admin/settings' },
];

const systemNav: NavItemDef[] = [
    { label: 'Email Logs', href: '/admin/email-logs', icon: Mail, match: '/admin/email-logs' },
    { label: 'Audit Logs', href: '/admin/audit-logs', icon: ScrollText, match: '/admin/audit-logs' },
    { label: 'Backup Logs', href: '/admin/backup-logs', icon: Database, match: '/admin/backup-logs' },
];

const allNav = [...primaryNav, ...financeNav, ...configNav, ...systemNav];

function isActive(match: string, exact?: boolean): boolean {
    const path = window.location.pathname;
    if (exact) return path === match || path === match + '/';
    return path.startsWith(match);
}

function isGroupActive(items: NavItemDef[]): boolean {
    return items.some((item) => isActive(item.match, item.exact));
}

function NavDropdown({ label, items }: { label: string; items: NavItemDef[] }) {
    const groupActive = isGroupActive(items);

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <button
                    className={cn(
                        'flex h-full items-center gap-1 border-b-2 px-3 text-[13px] font-medium tracking-wide transition-colors',
                        groupActive
                            ? 'border-amber-400 text-white'
                            : 'border-transparent text-slate-400 hover:text-slate-200',
                    )}
                >
                    {label}
                    <ChevronDown className="h-3 w-3 opacity-50" />
                </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start" className="min-w-[180px]">
                {items.map((item) => {
                    const Icon = item.icon;
                    const active = isActive(item.match, item.exact);
                    return (
                        <DropdownMenuItem key={item.href} asChild>
                            <a href={item.href} className={cn('cursor-pointer gap-2.5', active && 'bg-accent font-medium')}>
                                <Icon className="h-4 w-4 text-slate-400" />
                                {item.label}
                            </a>
                        </DropdownMenuItem>
                    );
                })}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}

export default function AdminLayout({ children, title, pageTitle, breadcrumbs, toolbarActions }: AdminLayoutProps) {
    const [mobileOpen, setMobileOpen] = useState(false);

    return (
        <>
            <Head title={title ?? pageTitle} />

            <div className="flex min-h-screen flex-col bg-[#f1f4f9]">
                {/* ── Header ── */}
                <header className="sticky top-0 z-30 bg-[#0f172a]">
                    <div className="mx-auto flex h-[52px] max-w-[1440px] items-center gap-1 px-4 lg:px-5">
                        {/* Logo */}
                        <Link href="/admin" className="mr-5 flex shrink-0 items-center gap-2.5">
                            <div className="flex h-8 w-8 items-center justify-center rounded-md bg-amber-400">
                                <Anchor className="h-[18px] w-[18px] text-[#0f172a]" />
                            </div>
                            <span className="text-[15px] font-extrabold tracking-tight text-white">
                                Mag<span className="text-amber-400">Ship</span>
                            </span>
                        </Link>

                        {/* Desktop nav */}
                        <nav className="hidden h-[52px] items-center gap-0.5 lg:flex">
                            {primaryNav.map((item) => {
                                const Icon = item.icon;
                                const active = isActive(item.match, item.exact);
                                return (
                                    <a
                                        key={item.href}
                                        href={item.href}
                                        className={cn(
                                            'flex h-full items-center gap-1.5 border-b-2 px-3 text-[13px] font-medium tracking-wide transition-colors',
                                            active
                                                ? 'border-amber-400 text-white'
                                                : 'border-transparent text-slate-400 hover:text-slate-200',
                                        )}
                                    >
                                        <Icon className={cn('h-4 w-4', active ? 'text-amber-400' : 'opacity-50')} />
                                        {item.label}
                                    </a>
                                );
                            })}

                            <div className="mx-2 h-5 w-px bg-slate-700" />

                            <NavDropdown label="Finance" items={financeNav} />
                            <NavDropdown label="Config" items={configNav} />
                            <NavDropdown label="System" items={systemNav} />
                        </nav>

                        {/* Right side */}
                        <div className="ml-auto flex items-center gap-0.5">
                            <NotificationDropdown />
                            <UserMenu />
                            <button
                                onClick={() => setMobileOpen(!mobileOpen)}
                                className="ml-1 rounded-md p-2 text-slate-400 transition-colors hover:bg-white/10 hover:text-white lg:hidden"
                            >
                                {mobileOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                            </button>
                        </div>
                    </div>
                </header>

                {/* ── Mobile nav ── */}
                {mobileOpen && (
                    <div className="border-b border-slate-200 bg-white shadow-lg shadow-slate-900/5 lg:hidden">
                        <nav className="mx-auto grid max-w-lg grid-cols-2 gap-1 p-3">
                            {allNav.map((item) => {
                                const Icon = item.icon;
                                const active = isActive(item.match, item.exact);
                                return (
                                    <a
                                        key={item.href}
                                        href={item.href}
                                        className={cn(
                                            'flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-[13px] font-medium transition-colors',
                                            active
                                                ? 'bg-slate-900 text-white'
                                                : 'text-slate-600 hover:bg-slate-100',
                                        )}
                                    >
                                        <Icon className={cn('h-4 w-4', active ? 'text-amber-400' : 'text-slate-400')} />
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
                                <h1 className="text-[17px] font-bold tracking-tight text-slate-900">
                                    {pageTitle}
                                </h1>
                            )}
                            {toolbarActions && (
                                <div className="flex items-center gap-2">{toolbarActions}</div>
                            )}
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
