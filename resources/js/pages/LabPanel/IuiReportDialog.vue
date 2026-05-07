<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { router, usePage } from '@inertiajs/vue3';
import { CheckCircle2, ClipboardList, Loader2, Printer, Search, User, X } from 'lucide-vue-next';
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

interface IuiReportData {
    id?: number;
    medical_order_id: number;
    patient_id?: string | null;
    wife_name?: string | null;
    wife_hn?: string | null;
    owner_sperm?: boolean;
    donor_sperm?: boolean;
    fresh_sperm?: boolean;
    frozen_sperm?: boolean;
    frozen_vial?: number | null;
    abstinence_days?: number | null;
    appearance?: string | null;
    liquefaction?: string | null;
    viscosity?: string | null;
    pre_volume?: number | null;
    pre_count?: number | null;
    pre_total_count?: number | null;
    pre_motile?: number | null;
    pre_total_motile?: number | null;
    pre_motility?: number | null;
    pre_motility_4_rapid?: number | null;
    pre_motility_3_medium?: number | null;
    pre_motility_2_slow?: number | null;
    pre_motility_1_static?: number | null;
    post_volume?: number | null;
    post_count?: number | null;
    post_total_count?: number | null;
    post_motile?: number | null;
    post_total_motile?: number | null;
    post_motility?: number | null;
    post_motility_4_rapid?: number | null;
    post_motility_3_medium?: number | null;
    post_motility_2_slow?: number | null;
    post_motility_1_static?: number | null;
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
    existingReport: IuiReportData | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', v: boolean): void;
    (e: 'saved'): void;
}>();

const isOpen = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

// ─── Patient age display ──────────────────────────────────────────────────────
// Female titles: Mrs, Ms, Miss — everything else treated as male
const patientIsMale = computed(() => {
    const name = props.patientName?.trim() ?? '';
    return !/^(mrs\.?\s|ms\.?\s|miss\s)/i.test(name);
});

const partnerLabel = computed(() => patientIsMale.value ? "Wife's Name" : "Husband's Name");
const partnerHnLabel = computed(() => patientIsMale.value ? "Wife's HN" : "Husband's HN");
const patientSexLabel = computed(() => patientIsMale.value ? 'MALE' : 'FEMALE');

// ─── Partner patient search ───────────────────────────────────────────────────
const selectedPartner = ref<PatientOption | null>(null);
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
    selectedPartner.value = p;
    form.wife_name = p.name;
    form.wife_hn = p.id;
    partnerQuery.value = '';
    partnerResults.value = [];
};

const clearPartner = () => {
    selectedPartner.value = null;
    form.wife_name = null;
    form.wife_hn = null;
};

const patientAge = computed(() => {
    const dob = props.patientDob;
    if (!dob) return null;
    const parts = dob.split('/');
    if (parts.length !== 3) return null;
    const d = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
    if (isNaN(d.getTime())) return null;
    const today = new Date();
    let age = today.getFullYear() - d.getFullYear();
    if (today.getMonth() < d.getMonth() || (today.getMonth() === d.getMonth() && today.getDate() < d.getDate())) {
        age--;
    }
    return age;
});

