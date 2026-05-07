<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { router } from '@inertiajs/vue3';
import { CheckCircle2, ClipboardList, Loader2, Search, User, X } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

// ─── Props & Emits ────────────────────────────────────────────────────────────
interface PatientOption {
    id: string;
    name: string;
    dob: string | null;
    phone: string | null;
    id_card: string | null;
    gender: string | null;
}

interface SaReportData {
    id?: number;
    medical_order_id: number;
    patient_id?: string | null;
    wife_name?: string | null;
    abstinence_days?: number | null;
    appearance?: string | null;
    liquefaction?: string | null;
    viscosity?: string | null;
    ph?: number | null;
    viability?: number | null;
    volume?: number | null;
    count_per_ml?: number | null;
    total_count?: number | null;
    motile?: number | null;
    total_motile?: number | null;
    motility?: number | null;
    motility_4_rapid?: number | null;
    motility_3_medium?: number | null;
    motility_2_slow?: number | null;
    motility_1_static?: number | null;
    wbc?: string | null;
    morphology_normal?: number | null;
    morphology_abnormal?: number | null;
    head_defect?: number | null;
    neck_defect?: number | null;
    tail_defect?: number | null;
    ejaculation_time?: string | null;
    examination_time?: string | null;
    receive_time?: string | null;
    finish_time?: string | null;
    remark?: string | null;
    reported_by?: string | null;
    reported_date?: string | null;
    reported_time?: string | null;
    approved_by?: string | null;
    approved_date?: string | null;
    approved_time?: string | null;
}

const props = defineProps<{
    modelValue: boolean;
    orderId: number;
    patientId: string | null;
    patientName: string;
    patientDob: string | null;
    patientHn: string | null;
    doctorName: string | null;
    existingReport: SaReportData | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', v: boolean): void;
    (e: 'saved'): void;
}>();

const isOpen = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

// ─── Gender detection ─────────────────────────────────────────────────────────
const patientIsMale = computed(() => {
    const name = props.patientName?.trim() ?? '';
    return !/^(mrs\.?\s|ms\.?\s|miss\s)/i.test(name);
});

const partnerLabel = computed(() => patientIsMale.value ? "Wife's Name" : "Husband's Name");
const patientSexLabel = computed(() => patientIsMale.value ? 'MALE' : 'FEMALE');

// ─── Partner patient search ───────────────────────────────────────────────────
const selectedPartner = ref<PatientOption | null>(null);
const partnerQuery = ref('');
const partnerResults = ref<PatientOption[]>([]);
const partnerLoading = ref(false);
let partnerTimer: ReturnType<typeof setTimeout> | null = null;

