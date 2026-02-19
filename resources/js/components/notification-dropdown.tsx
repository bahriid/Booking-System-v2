import { usePage } from '@inertiajs/react';
import { Bell, Clock, CheckCircle, XCircle, AlertTriangle } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Badge } from '@/components/ui/badge';
import { type SharedProps, type Notification } from '@/types';

function getNotificationConfig(type: Notification['type']) {
    switch (type) {
        case 'overbooking':
            return { icon: AlertTriangle, color: 'text-amber-500', bg: 'bg-amber-50', label: 'Overbooking Request' };
        case 'new_booking':
            return { icon: CheckCircle, color: 'text-emerald-500', bg: 'bg-emerald-50', label: 'New Booking' };
        case 'cancellation':
            return { icon: XCircle, color: 'text-red-500', bg: 'bg-red-50', label: 'Cancellation' };
    }
}

function timeAgo(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'just now';
    if (diffMins < 60) return `${diffMins}m ago`;

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours}h ago`;

    const diffDays = Math.floor(diffHours / 24);
    return `${diffDays}d ago`;
}

export function NotificationDropdown() {
    const { notifications } = usePage<SharedProps>().props;

    if (!notifications) return null;

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <button className="relative rounded-md p-2 text-slate-400 transition-colors hover:bg-white/10 hover:text-white">
                    <Bell className="h-[18px] w-[18px]" />
                    {notifications.count > 0 && (
                        <span className="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                            {notifications.count}
                        </span>
                    )}
                </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-80">
                <DropdownMenuLabel className="flex items-center justify-between">
                    <span>Notifications</span>
                    {notifications.count > 0 && (
                        <Badge variant="secondary" className="text-xs">
                            {notifications.count}
                        </Badge>
                    )}
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                {notifications.items.length === 0 ? (
                    <div className="px-4 py-6 text-center text-sm text-muted-foreground">
                        No new notifications
                    </div>
                ) : (
                    notifications.items.map((notification, index) => {
                        const config = getNotificationConfig(notification.type);
                        const Icon = config.icon;

                        return (
                            <DropdownMenuItem key={index} asChild>
                                <a
                                    href={`/admin/bookings/${notification.booking.id}`}
                                    className="flex cursor-pointer items-start gap-3 p-3"
                                >
                                    <div className={`mt-0.5 rounded-full p-1.5 ${config.bg}`}>
                                        <Icon className={`h-3.5 w-3.5 ${config.color}`} />
                                    </div>
                                    <div className="flex-1 space-y-1">
                                        <p className="text-xs font-medium text-muted-foreground">
                                            {config.label}
                                        </p>
                                        <p className="text-sm font-medium">
                                            {notification.booking.booking_code}
                                        </p>
                                        <p className="text-xs text-muted-foreground">
                                            {notification.booking.partner_name} &middot; {notification.booking.passenger_count} pax
                                        </p>
                                    </div>
                                    <span className="flex items-center gap-1 text-xs text-muted-foreground">
                                        <Clock className="h-3 w-3" />
                                        {timeAgo(notification.created_at)}
                                    </span>
                                </a>
                            </DropdownMenuItem>
                        );
                    })
                )}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
