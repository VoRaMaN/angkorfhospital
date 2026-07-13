// Maps lab item names (as they appear in the lab catalog / order items) to
// the structured report form that should capture their result, instead of
// the generic value/unit/notes editor. Names are compared case-insensitively.

const HORMONE_ITEMS = new Set([
    'e2',
    'estradiol',
    'lh',
    'fsh',
    'prolactin',
    'beta hcg',
    'beta-hcg',
    'progesterone',
    'testosterone',
    'tsh',
    't3',
    't4',
    'amh',
]);

const CBC_ITEMS = new Set([
    'cbc',
    'cbc, anti hbs, hbs ag, vdrl, hiv',
]);

export type LabItemForm = 'hormone' | 'cbc' | null;

export function labItemForm(itemName: string | null | undefined): LabItemForm {
    const name = (itemName ?? '').trim().toLowerCase();
    if (HORMONE_ITEMS.has(name)) return 'hormone';
    if (CBC_ITEMS.has(name)) return 'cbc';
    return null;
}
