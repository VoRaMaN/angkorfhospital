<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Edit2, FlaskConical, Printer } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import IuiReportDialog from './IuiReportDialog.vue';

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
    wife_hn: string | null;
    owner_sperm: boolean | null;
    donor_sperm: boolean | null;
    fresh_sperm: boolean | null;
    frozen_sperm: boolean | null;
    frozen_vial: number | null;
    abstinence_days: number | null;
    appearance: string | null;
    liquefaction: string | null;
    viscosity: string | null;
    pre_volume: number | null;
    pre_count: number | null;
    pre_total_count: number | null;
    pre_motile: number | null;
    pre_total_motile: number | null;
    pre_motility: number | null;
    pre_motility_4_rapid: number | null;
    pre_motility_3_medium: number | null;
    pre_motility_2_slow: number | null;
    pre_motility_1_static: number | null;
    post_volume: number | null;
    post_count: number | null;
    post_total_count: number | null;
    post_motile: number | null;
    post_total_motile: number | null;
    post_motility: number | null;
    post_motility_4_rapid: number | null;
    post_motility_3_medium: number | null;
    post_motility_2_slow: number | null;
    post_motility_1_static: number | null;
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
    { title: 'IUI Report', href: '#' },
];

const val = (v: string | number | null | undefined, suffix = ''): string =>
    v !== null && v !== undefined && v !== '' ? `${v}${suffix}` : '—';

const fmtDate = (d: string | null): string => {
    if (!d) { return '—'; }
    const dt = new Date(d);
    if (isNaN(dt.getTime())) { return d; }
    return `${String(dt.getDate()).padStart(2, '0')}/${String(dt.getMonth() + 1).padStart(2, '0')}/${dt.getFullYear()}`;
};

const pct = (v: number | null | undefined): string => val(v, ' %');

const patientIsMale = computed(() => {
    const name = props.report.patient_name?.trim() ?? '';
    return !/^(mrs\.?\s|ms\.?\s|miss\s)/i.test(name);
});

const reportDate = computed(() => {
    if (props.report.created_at) {
        const d = new Date(props.report.created_at);
        if (!isNaN(d.getTime())) {
            return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
        }
    }
    return '—';
});

const editOpen = ref(false);