watch(partnerQuery, (q) => {
    if (partnerTimer) { clearTimeout(partnerTimer); }
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
    selectedPartner.value = p;
    form.wife_name = p.name;
    partnerQuery.value = '';
    partnerResults.value = [];
};

const clearPartner = () => {
    selectedPartner.value = null;
    form.wife_name = null;
};

// ─── Patient age display ──────────────────────────────────────────────────────
const patientAge = computed(() => {
    const dob = props.patientDob;
    if (!dob) { return null; }
    const parts = dob.split('/');
    if (parts.length !== 3) { return null; }
    const d = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
    if (isNaN(d.getTime())) { return null; }
    const today = new Date();
    let age = today.getFullYear() - d.getFullYear();
    if (today.getMonth() < d.getMonth() || (today.getMonth() === d.getMonth() && today.getDate() < d.getDate())) {
        age--;
    }
    return age;
});

// ─── Form state ───────────────────────────────────────────────────────────────
const buildEmptyForm = (): SaReportData => ({
    medical_order_id: props.orderId,
    patient_id: props.patientId,
    wife_name: null,
    abstinence_days: null,
    appearance: null,
    liquefaction: null,
    viscosity: null,
    ph: null,
    viability: null,
    volume: null,
    count_per_ml: null,
    total_count: null,
    motile: null,
    total_motile: null,
    motility: null,
    motility_4_rapid: null,
    motility_3_medium: null,
    motility_2_slow: null,
    motility_1_static: null,
    wbc: null,
    morphology_normal: null,
    morphology_abnormal: null,
    head_defect: null,
    neck_defect: null,
    tail_defect: null,
    ejaculation_time: null,
    examination_time: null,
    receive_time: null,
    finish_time: null,
    remark: null,
    reported_by: null,
    reported_date: null,
    reported_time: null,
    approved_by: null,
    approved_date: null,
    approved_time: null,
});

const form = reactive<SaReportData>(buildEmptyForm());

watch(
    () => props.modelValue,
    (open) => {
        if (open) {
            const r = props.existingReport;
            if (r) {
                Object.assign(form, r);
                selectedPartner.value = r.wife_name
                    ? { id: '', name: r.wife_name, dob: null, phone: null, id_card: null, gender: null }
                    : null;
            } else {
                Object.assign(form, buildEmptyForm());
                selectedPartner.value = null;
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
    const url = isEdit ? `/sa-reports/${props.existingReport!.id}` : '/sa-reports';
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
        onError: () => { saving.value = false; },
    });
};
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
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                <ClipboardList class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold">SA Report</h2>
                                <p class="text-xs text-muted-foreground">IVF LAB — Semen Analysis</p>
                            </div>
                        </div>
                        <Button variant="ghost" size="icon" class="h-8 w-8" @click="isOpen = false">
                            <X class="h-4 w-4" />
                        </Button>
                    </div>

                    <div class="space-y-6 px-6 py-5">

                        <!-- ── Patient Header ─────────────────────────────── -->
                        <div class="rounded-xl border bg-muted/30 p-4">
                            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm sm:grid-cols-4">
                                <div class="col-span-2 sm:col-span-3">
                                    <span class="text-xs text-muted-foreground">Name</span>
                                    <p class="font-medium">{{ patientName || '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">SEX</span>
                                    <p class="font-medium uppercase">{{ patientSexLabel }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">HN</span>
                                    <p class="font-medium">{{ patientHn || patientId || '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">DOB</span>
                                    <p class="font-medium">{{ patientDob || '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">Age</span>
                                    <p class="font-medium">{{ patientAge !== null ? patientAge + ' Yrs.' : '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">Doctor</span>
                                    <p class="font-medium">{{ doctorName || '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- ── Semen Analysis ─────────────────────────────── -->
                        <div class="space-y-4">
                            <h3 class="text-center text-base font-bold tracking-wide">Semen Analysis</h3>

                            <!-- Partner name & Abstinence -->
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="space-y-1">
                                    <Label class="text-xs">{{ partnerLabel }}</Label>
                                    <!-- Selected partner -->
                                    <div v-if="selectedPartner" class="flex items-center gap-2 rounded-lg border bg-background px-3 py-1.5">
                                        <User class="h-4 w-4 shrink-0 text-muted-foreground" />
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium">{{ selectedPartner.name }}</p>
                                            <p v-if="selectedPartner.dob" class="text-xs text-muted-foreground">DOB: {{ selectedPartner.dob }}</p>
                                        </div>
                                        <button class="shrink-0 text-muted-foreground hover:text-destructive" @click="clearPartner">
                                            <X class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                    <!-- Search input -->
                                    <div v-else class="relative">
                                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <input
                                            v-model="partnerQuery"
                                            type="text"
                                            :placeholder="'Search ' + partnerLabel + ' by name, ID, phone…'"
                                            class="flex h-8 w-full rounded-lg border border-input bg-background py-1.5 pl-9 pr-3 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
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
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Abstinence Day</Label>
                                    <Input v-model="form.abstinence_days" type="number" placeholder="Days" class="h-8 text-sm" />
                                </div>
                            </div>

                            <!-- Appearance, Liquefaction, Viscosity, pH -->
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div class="space-y-1">
                                    <Label class="text-xs">Appearance</Label>
                                    <Input v-model="form.appearance" placeholder="" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Liquefaction <span class="text-muted-foreground">(norm: 30 mins)</span></Label>
                                    <Input v-model="form.liquefaction" placeholder="e.g. 30 mins" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Viscosity</Label>
                                    <Input v-model="form.viscosity" placeholder="" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">pH <span class="text-muted-foreground">(norm: 7.2–8.0)</span></Label>
                                    <Input v-model="form.ph" type="number" step="0.1" placeholder="7.4" class="h-8 text-sm" />
                                </div>
                            </div>

                            <!-- Main Parameters -->
                            <div class="rounded-lg border">
                                <div class="grid grid-cols-[auto_1fr_auto_auto] items-center gap-x-3 border-b bg-muted/40 px-3 py-1.5 text-xs font-semibold text-muted-foreground">
                                    <span>Parameter</span><span></span><span>Value</span><span class="text-right">Normal Range</span>
                                </div>
                                <div class="divide-y">
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Viability</Label>
                                        <span></span>
                                        <Input v-model="form.viability" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="whitespace-nowrap text-xs text-muted-foreground">&gt; 75</span>
                                    </div>
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Volume</Label>
                                        <span class="text-xs text-muted-foreground">ml.</span>
                                        <Input v-model="form.volume" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="whitespace-nowrap text-xs text-muted-foreground">&gt; 2</span>
                                    </div>
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Count</Label>
                                        <span class="text-xs text-muted-foreground">×10⁶/ml.</span>
                                        <Input v-model="form.count_per_ml" type="number" step="0.01" class="h-7 text-xs" />
                                        <span class="whitespace-nowrap text-xs text-muted-foreground">&gt; 20</span>
                                    </div>
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Total Count</Label>
                                        <span class="text-xs text-muted-foreground">×10⁶</span>
                                        <Input v-model="form.total_count" type="number" step="0.01" class="h-7 text-xs" />
                                        <span class="whitespace-nowrap text-xs text-muted-foreground">&gt; 40</span>
                                    </div>
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Motile</Label>
                                        <span class="text-xs text-muted-foreground">×10⁶/ml.</span>
                                        <Input v-model="form.motile" type="number" step="0.01" class="h-7 text-xs" />
                                        <span></span>
                                    </div>
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Total Motile</Label>
                                        <span class="text-xs text-muted-foreground">×10⁶</span>
                                        <Input v-model="form.total_motile" type="number" step="0.01" class="h-7 text-xs" />
                                        <span></span>
                                    </div>
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Motility</Label>
                                        <span class="text-xs text-muted-foreground">%</span>
                                        <Input v-model="form.motility" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="whitespace-nowrap text-xs text-muted-foreground">&gt; 50</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Motility Rate -->
                            <div class="space-y-1">
                                <Label class="text-xs font-semibold">Motility Rate (%)</Label>
                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                    <div class="space-y-1">
                                        <Label class="text-xs text-muted-foreground">4 Rapid <span class="text-muted-foreground">(norm: &gt;25)</span></Label>
                                        <div class="flex items-center gap-1">
                                            <Input v-model="form.motility_4_rapid" type="number" step="0.1" class="h-7 text-xs" />
                                            <span class="text-xs">%</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <Label class="text-xs text-muted-foreground">3 Medium</Label>
                                        <div class="flex items-center gap-1">
                                            <Input v-model="form.motility_3_medium" type="number" step="0.1" class="h-7 text-xs" />
                                            <span class="text-xs">%</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <Label class="text-xs text-muted-foreground">2 Slow</Label>
                                        <div class="flex items-center gap-1">
                                            <Input v-model="form.motility_2_slow" type="number" step="0.1" class="h-7 text-xs" />
                                            <span class="text-xs">%</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <Label class="text-xs text-muted-foreground">1 Static</Label>
                                        <div class="flex items-center gap-1">
                                            <Input v-model="form.motility_1_static" type="number" step="0.1" class="h-7 text-xs" />
                                            <span class="text-xs">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- WBC -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="space-y-1">
                                    <Label class="text-xs">WBC (/HPF)</Label>
                                    <Input v-model="form.wbc" placeholder="" class="h-8 text-sm" />
                                </div>
                            </div>

                            <!-- Morphology section with reference images -->
                            <div class="rounded-lg border p-3 space-y-3">
                                <Label class="text-xs font-semibold">Morphology (Strict Criteria)</Label>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- Normal -->
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <Label class="text-xs w-20 shrink-0">Normal <span class="text-muted-foreground">(norm: &gt;14%)</span></Label>
                                            <Input v-model="form.morphology_normal" type="number" step="0.1" class="h-7 text-xs w-24" />
                                            <span class="text-xs">%</span>
                                        </div>
                                        <img
                                            src="/images/sperm-forms/normal_form.png"
                                            alt="Normal Form"
                                            class="h-20 w-full rounded border object-contain bg-white p-1"
                                            onerror="this.style.display='none'"
                                        />
                                        <p class="text-center text-xs text-muted-foreground">Normal Form</p>
                                    </div>
                                    <!-- Abnormal -->
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <Label class="text-xs w-20 shrink-0">Abnormal</Label>
                                            <Input v-model="form.morphology_abnormal" type="number" step="0.1" class="h-7 text-xs w-24" />
                                            <span class="text-xs">%</span>
                                        </div>
                                        <img
                                            src="/images/sperm-forms/abnormal_form.png"
                                            alt="Abnormal Form"
                                            class="h-20 w-full rounded border object-contain bg-white p-1"
                                            onerror="this.style.display='none'"
                                        />
                                        <p class="text-center text-xs text-muted-foreground">Abnormal Form</p>
                                    </div>
                                </div>
                                <!-- Defects -->
                                <div class="grid grid-cols-3 gap-2 pt-1">
                                    <div class="flex items-center gap-1.5">
                                        <Label class="w-20 shrink-0 text-xs">Head Defect</Label>
                                        <Input v-model="form.head_defect" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs">%</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <Label class="w-20 shrink-0 text-xs">Neck Defect</Label>
                                        <Input v-model="form.neck_defect" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs">%</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <Label class="w-20 shrink-0 text-xs">Tail Defect</Label>
                                        <Input v-model="form.tail_defect" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs">%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Times -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <Label class="text-xs">Ejaculation Time</Label>
                                    <Input v-model="form.ejaculation_time" placeholder="e.g. 08:00" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Examination Time</Label>
                                    <Input v-model="form.examination_time" placeholder="e.g. 08:15" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Receive Time</Label>
                                    <Input v-model="form.receive_time" placeholder="e.g. 08:05" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Finish Time</Label>
                                    <Input v-model="form.finish_time" placeholder="e.g. 09:00" class="h-8 text-sm" />
                                </div>
                            </div>

                            <!-- Remark -->
                            <div class="space-y-1">
                                <Label class="text-xs">Remark</Label>
                                <Textarea v-model="form.remark" placeholder="Additional remarks…" class="min-h-[60px] text-sm" />
                            </div>

                            <!-- Sign-off -->
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="rounded-lg border p-3 space-y-2">
                                    <Label class="text-xs font-semibold">Reported By</Label>
                                    <Input v-model="form.reported_by" placeholder="Name" class="h-8 text-sm" />
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="space-y-1">
                                            <Label class="text-xs text-muted-foreground">Date</Label>
                                            <Input v-model="form.reported_date" type="date" class="h-7 text-xs" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs text-muted-foreground">Time</Label>
                                            <Input v-model="form.reported_time" placeholder="HH:MM" class="h-7 text-xs" />
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-lg border p-3 space-y-2">
                                    <Label class="text-xs font-semibold">Approved By</Label>
                                    <Input v-model="form.approved_by" placeholder="Name" class="h-8 text-sm" />
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="space-y-1">
                                            <Label class="text-xs text-muted-foreground">Date</Label>
                                            <Input v-model="form.approved_date" type="date" class="h-7 text-xs" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs text-muted-foreground">Time</Label>
                                            <Input v-model="form.approved_time" placeholder="HH:MM" class="h-7 text-xs" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Footer ─────────────────────────────────────── -->
                        <div class="sticky bottom-0 flex items-center justify-between rounded-b-2xl border-t bg-background px-6 py-4">
                            <Button variant="outline" @click="isOpen = false">Cancel</Button>
                            <Button :disabled="saving" class="gap-2 bg-blue-600 hover:bg-blue-700" @click="save">
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
