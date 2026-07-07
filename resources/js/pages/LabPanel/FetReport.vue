<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Edit2, FlaskConical, Printer } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import FetReportDialog from './FetReportDialog.vue';

interface ReportData {
    id: number;
    medical_order_id: number;
    female_patient_id: string | null;
    female_patient_name: string | null;
    female_hn: string | null;
    female_dob: string | null;
    male_patient_id: string | null;
    male_patient_name: string | null;
    male_hn: string | null;
    male_dob: string | null;
    procedure: string | null;
    fet_date: string | null;
    doctor: string | null;
    doctor_name: string | null;
    freeze_datetime: string | null;
    thaw_datetime: string | null;
    thawing_media: string | null;
    no_of_freeze: number | null;
    no_of_thaw: number | null;
    lot_no: string | null;
    stage_of_freeze: string | null;
    no_of_survival: number | null;
    exp_date: string | null;
    no_of_remaining: number | null;
    thawing_by: string | null;
    day3_datetime: string | null;
    day3_embryo_1: string | null;
    day3_embryo_2: string | null;
    day3_embryo_3: string | null;
    day3_embryo_4: string | null;
    day3_embryo_5: string | null;
    day5_datetime: string | null;
    day5_embryo_1: string | null;
    day5_embryo_2: string | null;
    day5_embryo_3: string | null;
    day5_embryo_4: string | null;
    day5_embryo_5: string | null;
    picture_day: number | null;
    picture_datetime: string | null;
    embryo_pictures: { no: string | null; path: string | null }[] | null;
    no_of_et: number | null;
    et_volume: string | null;
    number_of_transfer: number | null;
    et_day: number | null;
    et_catheter: string | null;
    number_of_freeze_et: number | null;
    et_datetime: string | null;
    et_doctor: string | null;
    number_of_discard: number | null;
    assisted_hatching: string | null;
    et_embryologist: string | null;
    embryologist_report: string | null;
    embryologist_approve: string | null;
    remark: string | null;
    created_at: string | null;
}

const props = defineProps<{ report: ReportData }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Lab Panels', href: '/lab-panels' },
    { title: 'FET Report', href: '#' },
];

const val = (v: string | number | null | undefined, suffix = ''): string =>
    v !== null && v !== undefined && v !== '' ? `${v}${suffix}` : '—';

const savedPictures = computed(() =>
    (props.report.embryo_pictures ?? []).filter((p) => p?.path),
);

