<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { router, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Droplet, Loader2, Printer, X } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';
import { cbcFlag, cbcTests, diffTests } from '@/lib/cbcReference';

// ─── Props & Emits ────────────────────────────────────────────────────────────
interface CbcReportData {
    id?: number;
    medical_order_id: number;
    patient_id?: string | null;
    lab_id?: string | null;
    requested_by?: string | null;
    requested_date?: string | null;
    analysis_date?: string | null;
    wbc?: number | null;
    rbc?: number | null;
    hemoglobin?: number | null;
    hematocrit?: number | null;
    mcv?: number | null;
    mch?: number | null;
    mchc?: number | null;
    platelets?: number | null;
    rdw?: number | null;
    neutrophils?: number | null;
    lymphocytes?: number | null;
    monocytes?: number | null;
    eosinophils?: number | null;
    basophils?: number | null;
    remark?: string | null;
    reported_by?: string | null;
    reported_date?: string | null;
}

const props = defineProps<{
    modelValue: boolean;
    orderId: number;
    patientId: string | null;
    patientName: string;
    patientDob: string | null;
    patientHn: string | null;
    doctorName: string | null;
    existingReport: CbcReportData | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', v: boolean): void;
    (e: 'saved'): void;
}>();

const isOpen = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

// ─── Patient age / sex display ────────────────────────────────────────────────
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

const patientSexLabel = computed(() => {
    const name = props.patientName?.trim() ?? '';
    return /^(mrs\.?\s|ms\.?\s|miss\s)/i.test(name) ? 'FEMALE' : 'MALE';
});

// ─── Form state ───────────────────────────────────────────────────────────────
const buildEmptyForm = (): CbcReportData => ({
    medical_order_id: props.orderId,
    patient_id: props.patientId,
    lab_id: null,
    requested_by: props.doctorName,
    requested_date: null,
    analysis_date: null,
    wbc: null,
    rbc: null,
    hemoglobin: null,
    hematocrit: null,
    mcv: null,
    mch: null,
    mchc: null,
    platelets: null,
    rdw: null,
    neutrophils: null,
    lymphocytes: null,
    monocytes: null,
    eosinophils: null,
    basophils: null,
    remark: null,
    reported_by: null,
    reported_date: null,
});

const form = reactive<CbcReportData>(buildEmptyForm());

watch(
    () => props.modelValue,
    (open) => {
        if (open) {
            savedReportId.value = null;
            const r = props.existingReport;
            if (r) {
                Object.assign(form, r);
            } else {
                Object.assign(form, buildEmptyForm());
            }
        }
    },
);

// ─── Print ────────────────────────────────────────────────────────────────────
const openPrintTab = (id: number) => window.open(`/cbc-reports/${id}`, '_blank');
const savedReportId = ref<number | null>(null);

// ─── Save ─────────────────────────────────────────────────────────────────────
const saving = ref(false);

