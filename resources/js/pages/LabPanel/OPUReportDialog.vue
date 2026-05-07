<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { router, usePage } from '@inertiajs/vue3';
import {
    CheckCircle2,
    ClipboardList,
    Eye,
    Loader2,
    Printer,
    Search,
    User,
    X,
} from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

// ─── Props ────────────────────────────────────────────────────────────────────
interface PatientOption {
    id: string;
    name: string;
    dob: string | null;
    phone: string | null;
    id_card: string | null;
    gender: string | null;
}

interface OPUReportData {
    id?: number;
    medical_order_id: number;
    female_patient_id?: string | null;
    female_patient_name?: string | null;
    female_hn?: string | null;
    female_dob?: string | null;
    male_patient_id?: string | null;
    male_patient_name?: string | null;
    male_hn?: string | null;
    male_dob?: string | null;
    procedure?: string | null;
    doctor_id?: number | null;
    opu_datetime?: string | null;
    no_of_opu_right?: number | null;
    no_of_opu_left?: number | null;
    maturation_datetime?: string | null;
    m_ii?: number | null;
    m_i?: number | null;
    gv?: number | null;
    post_mature?: number | null;
    maturation_abnormal?: number | null;
    maturation_dead?: number | null;
    fertilization_datetime?: string | null;
    pn2?: number | null;
    pn1?: number | null;
    pn3?: number | null;
    pn4?: number | null;
    no_pn?: number | null;
    fert_dead?: number | null;
    sperm_prep_datetime?: string | null;
    sperm_volume_ml?: number | null;
    sperm_count_per_ml?: number | null;
    sperm_total_count?: number | null;
    sperm_motile_per_ml?: number | null;
    sperm_total_motile?: number | null;
    sperm_motility_pct?: number | null;
    sperm_type?: 'fresh' | 'frozen' | null;
    embryo_freeze_datetime?: string | null;
    freeze_day?: string | null;
    freeze_stage?: string | null;
    no_of_embryo?: number | null;
    no_of_straw?: number | null;
    freeze_position?: string | null;
    freeze_method?: string | null;
    freeze_media?: string | null;
    day3_datetime?: string | null;
    day3_checked_by?: string | null;
    day3_embryos?: (string | null)[];
    day5_datetime?: string | null;
    day5_checked_by?: string | null;
    day5_embryos?: (string | null)[];
    et_no?: number | null;
    et_day?: string | null;
    et_datetime?: string | null;
    assisted_hatching?: boolean;
    et_volume?: string | null;
    et_catheter?: string | null;
    et_doctor?: string | null;
    et_embryologist?: string | null;
    number_of_transfer?: number | null;
    number_of_freeze?: number | null;
    number_of_discard?: number | null;
    remark?: string | null;
    embryologist_report?: string | null;
    embryologist_approve?: string | null;
}

const props = defineProps<{
    modelValue: boolean;
    orderId: number;
    femalePatientId: string | null;
    femalePatientName: string;
    femalePatientDob: string | null;
    doctorStaffId: number | null;
    doctorStaffName: string | null;
    existingReport: OPUReportData | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', v: boolean): void;
    (e: 'saved'): void;
}>();

const isOpen = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

// Format DOB as D.M.YY (Xyr)
const formattedDob = computed(() => {
    const dob = props.femalePatientDob;
    if (!dob) return null;
    const d = new Date(dob);
    if (isNaN(d.getTime())) return null;
    const day = d.getDate();
    const month = d.getMonth() + 1;
    const year = String(d.getFullYear()).slice(-2);
    const today = new Date();
    let age = today.getFullYear() - d.getFullYear();
    if (today.getMonth() < d.getMonth() || (today.getMonth() === d.getMonth() && today.getDate() < d.getDate())) {
        age--;
    }
    return `${day}.${month}.${year} (${age}yr)`;
});

// ─── Form state ───────────────────────────────────────────────────────────────
const now = () => {
    const d = new Date();
    return d.toISOString().slice(0, 16);
};

const isMaleProp = () => props.femalePatientName ? /^mr\.?\s/i.test(props.femalePatientName.trim()) : false;
const autoIsMale = computed(() => isMaleProp());