const existingReportForDialog = computed(() => ({
    id: props.report.id,
    medical_order_id: props.report.medical_order_id,
    patient_id: props.report.patient_id,
    wife_name: props.report.wife_name,
    wife_hn: props.report.wife_hn,
    owner_sperm: props.report.owner_sperm ?? false,
    donor_sperm: props.report.donor_sperm ?? false,
    fresh_sperm: props.report.fresh_sperm ?? false,
    frozen_sperm: props.report.frozen_sperm ?? false,
    frozen_vial: props.report.frozen_vial,
    abstinence_days: props.report.abstinence_days,
    appearance: props.report.appearance,
    liquefaction: props.report.liquefaction,
    viscosity: props.report.viscosity,
    pre_volume: props.report.pre_volume,
    pre_count: props.report.pre_count,
    pre_total_count: props.report.pre_total_count,
    pre_motile: props.report.pre_motile,
    pre_total_motile: props.report.pre_total_motile,
    pre_motility: props.report.pre_motility,
    pre_motility_4_rapid: props.report.pre_motility_4_rapid,
    pre_motility_3_medium: props.report.pre_motility_3_medium,
    pre_motility_2_slow: props.report.pre_motility_2_slow,
    pre_motility_1_static: props.report.pre_motility_1_static,
    post_volume: props.report.post_volume,
    post_count: props.report.post_count,
    post_total_count: props.report.post_total_count,
    post_motile: props.report.post_motile,
    post_total_motile: props.report.post_total_motile,
    post_motility: props.report.post_motility,
    post_motility_4_rapid: props.report.post_motility_4_rapid,
    post_motility_3_medium: props.report.post_motility_3_medium,
    post_motility_2_slow: props.report.post_motility_2_slow,
    post_motility_1_static: props.report.post_motility_1_static,
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

onMounted(() => window.print());
</script>

<template>
    <Head title="IUI Report" />
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
                    <FlaskConical class="h-5 w-5 text-purple-600" />
                    <div>
                        <h1 class="text-lg font-bold">IUI Report</h1>
                        <p class="text-xs text-muted-foreground">Angkor-F Hospital — IVF Lab</p>
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
                    class="inline-flex h-9 items-center gap-2 rounded-md bg-purple-600 px-3 text-sm font-medium text-white shadow-sm transition-colors hover:bg-purple-700"
                    @click="printReport"
                >
                    <Printer class="h-4 w-4" />
                    Print
                </button>
            </div>
        </div>

        <!-- ─── Printable Form ─────────────────────────────────────────────── -->
        <div class="mx-auto max-w-4xl px-6 pb-10">
            <div id="iui-print-form" class="bg-white text-black print:shadow-none print:p-0 rounded-lg border shadow-sm p-6">

                <!-- ── Header ── -->
                <div class="flex items-start justify-between border-b-2 border-black pb-3 mb-3">
                    <div class="flex items-center gap-3">
                        <img src="/images/logo.png" alt="Angkor-F Hospital" class="h-20 w-auto object-contain" />
                        <div>
                            <p class="font-bold text-sm leading-tight">មជ្ឈិមពេទ្យអង្គរ ភេហ្ស</p>
                            <p class="font-bold text-base leading-tight">ANGKOR-F HOSPITAL</p>
                            <p class="text-xs leading-tight text-gray-600">#National Road 6A, Salakonseng Village,</p>
                            <p class="text-xs leading-tight text-gray-600">Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
                            <p class="text-xs leading-tight text-gray-600">Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
                            <p class="text-xs leading-tight text-gray-600">E-mail: angkorfhospital@gmail.com</p>
                        </div>
                    </div>
                    <div class="text-sm space-y-0.5 text-right">
                        <p><span class="font-semibold">Name:</span> {{ val(report.patient_name) }}</p>
                        <p>
                            <span class="font-semibold">HN:</span> {{ val(report.patient_hn) }}
                            &nbsp;&nbsp;
                            <span class="font-semibold">SEX:</span> {{ patientIsMale ? 'MALE' : 'FEMALE' }}
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
                    <p class="font-semibold text-sm">( IUI Semen Analysis Report )</p>
                </div>

                <!-- ── Section title ── -->
                <div class="mb-2">
                    <p class="font-bold text-sm underline">IUI Semen Analysis</p>
                </div>

                <!-- ── Wife / Sperm info ── -->
                <table class="w-full text-sm mb-2">
                    <tr>
                        <td class="py-0.5 w-1/2">
                            <span class="font-semibold">{{ patientIsMale ? "Wife's Name" : "Husband's Name" }}:</span>
                            <span class="ml-2 border-b border-black min-w-[140px] inline-block">{{ val(report.wife_name) }}</span>
                        </td>
                        <td class="py-0.5">
                            <span class="font-semibold">H.N:</span>
                            <span class="ml-2">{{ val(report.wife_hn) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-0.5" colspan="2">
                            <span class="font-semibold mr-2">Sperm type:</span>
                            <span v-if="report.owner_sperm" class="mr-3">☑ Owner</span><span v-else class="mr-3">☐ Owner</span>
                            <span v-if="report.donor_sperm" class="mr-3">☑ Donor</span><span v-else class="mr-3">☐ Donor</span>
                            <span v-if="report.fresh_sperm" class="mr-3">☑ Fresh</span><span v-else class="mr-3">☐ Fresh</span>
                            <span v-if="report.frozen_sperm" class="mr-3">☑ Frozen</span><span v-else class="mr-3">☐ Frozen</span>
                            <span v-if="report.frozen_sperm && report.frozen_vial" class="ml-1 text-xs">({{ report.frozen_vial }} vial)</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-0.5 w-1/2">
                            <span class="font-semibold">Abstinence Days:</span>
                            <span class="ml-2">{{ val(report.abstinence_days, ' Days') }}</span>
                        </td>
                        <td class="py-0.5">
                            <span class="font-semibold">Appearance:</span>
                            <span class="ml-2">{{ val(report.appearance) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-0.5">
                            <span class="font-semibold">Liquefaction:</span>
                            <span class="ml-2">{{ val(report.liquefaction) }}</span>
                        </td>
                        <td class="py-0.5">
                            <span class="font-semibold">Viscosity:</span>
                            <span class="ml-2">{{ val(report.viscosity) }}</span>
                        </td>
                    </tr>
                </table>

                <!-- ── Pre/Post-wash tables ── -->
                <div class="grid grid-cols-2 gap-x-6 mt-3">
                    <!-- Pre-wash -->
                    <div>
                        <p class="font-bold text-xs uppercase tracking-wide text-gray-700 border-b border-gray-400 pb-0.5 mb-1">Pre-Wash</p>
                        <table class="text-sm w-full">
                            <thead>
                                <tr class="text-xs text-gray-500">
                                    <th class="text-left font-normal pb-1 w-1/2">Parameter</th>
                                    <th class="text-left font-normal pb-1">Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-0.5 font-medium">Volume</td>
                                    <td class="py-0.5">{{ val(report.pre_volume, ' ml') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Count / ml</td>
                                    <td class="py-0.5">{{ val(report.pre_count, ' ×10⁶/ml') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Total Count</td>
                                    <td class="py-0.5">{{ val(report.pre_total_count, ' ×10⁶') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Motile Count</td>
                                    <td class="py-0.5">{{ val(report.pre_motile, ' ×10⁶/ml') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Total Motile</td>
                                    <td class="py-0.5">{{ val(report.pre_total_motile, ' ×10⁶') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Motility</td>
                                    <td class="py-0.5">{{ pct(report.pre_motility) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Grade 4 (Rapid)</td>
                                    <td class="py-0.5">{{ pct(report.pre_motility_4_rapid) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Grade 3 (Medium)</td>
                                    <td class="py-0.5">{{ pct(report.pre_motility_3_medium) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Grade 2 (Slow)</td>
                                    <td class="py-0.5">{{ pct(report.pre_motility_2_slow) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Grade 1 (Static)</td>
                                    <td class="py-0.5">{{ pct(report.pre_motility_1_static) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Post-wash -->
                    <div>
                        <p class="font-bold text-xs uppercase tracking-wide text-gray-700 border-b border-gray-400 pb-0.5 mb-1">Post-Wash</p>
                        <table class="text-sm w-full">
                            <thead>
                                <tr class="text-xs text-gray-500">
                                    <th class="text-left font-normal pb-1 w-1/2">Parameter</th>
                                    <th class="text-left font-normal pb-1">Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-0.5 font-medium">Volume</td>
                                    <td class="py-0.5">{{ val(report.post_volume, ' ml') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Count / ml</td>
                                    <td class="py-0.5">{{ val(report.post_count, ' ×10⁶/ml') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Total Count</td>
                                    <td class="py-0.5">{{ val(report.post_total_count, ' ×10⁶') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Motile Count</td>
                                    <td class="py-0.5">{{ val(report.post_motile, ' ×10⁶/ml') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Total Motile</td>
                                    <td class="py-0.5">{{ val(report.post_total_motile, ' ×10⁶') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Motility</td>
                                    <td class="py-0.5">{{ pct(report.post_motility) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Grade 4 (Rapid)</td>
                                    <td class="py-0.5">{{ pct(report.post_motility_4_rapid) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Grade 3 (Medium)</td>
                                    <td class="py-0.5">{{ pct(report.post_motility_3_medium) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Grade 2 (Slow)</td>
                                    <td class="py-0.5">{{ pct(report.post_motility_2_slow) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 font-medium">Grade 1 (Static)</td>
                                    <td class="py-0.5">{{ pct(report.post_motility_1_static) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ── Times ── -->
                <div class="mt-4 grid grid-cols-2 gap-x-6 text-sm border-t border-gray-300 pt-3">
                    <div class="space-y-1">
                        <div>
                            <span class="font-semibold">Ejaculation Time:</span>
                            <span class="ml-2">{{ val(report.ejaculation_time) }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Examination Time:</span>
                            <span class="ml-2">{{ val(report.examination_time) }}</span>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div>
                            <span class="font-semibold">Receive Time:</span>
                            <span class="ml-2">{{ val(report.receive_time) }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Finish Time:</span>
                            <span class="ml-2">{{ val(report.finish_time) }}</span>
                        </div>
                    </div>
                </div>

                <!-- ── Remark ── -->
                <div v-if="report.remark" class="mt-3 text-sm border-t border-gray-300 pt-2">
                    <span class="font-semibold">Remark:</span>
                    <p class="mt-1 whitespace-pre-wrap text-gray-800">{{ report.remark }}</p>
                </div>

                <!-- ── Reported / Approved ── -->
                <div class="mt-4 grid grid-cols-2 gap-x-6 text-sm border-t border-gray-300 pt-3">
                    <div class="space-y-1">
                        <p class="font-semibold text-xs uppercase tracking-wide text-gray-500">Reported By</p>
                        <p>{{ val(report.reported_by) }}</p>
                        <p class="text-xs text-gray-500">
                            {{ fmtDate(report.reported_date) }}
                            <span v-if="report.reported_time"> {{ report.reported_time }}</span>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="font-semibold text-xs uppercase tracking-wide text-gray-500">Approved By</p>
                        <p>{{ val(report.approved_by) }}</p>
                        <p class="text-xs text-gray-500">
                            {{ fmtDate(report.approved_date) }}
                            <span v-if="report.approved_time"> {{ report.approved_time }}</span>
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Edit dialog -->
        <IuiReportDialog
            v-if="editOpen"
            v-model="editOpen"
            :order-id="report.medical_order_id"
            :patient-id="report.patient_id"
            :patient-name="report.patient_name ?? ''"
            :patient-dob="report.patient_dob"
            :patient-hn="report.patient_hn"
            :doctor-name="report.doctor_name"
            :existing-report="existingReportForDialog"
            @saved="onSaved"
        />
    </AppLayout>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