const save = () => {
    saving.value = true;
    const isEdit = !!props.existingReport?.id;
    const url = isEdit ? `/cbc-reports/${props.existingReport!.id}` : '/cbc-reports';
    const method = isEdit ? 'put' : 'post';

    router.visit(url, {
        method,
        data: { ...form },
        preserveScroll: true,
        preserveState: true,
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
                <div class="relative w-full max-w-3xl rounded-2xl bg-background shadow-2xl">

                    <!-- Header -->
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900">
                                <Droplet class="h-5 w-5 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold">CBC Report</h2>
                                <p class="text-xs text-muted-foreground">Complete Blood Count</p>
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
                                    <span class="text-xs text-muted-foreground">Patient ID</span>
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
                                    <span class="text-xs text-muted-foreground">Requested By</span>
                                    <p class="font-medium">{{ doctorName || '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- ── Lab / Request info ─────────────────────────── -->
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="space-y-1">
                                <Label class="text-xs">Lab ID</Label>
                                <Input v-model="form.lab_id" placeholder="S-XXX-000000-000" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Requested Date</Label>
                                <Input v-model="form.requested_date" type="date" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Analysis Date</Label>
                                <Input v-model="form.analysis_date" type="date" class="h-8 text-sm" />
                            </div>
                        </div>

                        <!-- ── Complete Blood Count ────────────────────────── -->
                        <div class="rounded-lg border">
                            <div class="grid grid-cols-[1fr_70px_36px_80px_110px] items-center gap-x-2 border-b bg-muted/40 px-3 py-2 text-xs font-semibold text-muted-foreground">
                                <span>Complete Blood Count</span>
                                <span class="text-center">Result</span>
                                <span class="text-center"></span>
                                <span class="text-center">Unit</span>
                                <span class="text-center">Ref. Range</span>
                            </div>
                            <div class="divide-y">
                                <div
                                    v-for="test in cbcTests"
                                    :key="test.field"
                                    class="grid grid-cols-[1fr_70px_36px_80px_110px] items-center gap-x-2 px-3 py-1.5"
                                >
                                    <Label class="text-sm">
                                        {{ test.label }}
                                        <span class="block text-[11px] font-normal text-muted-foreground">{{ test.khmer }}</span>
                                    </Label>
                                    <Input
                                        v-model="(form as any)[test.field]"
                                        type="number"
                                        :step="test.step"
                                        placeholder="—"
                                        class="h-7 text-xs text-center"
                                    />
                                    <span
                                        class="text-center text-xs font-bold"
                                        :class="cbcFlag((form as any)[test.field], test) ? 'text-red-600' : 'text-transparent'"
                                    >{{ cbcFlag((form as any)[test.field], test) ?? 'ok' }}</span>
                                    <span class="text-center text-xs text-muted-foreground">{{ test.unit }}</span>
                                    <span class="text-center text-xs text-muted-foreground">{{ test.low }} - {{ test.high }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- ── Differential White Cell Count ──────────────── -->
                        <div class="rounded-lg border">
                            <div class="grid grid-cols-[1fr_70px_36px_80px_110px] items-center gap-x-2 border-b bg-muted/40 px-3 py-2 text-xs font-semibold text-muted-foreground">
                                <span>Differential White Cell Count</span>
                                <span class="text-center">Result</span>
                                <span class="text-center"></span>
                                <span class="text-center">Unit</span>
                                <span class="text-center">Ref. Range</span>
                            </div>
                            <div class="divide-y">
                                <div
                                    v-for="test in diffTests"
                                    :key="test.field"
                                    class="grid grid-cols-[1fr_70px_36px_80px_110px] items-center gap-x-2 px-3 py-1.5"
                                >
                                    <Label class="text-sm">
                                        {{ test.label }}
                                        <span class="block text-[11px] font-normal text-muted-foreground">{{ test.khmer }}</span>
                                    </Label>
                                    <Input
                                        v-model="(form as any)[test.field]"
                                        type="number"
                                        :step="test.step"
                                        placeholder="—"
                                        class="h-7 text-xs text-center"
                                    />
                                    <span
                                        class="text-center text-xs font-bold"
                                        :class="cbcFlag((form as any)[test.field], test) ? 'text-red-600' : 'text-transparent'"
                                    >{{ cbcFlag((form as any)[test.field], test) ?? 'ok' }}</span>
                                    <span class="text-center text-xs text-muted-foreground">{{ test.unit }}</span>
                                    <span class="text-center text-xs text-muted-foreground">{{ test.low }} - {{ test.high }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- ── Remark ──────────────────────────────────────── -->
                        <div class="space-y-1">
                            <Label class="text-xs">Remark</Label>
                            <Textarea v-model="form.remark" placeholder="Additional remarks…" class="min-h-[50px] text-sm" />
                        </div>

                        <!-- ── Sign-off ────────────────────────────────────── -->
                        <div class="rounded-lg border p-3 space-y-2">
                            <Label class="text-xs font-semibold">Verified By</Label>
                            <div class="grid grid-cols-2 gap-2">
                                <Input v-model="form.reported_by" placeholder="Name" class="h-8 text-sm" />
                                <Input v-model="form.reported_date" type="date" class="h-8 text-sm" />
                            </div>
                        </div>

                        <!-- ── Footer ─────────────────────────────────────── -->
                        <div class="sticky bottom-0 flex items-center justify-between rounded-b-2xl border-t bg-background px-6 py-4">
                            <Button variant="outline" @click="isOpen = false">Cancel</Button>
                            <div class="flex gap-2">
                                <Button v-if="existingReport || savedReportId" variant="outline" class="gap-2" @click="() => openPrintTab((existingReport?.id ?? savedReportId)!)">
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
            </div>
        </Transition>
    </Teleport>
</template>
