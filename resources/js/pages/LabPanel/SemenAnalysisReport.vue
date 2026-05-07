<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Edit2, FileText, Printer } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import SemenAnalysisDialog from './SemenAnalysisDialog.vue';

interface ReportData {
    id: number;
    medical_order_id: number;
    patient_id: string | null;
    patient_name: string | null;
    patient_hn: string | null;
    patient_dob: string | null;
    patient_age: number | null;
    doctor_name: string | null;
    wife_name: string | null;
    abstinence_days: number | null;
    appearance: string | null;
    liquefaction: string | null;
    viscosity: string | null;
    ph: number | null;
    viability: number | null;
    volume: number | null;
    count_per_ml: number | null;
    total_count: number | null;
    motile: number | null;
    total_motile: number | null;
    motility: number | null;
    motility_4_rapid: number | null;
    motility_3_medium: number | null;
    motility_2_slow: number | null;
    motility_1_static: number | null;
    wbc: string | null;
    morphology_normal: number | null;
    morphology_abnormal: number | null;
    head_defect: number | null;
    neck_defect: number | null;
    tail_defect: number | null;
    no_of_vial: number | null;
    ejaculation_time: string | null;
    examination_time: string | null;
    receive_time: string | null;
    finish_time: string | null;
    remark: string | null;
    reported_by: string | null;
    reported_date: string | null;
    reported_time: string | null;
    approved_by: string | null;
    approved_date: string | null;
    approved_time: string | null;
    created_at: string | null;
}

const props = defineProps<{ report: ReportData }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Lab Panels', href: '/lab-panels' },
    { title: 'Semen Analysis Report', href: '#' },
];

// ─── Format helpers ───────────────────────────────────────────────────────────
const fmtDate = (d: string | null): string => {
    if (!d) return '—';
    // Try DD/MM/YYYY first (dob format)
    const parts = d.split('/');
    if (parts.length === 3) {
        const [day, month, year] = parts.map(Number);
        return `${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}/${year}`;
    }
    // ISO date
    const dt = new Date(d);
    if (isNaN(dt.getTime())) return d;
    return `${String(dt.getDate()).padStart(2, '0')}/${String(dt.getMonth() + 1).padStart(2, '0')}/${dt.getFullYear()}`;
};

const val = (v: string | number | null | undefined, suffix = ''): string =>
    v !== null && v !== undefined && v !== '' ? `${v}${suffix}` : '—';

const pct = (v: number | null | undefined): string => val(v, ' %');

// Gender from patient name title
const patientIsMale = computed(() => {
    const name = props.report.patient_name?.trim() ?? '';
    return !/^(mrs\.?\s|ms\.?\s|miss\s)/i.test(name);
});

const patientSex = computed(() => patientIsMale.value ? 'MALE' : 'FEMALE');

// Today's date for header
const reportDate = computed(() => {
    if (props.report.created_at) {
        const d = new Date(props.report.created_at);
        if (!isNaN(d.getTime())) {
            return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
        }
    }
    return '—';
});

// ─── Edit dialog ──────────────────────────────────────────────────────────────
const editOpen = ref(false);

const existingReportForDialog = computed(() => ({
    id: props.report.id,
    medical_order_id: props.report.medical_order_id,
    patient_id: props.report.patient_id,
    wife_name: props.report.wife_name,
    abstinence_days: props.report.abstinence_days,
    appearance: props.report.appearance,
    liquefaction: props.report.liquefaction,
    viscosity: props.report.viscosity,
    ph: props.report.ph,
    viability: props.report.viability,
    volume: props.report.volume,
    count_per_ml: props.report.count_per_ml,
    total_count: props.report.total_count,
    motile: props.report.motile,
    total_motile: props.report.total_motile,
    motility: props.report.motility,
    motility_4_rapid: props.report.motility_4_rapid,
    motility_3_medium: props.report.motility_3_medium,
    motility_2_slow: props.report.motility_2_slow,
    motility_1_static: props.report.motility_1_static,
    wbc: props.report.wbc,
    morphology_normal: props.report.morphology_normal,
    morphology_abnormal: props.report.morphology_abnormal,
    head_defect: props.report.head_defect,
    neck_defect: props.report.neck_defect,
    tail_defect: props.report.tail_defect,
    no_of_vial: props.report.no_of_vial,
    ejaculation_time: props.report.ejaculation_time,
    examination_time: props.report.examination_time,
    receive_time: props.report.receive_time,
    finish_time: props.report.finish_time,
    remark: props.report.remark,
    reported_by: props.report.reported_by,
    reported_date: props.report.reported_date,
    reported_time: props.report.reported_time,
    approved_by: props.report.approved_by,
    approved_date: props.report.approved_date,
    approved_time: props.report.approved_time,
}));

