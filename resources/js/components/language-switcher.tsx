import { Link, usePage } from '@inertiajs/react';
import { Languages } from 'lucide-react';
import { type SharedProps } from '@/types';

const languages = [
    { code: 'en', label: 'English', flag: '🇬🇧' },
    { code: 'it', label: 'Italiano', flag: '🇮🇹' },
] as const;

export function LanguageSwitcher() {
    const { locale } = usePage<SharedProps>().props;

    return (
        <div className="flex items-center gap-1">
            <Languages className="mr-1 h-4 w-4 text-muted-foreground" />
            {languages.map((lang) => (
                <a
                    key={lang.code}
                    href={`/language/${lang.code}`}
                    className={cn(
                        'rounded px-2 py-1 text-xs transition-colors',
                        locale === lang.code
                            ? 'bg-primary text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground',
                    )}
                >
                    {lang.flag} {lang.label}
                </a>
            ))}
        </div>
    );
}

function cn(...classes: (string | false | undefined)[]) {
    return classes.filter(Boolean).join(' ');
}