const pictureUrl = (path: string) =>
    `/fet-reports/embryo-image/${encodeURIComponent(path.replace(/^fet_embryos\//, ''))}`;

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
    <Head title="FET Report" />
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
                    <FlaskConical class="h-5 w-5 text-orange-500" />
                    <div>
                        <h1 class="text-lg font-bold">FET Report</h1>
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
                    class="inline-flex h-9 items-center gap-2 rounded-md bg-orange-500 px-3 text-sm font-medium text-white shadow-sm transition-colors hover:bg-orange-600"
                    @click="printReport"
                >
                    <Printer class="h-4 w-4" />
                    Print
                </button>
            </div>
        </div>

        <!-- ─── Printable Form ─────────────────────────────────────────────── -->
        <div class="mx-auto max-w-4xl px-6 pb-10">
            <div id="fet-print-form" class="bg-white text-black print:shadow-none print:p-0 rounded-lg border shadow-sm p-6">

                <!-- ── Header ── -->
                <div class="flex items-start justify-between border-b-2 border-black pb-3 mb-3">
                    <div class="flex items-center gap-3">
                        <img src="/images/logo1.png" alt="Angkor-F Hospital" class="h-24 w-auto object-contain" />
                        <div class="ml-1">
                            <p class="text-xs leading-tight text-gray-600">#National Road 6A, Salakonseng Village,</p>
                            <p class="text-xs leading-tight text-gray-600">Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
                            <p class="text-xs leading-tight text-gray-600">Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
                            <p class="text-xs leading-tight text-gray-600">E-mail: angkorfhospital@gmail.com</p>
                        </div>
                    </div>
                    <div class="text-sm space-y-0.5 text-right">
                        <p><span class="font-semibold">Date:</span> {{ reportDate }}</p>
                        <p><span class="font-semibold">Doctor:</span> {{ val(report.doctor_name ?? report.doctor) }}</p>
                        <p><span class="font-semibold">Procedure:</span> {{ val(report.procedure) }}</p>
                        <p><span class="font-semibold">FET Date:</span> {{ val(report.fet_date) }}</p>
                    </div>
                </div>

                <!-- ── Title ── -->
                <div class="text-center mb-4">
                    <p class="font-bold text-base tracking-wide">IVF LAB</p>
                    <p class="font-semibold text-sm">( Frozen Embryo Transfer Report )</p>
                </div>

                <!-- ── Patient Info ── -->
                <div class="grid grid-cols-2 gap-x-6 text-sm mb-3 border-b border-gray-300 pb-3">
                    <div class="space-y-1">
                        <p class="font-bold text-xs uppercase tracking-wide text-gray-500 mb-1">Female Patient</p>
                        <div><span class="font-semibold">Name:</span> <span>{{ val(report.female_patient_name) }}</span></div>
                        <div><span class="font-semibold">H.N:</span> <span>{{ val(report.female_hn) }}</span></div>
                        <div><span class="font-semibold">DOB:</span> <span>{{ val(report.female_dob) }}</span></div>
                    </div>
                    <div class="space-y-1">
                        <p class="font-bold text-xs uppercase tracking-wide text-gray-500 mb-1">Male Patient (Husband)</p>
                        <div><span class="font-semibold">Name:</span> <span>{{ val(report.male_patient_name) }}</span></div>
                        <div><span class="font-semibold">H.N:</span> <span>{{ val(report.male_hn) }}</span></div>
                        <div><span class="font-semibold">DOB:</span> <span>{{ val(report.male_dob) }}</span></div>
                    </div>
                </div>

                <!-- ── Freeze / Thaw Info ── -->
                <div class="mb-3">
                    <p class="font-bold text-xs uppercase tracking-wide text-gray-700 border-b border-gray-400 pb-0.5 mb-2">Freeze / Thaw Information</p>
                    <div class="grid grid-cols-2 gap-x-6 text-sm">
                        <div class="space-y-1">
                            <div><span class="font-semibold">Freeze Date/Time:</span> <span>{{ val(report.freeze_datetime) }}</span></div>
                            <div><span class="font-semibold">Thaw Date/Time:</span> <span>{{ val(report.thaw_datetime) }}</span></div>
                            <div><span class="font-semibold">Thawing Media:</span> <span>{{ val(report.thawing_media) }}</span></div>
                            <div><span class="font-semibold">No. of Freeze:</span> <span>{{ val(report.no_of_freeze) }}</span></div>
                            <div><span class="font-semibold">No. of Thaw:</span> <span>{{ val(report.no_of_thaw) }}</span></div>
                        </div>
                        <div class="space-y-1">
                            <div><span class="font-semibold">Lot No.:</span> <span>{{ val(report.lot_no) }}</span></div>
                            <div><span class="font-semibold">Stage of Freeze:</span> <span>{{ val(report.stage_of_freeze) }}</span></div>
                            <div><span class="font-semibold">No. of Survival:</span> <span>{{ val(report.no_of_survival) }}</span></div>
                            <div><span class="font-semibold">Exp. Date:</span> <span>{{ val(report.exp_date) }}</span></div>
                            <div><span class="font-semibold">No. of Remaining:</span> <span>{{ val(report.no_of_remaining) }}</span></div>
                            <div><span class="font-semibold">Thawing By:</span> <span>{{ val(report.thawing_by) }}</span></div>
                        </div>
                    </div>
                </div>

                <!-- ── Day 3 Embryos ── -->
                <div class="mb-3">
                    <p class="font-bold text-xs uppercase tracking-wide text-gray-700 border-b border-gray-400 pb-0.5 mb-2">Day 3 Embryos</p>
                    <div class="text-sm mb-1">
                        <span class="font-semibold">Date/Time:</span> <span>{{ val(report.day3_datetime) }}</span>
                    </div>
                    <div class="grid grid-cols-5 gap-2 text-sm">
                        <div v-for="n in 5" :key="n" class="border border-gray-300 rounded p-1 text-center">
                            <p class="text-xs text-gray-500 font-semibold">E{{ n }}</p>
                            <p>{{ val((report as any)[`day3_embryo_${n}`]) }}</p>
                        </div>
                    </div>
                </div>

                <!-- ── Day 5 Embryos ── -->
                <div class="mb-3">
                    <p class="font-bold text-xs uppercase tracking-wide text-gray-700 border-b border-gray-400 pb-0.5 mb-2">Day 5 Embryos</p>
                    <div class="text-sm mb-1">
                        <span class="font-semibold">Date/Time:</span> <span>{{ val(report.day5_datetime) }}</span>
                    </div>
                    <div class="grid grid-cols-5 gap-2 text-sm">
                        <div v-for="n in 5" :key="n" class="border border-gray-300 rounded p-1 text-center">
                            <p class="text-xs text-gray-500 font-semibold">E{{ n }}</p>
                            <p>{{ val((report as any)[`day5_embryo_${n}`]) }}</p>
                        </div>
                    </div>
                </div>

                <!-- ── Picture of Embryo Development ── -->
                <div v-if="savedPictures.length" class="mb-3">
                    <p class="font-bold text-xs uppercase tracking-wide text-gray-700 border-b border-gray-400 pb-0.5 mb-2">Picture of Embryo Development</p>
                    <div class="text-sm mb-1">
                        <span class="font-semibold">Day:</span> <span>{{ val(report.picture_day) }}</span>
                        <span class="ml-4 font-semibold">Date &amp; Time:</span> <span>{{ val(report.picture_datetime) }}</span>
                    </div>
                    <div class="grid grid-cols-5 gap-2">
                        <div v-for="(pic, pi) in savedPictures" :key="pi" class="border border-gray-300 rounded p-1 text-center">
                            <p class="text-left text-xs font-semibold text-gray-600">{{ pic.no || '' }}</p>
                            <img :src="pictureUrl(pic.path!)" class="mx-auto max-h-24 object-contain" alt="Embryo" />
                        </div>
                    </div>
                </div>

                <!-- ── Embryo Transfer ── -->
                <div class="mb-3">
                    <p class="font-bold text-xs uppercase tracking-wide text-gray-700 border-b border-gray-400 pb-0.5 mb-2">Embryo Transfer (ET)</p>
                    <div class="grid grid-cols-2 gap-x-6 text-sm">
                        <div class="space-y-1">
                            <div><span class="font-semibold">ET Date/Time:</span> <span>{{ val(report.et_datetime) }}</span></div>
                            <div><span class="font-semibold">ET Day:</span> <span>{{ val(report.et_day) }}</span></div>
                            <div><span class="font-semibold">No. of ET:</span> <span>{{ val(report.no_of_et) }}</span></div>
                            <div><span class="font-semibold">ET Volume:</span> <span>{{ val(report.et_volume) }}</span></div>
                            <div><span class="font-semibold">ET Catheter:</span> <span>{{ val(report.et_catheter) }}</span></div>
                        </div>
                        <div class="space-y-1">
                            <div><span class="font-semibold">No. of Transfer:</span> <span>{{ val(report.number_of_transfer) }}</span></div>
                            <div><span class="font-semibold">No. Freeze after ET:</span> <span>{{ val(report.number_of_freeze_et) }}</span></div>
                            <div><span class="font-semibold">No. of Discard:</span> <span>{{ val(report.number_of_discard) }}</span></div>
                            <div><span class="font-semibold">Assisted Hatching:</span> <span>{{ val(report.assisted_hatching) }}</span></div>
                            <div><span class="font-semibold">ET Doctor:</span> <span>{{ val(report.et_doctor) }}</span></div>
                        </div>
                    </div>
                </div>

                <!-- ── Remark ── -->
                <div v-if="report.remark" class="mt-3 text-sm border-t border-gray-300 pt-2">
                    <span class="font-semibold">Remark:</span>
                    <p class="mt-1 whitespace-pre-wrap text-gray-800">{{ report.remark }}</p>
                </div>

                <!-- ── Embryologist / Approval ── -->
                <div class="mt-4 grid grid-cols-3 gap-x-4 text-sm border-t border-gray-300 pt-3">
                    <div class="space-y-1">
                        <p class="font-semibold text-xs uppercase tracking-wide text-gray-500">Embryologist</p>
                        <p>{{ val(report.et_embryologist) }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="font-semibold text-xs uppercase tracking-wide text-gray-500">Reported By</p>
                        <p>{{ val(report.embryologist_report) }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="font-semibold text-xs uppercase tracking-wide text-gray-500">Approved By</p>
                        <p>{{ val(report.embryologist_approve) }}</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Edit dialog -->
        <FetReportDialog
            v-if="editOpen"
            v-model="editOpen"
            :order-id="report.medical_order_id"
            :patient-id="report.female_patient_id"
            :patient-name="report.female_patient_name ?? ''"
            :patient-dob="report.female_dob"
            :staff-name="report.doctor_name ?? report.doctor"
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
