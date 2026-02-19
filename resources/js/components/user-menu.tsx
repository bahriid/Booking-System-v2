import { usePage, router } from '@inertiajs/react';
import { KeyRound, LogOut, Settings, ChevronDown } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { LanguageSwitcher } from '@/components/language-switcher';
import { type SharedProps, type UserRole } from '@/types';

const avatarColors: Record<UserRole, string> = {
    admin: 'bg-amber-400 text-slate-900',
    partner: 'bg-emerald-400 text-slate-900',
    driver: 'bg-sky-400 text-slate-900',
};

export function UserMenu() {
    const { auth } = usePage<SharedProps>().props;

    if (!auth) return null;

    const user = auth.user;

    function handleLogout() {
        router.post('/logout');
    }

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <button className="flex items-center gap-2 rounded-md px-2 py-1.5 transition-colors hover:bg-white/10">
                    <div className={`flex h-7 w-7 items-center justify-center rounded-md text-xs font-bold ${avatarColors[user.role]}`}>
                        {user.initials}
                    </div>
                    <span className="hidden max-w-[120px] truncate text-[13px] font-medium text-slate-300 md:inline">
                        {user.name}
                    </span>
                    <ChevronDown className="hidden h-3 w-3 text-slate-500 md:block" />
                </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-56">
                <DropdownMenuLabel>
                    <div>
                        <p className="font-medium">{user.name}</p>
                        <p className="text-xs font-normal text-muted-foreground">{user.email}</p>
                    </div>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                {user.role === 'admin' && (
                    <DropdownMenuItem asChild>
                        <a href="/admin/settings" className="cursor-pointer">
                            <Settings className="mr-2 h-4 w-4" />
                            Settings
                        </a>
                    </DropdownMenuItem>
                )}
                <DropdownMenuItem asChild>
                    <a href="/profile/password" className="cursor-pointer">
                        <KeyRound className="mr-2 h-4 w-4" />
                        Change Password
                    </a>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <div className="px-2 py-1.5">
                    <LanguageSwitcher />
                </div>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={handleLogout} className="cursor-pointer text-red-600 focus:text-red-600">
                    <LogOut className="mr-2 h-4 w-4" />
                    Logout
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