const buildEmptyForm = (): OPUReportData => ({
    medical_order_id: props.orderId,
    female_patient_id: isMaleProp() ? null : props.femalePatientId,
    male_patient_id: isMaleProp() ? props.femalePatientId : null,
    procedure: 'OPU-ICSI',
    doctor_id: props.doctorStaffId,
    opu_datetime: now(),
    no_of_opu_right: null, no_of_opu_left: null,
    maturation_datetime: null, m_ii: null, m_i: null, gv: null,
    post_mature: null, maturation_abnormal: null, maturation_dead: null,
    fertilization_datetime: null, pn2: null, pn1: null, pn3: null,
    pn4: null, no_pn: null, fert_dead: null,
    sperm_prep_datetime: null, sperm_volume_ml: null, sperm_count_per_ml: null,
    sperm_total_count: null, sperm_motile_per_ml: null, sperm_total_motile: null,
    sperm_motility_pct: null, sperm_type: null,
    embryo_freeze_datetime: null, freeze_day: null, freeze_stage: null,
    no_of_embryo: null, no_of_straw: null, freeze_position: null,
    freeze_method: null, freeze_media: null,
    day3_datetime: null, day3_checked_by: null, day3_embryos: Array(20).fill(null),
    day5_datetime: null, day5_checked_by: null, day5_embryos: Array(20).fill(null),
    et_no: null, et_day: null, et_datetime: null, assisted_hatching: false,
    et_volume: '15µl', et_catheter: null, et_doctor: null, et_embryologist: null,
    number_of_transfer: null, number_of_freeze: null, number_of_discard: null,
    remark: null, embryologist_report: null, embryologist_approve: null,
});

const form = reactive<OPUReportData>(buildEmptyForm());

// Populate from existing report when dialog opens
watch(
    () => props.modelValue,
    (open) => {
        if (open) {
            savedReportId.value = null;
            const r = props.existingReport;
            if (r) {
                Object.assign(form, {
                    ...r,
                    day3_embryos: r.day3_embryos ?? Array(20).fill(null),
                    day5_embryos: r.day5_embryos ?? Array(20).fill(null),
                });
            } else {
                Object.assign(form, buildEmptyForm());
            }
            // Determine if the auto-patient is male by checking the name title
            const isMaleTitle = props.femalePatientName
                ? /^mr\.?\s/i.test(props.femalePatientName.trim())
                : false;

            if (r) {
                // Existing report: restore saved patient slots
                selectedFemale.value = r.female_patient_id
                    ? { id: r.female_patient_id, name: r.female_patient_name ?? r.female_patient_id, dob: r.female_dob ?? null, phone: null, id_card: null, gender: null }
                    : null;
                selectedMale.value = r.male_patient_id
                    ? { id: r.male_patient_id, name: r.male_patient_name ?? r.male_patient_id, dob: r.male_dob ?? null, phone: null, id_card: null, gender: null }
                    : null;
            } else {
                // New report: route auto-patient to correct gender slot
                const autoPatient = props.femalePatientId
                    ? { id: props.femalePatientId, name: props.femalePatientName, dob: null, phone: null, id_card: null, gender: null }
                    : null;
                if (isMaleTitle) {
                    selectedFemale.value = null;
                    selectedMale.value = autoPatient;
                    form.female_patient_id = null;
                    form.male_patient_id = autoPatient?.id ?? null;
                } else {
                    selectedFemale.value = autoPatient;
                    selectedMale.value = null;
                }
            }
        }
    },
);

// ─── Patient search ───────────────────────────────────────────────────────────
const selectedFemale = ref<PatientOption | null>(null);
const selectedMale = ref<PatientOption | null>(null);

const maleSearchQuery = ref('');
const maleSearchResults = ref<PatientOption[]>([]);
const maleSearchLoading = ref(false);
let maleSearchTimer: ReturnType<typeof setTimeout> | null = null;

watch(maleSearchQuery, (q) => {
    if (maleSearchTimer) clearTimeout(maleSearchTimer);
    if (q.length < 2) { maleSearchResults.value = []; return; }
    maleSearchTimer = setTimeout(async () => {
        maleSearchLoading.value = true;
        try {
            const resp = await fetch(`/opu-reports/search-patients?q=${encodeURIComponent(q)}`);
            maleSearchResults.value = await resp.json();
        } finally {
            maleSearchLoading.value = false;
        }
    }, 300);
});

