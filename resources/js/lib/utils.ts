import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    return toUrl(urlToCheck) === currentUrl;
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

const MONTH_ABBR = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

/**
 * Format a date string to DD/Mon/YY format
 */
export function formatDate(date: string | Date | null | undefined): string {
    if (!date) return '';

    const d = new Date(date);
    if (isNaN(d.getTime())) return '';

    const day = String(d.getDate()).padStart(2, '0');
    const month = MONTH_ABBR[d.getMonth()];
    const year = String(d.getFullYear()).slice(-2);

    return `${day}/${month}/${year}`;
}

/**
 * Format a datetime string to DD/Mon/YY HH:mm format
 */
export function formatDateTime(date: string | Date | null | undefined): string {
    if (!date) return '';

    const d = new Date(date);
    if (isNaN(d.getTime())) return '';

    const day = String(d.getDate()).padStart(2, '0');
    const month = MONTH_ABBR[d.getMonth()];
    const year = String(d.getFullYear()).slice(-2);
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');

    return `${day}/${month}/${year} ${hours}:${minutes}`;
}

/**
 * Today's date in Asia/Phnom_Penh, as YYYY-MM-DD (for native `type="date"` inputs).
 * Uses explicit timezone conversion rather than toISOString(), which would shift
 * to UTC and can land on the wrong calendar day near midnight.
 */
export function todayInPhnomPenh(): string {
    const now = new Date();
    const pp = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Phnom_Penh' }));
    return `${pp.getFullYear()}-${String(pp.getMonth() + 1).padStart(2, '0')}-${String(pp.getDate()).padStart(2, '0')}`;
}

/**
 * Current date & time in Asia/Phnom_Penh, as "DD/MM/YYYY HH:mm" (for free-text
 * datetime inputs that display in this format).
 */
export function nowInPhnomPenhDisplay(): string {
    const now = new Date();
    const pp = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Phnom_Penh' }));
    const date = `${String(pp.getDate()).padStart(2, '0')}/${String(pp.getMonth() + 1).padStart(2, '0')}/${pp.getFullYear()}`;
    const time = `${String(pp.getHours()).padStart(2, '0')}:${String(pp.getMinutes()).padStart(2, '0')}`;
    return `${date} ${time}`;
}

/**
 * Format a datetime string to HH:mm format
 */
export function formatTime(date: string | Date | null | undefined): string {
    if (!date) return '';

    const d = new Date(date);
    if (isNaN(d.getTime())) return '';

    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');

    return `${hours}:${minutes}`;
}
