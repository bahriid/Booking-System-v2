import { usePage } from '@inertiajs/react';
import { type SharedProps } from '@/types';

/**
 * Translation hook that wraps usePage().props.translations.
 * Usage: const { t } = useTranslation();
 *        t('general.save')  →  looks up translations.general.save
 *        t('bookings.total', { count: '5' })  →  replaces :count in the string
 */
export function useTranslation() {
    const { translations } = usePage<SharedProps>().props;

    function t(key: string, replacements?: Record<string, string>): string {
        const [file, ...rest] = key.split('.');
        const translationKey = rest.join('.');

        let value = translations?.[file]?.[translationKey] ?? key;

        if (replacements) {
            Object.entries(replacements).forEach(([search, replace]) => {
                value = value.replace(new RegExp(`:${search}`, 'g'), replace);
            });
        }

        return value;
    }

    return { t };
}
