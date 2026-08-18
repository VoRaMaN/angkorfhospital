<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { todayInPhnomPenh } from '@/lib/utils';
import { router, usePage } from '@inertiajs/vue3';
import { CheckCircle2, FlaskConical, Loader2, Printer, X } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

// ─── Props & Emits ────────────────────────────────────────────────────────────
interface HormoneReportData {
    id?: number;
    medical_order_id: number;
    patient_id?: string | null;
    specimen?: string | null;
    collection_date?: string | null;
    collection_time?: string | null;
    received_date?: string | null;
    received_time?: string | null;
    lh?: number | null;
    fsh?: number | null;
    prolactin?: number | null;
    estradiol?: number | null;
    progesterone?: number | null;
    testosterone?: number | null;
    tsh?: number | null;
    t3?: number | null;
    t4?: number | null;
    amh?: number | null;
    beta_hcg?: number | null;
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
    existingReport: HormoneReportData | null;
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
const buildEmptyForm = (): HormoneReportData => ({
    medical_order_id: props.orderId,
    patient_id: props.patientId,
    specimen: 'Serum',
    collection_date: todayInPhnomPenh(),
    collection_time: null,
    received_date: todayInPhnomPenh(),
    received_time: null,
    lh: null,
    fsh: null,
    prolactin: null,
    estradiol: null,
    progesterone: null,
    testosterone: null,
    tsh: null,
    t3: null,
    t4: null,
    amh: null,
    beta_hcg: null,
    remark: null,
    reported_by: null,
    reported_date: todayInPhnomPenh(),
    reported_time: null,
    approved_by: null,
    approved_date: todayInPhnomPenh(),
    approved_time: null,
});

const form = reactive<HormoneReportData>(buildEmptyForm());

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
const openPrintTab = (id: number) => window.open(`/hormone-reports/${id}/pdf`, '_blank');
const savedReportId = ref<number | null>(null);

// ─── Save ─────────────────────────────────────────────────────────────────────
const saving = ref(false);

const save = () => {
    saving.value = true;
    const isEdit = !!props.existingReport?.id;
    const url = isEdit ? `/hormone-reports/${props.existingReport!.id}` : '/hormone-reports';
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

// ─── Hormone tests list ───────────────────────────────────────────────────────
const hormoneTests = [
    { label: 'LH',           field: 'lh',           unit: 'mIU/mL', step: '0.01' },
    { label: 'FSH',          field: 'fsh',          unit: 'mIU/mL', step: '0.01' },
    { label: 'Prolactin',    field: 'prolactin',    unit: 'ng/mL',  step: '0.01' },
    { label: 'Estradiol',    field: 'estradiol',    unit: 'pg/mL',  step: '0.01' },
    { label: 'Progesterone', field: 'progesterone', unit: 'ng/mL',  step: '0.01' },
    { label: 'Testosterone', field: 'testosterone', unit: 'ng/mL',  step: '0.001' },
    { label: 'TSH',          field: 'tsh',          unit: 'mIU/L',  step: '0.001' },
    { label: 'T3',           field: 't3',           unit: 'ng/mL',  step: '0.01' },
    { label: 'T4',           field: 't4',           unit: 'µg/dL',  step: '0.01' },
    { label: 'AMH',          field: 'amh',          unit: 'ng/mL',  step: '0.01' },
    { label: 'Beta-hCG',     field: 'beta_hcg',     unit: 'mIU/mL', step: '0.01' },
] as const;
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
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                <FlaskConical class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold">Hormone Report</h2>
                                <p class="text-xs text-muted-foreground">Fertility Hormone Panel</p>
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

                        <!-- ── Collection & Specimen ──────────────────────── -->
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="space-y-1">
                                <Label class="text-xs">Specimen</Label>
                                <Input v-model="form.specimen" placeholder="Serum" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Collection Date</Label>
                                <Input v-model="form.collection_date" type="date" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Collection Time</Label>
                                <Input v-model="form.collection_time" placeholder="HH:MM" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1 sm:col-start-2">
                                <Label class="text-xs">Received Date</Label>
                                <Input v-model="form.received_date" type="date" class="h-8 text-sm" />
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs">Received Time</Label>
                                <Input v-model="form.received_time" placeholder="HH:MM" class="h-8 text-sm" />
                            </div>
                        </div>

                        <!-- ── Hormone Results Table ───────────────────────── -->
                        <div class="rounded-lg border">
                            <div class="grid grid-cols-[1fr_80px_100px] items-center gap-x-2 border-b bg-muted/40 px-3 py-2 text-xs font-semibold text-muted-foreground">
                                <span>Test (Method: ELFA)</span>
                                <span class="text-center">Value</span>
                                <span class="text-center">Unit</span>
                            </div>
                            <div class="divide-y">
                                <div
                                    v-for="test in hormoneTests"
                                    :key="test.field"
                                    class="grid grid-cols-[1fr_80px_100px] items-center gap-x-2 px-3 py-1.5"
                                >
                                    <Label class="text-sm">{{ test.label }}</Label>
                                    <Input
                                        v-model="(form as any)[test.field]"
                                        type="number"
                                        :step="test.step"
                                        placeholder="—"
                                        class="h-7 text-xs text-center"
                                    />
                                    <span class="text-center text-xs text-muted-foreground">{{ test.unit }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- ── Remark ──────────────────────────────────────── -->
                        <div class="space-y-1">
                            <Label class="text-xs">Remark</Label>
                            <Textarea v-model="form.remark" placeholder="Additional remarks…" class="min-h-[50px] text-sm" />
                        </div>

                        <!-- ── Sign-off ────────────────────────────────────── -->
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
