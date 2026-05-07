<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, ClipboardList, Edit2, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import OPUReportDialog from './OPUReportDialog.vue';

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
    doctor_id: number | null;
    doctor_name: string | null;
    opu_datetime: string | null;
    no_of_opu_right: number | null;
    no_of_opu_left: number | null;
    maturation_datetime: string | null;
    m_ii: number | null;
    m_i: number | null;
    gv: number | null;
    post_mature: number | null;
    maturation_abnormal: number | null;
    maturation_dead: number | null;
    fertilization_datetime: string | null;
    pn2: number | null;
    pn1: number | null;
    pn3: number | null;
    pn4: number | null;
    no_pn: number | null;
    fert_dead: number | null;
    sperm_prep_datetime: string | null;
    sperm_volume_ml: number | null;
    sperm_count_per_ml: number | null;
    sperm_total_count: number | null;
    sperm_motile_per_ml: number | null;
    sperm_total_motile: number | null;
    sperm_motility_pct: number | null;
    sperm_type: string | null;
    embryo_freeze_datetime: string | null;
    freeze_day: string | null;
    freeze_stage: string | null;
    no_of_embryo: number | null;
    no_of_straw: number | null;
    freeze_position: string | null;
    freeze_method: string | null;
    freeze_media: string | null;
    day3_datetime: string | null;
    day3_checked_by: string | null;
    day3_embryos: (string | null)[];
    day5_datetime: string | null;
    day5_checked_by: string | null;
    day5_embryos: (string | null)[];
    et_no: number | null;
    et_day: string | null;
    et_datetime: string | null;
    assisted_hatching: boolean;
    et_volume: string | null;
    et_catheter: string | null;
    et_doctor: string | null;
    et_embryologist: string | null;
    number_of_transfer: number | null;
    number_of_freeze: number | null;
    number_of_discard: number | null;
    remark: string | null;
    embryologist_report: string | null;
    embryologist_approve: string | null;
}

const props = defineProps<{ report: ReportData }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Lab Panels', href: '/lab-panels' },
    { title: 'OPU Report', href: '#' },
];

// ─── Format helpers ───────────────────────────────────────────────────────────
const fmtDob = (dob: string | null): string | null => {
    if (!dob) return null;
    // dob is DD/MM/YYYY from server
    const parts = dob.split('/');
    if (parts.length !== 3) return dob;
    const [day, month, year] = parts.map(Number);
    const d = new Date(year, month - 1, day);
    if (isNaN(d.getTime())) return dob;
    const today = new Date();
    let age = today.getFullYear() - year;
    if (today.getMonth() + 1 < month || (today.getMonth() + 1 === month && today.getDate() < day)) {
        age--;
    }
    const yy = String(year).slice(-2);
    return `${day}.${month}.${yy} (${age}yr)`;
};

const fmtDatetime = (dt: string | null): string | null => {
    if (!dt) return null;
    const d = new Date(dt);
    if (isNaN(d.getTime())) return dt;
    return d.toLocaleString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false });
};

const val = (v: string | number | null | undefined, unit = ''): string =>
    v !== null && v !== undefined ? `${v}${unit}` : '—';

const embryoCols = [[0,1,2,3,4],[5,6,7,8,9],[10,11,12,13,14],[15,16,17,18,19]];

// Auto-detect gender from name title
const femaleSlotIsMale = computed(() =>
    props.report.female_patient_name ? /^mr\.?\s/i.test(props.report.female_patient_name.trim()) : false,
);

// ─── Edit dialog ──────────────────────────────────────────────────────────────
const editOpen = ref(false);

