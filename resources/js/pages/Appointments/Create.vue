<script setup lang="ts">
import DateInput from '@/components/DateInput.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Props {
    patients: Array<{ id: number; name: string; surname?: string; id_card_or_passport?: string; date_of_birth_day?: number; date_of_birth_month?: number; date_of_birth_year?: number }>;
    staff: Array<{
        id: number;
        user: { name: string };
        role: { name: string };
    }>;
    selectedPatient?: { id: number; name: string; surname?: string; date_of_birth_day?: number; date_of_birth_month?: number; date_of_birth_year?: number } | null;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appointments',
        href: '/appointments',
    },
    {
        title: 'Create',
        href: '#',
    },
];

// Get current time in Phnom Penh timezone and format as DD/MM/YYYY
const getPhnomPenhDate = () => {
    const now = new Date();
    const phnomPenhTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Phnom_Penh' }));
    const day = String(phnomPenhTime.getDate()).padStart(2, '0');
    const month = String(phnomPenhTime.getMonth() + 1).padStart(2, '0');
    const year = phnomPenhTime.getFullYear();
    return `${day}/${month}/${year}`;
};

const getPhnomPenhTimeOnly = () => {
    const now = new Date();
    const phnomPenhTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Phnom_Penh' }));
    const hours = String(phnomPenhTime.getHours()).padStart(2, '0');
    const minutes = String(phnomPenhTime.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
};


// Date dropdowns for day, month, year
const now = new Date();
const currentYear = now.getFullYear();
const years = Array.from({ length: 11 }, (_, i) => currentYear - 5 + i); // 5 years back and 5 years forward
const months = [
    { value: 1, label: 'Jan' },
    { value: 2, label: 'Feb' },
    { value: 3, label: 'Mar' },
    { value: 4, label: 'Apr' },
    { value: 5, label: 'May' },
    { value: 6, label: 'Jun' },
    { value: 7, label: 'Jul' },
    { value: 8, label: 'Aug' },
    { value: 9, label: 'Sep' },
    { value: 10, label: 'Oct' },
    { value: 11, label: 'Nov' },
    { value: 12, label: 'Dec' },
];
const days = Array.from({ length: 31 }, (_, i) => i + 1);

const selectedDay = ref(now.getDate());
const selectedMonth = ref(now.getMonth() + 1);
const selectedYear = ref(currentYear);
const appointmentTime = ref(getPhnomPenhTimeOnly());

const appointmentDate = computed(() => {
    // Pad day and month to 2 digits
    const day = String(selectedDay.value).padStart(2, '0');
    const month = String(selectedMonth.value).padStart(2, '0');
    const year = selectedYear.value;
    return `${day}/${month}/${year}`;
});

// Computed property to combine date and time into ISO format for backend
const combinedDateTime = computed(() => {
    if (!appointmentDate.value || !appointmentTime.value) return '';
    const [day, month, year] = appointmentDate.value.split('/');
    const [hours, minutes] = appointmentTime.value.split(':');
    const date = new Date(`${year}-${month}-${day}T${hours}:${minutes}:00+07:00`);
    return date.toISOString().slice(0, 16);
});

const form = useForm({
    patient_id: props.selectedPatient?.id?.toString() || '',
    staff_id: '',
    appointment_date_time: '',
    duration_minutes: '30',
    appointment_type: 'consultation',
    reason_for_visit: '',
    notes: '',
    is_hormone_test: false,
    is_tvs: false,
    opu_time: '',
    et_fet_time: '',
    is_beta_hcg: false,
});
</script>

<template>
    <Head title="Create Appointment" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/appointments">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Create Appointment</h1>
                    <p class="text-muted-foreground">
                        Schedule a new appointment
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <!-- Patient Information Display -->
                <div v-if="props.selectedPatient" class="mb-6 rounded-lg border bg-blue-50 p-4">
                    <h3 class="mb-2 font-semibold text-blue-900">Selected Patient</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex gap-2">
                            <span class="font-medium">Name:</span>
                            <span>{{ props.selectedPatient.name }} {{ props.selectedPatient.surname || '' }}</span>
                        </div>
                        <div v-if="props.selectedPatient.date_of_birth_day" class="flex gap-2">
                            <span class="font-medium">DOB:</span>
                            <span>{{ props.selectedPatient.date_of_birth_day }}/{{ props.selectedPatient.date_of_birth_month }}/{{ props.selectedPatient.date_of_birth_year }}</span>
                        </div>
                    </div>
                </div>

                <form
                    @submit.prevent="() => {
                        form.appointment_date_time = combinedDateTime;
                        form.post('/appointments');
                    }"
                    class="space-y-6"
                >
                    <div class="space-y-2">
                        <Label for="patient_id">Patient</Label>
                        <SearchableSelect
                            v-model="form.patient_id"
                            :options="
                                props.patients.map((p) => ({
                                    value: p.id.toString(),
                                    label: `${p?.name || 'Unknown'} ${p?.surname || ''} ${p?.id_card_or_passport ? '(ID: ' + p.id_card_or_passport + ')' : ''}`.trim(),
                                }))
                            "
                            placeholder="Select a patient"
                            search-placeholder="Search by name or ID..."
                            empty-text="No patients found."
                            :disabled="!!props.selectedPatient"
                        />
                        <div
                            v-if="form.errors.patient_id"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.patient_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="staff_id">Doctor</Label>
                        <SearchableSelect
                            v-model="form.staff_id"
                            :options="
                                props.staff.map((s) => ({
                                    value: s.id.toString(),
                                    label: s.user?.name || 'Unknown Staff',
                                }))
                            "
                            placeholder="Select doctor"
                            search-placeholder="Search doctor..."
                            empty-text="No doctors found."
                        />
                        <div
                            v-if="form.errors.staff_id"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.staff_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label>Appointment Date & Time</Label>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex gap-2 items-center">
                                <Select v-model="selectedDay">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Day" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="d in days" :key="d" :value="d">{{ d }}</SelectItem>
                                    </SelectContent>
                                </Select>
                                <Select v-model="selectedMonth">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Month" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</SelectItem>
                                    </SelectContent>
                                </Select>
                                <Select v-model="selectedYear">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Year" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="y in years" :key="y" :value="y">{{ y }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <Input
                                    v-model="appointmentTime"
                                    type="time"
                                />
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Phnom Penh Time (GMT+7)
                                </p>
                            </div>
                        </div>
                        <div
                            v-if="form.errors.appointment_date_time"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.appointment_date_time }}
                        </div>
                    </div>

                    <div class="space-y-4 rounded-lg border p-4">
                        <h3 class="font-medium">Procedure Details</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_hormone_test" v-model="form.is_hormone_test" />
                                <Label for="is_hormone_test"
                                    >Hormone Test</Label
                                >
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_tvs" v-model="form.is_tvs" />
                                <Label for="is_tvs">TVS</Label>
                            </div>

                            <div class="flex items-center gap-2">
                                <Label for="opu_time" class="whitespace-nowrap"
                                    >OPU Time</Label
                                >
                                <Input
                                    id="opu_time"
                                    v-model="form.opu_time"
                                    type="time"
                                    class="w-full"
                                />
                            </div>

                            <div class="flex items-center gap-2">
                                <Label
                                    for="et_fet_time"
                                    class="whitespace-nowrap"
                                    >ET/FET Time</Label
                                >
                                <Input
                                    id="et_fet_time"
                                    v-model="form.et_fet_time"
                                    type="time"
                                    class="w-full"
                                />
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_beta_hcg" v-model="form.is_beta_hcg" />
                                <Label for="is_beta_hcg">Beta HCG</Label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="reason_for_visit">Other</Label>
                        <Textarea
                            id="reason_for_visit"
                            v-model="form.reason_for_visit"
                            placeholder="Describe the reason for this appointment..."
                            rows="3"
                        />
                        <div
                            v-if="form.errors.reason_for_visit"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.reason_for_visit }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="notes">Additional Notes</Label>
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            placeholder="Any additional notes or special instructions..."
                            rows="2"
                        />
                        <div
                            v-if="form.errors.notes"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.notes }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Create Appointment
                        </Button>
                        <Button variant="outline" as-child>
                            <a href="/appointments">Cancel</a>
                        </Button>
                    </div>

                    <div v-if="props.selectedPatient" class="text-sm text-muted-foreground border-t pt-4">
                        <p>Note: After creating the appointment, you can print it from the appointment details page.</p>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
