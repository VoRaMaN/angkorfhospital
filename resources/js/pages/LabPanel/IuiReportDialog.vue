<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { router } from '@inertiajs/vue3';
import { CheckCircle2, ClipboardList, Loader2, X } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

// ─── Types ────────────────────────────────────────────────────────────────────
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
    const url = isEdit ? `/iui-reports/${props.existingReport!.id}` : '/iui-reports';
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

                        <!-- ── Form Title ─────────────────────────────────── -->
                        <h3 class="text-center text-base font-bold tracking-wide">Sperm preparation for IUI</h3>

                        <!-- ── Wife Info ──────────────────────────────────── -->
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="space-y-1">
                                <Label class="text-xs">Wife's Name</Label>
                                <Input v-model="form.wife_name" placeholder="Wife's name" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Wife's HN</Label>
                                <Input v-model="form.wife_hn" placeholder="HN" class="h-8 text-sm" />
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
                        <Button :disabled="saving" @click="save">
                            <Loader2 v-if="saving" class="mr-2 h-4 w-4 animate-spin" />
                            <CheckCircle2 v-else class="mr-2 h-4 w-4" />
                            {{ saving ? 'Saving…' : 'Save IUI Report' }}
                        </Button>
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>
