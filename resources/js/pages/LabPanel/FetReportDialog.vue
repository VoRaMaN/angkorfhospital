<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { router } from '@inertiajs/vue3';
import { CheckCircle2, FlaskConical, Loader2, Search, User, X } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

// ─── Types ────────────────────────────────────────────────────────────────────
interface PatientOption {
    id: string;
    name: string;
    dob: string | null;
    phone: string | null;
    id_card: string | null;
    gender: string | null;
}

interface FetReportData {
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
    fet_date?: string | null;
    doctor?: string | null;
    freeze_datetime?: string | null;
    thaw_datetime?: string | null;
    thawing_media?: string | null;
    no_of_freeze?: number | null;
    no_of_thaw?: number | null;
    lot_no?: string | null;
    stage_of_freeze?: string | null;
    no_of_survival?: number | null;
    exp_date?: string | null;
    no_of_remaining?: number | null;
    thawing_by?: string | null;
    day3_datetime?: string | null;
    day3_embryo_1?: string | null;
    day3_embryo_2?: string | null;
    day3_embryo_3?: string | null;
    day3_embryo_4?: string | null;
    day3_embryo_5?: string | null;
    day5_datetime?: string | null;
    day5_embryo_1?: string | null;
    day5_embryo_2?: string | null;
    day5_embryo_3?: string | null;
    day5_embryo_4?: string | null;
    day5_embryo_5?: string | null;
    no_of_et?: number | null;
    et_volume?: string | null;
    number_of_transfer?: number | null;
    et_day?: number | null;
    et_catheter?: string | null;
    number_of_freeze_et?: number | null;
    et_datetime?: string | null;
    et_doctor?: string | null;
    number_of_discard?: number | null;
    assisted_hatching?: string | null;
    et_embryologist?: string | null;
    embryologist_report?: string | null;
    embryologist_approve?: string | null;
    remark?: string | null;
}

const props = defineProps<{
    modelValue: boolean;
    orderId: number;
    patientId: string | null;
    patientName: string;
    patientDob: string | null;
    staffName: string | null;
    existingReport: FetReportData | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', v: boolean): void;
    (e: 'saved'): void;
}>();

const isOpen = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

// ─── Auto-detect gender from name title ───────────────────────────────────────
const autoIsMale = computed(() =>
    props.patientName ? /^mr\.?\s/i.test(props.patientName.trim()) : false,
);

