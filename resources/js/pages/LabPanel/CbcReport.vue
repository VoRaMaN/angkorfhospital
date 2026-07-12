<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { cbcFlag, cbcTests, diffTests } from '@/lib/cbcReference';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Droplet, Edit2, Printer } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import CbcReportDialog from './CbcReportDialog.vue';

interface ReportData {
    id: number;
    medical_order_id: number;
    patient_id: string | null;
    patient_name: string | null;
    patient_hn: string | null;
    patient_dob: string | null;
    patient_age: number | null;
    patient_sex: string | null;
    patient_phone: string | null;
    doctor_name: string | null;
    lab_id: string | null;
    requested_by: string | null;
    requested_date: string | null;
    analysis_date: string | null;
    wbc: number | null;
    rbc: number | null;
    hemoglobin: number | null;
    hematocrit: number | null;
    mcv: number | null;
    mch: number | null;
    mchc: number | null;
    platelets: number | null;
    rdw: number | null;
    neutrophils: number | null;
    lymphocytes: number | null;
    monocytes: number | null;
    eosinophils: number | null;
    basophils: number | null;
    remark: string | null;
    reported_by: string | null;
    reported_date: string | null;
    created_at: string | null;
}

const props = defineProps<{ report: ReportData }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Lab Panels', href: '/lab-panels' },
    { title: 'CBC Report', href: '#' },
];

const val = (v: string | number | null | undefined, suffix = ''): string =>
    v !== null && v !== undefined && v !== '' ? `${v}${suffix}` : '—';

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
const existingReportForDialog = computed(() => ({ ...props.report }));

const onSaved = () => {
    router.reload();
};

const printReport = () => {
    window.print();
};

onMounted(() => window.print());
</script>

