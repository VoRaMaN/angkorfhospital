<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { router } from '@inertiajs/vue3';
import { CheckCircle2, FlaskConical, Loader2, X } from 'lucide-vue-next';
import { reactive, ref, watch, computed } from 'vue';

// ─── Types ────────────────────────────────────────────────────────────────────
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

// ─── Form state ───────────────────────────────────────────────────────────────
const buildEmptyForm = (): FetReportData => ({
    medical_order_id: props.orderId,
    female_patient_id: null,
    female_patient_name: null,
    female_hn: null,
    female_dob: null,
    male_patient_id: null,
    male_patient_name: null,
    male_hn: null,
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

                        <!-- ── Patient Header ─────────────────────────────── -->
                        <div class="rounded-xl border bg-muted/30 p-4">
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-2">
                                <!-- Female -->
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Female (Wife)</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="space-y-1">
                                            <Label class="text-xs">Name</Label>
                                            <Input v-model="form.female_patient_name" placeholder="Female name" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">H.N.</Label>
                                            <Input v-model="form.female_hn" placeholder="HN" class="h-8 text-sm" />
                                        </div>
                                        <div class="col-span-2 space-y-1">
                                            <Label class="text-xs">DOB</Label>
                                            <Input v-model="form.female_dob" placeholder="DD/MM/YYYY" class="h-8 text-sm" />
                                        </div>
                                    </div>
                                </div>
                                <!-- Male -->
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Male (Husband)</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="space-y-1">
                                            <Label class="text-xs">Name</Label>
                                            <Input v-model="form.male_patient_name" placeholder="Male name" class="h-8 text-sm" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">H.N.</Label>
                                            <Input v-model="form.male_hn" placeholder="HN" class="h-8 text-sm" />
                                        </div>
                                        <div class="col-span-2 space-y-1">
                                            <Label class="text-xs">DOB</Label>
                                            <Input v-model="form.male_dob" placeholder="DD/MM/YYYY" class="h-8 text-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Procedure row -->
                            <div class="mt-3 grid grid-cols-3 gap-2">
                                <div class="space-y-1">
                                    <Label class="text-xs">Procedure</Label>
                                    <Input v-model="form.procedure" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Date of FET</Label>
                                    <Input v-model="form.fet_date" placeholder="DD/MM/YYYY" class="h-8 text-sm" />
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Doctor</Label>
                                    <Input v-model="form.doctor" placeholder="Doctor name" class="h-8 text-sm" />
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
