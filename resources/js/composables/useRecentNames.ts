import { ref } from 'vue';

const MAX_RECENT = 8;

function readStored(key: string): string[] {
    if (typeof window === 'undefined') {
        return [];
    }

    try {
        const raw = localStorage.getItem(key);
        const parsed = raw ? JSON.parse(raw) : [];
        return Array.isArray(parsed) ? parsed.filter((v) => typeof v === 'string') : [];
    } catch {
        return [];
    }
}

/**
 * Remembers recently-entered free-text values (e.g. a "Closed By" name) per
 * browser, keyed by `storageKey`. Newest first, deduplicated, capped at 8.
 */
export function useRecentNames(storageKey: string) {
    const recent = ref<string[]>(readStored(storageKey));

    const remember = (name: string) => {
        const trimmed = name.trim();
        if (!trimmed || typeof window === 'undefined') {
            return;
        }

        const next = [trimmed, ...recent.value.filter((v) => v !== trimmed)].slice(0, MAX_RECENT);
        recent.value = next;

        try {
            localStorage.setItem(storageKey, JSON.stringify(next));
        } catch {
            // localStorage unavailable (private browsing, quota, etc.) — not critical.
        }
    };

    return { recent, remember };
}
