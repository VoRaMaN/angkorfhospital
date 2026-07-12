// Shared CBC reference-range definitions, used by both the input dialog
// (CbcReportDialog.vue) and the printable report page (CbcReport.vue) so the
// flagged H/L values are always computed the same way in both places.

export interface CbcTest {
    label: string;
    khmer: string;
    field: string;
    unit: string;
    low: number;
    high: number;
    step: string;
}

export const cbcTests: CbcTest[] = [
    { label: 'WBC', khmer: 'ចំនួនកោសិកាឈាមស', field: 'wbc', unit: '10³/mm³', low: 4, high: 10, step: '0.1' },
    { label: 'RBC', khmer: 'ចំនួនកោសិកាឈាមក្រហម', field: 'rbc', unit: '10⁶/mm³', low: 4.5, high: 5.5, step: '0.01' },
    { label: 'Hemoglobin', khmer: 'អេម៉ូក្លូប៊ីន', field: 'hemoglobin', unit: 'g/dL', low: 13, high: 17, step: '0.1' },
    { label: 'Hematocrit', khmer: 'អែម៉ាតូគ្រីត', field: 'hematocrit', unit: '%', low: 40, high: 50, step: '0.1' },
    { label: 'MCV', khmer: 'មធ្យមទំហំកោសិកាឈាមក្រហម', field: 'mcv', unit: 'μm³', low: 83, high: 101, step: '0.1' },
    { label: 'MCH', khmer: 'មធ្យមបរិមាណអេម៉ូក្លូប៊ីនក្នុងកោសិកា', field: 'mch', unit: 'pg', low: 27, high: 32, step: '0.1' },
    { label: 'MCHC', khmer: 'មធ្យមកំហាប់អេម៉ូក្លូប៊ីនក្នុងកោសិកា', field: 'mchc', unit: 'g/dL', low: 31.5, high: 34.5, step: '0.1' },
    { label: 'Platelets', khmer: 'ប្លាកែត', field: 'platelets', unit: '10³/mm³', low: 150, high: 450, step: '1' },
    { label: 'RDW', khmer: 'ភាពខុសគ្នានៃទំហំកោសិកាឈាមក្រហម', field: 'rdw', unit: '%', low: 11.5, high: 14, step: '0.1' },
];

export const diffTests: CbcTest[] = [
    { label: 'Neutrophils', khmer: 'នឺទ្រូហ្វិល', field: 'neutrophils', unit: '%', low: 36, high: 75, step: '0.1' },
    { label: 'Lymphocytes', khmer: 'លីមហ្វូស៊ីត', field: 'lymphocytes', unit: '%', low: 20, high: 50, step: '0.1' },
    { label: 'Monocytes', khmer: 'មូណូស៊ីត', field: 'monocytes', unit: '%', low: 3, high: 8, step: '0.1' },
    { label: 'Eosinophils', khmer: 'អ៊ីអូស៊ីណូហ្វិល', field: 'eosinophils', unit: '%', low: 0.5, high: 5, step: '0.1' },
    { label: 'Basophils', khmer: 'បាហ្សូហ្វិល', field: 'basophils', unit: '%', low: 0, high: 2, step: '0.1' },
];

export function cbcFlag(value: number | string | null | undefined, test: CbcTest): 'H' | 'L' | null {
    if (value === null || value === undefined || value === '') return null;
    const n = Number(value);
    if (isNaN(n)) return null;
    if (n > test.high) return 'H';
    if (n < test.low) return 'L';
    return null;
}
