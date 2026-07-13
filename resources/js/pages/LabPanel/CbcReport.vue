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
            <div id="cbc-print-form" class="khmer-doc bg-white text-black print:shadow-none print:p-0 rounded-lg border shadow-sm p-8">

                <!-- ── Letterhead ── -->
                <div class="flex items-start justify-between">
                    <img src="/images/logo1.png" alt="Angkor-F Hospital" class="h-16 w-16 object-contain mt-1" />
                    <div class="flex-1 text-center leading-relaxed">
                        <p class="khmer-muol text-[13px] blue">ព្រះរាជាណាចក្រកម្ពុជា</p>
                        <p class="khmer-muol text-[10px] blue">ជាតិ សាសនា ព្រះមហាក្សត្រ</p>
                        <p class="khmer-muol text-[14px] blue mt-1">មន្ទីរពេទ្យ អង្គរ-អេហ្វ</p>
                        <p class="text-[15px] font-bold blue tracking-wide">ANGKOR-F HOSPITAL</p>
                    </div>
                    <img src="/images/logo1.png" alt="Angkor-F Hospital" class="h-16 w-16 object-contain mt-1" />
                </div>
                <div class="mt-1 mb-2 flex items-center justify-between text-[11px]">
                    <p class="khmer blue">សេវាថែទាំសុខភាពដែលមានគុណភាពល្អបំផុត</p>
                    <p class="blue font-semibold">Best Quality Health Care Services</p>
                </div>

                <p class="text-center font-bold text-[15px] navy tracking-wide mb-1">LABORATORY RESULT</p>

                <!-- ── Patient Info (3-column, bilingual labels) ── -->
                <div class="border-t-2 border-b-2 border-gray-400 py-1.5 mb-3 text-[13px] navy">
                    <div class="grid grid-cols-3 gap-x-4 gap-y-0.5">
                        <div><span class="font-semibold">ឈ្មោះ/Name</span> : {{ val(report.patient_name) }}</div>
                        <div><span class="font-semibold">អាយុ/Age</span> : {{ val(report.patient_age, ' Y') }}</div>
                        <div><span class="font-semibold">ភេទ/Sex</span> : {{ report.patient_sex === 'FEMALE' ? 'Female' : 'Male' }}</div>
                        <div><span class="font-semibold">Patient ID</span> : {{ val(report.patient_hn) }}</div>
                        <div><span class="font-semibold">Lab ID</span> : {{ val(report.lab_id) }}</div>
                        <div><span class="font-semibold">ទូរស័ព្ទ/Phone</span> : {{ val(report.patient_phone, '') !== '—' ? report.patient_phone : '' }}</div>
                        <div><span class="font-semibold">Requested By</span> : {{ val(report.requested_by ?? report.doctor_name) }}</div>
                        <div><span class="font-semibold">Requested Date</span> : {{ val(report.requested_date) }}</div>
                        <div><span class="font-semibold">Analysis Date</span> : {{ val(report.analysis_date) }}</div>
                    </div>
                </div>

                <!-- ── HEMATOLOGY band ── -->
                <div class="bg-gray-200 py-0.5 mb-1 text-center">
                    <p class="font-bold text-[13px] text-black tracking-wide">HEMATOLOGY</p>
                </div>

                <!-- Column headers -->
                <div class="flex text-[13px] font-bold border-b border-transparent mb-0.5">
                    <div class="flex-1">Test Name</div>
                    <div class="w-24">Result</div>
                    <div class="w-8"></div>
                    <div class="w-24">Units</div>
                    <div class="w-28 text-center">Ref. Ranges</div>
                </div>

                <p class="font-bold text-[13px] mb-0.5">COMPLETE BLOOD COUNT <span class="khmer">(រាប់គ្រាប់ឈាម)</span></p>

                <div class="mb-1">
                    <div v-for="test in cbcTests" :key="test.field" class="flex items-end text-[13px] leading-6">
                        <div class="flex flex-1 items-end pl-3 min-w-0">
                            <span class="whitespace-nowrap">{{ test.label }} <span class="khmer text-[11.5px]">({{ test.khmer }})</span></span>
                            <span class="leader"></span>
                        </div>
                        <div class="w-24 font-semibold" :class="cbcFlag((report as any)[test.field], test) ? 'text-red-600' : 'navy'">
                            : {{ val((report as any)[test.field]) }}
                        </div>
                        <div class="w-8 font-bold text-red-600">{{ cbcFlag((report as any)[test.field], test) }}</div>
                        <div class="w-24 text-[12px]">{{ test.unit }}</div>
                        <div class="w-28 text-center text-[12px]">{{ test.low }} - {{ test.high }}</div>
                    </div>
                </div>

                <p class="font-bold text-[13px] mb-0.5 pl-1">Differential White Cell Count</p>
                <div class="mb-2">
                    <div v-for="test in diffTests" :key="test.field" class="flex items-end text-[13px] leading-6">
                        <div class="flex flex-1 items-end pl-3 min-w-0">
                            <span class="whitespace-nowrap">{{ test.label }} (%)</span>
                            <span class="leader"></span>
                        </div>
                        <div class="w-24 font-semibold" :class="cbcFlag((report as any)[test.field], test) ? 'text-red-600' : 'navy'">
                            : {{ val((report as any)[test.field]) }}
                        </div>
                        <div class="w-8 font-bold text-red-600">{{ cbcFlag((report as any)[test.field], test) }}</div>
                        <div class="w-24 text-[12px]">%</div>
                        <div class="w-28 text-center text-[12px]">{{ test.low }} - {{ test.high }}</div>
                    </div>
                </div>

                <!-- ── Remark ── -->
                <div v-if="report.remark" class="mb-2 text-[13px]">
                    <span class="font-semibold">Remark:</span> {{ report.remark }}
                </div>

                <!-- ── Footer / Sign-off ── -->
                <div class="mt-8 flex items-end justify-between text-[13px]">
                    <div class="text-center">
                        <p class="mb-10">Verified by</p>
                        <p class="font-semibold border-t border-gray-500 pt-1 min-w-[180px] navy">{{ val(report.reported_by) }}</p>
                    </div>
                    <div class="text-right">
                        <p>Report date : {{ val(report.reported_date, '') !== '—' ? report.reported_date : reportDate }}</p>
                        <p>Lab. Technician</p>
                    </div>
                </div>

                <div class="mt-4 border-t-2 border-gray-400 pt-1.5 text-center text-[11px] leading-snug">
                    <p>#National Road 6A, Salakonseng Village, Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
                    <p>Tel: (855) 31 3 5555 88 / (855) 12 881 307 &nbsp; angkorfhospital@gmail.com</p>
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
@font-face {
    font-family: 'Khmer OS Muol Light';
    src: url('/fonts/KhmerOSMuolLight.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

.khmer-doc .khmer,
.khmer-doc {
    font-family: 'Noto Sans Khmer', 'DejaVu Sans', sans-serif;
}

/* Ornate Muol script for the official letterhead titles */
.khmer-doc .khmer-muol {
    font-family: 'Khmer OS Muol Light', 'Noto Sans Khmer', sans-serif;
    line-height: 2;
}

.khmer-doc .blue {
    color: #2456a6;
}

.khmer-doc .navy {
    color: #1e3a6e;
}

/* Dotted leader between test name and result, like the paper form */
.khmer-doc .leader {
    flex: 1;
    min-width: 12px;
    margin: 0 6px 5px 6px;
    border-bottom: 1.5px dotted #777;
}

@media print {
    .no-print {
        display: none !important;
    }

    #cbc-print-form {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