const onSaved = () => {
    router.reload();
};

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="Semen Analysis Report" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Action bar (hidden on print) -->
        <div class="no-print mx-auto max-w-4xl px-6 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <button
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                    @click="router.visit('/lab-panels')"
                >
                    <ArrowLeft class="h-4 w-4" />
                </button>
                <div class="flex items-center gap-2">
                    <FileText class="h-5 w-5 text-teal-600" />
                    <div>
                        <h1 class="text-lg font-bold">Semen Analysis Report</h1>
                        <p class="text-xs text-muted-foreground">Angkor-F Clinic — IVF Lab</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <button
                    class="inline-flex h-9 items-center gap-2 rounded-md border border-input bg-background px-3 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                    @click="editOpen = true"
                >
                    <Edit2 class="h-4 w-4" />
                    Edit
                </button>
                <button
                    class="inline-flex h-9 items-center gap-2 rounded-md bg-teal-600 px-3 text-sm font-medium text-white shadow-sm transition-colors hover:bg-teal-700"
                    @click="printReport"
                >
                    <Printer class="h-4 w-4" />
                    Print
                </button>
            </div>
        </div>

        <!-- ─── Printable Form ─────────────────────────────────────────────── -->
        <div class="mx-auto max-w-4xl px-6 pb-10">
            <div id="sa-print-form" class="bg-white text-black print:shadow-none print:p-0 rounded-lg border shadow-sm p-6">

                <!-- ── Header ── -->
                <div class="flex items-start justify-between border-b-2 border-black pb-3 mb-3">
                    <div class="flex items-center gap-3">
                        <img src="/images/logo.png" alt="Angkor-F Clinic" class="h-16 w-auto object-contain" />
                        <div>
                            <p class="font-bold text-base leading-tight">ANGKOR-F CLINIC</p>
                            <p class="text-xs leading-tight">IVF CENTER</p>
                            <p class="text-xs leading-tight text-gray-600">Phnom Penh, Cambodia</p>
                        </div>
                    </div>
                    <div class="text-sm space-y-0.5 text-right">
                        <p><span class="font-semibold">Name:</span> {{ val(report.patient_name) }}</p>
                        <p>
                            <span class="font-semibold">HN:</span> {{ val(report.patient_hn) }}
                            &nbsp;&nbsp;
                            <span class="font-semibold">SEX:</span> {{ patientSex }}
                        </p>
                        <p>
                            <span class="font-semibold">DOB:</span> {{ fmtDate(report.patient_dob) }}
                            &nbsp;&nbsp;
                            <span class="font-semibold">Age:</span> {{ val(report.patient_age, ' Yrs.') }}
                        </p>
                        <p><span class="font-semibold">Date:</span> {{ reportDate }}</p>
                        <p><span class="font-semibold">Doctor:</span> {{ val(report.doctor_name) }}</p>
                    </div>
                </div>

                <!-- ── Title ── -->
                <div class="text-center mb-4">
                    <p class="font-bold text-base tracking-wide">IVF LAB</p>
                    <p class="font-semibold text-sm">( Semen Analysis Report )</p>
                </div>

                <!-- ── Section title ── -->
                <div class="mb-2">
                    <p class="font-bold text-sm underline">Semen Analysis</p>
                </div>

                <!-- ── Wife info ── -->
                <table class="w-full text-sm mb-1">
                    <tr>
                        <td class="py-0.5 w-1/2">
                            <span class="font-semibold">Wife's name:</span>
                            <span class="ml-2 border-b border-black min-w-[160px] inline-block">{{ val(report.wife_name) }}</span>
                        </td>
                        <td class="py-0.5">
                            <span class="font-semibold">H.N:</span>
                            <span class="ml-2">—</span>
                        </td>
                    </tr>
                </table>

                <!-- ── Main data table ── -->
                <div class="grid grid-cols-2 gap-x-8 mt-3">
                    <!-- Left column: parameters -->
                    <table class="text-sm w-full">
                        <thead>
                            <tr class="text-xs text-gray-500">
                                <th class="text-left font-normal pb-1 w-1/2">Parameter</th>
                                <th class="text-left font-normal pb-1 w-1/4">Value</th>
                                <th class="text-left font-normal pb-1 w-1/4">Normal Range</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="py-0.5 font-medium">Abstinence day</td>
                                <td class="py-0.5">{{ val(report.abstinence_days, ' Days') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Appearance</td>
                                <td class="py-0.5">{{ val(report.appearance) }}</td>
                                <td class="text-xs text-gray-500"></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Liquefaction</td>
                                <td class="py-0.5">{{ val(report.liquefaction) }}</td>
                                <td class="text-xs text-gray-500">30 mins</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Viscosity</td>
                                <td class="py-0.5">{{ val(report.viscosity) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">pH</td>
                                <td class="py-0.5">{{ val(report.ph) }}</td>
                                <td class="text-xs text-gray-500">7.2 – 8.0</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Viability</td>
                                <td class="py-0.5">{{ pct(report.viability) }}</td>
                                <td class="text-xs text-gray-500">&gt; 75</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Volume</td>
                                <td class="py-0.5">{{ val(report.volume, ' ml.') }}</td>
                                <td class="text-xs text-gray-500">&gt; 2</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Count</td>
                                <td class="py-0.5">{{ val(report.count_per_ml, ' ×10⁶/ml.') }}</td>
                                <td class="text-xs text-gray-500">&gt; 20</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Total count</td>
                                <td class="py-0.5">{{ val(report.total_count, ' ×10⁶') }}</td>
                                <td class="text-xs text-gray-500">&gt; 40</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Motile</td>
                                <td class="py-0.5">{{ val(report.motile, ' ×10⁶/ml.') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Total motile</td>
                                <td class="py-0.5">{{ val(report.total_motile, ' ×10⁶') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">Motility</td>
                                <td class="py-0.5">{{ pct(report.motility) }}</td>
                                <td class="text-xs text-gray-500">&gt; 50</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-semibold" colspan="3">Motility rate <span class="font-normal text-xs text-gray-500">(WHO 1999)</span></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-4">4 rapid</td>
                                <td class="py-0.5">{{ pct(report.motility_4_rapid) }}</td>
                                <td class="text-xs text-gray-500">&gt; 25</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-4">3 medium</td>
                                <td class="py-0.5">{{ pct(report.motility_3_medium) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-4">2 slow</td>
                                <td class="py-0.5">{{ pct(report.motility_2_slow) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-4">1 static</td>
                                <td class="py-0.5">{{ pct(report.motility_1_static) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-medium">WBC</td>
                                <td class="py-0.5">{{ val(report.wbc, ' /HPF') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 font-semibold" colspan="3">Morphology <span class="font-normal text-xs">(Strict criteria)</span></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-4">Normal</td>
                                <td class="py-0.5">{{ pct(report.morphology_normal) }}</td>
                                <td class="text-xs text-gray-500">&gt; 14</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-4">Abnormal</td>
                                <td class="py-0.5">{{ pct(report.morphology_abnormal) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-8">Head Defect</td>
                                <td class="py-0.5">{{ pct(report.head_defect) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-8">Neck Defect</td>
                                <td class="py-0.5">{{ pct(report.neck_defect) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-0.5 pl-8">Tail Defect</td>
                                <td class="py-0.5">{{ pct(report.tail_defect) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Right column: sperm images -->
                    <div class="flex flex-col gap-4 items-center justify-start pt-6">
                        <div class="text-center">
                            <p class="text-xs font-semibold mb-1 text-gray-600">Normal Form</p>
                            <img
                                src="/images/sperm-forms/normal_form.png"
                                alt="Normal Form"
                                class="max-h-40 w-auto object-contain border border-gray-200 rounded"
                            />
                        </div>
                        <div class="text-center">
                            <p class="text-xs font-semibold mb-1 text-gray-600">Abnormal Form</p>
                            <img
                                src="/images/sperm-forms/abnormal_form.png"
                                alt="Abnormal Form"
                                class="max-h-40 w-auto object-contain border border-gray-200 rounded"
                            />
                        </div>
                    </div>
                </div>

                <!-- ── Times row ── -->
                <div class="mt-4 grid grid-cols-2 gap-x-8 text-sm border-t pt-3">
                    <p>
                        <span class="font-semibold">Ejaculation time:</span>
                        {{ val(report.ejaculation_time) }}
                    </p>
                    <p>
                        <span class="font-semibold">Examination time:</span>
                        {{ val(report.examination_time) }}
                    </p>
                    <p>
                        <span class="font-semibold">Receive time:</span>
                        {{ val(report.receive_time) }}
                    </p>
                    <p>
                        <span class="font-semibold">Finish time:</span>
                        {{ val(report.finish_time) }}
                    </p>
                </div>

                <!-- ── Remark ── -->
                <div class="mt-3 text-sm">
                    <span class="font-semibold">Remark:</span>
                    <span class="ml-2">{{ val(report.remark) }}</span>
                </div>

                <!-- ── Signatures ── -->
                <div class="mt-4 grid grid-cols-2 gap-x-8 text-sm border-t pt-3">
                    <div>
                        <p class="font-semibold">Reported by:</p>
                        <p>{{ val(report.reported_by) }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">
                            <span v-if="report.reported_date">Date: {{ fmtDate(report.reported_date) }}</span>
                            <span v-if="report.reported_date && report.reported_time"> &nbsp; </span>
                            <span v-if="report.reported_time">Time: {{ report.reported_time }}</span>
                            <span v-if="!report.reported_date && !report.reported_time">—</span>
                        </p>
                    </div>
                    <div>
                        <p class="font-semibold">Approved by:</p>
                        <p>{{ val(report.approved_by) }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">
                            <span v-if="report.approved_date">Date: {{ fmtDate(report.approved_date) }}</span>
                            <span v-if="report.approved_date && report.approved_time"> &nbsp; </span>
                            <span v-if="report.approved_time">Time: {{ report.approved_time }}</span>
                            <span v-if="!report.approved_date && !report.approved_time">—</span>
                        </p>
                    </div>
                </div>

            </div><!-- end print form -->
        </div>
    </AppLayout>

    <!-- Edit dialog -->
    <SemenAnalysisDialog
        v-model="editOpen"
        :order-id="report.medical_order_id"
        :patient-id="report.patient_id"
        :patient-name="report.patient_name ?? ''"
        :patient-dob="report.patient_dob"
        :patient-hn="report.patient_hn ? String(report.patient_hn) : null"
        :doctor-name="report.doctor_name"
        :existing-report="existingReportForDialog"
        @saved="onSaved"
    />
</template>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    nav,
    header,
    aside,
    footer,
    [class*="breadcrumb"],
    [class*="sidebar"] {
        display: none !important;
    }
    body {
        background: white !important;
    }
    #sa-print-form {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        max-width: 100% !important;
    }
}
</style>