const formattedDob = computed(() => {
    const dob = props.patientDob;
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
const selectedFemale = ref<PatientOption | null>(null);
const selectedMale = ref<PatientOption | null>(null);

// ─── Partner patient search ───────────────────────────────────────────────────
const partnerQuery = ref('');
const partnerResults = ref<PatientOption[]>([]);
const partnerLoading = ref(false);
let partnerTimer: ReturnType<typeof setTimeout> | null = null;

watch(partnerQuery, (q) => {
    if (partnerTimer) clearTimeout(partnerTimer);
    if (q.length < 2) { partnerResults.value = []; return; }
    partnerTimer = setTimeout(async () => {
        partnerLoading.value = true;
        try {
            const resp = await fetch(`/opu-reports/search-patients?q=${encodeURIComponent(q)}`);
            partnerResults.value = await resp.json();
        } finally {
            partnerLoading.value = false;
        }
    }, 300);
});

const selectPartner = (p: PatientOption) => {
    if (autoIsMale.value) {
        selectedFemale.value = p;
        form.female_patient_id = p.id;
        form.female_patient_name = p.name;
        form.female_hn = p.id;
        form.female_dob = p.dob;
    } else {
        selectedMale.value = p;
        form.male_patient_id = p.id;
        form.male_patient_name = p.name;
        form.male_hn = p.id;
        form.male_dob = p.dob;
    }
    partnerQuery.value = '';
    partnerResults.value = [];
};

const clearPartner = () => {
    if (autoIsMale.value) {
        selectedFemale.value = null;
        form.female_patient_id = null;
        form.female_patient_name = null;
        form.female_hn = null;
        form.female_dob = null;
    } else {
        selectedMale.value = null;
        form.male_patient_id = null;
        form.male_patient_name = null;
        form.male_hn = null;
        form.male_dob = null;
    }
};
const buildEmptyForm = (): FetReportData => ({
    medical_order_id: props.orderId,
    female_patient_id: autoIsMale.value ? null : (props.patientId ?? null),
    female_patient_name: autoIsMale.value ? null : props.patientName,
    female_hn: autoIsMale.value ? null : (props.patientId ?? null),
    female_dob: null,
    male_patient_id: autoIsMale.value ? (props.patientId ?? null) : null,
    male_patient_name: autoIsMale.value ? props.patientName : null,
    male_hn: autoIsMale.value ? (props.patientId ?? null) : null,
    male_dob: null,
    procedure: 'FET',
    fet_date: null,
    doctor: props.staffName ?? null,
    freeze_datetime: null,
    thaw_datetime: null,
    thawing_media: null,
    no_of_freeze: null,
    no_of_thaw: null,
    lot_no: null,
    stage_of_freeze: null,
    no_of_survival: null,
    exp_date: null,
    no_of_remaining: null,
    thawing_by: null,
    day3_datetime: null,
    day3_embryo_1: null,
    day3_embryo_2: null,
    day3_embryo_3: null,
    day3_embryo_4: null,
    day3_embryo_5: null,
    day5_datetime: null,
    day5_embryo_1: null,
    day5_embryo_2: null,
    day5_embryo_3: null,
    day5_embryo_4: null,
    day5_embryo_5: null,
    no_of_et: null,
    et_volume: '15µl',
    number_of_transfer: null,
    et_day: null,
    et_catheter: null,
    number_of_freeze_et: null,
    et_datetime: null,
    et_doctor: null,
    number_of_discard: null,
    assisted_hatching: null,
    et_embryologist: null,
    embryologist_report: null,
    embryologist_approve: null,
    remark: null,
});

const form = reactive<FetReportData>(buildEmptyForm());

watch(
    () => props.modelValue,
    (open) => {
        if (open) {
            const r = props.existingReport;
            if (r) {
                Object.assign(form, r);
                selectedFemale.value = r.female_patient_id
                    ? { id: r.female_patient_id, name: r.female_patient_name ?? r.female_patient_id, dob: r.female_dob ?? null, phone: null, id_card: null, gender: null }
                    : null;
                selectedMale.value = r.male_patient_id
                    ? { id: r.male_patient_id, name: r.male_patient_name ?? r.male_patient_id, dob: r.male_dob ?? null, phone: null, id_card: null, gender: null }
                    : null;
            } else {
                Object.assign(form, buildEmptyForm());
                const autoPatient: PatientOption = {
                    id: props.patientId ?? '',
                    name: props.patientName,
                    dob: props.patientDob ?? null,
                    phone: null,
                    id_card: null,
                    gender: null,
                };
                if (autoIsMale.value) {
                    selectedFemale.value = null;
                    selectedMale.value = autoPatient;
                } else {
                    selectedFemale.value = autoPatient;
                    selectedMale.value = null;
                }
            }
            partnerQuery.value = '';
            partnerResults.value = [];
        }
    },
);

// ─── Save ─────────────────────────────────────────────────────────────────────
const saving = ref(false);

const save = () => {
    saving.value = true;
    const isEdit = !!props.existingReport?.id;
    const url = isEdit ? `/fet-reports/${props.existingReport!.id}` : '/fet-reports';
    const method = isEdit ? 'put' : 'post';

    router.visit(url, {
        method,
        data: { ...form },
        preserveScroll: true,
        onSuccess: () => {
            saving.value = false;
            emit('saved');
            isOpen.value = false;
        },
        onError: () => {
            saving.value = false;
        },
    });
};

// ─── Embryo grading image helper ──────────────────────────────────────────────
const gradingDays = [1, 2, 3, 4, 5];
</script>

<template>
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
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900">
                                <FlaskConical class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold">FET Report</h2>
                                <p class="text-xs text-muted-foreground">IVF LAB — Summary of FET Report</p>
                            </div>
                        </div>
                        <Button variant="ghost" size="icon" class="h-8 w-8" @click="isOpen = false">
                            <X class="h-4 w-4" />
                        </Button>
                    </div>

                    <div class="space-y-6 px-6 py-5">

                        <!-- ── Patient Information ────────────────────────── -->
                        <div class="rounded-xl border bg-muted/30 p-4">
                            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Patient Information</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Auto patient (gender-aware) -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium">{{ autoIsMale ? 'Name (Male)' : 'Name (Female)' }}</Label>
                                    <div class="flex items-center gap-2 rounded-lg border bg-background px-3 py-2">
                                        <User :class="autoIsMale ? 'h-4 w-4 shrink-0 text-blue-500' : 'h-4 w-4 shrink-0 text-pink-500'" />
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium">{{ patientName }}</p>
                                            <p v-if="formattedDob" class="text-xs text-muted-foreground">DOB: {{ formattedDob }}</p>
                                        </div>
                                        <Badge :class="autoIsMale ? 'ml-auto shrink-0 bg-blue-100 text-blue-700 border-blue-200 text-xs' : 'ml-auto shrink-0 bg-pink-100 text-pink-700 border-pink-200 text-xs'">Auto</Badge>
                                    </div>
                                    <p class="text-xs text-muted-foreground">H.N.: {{ patientId ?? '—' }}</p>
                                </div>

                                <!-- Partner patient (search) -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium">{{ autoIsMale ? 'Name (Female / Partner)' : 'Name (Male / Partner)' }}</Label>
                                    <div v-if="autoIsMale ? selectedFemale : selectedMale" class="flex items-center gap-2 rounded-lg border bg-background px-3 py-2">
                                        <User :class="autoIsMale ? 'h-4 w-4 shrink-0 text-pink-500' : 'h-4 w-4 shrink-0 text-blue-500'" />
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium">{{ (autoIsMale ? selectedFemale : selectedMale)?.name }}</p>
                                            <p v-if="(autoIsMale ? selectedFemale : selectedMale)?.dob" class="text-xs text-muted-foreground">DOB: {{ (autoIsMale ? selectedFemale : selectedMale)?.dob }}</p>
                                        </div>
                                        <button class="shrink-0 text-muted-foreground hover:text-destructive" @click="clearPartner">
                                            <X class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                    <div v-else class="relative">
                                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <input
                                            v-model="partnerQuery"
                                            type="text"
                                            placeholder="Search by name, ID or phone..."
                                            class="flex h-10 w-full rounded-lg border border-input bg-background py-2 pl-9 pr-3 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                        />
                                        <Loader2 v-if="partnerLoading" class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-muted-foreground" />
                                        <div v-if="partnerResults.length > 0" class="absolute z-20 mt-1 w-full rounded-xl border bg-background shadow-lg">
                                            <button
                                                v-for="p in partnerResults"
                                                :key="p.id"
                                                class="flex w-full items-start gap-3 px-3 py-2 text-left hover:bg-muted"
                                                @click="selectPartner(p)"
                                            >
                                                <User class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
                                                <div>
                                                    <p class="text-sm font-medium">{{ p.name }}</p>
                                                    <p class="text-xs text-muted-foreground">{{ p.id }}{{ p.dob ? ' · DOB: ' + p.dob : '' }}{{ p.phone ? ' · ' + p.phone : '' }}</p>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                    <p v-if="autoIsMale ? selectedFemale : selectedMale" class="text-xs text-muted-foreground">H.N.: {{ (autoIsMale ? selectedFemale : selectedMale)?.id }}</p>
                                </div>
                            </div>

                            <!-- Procedure / Date of FET / Doctor -->
                            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="space-y-1.5">
                                    <Label class="text-xs">Procedure</Label>
                                    <Input v-model="form.procedure" placeholder="e.g. FET" class="h-9 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-xs">Date of FET</Label>
                                    <Input v-model="form.fet_date" type="date" class="h-9 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-xs">Doctor</Label>
                                    <div class="flex h-9 items-center rounded-lg border bg-muted px-3 text-sm text-muted-foreground">
                                        {{ staffName ?? '—' }}
                                        <Badge class="ml-auto bg-muted text-xs">Auto</Badge>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Embryo Thawing ─────────────────────────────── -->
                        <div class="space-y-3">
                            <h3 class="text-center text-sm font-bold tracking-wide uppercase text-muted-foreground border-b pb-1">Embryo Thawing</h3>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                <div class="space-y-1">
                                    <Label class="text-xs">Date &amp; Time of Freeze</Label>
                                    <Input v-model="form.freeze_datetime" placeholder="DD/MM/YYYY HH:MM" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Date &amp; Time of Thaw</Label>
                                    <Input v-model="form.thaw_datetime" placeholder="DD/MM/YYYY HH:MM" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Thawing Media</Label>
                                    <Input v-model="form.thawing_media" placeholder="e.g. KITAZATO" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">No. of Freeze</Label>
                                    <Input v-model="form.no_of_freeze" type="number" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">No. of Thaw</Label>
                                    <Input v-model="form.no_of_thaw" type="number" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Lot No.</Label>
                                    <Input v-model="form.lot_no" placeholder="e.g. L0804" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Stage of Freeze</Label>
                                    <Input v-model="form.stage_of_freeze" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">No. of Survival</Label>
                                    <Input v-model="form.no_of_survival" type="number" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Exp.</Label>
                                    <Input v-model="form.exp_date" placeholder="YYYY.MM.DD" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">No. of Remaining</Label>
                                    <Input v-model="form.no_of_remaining" type="number" class="h-8 text-sm" />
                                </div>
                                <div class="col-span-2 space-y-1">
                                    <Label class="text-xs">Thawing by</Label>
                                    <Input v-model="form.thawing_by" class="h-8 text-sm" />
                                </div>
                            </div>
                        </div>

                        <!-- ── Embryo Development ─────────────────────────── -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Day 3 -->
                            <div class="space-y-2 rounded-lg border p-3">
                                <h4 class="text-center text-xs font-bold uppercase tracking-wide">Embryo Development (Day 3)</h4>
                                <div class="space-y-1">
                                    <Label class="text-xs">Date &amp; Time</Label>
                                    <Input v-model="form.day3_datetime" placeholder="DD/MM/YYYY HH:MM" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <div v-for="n in 5" :key="n" class="flex items-center gap-2">
                                        <span class="w-4 shrink-0 text-xs text-muted-foreground">{{ n }}</span>
                                        <Input
                                            v-model="(form as any)[`day3_embryo_${n}`]"
                                            placeholder="e.g. g4A"
                                            class="h-7 text-xs"
                                        />
                                    </div>
                                </div>
                            </div>
                            <!-- Day 5 -->
                            <div class="space-y-2 rounded-lg border p-3">
                                <h4 class="text-center text-xs font-bold uppercase tracking-wide">Embryo Development (Day 5)</h4>
                                <div class="space-y-1">
                                    <Label class="text-xs">Date &amp; Time</Label>
                                    <Input v-model="form.day5_datetime" placeholder="DD/MM/YYYY HH:MM" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <div v-for="n in 5" :key="n" class="flex items-center gap-2">
                                        <span class="w-4 shrink-0 text-xs text-muted-foreground">{{ n }}</span>
                                        <Input
                                            v-model="(form as any)[`day5_embryo_${n}`]"
                                            placeholder="e.g. g4A"
                                            class="h-7 text-xs"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Embryo for ET ──────────────────────────────── -->
                        <div class="space-y-3">
                            <h3 class="text-center text-sm font-bold tracking-wide uppercase text-muted-foreground border-b pb-1">Embryo for ET</h3>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                <div class="space-y-1">
                                    <Label class="text-xs">No. of ET</Label>
                                    <Input v-model="form.no_of_et" type="number" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">ET Volume</Label>
                                    <Input v-model="form.et_volume" placeholder="15µl" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Number of Transfer</Label>
                                    <Input v-model="form.number_of_transfer" type="number" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Day</Label>
                                    <Input v-model="form.et_day" type="number" placeholder="5" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">ET Catheter</Label>
                                    <Input v-model="form.et_catheter" placeholder="e.g. KJET" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Number of Freeze</Label>
                                    <Input v-model="form.number_of_freeze_et" type="number" class="h-8 text-sm" />
                                </div>
                                <div class="col-span-2 space-y-1">
                                    <Label class="text-xs">Date &amp; Time ET</Label>
                                    <Input v-model="form.et_datetime" placeholder="DD/MM/YYYY HH:MM" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Number of Discard</Label>
                                    <Input v-model="form.number_of_discard" type="number" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">ET Doctor</Label>
                                    <Input v-model="form.et_doctor" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Assisted Hatching</Label>
                                    <Input v-model="form.assisted_hatching" placeholder="—" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">ET Embryologist</Label>
                                    <Input v-model="form.et_embryologist" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Embryologist Report</Label>
                                    <Input v-model="form.embryologist_report" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Embryologist Approve</Label>
                                    <Input v-model="form.embryologist_approve" class="h-8 text-sm" />
                                </div>
                            </div>
                        </div>

                        <!-- ── Remark ─────────────────────────────────────── -->
                        <div class="space-y-1">
                            <Label class="text-xs">Remark</Label>
                            <Textarea v-model="form.remark" rows="2" placeholder="Remarks…" class="text-sm" />
                        </div>

                        <!-- ── Embryo Development Grading ─────────────────── -->
                        <div class="rounded-lg border p-4 space-y-3">
                            <h3 class="text-center text-sm font-bold tracking-wide">Embryo Development Grading</h3>
                            <!-- Day images -->
                            <div class="grid grid-cols-5 gap-2">
                                <div v-for="day in gradingDays" :key="day" class="space-y-1 text-center">
                                    <p class="text-xs font-semibold text-muted-foreground">Day {{ day }}</p>
                                    <img
                                        :src="`/images/embryo-grading/day${day}.png`"
                                        :alt="`Day ${day} embryo`"
                                        class="mx-auto h-16 w-16 rounded border object-contain bg-white p-0.5"
                                        onerror="this.style.display='none'"
                                    />
                                </div>
                            </div>
                            <!-- Grading key -->
                            <div class="grid grid-cols-2 gap-x-6 gap-y-0.5 text-xs text-muted-foreground sm:grid-cols-3">
                                <span>g4 = grade 4 (Very good embryo)</span>
                                <span>g2 = grade 2 (Fair embryo)</span>
                                <span>A = Very good</span>
                                <span>g3 = grade 3 (Good embryo)</span>
                                <span>g1 = grade 1 (Not good embryo)</span>
                                <span>B = Good</span>
                                <span></span><span></span>
                                <span>C = Fair</span>
                            </div>
                        </div>

                    </div><!-- /content -->

                    <!-- ── Footer ─────────────────────────────────────────── -->
                    <div class="sticky bottom-0 flex items-center justify-between rounded-b-2xl border-t bg-background px-6 py-4">
                        <Button variant="outline" @click="isOpen = false">
                            <X class="mr-2 h-4 w-4" />
                            Cancel
                        </Button>
                        <Button :disabled="saving" @click="save">
                            <Loader2 v-if="saving" class="mr-2 h-4 w-4 animate-spin" />
                            <CheckCircle2 v-else class="mr-2 h-4 w-4" />
                            {{ saving ? 'Saving…' : 'Save FET Report' }}
                        </Button>
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>
