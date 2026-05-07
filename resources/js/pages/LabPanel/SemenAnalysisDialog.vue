<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { router } from '@inertiajs/vue3';
import { ClipboardList, Loader2, X } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

// ─── Props & Emits ────────────────────────────────────────────────────────────
interface SemenAnalysisData {
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
    no_of_vial?: number | null;
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
    existingReport: SemenAnalysisData | null;
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
const patientAge = computed(() => {
    const dob = props.patientDob;
    if (!dob) return null;
    // dob format: DD/MM/YYYY
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
const buildEmptyForm = (): SemenAnalysisData => ({
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
    no_of_vial: null,
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

const form = reactive<SemenAnalysisData>(buildEmptyForm());

watch(
    () => props.modelValue,
    (open) => {
        if (open) {
            const r = props.existingReport;
            if (r) {
                Object.assign(form, r);
            } else {
                Object.assign(form, buildEmptyForm());
            }
        }
    },
);

// ─── Save ─────────────────────────────────────────────────────────────────────
const saving = ref(false);

const save = () => {
    saving.value = true;
    const isEdit = !!props.existingReport?.id;
    const url = isEdit
        ? `/semen-analysis-reports/${props.existingReport!.id}`
        : '/semen-analysis-reports';
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

// ─── Helpers ──────────────────────────────────────────────────────────────────
const today = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '/');
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
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900">
                                <ClipboardList class="h-5 w-5 text-teal-600 dark:text-teal-400" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold">Sa + Sperm Freezing</h2>
                                <p class="text-xs text-muted-foreground">IVF LAB — Semen Analysis Report</p>
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
                                    <p class="font-medium uppercase">MALE</p>
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

                        <!-- ── Semen Analysis & Freezing ─────────────────── -->
                        <div class="space-y-4">
                            <h3 class="text-center text-base font-bold tracking-wide">Semen Analysis and Freezing</h3>

                            <!-- Wife's name & Abstinence -->
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="space-y-1">
                                    <Label class="text-xs">Wife's Name</Label>
                                    <Input v-model="form.wife_name" placeholder="Wife's name" class="h-8 text-sm" />
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
                                    <!-- Viability -->
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Viability</Label>
                                        <span></span>
                                        <Input v-model="form.viability" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs text-muted-foreground whitespace-nowrap">&gt; 75</span>
                                    </div>
                                    <!-- Volume -->
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Volume</Label>
                                        <span class="text-xs text-muted-foreground">ml.</span>
                                        <Input v-model="form.volume" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs text-muted-foreground whitespace-nowrap">&gt; 2</span>
                                    </div>
                                    <!-- Count -->
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Count</Label>
                                        <span class="text-xs text-muted-foreground">×10⁶/ml.</span>
                                        <Input v-model="form.count_per_ml" type="number" step="0.01" class="h-7 text-xs" />
                                        <span class="text-xs text-muted-foreground whitespace-nowrap">&gt; 20</span>
                                    </div>
                                    <!-- Total Count -->
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Total Count</Label>
                                        <span class="text-xs text-muted-foreground">×10⁶</span>
                                        <Input v-model="form.total_count" type="number" step="0.01" class="h-7 text-xs" />
                                        <span class="text-xs text-muted-foreground whitespace-nowrap">&gt; 40</span>
                                    </div>
                                    <!-- Motile -->
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Motile</Label>
                                        <span class="text-xs text-muted-foreground">×10⁶/ml.</span>
                                        <Input v-model="form.motile" type="number" step="0.01" class="h-7 text-xs" />
                                        <span></span>
                                    </div>
                                    <!-- Total Motile -->
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Total Motile</Label>
                                        <span class="text-xs text-muted-foreground">×10⁶</span>
                                        <Input v-model="form.total_motile" type="number" step="0.01" class="h-7 text-xs" />
                                        <span></span>
                                    </div>
                                    <!-- Motility -->
                                    <div class="grid grid-cols-[120px_1fr_100px_auto] items-center gap-x-3 px-3 py-1.5">
                                        <Label class="text-xs">Motility</Label>
                                        <span class="text-xs text-muted-foreground">%</span>
                                        <Input v-model="form.motility" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs text-muted-foreground whitespace-nowrap">&gt; 50</span>
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

                            <!-- WBC & Morphology -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- WBC -->
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
                                        <Label class="text-xs w-20 shrink-0">Head Defect</Label>
                                        <Input v-model="form.head_defect" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs">%</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <Label class="text-xs w-20 shrink-0">Neck Defect</Label>
                                        <Input v-model="form.neck_defect" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs">%</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <Label class="text-xs w-20 shrink-0">Tail Defect</Label>
                                        <Input v-model="form.tail_defect" type="number" step="0.1" class="h-7 text-xs" />
                                        <span class="text-xs">%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- No. of Vial -->
                            <div class="flex items-center gap-3">
                                <Label class="text-xs font-semibold shrink-0">No. of Vial</Label>
                                <Input v-model="form.no_of_vial" type="number" class="h-8 w-24 text-sm" />
                                <span class="text-xs text-muted-foreground">vials</span>
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
                                <!-- Reported by -->
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
                                <!-- Approved by -->
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
                        <div class="flex justify-end gap-3 border-t pt-4">
                            <Button variant="outline" @click="isOpen = false">Cancel</Button>
                            <Button class="gap-2 bg-teal-600 hover:bg-teal-700" :disabled="saving" @click="save">
                                <Loader2 v-if="saving" class="h-4 w-4 animate-spin" />
                                {{ saving ? 'Saving…' : (existingReport ? 'Update Report' : 'Save Report') }}
                            </Button>
                        </div>

                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