const selectPartnerPatient = (p: PatientOption) => {
    if (autoIsMale.value) {
        selectedFemale.value = p;
        form.female_patient_id = p.id;
    } else {
        selectedMale.value = p;
        form.male_patient_id = p.id;
    }
    maleSearchQuery.value = '';
    maleSearchResults.value = [];
};
const clearPartnerPatient = () => {
    if (autoIsMale.value) {
        selectedFemale.value = null;
        form.female_patient_id = null;
    } else {
        selectedMale.value = null;
        form.male_patient_id = null;
    }
};

// ─── Embryo helpers ───────────────────────────────────────────────────────────
const embryoSlots = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];

// Embryo grid: 4 columns of 5 rows each displayed as pairs side-by-side
// Slots 1-5, 6-10, 11-15, 16-20
const embryoCols = [[0,1,2,3,4],[5,6,7,8,9],[10,11,12,13,14],[15,16,17,18,19]];

// ─── Print ────────────────────────────────────────────────────────────────────
const openPrintTab = (orderId: number) => window.open(`/opu-reports/order/${orderId}/pdf`, '_blank');
const savedReportId = ref<number | null>(null);

// ─── Save ─────────────────────────────────────────────────────────────────────
const saving = ref(false);

const save = () => {
    saving.value = true;
    const payload = { ...form };
    const isEdit = !!props.existingReport?.id;

    const url = isEdit ? `/opu-reports/${props.existingReport!.id}` : '/opu-reports';
    const method = isEdit ? 'put' : 'post';

    router.visit(url, {
        method,
        data: payload,
        preserveScroll: true,
        onSuccess: () => {
            saving.value = false;
            const flash = (usePage().props as any).flash;
            if (flash?.report_id) { savedReportId.value = flash.report_id; }
            emit('saved');
        },
        onError: () => { saving.value = false; },
    });
};
</script>

