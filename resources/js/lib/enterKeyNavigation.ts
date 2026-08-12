// Global "Enter moves to the next field" behavior for data-entry forms
// (Lab Panel report dialogs, Process.vue, Patient/Staff/Inventory forms).
//
// Scoped to plain text-like <input> elements only — <textarea> keeps
// inserting newlines, and <select>/Reka Select triggers (role=combobox)
// keep opening their dropdown on Enter. Registered once as a capture-phase
// document listener so it also pre-empts native implicit form-submission
// (which fires on Enter regardless of @submit.prevent).

const TEXT_LIKE_INPUT_TYPES = new Set([
    'text',
    'number',
    'date',
    'time',
    'email',
    'tel',
    'search',
    'url',
    'password',
]);

const FOCUSABLE_SELECTOR = 'input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled])';

function isVisible(el: HTMLElement): boolean {
    return el.offsetParent !== null;
}

function isEligibleTarget(target: EventTarget | null): target is HTMLInputElement {
    if (!(target instanceof HTMLInputElement)) return false;
    if (target.dataset.enterNavSkip !== undefined) return false;
    if (target.getAttribute('role') === 'combobox') return false;
    return TEXT_LIKE_INPUT_TYPES.has(target.type);
}

function handleKeydown(event: KeyboardEvent): void {
    if (event.key !== 'Enter') return;
    if (event.isComposing) return;
    if (event.shiftKey || event.ctrlKey || event.metaKey || event.altKey) return;
    if (!isEligibleTarget(event.target)) return;

    const target = event.target as HTMLInputElement;
    const scope = target.closest<HTMLElement>('[role="dialog"], form') ?? document.body;

    const candidates = Array.from(scope.querySelectorAll<HTMLElement>(FOCUSABLE_SELECTOR)).filter(isVisible);
    const currentIndex = candidates.indexOf(target);
    if (currentIndex === -1) return;

    const next = candidates[currentIndex + 1];
    if (!next) return;

    event.preventDefault();
    next.focus();
    if (next instanceof HTMLInputElement && TEXT_LIKE_INPUT_TYPES.has(next.type)) {
        next.select();
    }
}

let initialized = false;

export function initEnterKeyNavigation(): void {
    if (initialized) return;
    initialized = true;
    document.addEventListener('keydown', handleKeydown, true);
}