// OPUReportDialog expects femalePatientDob in a format new Date() can parse.
// Convert DD/MM/YYYY → YYYY-MM-DD for the dialog's computed.
const femaleDobForDialog = computed(() => {
    const dob = props.report.female_dob;
    if (!dob) return null;
    const parts = dob.split('/');
    if (parts.length !== 3) return null;
    const [d, m, y] = parts;
    return `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
});

const onSaved = () => {
    router.reload();
};
</script>

<template>
    <Head title="OPU Report" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-5xl space-y-6 px-6 py-4">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button variant="ghost" size="icon" @click="router.visit('/lab-panels')">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div class="flex items-center gap-2">
                        <ClipboardList class="h-5 w-5 text-primary" />
                        <div>
                            <h1 class="text-xl font-bold">OPU Report</h1>
                            <p class="text-xs text-muted-foreground">Summary of OPU Report — Angkor-F Clinic</p>
                        </div>
                    </div>
                </div>
                <Button @click="editOpen = true" class="gap-2">
                    <Edit2 class="h-4 w-4" />
                    Edit Report
                </Button>
            </div>

            <!-- Patient Information -->
            <div class="rounded-xl border p-4 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Patient Information</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Left slot (gender-aware) -->
                    <div class="space-y-1">
                        <p class="text-xs text-muted-foreground">{{ femaleSlotIsMale ? 'Name (Male)' : 'Name (Female)' }}</p>
                        <div v-if="report.female_patient_name" class="flex items-center gap-2 rounded-lg border bg-background px-3 py-2">
                            <User :class="femaleSlotIsMale ? 'h-4 w-4 shrink-0 text-blue-500' : 'h-4 w-4 shrink-0 text-pink-500'" />
                            <div class="flex-1 min-w-0">
                                <p class="truncate text-sm font-medium">{{ report.female_patient_name }}</p>
                                <p v-if="fmtDob(report.female_dob)" class="text-xs text-muted-foreground">DOB: {{ fmtDob(report.female_dob) }}</p>
                            </div>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">—</p>
                        <p class="text-xs text-muted-foreground">H.N.: {{ report.female_hn ?? '—' }}</p>
                    </div>
                    <!-- Right slot (partner, gender-aware) -->
                    <div class="space-y-1">
                        <p class="text-xs text-muted-foreground">{{ femaleSlotIsMale ? 'Name (Female / Partner)' : 'Name (Male / Partner)' }}</p>
                        <div v-if="report.male_patient_name" class="flex items-center gap-2 rounded-lg border bg-background px-3 py-2">
                            <User class="h-4 w-4 shrink-0 text-blue-500" />
                            <div class="flex-1 min-w-0">
                                <p class="truncate text-sm font-medium">{{ report.male_patient_name }}</p>
                                <p v-if="fmtDob(report.male_dob)" class="text-xs text-muted-foreground">DOB: {{ fmtDob(report.male_dob) }}</p>
                            </div>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">—</p>
                        <p v-if="report.male_hn" class="text-xs text-muted-foreground">H.N.: {{ report.male_hn }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs text-muted-foreground">Procedure</p>
                        <p class="text-sm font-medium">{{ val(report.procedure) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Doctor</p>
                        <p class="text-sm font-medium">{{ val(report.doctor_name) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Date &amp; Time of OPU</p>
                        <p class="text-sm font-medium">{{ fmtDatetime(report.opu_datetime) ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Two-column grid for middle sections -->
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            <!-- OPU Egg Count -->
            <div class="rounded-xl border p-4 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">OPU Egg Count</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-muted-foreground">Right Ovary</p>
                        <p class="text-sm font-medium">{{ val(report.no_of_opu_right) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Left Ovary</p>
                        <p class="text-sm font-medium">{{ val(report.no_of_opu_left) }}</p>
                    </div>
                </div>
            </div>

            <!-- Maturation -->
            <div class="rounded-xl border p-4 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Maturation Check</h2>
                <p class="text-xs text-muted-foreground">{{ fmtDatetime(report.maturation_datetime) ?? '—' }}</p>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                    <div><p class="text-xs text-muted-foreground">MII</p><p class="text-sm font-medium">{{ val(report.m_ii) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">MI</p><p class="text-sm font-medium">{{ val(report.m_i) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">GV</p><p class="text-sm font-medium">{{ val(report.gv) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Post Mature</p><p class="text-sm font-medium">{{ val(report.post_mature) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Abnormal</p><p class="text-sm font-medium">{{ val(report.maturation_abnormal) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Dead</p><p class="text-sm font-medium">{{ val(report.maturation_dead) }}</p></div>
                </div>
            </div>

            <!-- Fertilization -->
            <div class="rounded-xl border p-4 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Fertilization Check</h2>
                <p class="text-xs text-muted-foreground">{{ fmtDatetime(report.fertilization_datetime) ?? '—' }}</p>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                    <div><p class="text-xs text-muted-foreground">2PN</p><p class="text-sm font-medium">{{ val(report.pn2) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">1PN</p><p class="text-sm font-medium">{{ val(report.pn1) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">3PN</p><p class="text-sm font-medium">{{ val(report.pn3) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">4PN</p><p class="text-sm font-medium">{{ val(report.pn4) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">0PN</p><p class="text-sm font-medium">{{ val(report.no_pn) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Dead</p><p class="text-sm font-medium">{{ val(report.fert_dead) }}</p></div>
                </div>
            </div>

            <!-- Sperm Preparation -->
            <div class="rounded-xl border p-4 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Sperm Preparation</h2>
                <p class="text-xs text-muted-foreground">{{ fmtDatetime(report.sperm_prep_datetime) ?? '—' }}</p>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div><p class="text-xs text-muted-foreground">Type</p><p class="text-sm font-medium capitalize">{{ val(report.sperm_type) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Volume (ml)</p><p class="text-sm font-medium">{{ val(report.sperm_volume_ml) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Count/ml (M)</p><p class="text-sm font-medium">{{ val(report.sperm_count_per_ml) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Total Count (M)</p><p class="text-sm font-medium">{{ val(report.sperm_total_count) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Motile/ml (M)</p><p class="text-sm font-medium">{{ val(report.sperm_motile_per_ml) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Total Motile (M)</p><p class="text-sm font-medium">{{ val(report.sperm_total_motile) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Motility (%)</p><p class="text-sm font-medium">{{ val(report.sperm_motility_pct) }}</p></div>
                </div>
            </div>

            <!-- Embryo Freezing -->
            <div class="rounded-xl border p-4 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo Freezing</h2>
                <p class="text-xs text-muted-foreground">{{ fmtDatetime(report.embryo_freeze_datetime) ?? '—' }}</p>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div><p class="text-xs text-muted-foreground">Day</p><p class="text-sm font-medium">{{ val(report.freeze_day) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Stage</p><p class="text-sm font-medium">{{ val(report.freeze_stage) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">No. of Embryo</p><p class="text-sm font-medium">{{ val(report.no_of_embryo) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">No. of Straw</p><p class="text-sm font-medium">{{ val(report.no_of_straw) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Position</p><p class="text-sm font-medium">{{ val(report.freeze_position) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Method</p><p class="text-sm font-medium">{{ val(report.freeze_method) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Media</p><p class="text-sm font-medium">{{ val(report.freeze_media) }}</p></div>
                </div>
            </div>

            </div><!-- end 2-col grid -->

            <!-- Embryo Development Day 3 -->
            <div class="rounded-xl border p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo Development (Day 3)</h2>
                    <span v-if="report.day3_datetime" class="text-xs text-muted-foreground">{{ fmtDatetime(report.day3_datetime) }}</span>
                </div>
                <p v-if="report.day3_checked_by" class="text-xs text-muted-foreground">Checked by: {{ report.day3_checked_by }}</p>
                <div class="grid grid-cols-4 gap-2">
                    <div v-for="col in embryoCols" :key="col[0]" class="space-y-1">
                        <div v-for="idx in col" :key="idx" class="flex items-center gap-1.5">
                            <span class="w-5 shrink-0 text-xs text-muted-foreground">{{ idx + 1 }}</span>
                            <span class="text-xs font-medium">{{ report.day3_embryos?.[idx] || '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Embryo Development Day 5 -->
            <div class="rounded-xl border p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo Development (Day 5)</h2>
                    <span v-if="report.day5_datetime" class="text-xs text-muted-foreground">{{ fmtDatetime(report.day5_datetime) }}</span>
                </div>
                <p v-if="report.day5_checked_by" class="text-xs text-muted-foreground">Checked by: {{ report.day5_checked_by }}</p>
                <div class="grid grid-cols-4 gap-2">
                    <div v-for="col in embryoCols" :key="col[0]" class="space-y-1">
                        <div v-for="idx in col" :key="idx" class="flex items-center gap-1.5">
                            <span class="w-5 shrink-0 text-xs text-muted-foreground">{{ idx + 1 }}</span>
                            <span class="text-xs font-medium">{{ report.day5_embryos?.[idx] || '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Embryo for ET -->
            <div class="rounded-xl border p-4 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo for ET (Embryo Transfer)</h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div><p class="text-xs text-muted-foreground">No. of ET</p><p class="text-sm font-medium">{{ val(report.et_no) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Day</p><p class="text-sm font-medium">{{ val(report.et_day) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Date &amp; Time</p><p class="text-sm font-medium">{{ fmtDatetime(report.et_datetime) ?? '—' }}</p></div>
                    <div><p class="text-xs text-muted-foreground">ET Volume</p><p class="text-sm font-medium">{{ val(report.et_volume) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">ET Catheter</p><p class="text-sm font-medium">{{ val(report.et_catheter) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Number of Transfer</p><p class="text-sm font-medium">{{ val(report.number_of_transfer) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Number of Freeze</p><p class="text-sm font-medium">{{ val(report.number_of_freeze) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">Number of Discard</p><p class="text-sm font-medium">{{ val(report.number_of_discard) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">ET Doctor</p><p class="text-sm font-medium">{{ val(report.et_doctor) }}</p></div>
                    <div><p class="text-xs text-muted-foreground">ET Embryologist</p><p class="text-sm font-medium">{{ val(report.et_embryologist) }}</p></div>
                    <div>
                        <p class="text-xs text-muted-foreground">Assisted Hatching</p>
                        <Badge :class="report.assisted_hatching ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'" class="mt-0.5 border text-xs">
                            {{ report.assisted_hatching ? 'Yes' : 'No' }}
                        </Badge>
                    </div>
                </div>
            </div>

            <!-- Embryo Development Grading -->
            <div class="rounded-xl border p-4 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo Development Grading</h2>
                <div class="grid grid-cols-5 gap-3">
                    <div v-for="day in [1,2,3,4,5]" :key="day" class="text-center">
                        <p class="mb-2 text-xs font-medium text-muted-foreground">Day {{ day }}</p>
                        <img
                            :src="`/images/embryo-grading/day${day}.png`"
                            :alt="`Day ${day} embryo`"
                            class="mx-auto h-16 w-16 rounded-lg object-contain border bg-white dark:bg-white/10"
                            @error="(e) => (e.target as HTMLImageElement).style.display='none'"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-2 text-xs text-muted-foreground sm:grid-cols-2">
                    <div>
                        <p><strong class="text-foreground">Cleavage (Day 1–3):</strong></p>
                        <p>g4 = grade 4 (Very good embryo)</p>
                        <p>g3 = grade 3 (Good embryo)</p>
                        <p>g2 = grade 2 (Fair embryo)</p>
                        <p>g1 = grade 1 (Not good embryo)</p>
                    </div>
                    <div>
                        <p><strong class="text-foreground">Blastocyst (Day 4–5):</strong></p>
                        <p>A = Very good</p>
                        <p>B = Good</p>
                        <p>C = Fair</p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="rounded-xl border p-4 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Notes</h2>
                <div v-if="report.remark">
                    <p class="text-xs text-muted-foreground">Remark</p>
                    <p class="text-sm whitespace-pre-wrap">{{ report.remark }}</p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div v-if="report.embryologist_report">
                        <p class="text-xs text-muted-foreground">Embryologist Report</p>
                        <p class="text-sm whitespace-pre-wrap">{{ report.embryologist_report }}</p>
                    </div>
                    <div v-if="report.embryologist_approve">
                        <p class="text-xs text-muted-foreground">Embryologist Approve</p>
                        <p class="text-sm whitespace-pre-wrap">{{ report.embryologist_approve }}</p>
                    </div>
                </div>
                <p v-if="!report.remark && !report.embryologist_report && !report.embryologist_approve" class="text-sm text-muted-foreground">—</p>
            </div>

        </div>

        <!-- Edit Dialog -->
        <OPUReportDialog
            v-model="editOpen"
            :order-id="report.medical_order_id"
            :female-patient-id="report.female_patient_id"
            :female-patient-name="report.female_patient_name ?? ''"
            :female-patient-dob="femaleDobForDialog"
            :doctor-staff-id="report.doctor_id"
            :doctor-staff-name="report.doctor_name"
            :existing-report="(report as any)"
            @saved="onSaved"
        />
    </AppLayout>
</template>