<template>
    <!-- Full-screen overlay dialog -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isOpen" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/60 p-4 pt-8">
                <div class="relative w-full max-w-4xl rounded-2xl bg-background shadow-2xl">
                    <!-- Header -->
                    <div class="sticky top-0 z-10 flex items-center justify-between rounded-t-2xl border-b bg-background px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/50">
                                <ClipboardList class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h2 class="text-lg font-bold">OPU Report</h2>
                                <p class="text-xs text-muted-foreground">Summary of OPU Report — Angkor-F Clinic</p>
                            </div>
                        </div>
                        <button class="rounded-lg p-1.5 hover:bg-muted" @click="isOpen = false">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="space-y-6 p-6">
                        <!-- ── Section: Patient Info ─────────────────────── -->
                        <div class="rounded-xl border bg-muted/30 p-4">
                            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Patient Information</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Auto Patient (left slot — gender-aware) -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium">{{ autoIsMale ? 'Name (Male)' : 'Name (Female)' }}</Label>
                                    <div class="flex items-center gap-2 rounded-lg border bg-background px-3 py-2">
                                        <User :class="autoIsMale ? 'h-4 w-4 shrink-0 text-blue-500' : 'h-4 w-4 shrink-0 text-pink-500'" />
                                        <div class="flex-1 min-w-0">
                                            <p class="truncate text-sm font-medium">{{ femalePatientName }}</p>
                                            <p v-if="formattedDob" class="text-xs text-muted-foreground">DOB: {{ formattedDob }}</p>
                                        </div>
                                        <Badge :class="autoIsMale ? 'ml-auto shrink-0 bg-blue-100 text-blue-700 border-blue-200 text-xs' : 'ml-auto shrink-0 bg-pink-100 text-pink-700 border-pink-200 text-xs'">Auto</Badge>
                                    </div>
                                    <div class="text-xs text-muted-foreground">H.N.: {{ femalePatientId ?? '—' }}</div>
                                </div>

                                <!-- Partner Patient (right slot — search) -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium">{{ autoIsMale ? 'Name (Female / Partner)' : 'Name (Male / Partner)' }}</Label>
                                    <div v-if="autoIsMale ? selectedFemale : selectedMale" class="flex items-center gap-2 rounded-lg border bg-background px-3 py-2">
                                        <User :class="autoIsMale ? 'h-4 w-4 shrink-0 text-pink-500' : 'h-4 w-4 shrink-0 text-blue-500'" />
                                        <div class="flex-1 min-w-0">
                                            <p class="truncate text-sm font-medium">{{ (autoIsMale ? selectedFemale : selectedMale)?.name }}</p>
                                            <p v-if="(autoIsMale ? selectedFemale : selectedMale)?.dob" class="text-xs text-muted-foreground">DOB: {{ (autoIsMale ? selectedFemale : selectedMale)?.dob }}</p>
                                        </div>
                                        <button class="shrink-0 text-muted-foreground hover:text-destructive" @click="clearPartnerPatient">
                                            <X class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                    <div v-else class="relative">
                                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <input
                                            v-model="maleSearchQuery"
                                            type="text"
                                            placeholder="Search by name, ID or phone..."
                                            class="flex h-10 w-full rounded-lg border border-input bg-background py-2 pl-9 pr-3 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                        />
                                        <Loader2 v-if="maleSearchLoading" class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-muted-foreground" />
                                        <!-- Search results dropdown -->
                                        <div v-if="maleSearchResults.length > 0" class="absolute z-20 mt-1 w-full rounded-xl border bg-background shadow-lg">
                                            <button
                                                v-for="p in maleSearchResults"
                                                :key="p.id"
                                                class="flex w-full items-start gap-3 px-3 py-2 text-left hover:bg-muted"
                                                @click="selectPartnerPatient(p)"
                                            >
                                                <User class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
                                                <div>
                                                    <p class="text-sm font-medium">{{ p.name }}</p>
                                                    <p class="text-xs text-muted-foreground">{{ p.id }}{{ p.dob ? ' · DOB: ' + p.dob : '' }}{{ p.phone ? ' · ' + p.phone : '' }}</p>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="autoIsMale ? selectedFemale : selectedMale" class="text-xs text-muted-foreground">H.N.: {{ (autoIsMale ? selectedFemale : selectedMale)?.id }}</div>
                                </div>
                            </div>

                            <!-- Procedure / Doctor / OPU DateTime -->
                            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="space-y-1.5">
                                    <Label class="text-xs">Procedure</Label>
                                    <Input v-model="form.procedure" placeholder="e.g. OPU-ICSI" class="h-9 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-xs">Doctor</Label>
                                    <div class="flex h-9 items-center rounded-lg border bg-muted px-3 text-sm text-muted-foreground">
                                        {{ doctorStaffName ?? '—' }}
                                        <Badge class="ml-auto bg-muted text-xs">Auto</Badge>
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-xs">Date &amp; Time of OPU</Label>
                                    <Input v-model="form.opu_datetime" type="datetime-local" class="h-9 text-sm" />
                                </div>
                            </div>
                        </div>

                        <!-- ── Section: Maturation / Fertilization / Sperm / Freeze ── -->
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <!-- Maturation -->
                            <div class="rounded-xl border p-4">
                                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Maturation</h3>
                                <div class="space-y-2.5">
                                    <div class="flex gap-2">
                                        <div class="flex-1 space-y-1">
                                            <Label class="text-xs">No of OPU (Rt)</Label>
                                            <Input v-model.number="form.no_of_opu_right" type="number" min="0" class="h-8 text-sm" />
                                        </div>
                                        <div class="flex-1 space-y-1">
                                            <Label class="text-xs">No of OPU (Lt)</Label>
                                            <Input v-model.number="form.no_of_opu_left" type="number" min="0" class="h-8 text-sm" />
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <Label class="text-xs">Date &amp; Time</Label>
                                        <Input v-model="form.maturation_datetime" type="datetime-local" class="h-8 text-sm" />
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div v-for="(label, key) in {m_ii:'M II',m_i:'M I',gv:'GV',post_mature:'Post Mature',maturation_abnormal:'Abnormal',maturation_dead:'Dead'}" :key="key" class="space-y-1">
                                            <Label class="text-xs">{{ label }}</Label>
                                            <Input v-model.number="(form as any)[key]" type="number" min="0" class="h-8 text-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fertilization -->
                            <div class="rounded-xl border p-4">
                                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Fertilization</h3>
                                <div class="space-y-2.5">
                                    <div class="space-y-1">
                                        <Label class="text-xs">Date &amp; Time</Label>
                                        <Input v-model="form.fertilization_datetime" type="datetime-local" class="h-8 text-sm" />
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div v-for="(label, key) in {pn2:'2 PN',pn1:'1 PN',pn3:'3 PN',pn4:'4 PN',no_pn:'No PN',fert_dead:'Dead'}" :key="key" class="space-y-1">
                                            <Label class="text-xs">{{ label }}</Label>
                                            <Input v-model.number="(form as any)[key]" type="number" min="0" class="h-8 text-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sperm Preparation -->
                            <div class="rounded-xl border p-4">
                                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Sperm Preparation</h3>
                                <div class="space-y-2.5">
                                    <div class="space-y-1">
                                        <Label class="text-xs">Date &amp; Time</Label>
                                        <Input v-model="form.sperm_prep_datetime" type="datetime-local" class="h-8 text-sm" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="space-y-1">
                                            <Label class="text-xs">Volume (ml)</Label>
                                            <Input v-model.number="form.sperm_volume_ml" type="number" step="0.01" min="0" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Count (×10⁶/ml)</Label>
                                            <Input v-model.number="form.sperm_count_per_ml" type="number" step="0.01" min="0" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Total count (×10⁶)</Label>
                                            <Input v-model.number="form.sperm_total_count" type="number" step="0.01" min="0" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Motile (×10⁶/ml)</Label>
                                            <Input v-model.number="form.sperm_motile_per_ml" type="number" step="0.01" min="0" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Total motile (×10⁶)</Label>
                                            <Input v-model.number="form.sperm_total_motile" type="number" step="0.01" min="0" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Motility (%)</Label>
                                            <Input v-model.number="form.sperm_motility_pct" type="number" step="0.1" min="0" max="100" class="h-8 text-sm" />
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <Label class="text-xs">Sperm Type:</Label>
                                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                            <input type="radio" v-model="form.sperm_type" value="fresh" class="accent-blue-600" /> Fresh
                                        </label>
                                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                            <input type="radio" v-model="form.sperm_type" value="frozen" class="accent-blue-600" /> Frozen
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Embryo Freezing -->
                            <div class="rounded-xl border p-4">
                                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo Freezing</h3>
                                <div class="space-y-2.5">
                                    <div class="space-y-1">
                                        <Label class="text-xs">Date &amp; Time</Label>
                                        <Input v-model="form.embryo_freeze_datetime" type="datetime-local" class="h-8 text-sm" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="space-y-1">
                                            <Label class="text-xs">Day</Label>
                                            <Input v-model="form.freeze_day" placeholder="e.g. Day 5" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Stage</Label>
                                            <Input v-model="form.freeze_stage" placeholder="e.g. Blastocyst" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">No. of Embryo</Label>
                                            <Input v-model.number="form.no_of_embryo" type="number" min="0" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">No. of Straw</Label>
                                            <Input v-model.number="form.no_of_straw" type="number" min="0" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Position</Label>
                                            <Input v-model="form.freeze_position" placeholder="e.g. Tank A-1" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Method</Label>
                                            <Input v-model="form.freeze_method" placeholder="e.g. Vitrification" class="h-8 text-sm" />
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <Label class="text-xs">Freeze Media</Label>
                                        <Input v-model="form.freeze_media" placeholder="e.g. Kitazato" class="h-8 text-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Embryo Development Day 3 ──────────────────── -->
                        <div class="rounded-xl border p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo Development (Day 3)</h3>
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="space-y-1">
                                    <Label class="text-xs">Date &amp; Time</Label>
                                    <Input v-model="form.day3_datetime" type="datetime-local" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Checked By</Label>
                                    <Input v-model="form.day3_checked_by" placeholder="Embryologist name" class="h-8 text-sm" />
                                </div>
                            </div>
                            <!-- Embryo grid 4×5 -->
                            <div class="grid grid-cols-4 gap-2">
                                <div v-for="col in embryoCols" :key="col[0]" class="space-y-2">
                                    <div v-for="idx in col" :key="idx" class="flex items-center gap-1.5">
                                        <span class="w-5 shrink-0 text-xs font-medium text-muted-foreground">{{ idx + 1 }}</span>
                                        <Input
                                            v-model="form.day3_embryos![idx]"
                                            type="text"
                                            :placeholder="`g4/A`"
                                            class="h-7 text-xs"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Embryo Development Day 5 ──────────────────── -->
                        <div class="rounded-xl border p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo Development (Day 5)</h3>
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="space-y-1">
                                    <Label class="text-xs">Date &amp; Time</Label>
                                    <Input v-model="form.day5_datetime" type="datetime-local" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Checked By</Label>
                                    <Input v-model="form.day5_checked_by" placeholder="Embryologist name" class="h-8 text-sm" />
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <div v-for="col in embryoCols" :key="col[0]" class="space-y-2">
                                    <div v-for="idx in col" :key="idx" class="flex items-center gap-1.5">
                                        <span class="w-5 shrink-0 text-xs font-medium text-muted-foreground">{{ idx + 1 }}</span>
                                        <Input
                                            v-model="form.day5_embryos![idx]"
                                            type="text"
                                            :placeholder="`A/B`"
                                            class="h-7 text-xs"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Embryo for ET ──────────────────────────────── -->
                        <div class="rounded-xl border p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo for ET (Embryo Transfer)</h3>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                <div class="space-y-1">
                                    <Label class="text-xs">No. of ET</Label>
                                    <Input v-model.number="form.et_no" type="number" min="0" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Day</Label>
                                    <Input v-model="form.et_day" placeholder="e.g. Day 5" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Date &amp; Time</Label>
                                    <Input v-model="form.et_datetime" type="datetime-local" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">ET Volume</Label>
                                    <Input v-model="form.et_volume" placeholder="15µl" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">ET Catheter</Label>
                                    <Input v-model="form.et_catheter" placeholder="Catheter type" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Number of Transfer</Label>
                                    <Input v-model.number="form.number_of_transfer" type="number" min="0" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Number of Freeze</Label>
                                    <Input v-model.number="form.number_of_freeze" type="number" min="0" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Number of Discard</Label>
                                    <Input v-model.number="form.number_of_discard" type="number" min="0" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">ET Doctor</Label>
                                    <Input v-model="form.et_doctor" placeholder="Doctor name" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1 sm:col-span-2">
                                    <Label class="text-xs">ET Embryologist</Label>
                                    <Input v-model="form.et_embryologist" placeholder="Embryologist name" class="h-8 text-sm" />
                                </div>
                                <div class="flex items-center gap-2 pt-2">
                                    <Checkbox id="assisted_hatching" v-model:checked="form.assisted_hatching" />
                                    <Label for="assisted_hatching" class="cursor-pointer text-sm">Assisted Hatching</Label>
                                </div>
                            </div>
                        </div>

                        <!-- ── Embryo Development Grading Legend ─────────── -->
                        <div class="rounded-xl border p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Embryo Development Grading</h3>
                            <div class="mb-4 grid grid-cols-5 gap-3">
                                <div v-for="day in [1,2,3,4,5]" :key="day" class="text-center">
                                    <p class="mb-2 text-xs font-medium text-muted-foreground">Day {{ day }}</p>
                                    <img
                                        :src="`/images/embryo-grading/day${day}.png`"
                                        :alt="`Day ${day} embryo`"
                                        class="mx-auto h-16 w-16 rounded-lg object-contain border bg-white dark:bg-white/10"
                                        @error="(e) => (e.target as HTMLImageElement).src = '/images/embryo-grading/placeholder.png'"
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

                        <!-- ── Remark & Embryologist ──────────────────────── -->
                        <div class="rounded-xl border p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Notes</h3>
                            <div class="space-y-3">
                                <div class="space-y-1.5">
                                    <Label class="text-xs">Remark</Label>
                                    <Textarea v-model="form.remark" placeholder="Any remarks..." class="min-h-[60px] text-sm" />
                                </div>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div class="space-y-1.5">
                                        <Label class="text-xs">Embryologist Report</Label>
                                        <Textarea v-model="form.embryologist_report" placeholder="Report text..." class="min-h-[60px] text-sm" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <Label class="text-xs">Embryologist Approve</Label>
                                        <Textarea v-model="form.embryologist_approve" placeholder="Approval notes..." class="min-h-[60px] text-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="sticky bottom-0 flex items-center justify-between rounded-b-2xl border-t bg-background px-6 py-4">
                        <Button variant="outline" @click="isOpen = false">Cancel</Button>
                        <div class="flex gap-2">
                            <Button v-if="existingReport || savedReportId" variant="outline" class="gap-2" @click="() => openPrintTab(props.orderId)">
                                <Printer class="h-4 w-4" />
                                Print
                            </Button>
                            <Button :disabled="saving" class="gap-2" @click="save">
                            <Loader2 v-if="saving" class="h-4 w-4 animate-spin" />
                            <CheckCircle2 v-else class="h-4 w-4" />
                            {{ existingReport ? 'Update Report' : 'Save Report' }}
                        </Button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