// ─── Form state ───────────────────────────────────────────────────────────────
const buildEmptyForm = (): IuiReportData => ({
    medical_order_id: props.orderId,
    patient_id: props.patientId,
    wife_name: null,
    wife_hn: null,
    owner_sperm: false,
    donor_sperm: false,
    fresh_sperm: false,
    frozen_sperm: false,
    frozen_vial: null,
    abstinence_days: null,
    appearance: null,
    liquefaction: null,
    viscosity: null,
    pre_volume: null,
    pre_count: null,
    pre_total_count: null,
    pre_motile: null,
    pre_total_motile: null,
    pre_motility: null,
    pre_motility_4_rapid: null,
    pre_motility_3_medium: null,
    pre_motility_2_slow: null,
    pre_motility_1_static: null,
    post_volume: null,
    post_count: null,
    post_total_count: null,
    post_motile: null,
    post_total_motile: null,
    post_motility: null,
    post_motility_4_rapid: null,
    post_motility_3_medium: null,
    post_motility_2_slow: null,
    post_motility_1_static: null,
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

const form = reactive<IuiReportData>(buildEmptyForm());

watch(
    () => props.modelValue,
    (open) => {
        if (open) {
            savedReportId.value = null;
            const r = props.existingReport;
            if (r) {
                Object.assign(form, r);
                selectedPartner.value = r.wife_name
                    ? { id: r.wife_hn ?? '', name: r.wife_name, dob: null, phone: null, id_card: null, gender: null }
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

// ─── Print ────────────────────────────────────────────────────────────────────
const openPrintTab = (id: number) => window.open(`/iui-reports/${id}/pdf`, '_blank');
const savedReportId = ref<number | null>(null);

// ─── Save ─────────────────────────────────────────────────────────────────────
const saving = ref(false);

const save = () => {
    saving.value = true;
    const isEdit = !!props.existingReport?.id;
    const url = isEdit ? `/iui-reports/${props.existingReport!.id}` : '/iui-reports';
    const method = isEdit ? 'put' : 'post';

    router.visit(url, {
        method,
        data: { ...form },
        preserveScroll: true,
        onSuccess: () => {
            saving.value = false;
            const flash = (usePage().props as any).flash;
            if (flash?.report_id) { savedReportId.value = flash.report_id; }
            emit('saved');
        },
        onError: () => {
            saving.value = false;
        },
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
                                <h2 class="text-lg font-semibold">IUI Report</h2>
                                <p class="text-xs text-muted-foreground">IVF LAB — Sperm Preparation for IUI</p>
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

                        <!-- ── Form Title ─────────────────────────────────── -->
                        <h3 class="text-center text-base font-bold tracking-wide">Sperm preparation for IUI</h3>

                        <!-- ── Partner Info ──────────────────────────────────── -->
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
                                <Label class="text-xs">{{ partnerHnLabel }}</Label>
                                <div v-if="selectedPartner" class="flex h-8 items-center rounded-lg border bg-muted px-3 text-sm text-muted-foreground">
                                    {{ selectedPartner.id || '—' }}
                                </div>
                                <Input v-else v-model="form.wife_hn" placeholder="HN" class="h-8 text-sm" />
                            </div>
                        </div>

                        <!-- ── Sperm Type ─────────────────────────────────── -->
                        <div class="rounded-lg border p-3 space-y-2">
                            <Label class="text-xs font-semibold">Sperm Type</Label>
                            <div class="grid grid-cols-2 gap-x-8 gap-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" v-model="form.owner_sperm" class="h-4 w-4 rounded" />
                                    <span class="text-sm">Owner sperm</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" v-model="form.donor_sperm" class="h-4 w-4 rounded" />
                                    <span class="text-sm">Donor sperm</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" v-model="form.fresh_sperm" class="h-4 w-4 rounded" />
                                    <span class="text-sm">Fresh sperm</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" v-model="form.frozen_sperm" class="h-4 w-4 rounded" />
                                        <span class="text-sm">Frozen sperm</span>
                                    </label>
                                    <Input
                                        v-if="form.frozen_sperm"
                                        v-model="form.frozen_vial"
                                        type="number"
                                        placeholder="Vials"
                                        class="h-7 w-20 text-xs"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- ── Basic Parameters ───────────────────────────── -->
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="space-y-1">
                                <Label class="text-xs">Abstinence Day</Label>
                                <Input v-model="form.abstinence_days" type="number" placeholder="Days" class="h-8 text-sm" />
                            </div>
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
                        </div>

                        <!-- ── Pre / Post Preparation Table ──────────────── -->
                        <div class="rounded-lg border overflow-hidden">
                            <div class="grid grid-cols-[1fr_90px_90px_90px] gap-x-2 bg-muted/40 px-3 py-2 text-xs font-semibold text-muted-foreground border-b">
                                <span>Parameter</span>
                                <span class="text-center">Pre</span>
                                <span class="text-center">Normal</span>
                                <span class="text-center">Post</span>
                            </div>
                            <div class="divide-y">
                                <!-- Volume -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">Volume (ml.)</Label>
                                    <Input v-model="form.pre_volume" type="number" step="0.1" class="h-7 text-xs" />
                                    <span class="text-center text-xs text-muted-foreground">&gt; 2</span>
                                    <Input v-model="form.post_volume" type="number" step="0.1" class="h-7 text-xs" />
                                </div>
                                <!-- Count -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">Count (×10⁶/ml.)</Label>
                                    <Input v-model="form.pre_count" type="number" step="0.01" class="h-7 text-xs" />
                                    <span class="text-center text-xs text-muted-foreground">&gt; 20</span>
                                    <Input v-model="form.post_count" type="number" step="0.01" class="h-7 text-xs" />
                                </div>
                                <!-- Total Count -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">Total count (×10⁶)</Label>
                                    <Input v-model="form.pre_total_count" type="number" step="0.01" class="h-7 text-xs" />
                                    <span class="text-center text-xs text-muted-foreground">&gt; 40</span>
                                    <Input v-model="form.post_total_count" type="number" step="0.01" class="h-7 text-xs" />
                                </div>
                                <!-- Motile -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">Motile (×10⁶/ml.)</Label>
                                    <Input v-model="form.pre_motile" type="number" step="0.01" class="h-7 text-xs" />
                                    <span></span>
                                    <Input v-model="form.post_motile" type="number" step="0.01" class="h-7 text-xs" />
                                </div>
                                <!-- Total Motile -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">Total motile (×10⁶)</Label>
                                    <Input v-model="form.pre_total_motile" type="number" step="0.01" class="h-7 text-xs" />
                                    <span></span>
                                    <Input v-model="form.post_total_motile" type="number" step="0.01" class="h-7 text-xs" />
                                </div>
                                <!-- Motility -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">Motility (%)</Label>
                                    <Input v-model="form.pre_motility" type="number" step="0.1" class="h-7 text-xs" />
                                    <span class="text-center text-xs text-muted-foreground">&gt; 50</span>
                                    <Input v-model="form.post_motility" type="number" step="0.1" class="h-7 text-xs" />
                                </div>
                                <!-- 4 Rapid -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">Motility rate: 4 rapid (%)</Label>
                                    <Input v-model="form.pre_motility_4_rapid" type="number" step="0.1" class="h-7 text-xs" />
                                    <span class="text-center text-xs text-muted-foreground">&gt; 25</span>
                                    <Input v-model="form.post_motility_4_rapid" type="number" step="0.1" class="h-7 text-xs" />
                                </div>
                                <!-- 3 Medium -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">3 medium (%)</Label>
                                    <Input v-model="form.pre_motility_3_medium" type="number" step="0.1" class="h-7 text-xs" />
                                    <span></span>
                                    <Input v-model="form.post_motility_3_medium" type="number" step="0.1" class="h-7 text-xs" />
                                </div>
                                <!-- 2 Slow -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">2 slow (%)</Label>
                                    <Input v-model="form.pre_motility_2_slow" type="number" step="0.1" class="h-7 text-xs" />
                                    <span></span>
                                    <Input v-model="form.post_motility_2_slow" type="number" step="0.1" class="h-7 text-xs" />
                                </div>
                                <!-- 1 Static -->
                                <div class="grid grid-cols-[1fr_90px_90px_90px] items-center gap-x-2 px-3 py-1.5">
                                    <Label class="text-xs">1 static (%)</Label>
                                    <Input v-model="form.pre_motility_1_static" type="number" step="0.1" class="h-7 text-xs" />
                                    <span></span>
                                    <Input v-model="form.post_motility_1_static" type="number" step="0.1" class="h-7 text-xs" />
                                </div>
                            </div>
                        </div>

                        <!-- ── Times ──────────────────────────────────────── -->
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="space-y-1">
                                <Label class="text-xs">Ejaculation Time</Label>
                                <Input v-model="form.ejaculation_time" placeholder="HH:MM" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Examination Time</Label>
                                <Input v-model="form.examination_time" placeholder="HH:MM" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Receive Time</Label>
                                <Input v-model="form.receive_time" placeholder="HH:MM" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Finish Time</Label>
                                <Input v-model="form.finish_time" placeholder="HH:MM" class="h-8 text-sm" />
                            </div>
                        </div>

                        <!-- ── Remark ─────────────────────────────────────── -->
                        <div class="space-y-1">
                            <Label class="text-xs">Remark</Label>
                            <Textarea v-model="form.remark" rows="2" placeholder="Remarks…" class="text-sm" />
                        </div>

                        <!-- ── Sign-off ────────────────────────────────────── -->
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <!-- Reported by -->
                            <div class="rounded-lg border p-3 space-y-2">
                                <Label class="text-xs font-semibold">Reported by</Label>
                                <Input v-model="form.reported_by" placeholder="Name" class="h-8 text-sm" />
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1">
                                        <Label class="text-xs">Date</Label>
                                        <Input v-model="form.reported_date" type="date" class="h-8 text-sm" />
                                    </div>
                                    <div class="space-y-1">
                                        <Label class="text-xs">Time</Label>
                                        <Input v-model="form.reported_time" placeholder="HH:MM" class="h-8 text-sm" />
                                    </div>
                                </div>
                            </div>
                            <!-- Approved by -->
                            <div class="rounded-lg border p-3 space-y-2">
                                <Label class="text-xs font-semibold">Approved by</Label>
                                <Input v-model="form.approved_by" placeholder="Name" class="h-8 text-sm" />
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1">
                                        <Label class="text-xs">Date</Label>
                                        <Input v-model="form.approved_date" type="date" class="h-8 text-sm" />
                                    </div>
                                    <div class="space-y-1">
                                        <Label class="text-xs">Time</Label>
                                        <Input v-model="form.approved_time" placeholder="HH:MM" class="h-8 text-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /content -->

                    <!-- ── Footer ─────────────────────────────────────────── -->
                    <div class="sticky bottom-0 flex items-center justify-between rounded-b-2xl border-t bg-background px-6 py-4">
                        <Button variant="outline" @click="isOpen = false">
                            <X class="mr-2 h-4 w-4" />
                            Cancel
                        </Button>
                        <div class="flex gap-2">
                            <Button v-if="existingReport || savedReportId" variant="outline" class="gap-2" @click="() => openPrintTab((existingReport?.id ?? savedReportId)!)">
                                <Printer class="h-4 w-4" />
                                Print
                            </Button>
                            <Button :disabled="saving" @click="save">
                                <Loader2 v-if="saving" class="mr-2 h-4 w-4 animate-spin" />
                                <CheckCircle2 v-else class="mr-2 h-4 w-4" />
                                {{ saving ? 'Saving…' : 'Save IUI Report' }}
                            </Button>
                        </div>
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>