<template>
    <Head title="CBC Report">
        <link href="https://fonts.bunny.net/css?family=noto-sans-khmer:400,500,700" rel="stylesheet" />
    </Head>
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
                    <Droplet class="h-5 w-5 text-red-500" />
                    <div>
                        <h1 class="text-lg font-bold">CBC Report</h1>
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
                    class="inline-flex h-9 items-center gap-2 rounded-md bg-red-500 px-3 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-600"
                    @click="printReport"
                >
                    <Printer class="h-4 w-4" />
                    Print
                </button>
            </div>
        </div>

        <!-- ─── Printable Form ─────────────────────────────────────────────── -->
        <div class="mx-auto max-w-4xl px-6 pb-10">
            <div id="cbc-print-form" class="khmer-doc bg-white text-black print:shadow-none print:p-0 rounded-lg border shadow-sm p-6">

                <!-- ── Header ── -->
                <div class="flex items-start justify-between border-b-2 border-black pb-3 mb-3">
                    <div class="flex items-center gap-3">
                        <img src="/images/logo1.png" alt="Angkor-F Hospital" class="h-20 w-auto object-contain" />
                        <div class="ml-1">
                            <p class="text-sm font-bold">Angkor-F Hospital</p>
                            <p class="text-xs leading-tight text-gray-600">#National Road 6A, Salakonseng Village,</p>
                            <p class="text-xs leading-tight text-gray-600">Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
                            <p class="text-xs leading-tight text-gray-600">Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
                            <p class="text-xs leading-tight text-gray-600">E-mail: angkorfhospital@gmail.com</p>
                        </div>
                    </div>
                    <div class="text-sm space-y-0.5 text-right">
                        <p class="font-bold text-base">LABORATORY RESULT</p>
                        <p><span class="font-semibold">Report Date:</span> {{ reportDate }}</p>
                    </div>
                </div>

                <!-- ── Title ── -->
                <div class="text-center mb-4">
                    <p class="font-bold text-base tracking-wide">MEDICAL LABORATORY ANALYSIS</p>
                    <p class="font-semibold text-sm">( Complete Blood Count — CBC Report )</p>
                </div>

                <!-- ── Patient Info ── -->
                <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm mb-4 border-b border-gray-300 pb-3">
                    <div><span class="font-semibold">Name:</span> <span>{{ val(report.patient_name) }}</span></div>
                    <div><span class="font-semibold">Sex:</span> <span>{{ val(report.patient_sex) }}</span></div>
                    <div><span class="font-semibold">Patient ID:</span> <span>{{ val(report.patient_hn) }}</span></div>
                    <div><span class="font-semibold">Age:</span> <span>{{ val(report.patient_age, ' Y') }}</span></div>
                    <div><span class="font-semibold">Lab ID:</span> <span>{{ val(report.lab_id) }}</span></div>
                    <div><span class="font-semibold">Phone:</span> <span>{{ val(report.patient_phone) }}</span></div>
                    <div><span class="font-semibold">Requested By:</span> <span>{{ val(report.requested_by ?? report.doctor_name) }}</span></div>
                    <div><span class="font-semibold">Requested Date:</span> <span>{{ val(report.requested_date) }}</span></div>
                    <div><span class="font-semibold">Analysis Date:</span> <span>{{ val(report.analysis_date) }}</span></div>
                </div>

                <!-- ── HEMATOLOGY ── -->
                <div class="mb-1 bg-gray-100 border border-gray-400 px-2 py-1">
                    <p class="font-bold text-sm">HEMATOLOGY</p>
                </div>

                <div class="mb-3">
                    <p class="font-bold text-sm mb-1">COMPLETE BLOOD COUNT <span class="khmer">(ការរាប់កោសិកាឈាមពេញលេញ)</span></p>
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-gray-400 text-left text-xs text-gray-600">
                                <th class="py-1 font-semibold">Test Name</th>
                                <th class="py-1 font-semibold text-right">Result</th>
                                <th class="py-1 font-semibold text-center w-8"></th>
                                <th class="py-1 font-semibold pl-4">Units</th>
                                <th class="py-1 font-semibold pl-4">Ref. Ranges</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="test in cbcTests" :key="test.field" class="border-b border-dotted border-gray-300">
                                <td class="py-1">
                                    {{ test.label }}
                                    <span class="khmer text-xs text-gray-500">({{ test.khmer }})</span>
                                </td>
                                <td
                                    class="py-1 text-right font-semibold"
                                    :class="cbcFlag((report as any)[test.field], test) ? 'text-red-600' : ''"
                                >{{ val((report as any)[test.field]) }}</td>
                                <td class="py-1 text-center text-xs font-bold text-red-600">{{ cbcFlag((report as any)[test.field], test) }}</td>
                                <td class="py-1 pl-4 text-gray-600">{{ test.unit }}</td>
                                <td class="py-1 pl-4 text-gray-600">{{ test.low }} - {{ test.high }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <p class="font-bold text-sm mb-1">Differential White Cell Count <span class="khmer">(ចំណាត់ថ្នាក់កោសិកាឈាមស)</span></p>
                    <table class="w-full text-sm border-collapse">
                        <tbody>
                            <tr v-for="test in diffTests" :key="test.field" class="border-b border-dotted border-gray-300">
                                <td class="py-1">
                                    {{ test.label }}
                                    <span class="khmer text-xs text-gray-500">({{ test.khmer }})</span>
                                </td>
                                <td
                                    class="py-1 text-right font-semibold"
                                    :class="cbcFlag((report as any)[test.field], test) ? 'text-red-600' : ''"
                                >{{ val((report as any)[test.field]) }}</td>
                                <td class="py-1 text-center text-xs font-bold text-red-600 w-8">{{ cbcFlag((report as any)[test.field], test) }}</td>
                                <td class="py-1 pl-4 text-gray-600">{{ test.unit }}</td>
                                <td class="py-1 pl-4 text-gray-600">{{ test.low }} - {{ test.high }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ── Remark ── -->
                <div v-if="report.remark" class="mb-4 text-sm">
                    <span class="font-semibold">Remark:</span> {{ report.remark }}
                </div>

                <!-- ── Footer / Sign-off ── -->
                <div class="flex items-end justify-between border-t border-gray-300 pt-4 mt-6 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 mb-6">Verified by</p>
                        <p class="font-semibold border-t border-gray-500 pt-1 min-w-[180px]">{{ val(report.reported_by) }}</p>
                        <p class="text-xs text-gray-500">Lab. Technician</p>
                    </div>
                    <div class="text-right text-xs text-gray-500">
                        <p>Report date: {{ val(report.reported_date, '') !== '—' ? report.reported_date : reportDate }}</p>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-300 pt-2 text-center text-[10px] text-gray-500">
                    Phum Mondul Muy, Sangkat Svay Dangkum, Krong Siem Reap, Siem Reap Province — Tel: (855) 31 3 5555 88
                </div>

            </div>
        </div>

        <!-- Edit dialog -->
        <CbcReportDialog
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
.khmer-doc .khmer,
.khmer-doc {
    font-family: 'Noto Sans Khmer', 'DejaVu Sans', sans-serif;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>
