import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { cn } from '@/lib/utils';

interface PartnerAvatarProps {
    name: string;
    initials: string;
    className?: string;
}

export function PartnerAvatar({ name, initials, className }: PartnerAvatarProps) {
    return (
        <Avatar className={cn('h-10 w-10', className)}>
            <AvatarFallback className="bg-emerald-100 text-emerald-700" title={name}>
                {initials}
            </AvatarFallback>
        </Avatar>
    );
}
