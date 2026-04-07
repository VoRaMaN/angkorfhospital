<script setup lang="ts">
import DateInput from '@/components/DateInput.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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

const appointmentDate = ref(getPhnomPenhDate());
const appointmentTime = ref(getPhnomPenhTimeOnly());

// Computed property to combine date and time into ISO format for backend
const combinedDateTime = computed(() => {
    if (!appointmentDate.value || !appointmentTime.value) return '';

    // Parse DD/MM/YYYY
    const [day, month, year] = appointmentDate.value.split('/');
    const [hours, minutes] = appointmentTime.value.split(':');

    // Create date in Phnom Penh timezone
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
                                    label: `${s.user?.name || 'Unknown Staff'} (${s.role?.name || 'Staff'})`,
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
                            <div>
                                <DateInput
                                    v-model="appointmentDate"
                                />
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Format: DD/MM/YYYY
                                </p>
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

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="duration_minutes"
                                >Duration (minutes)</Label
                            >
                            <Input
                                id="duration_minutes"
                                v-model="form.duration_minutes"
                                type="number"
                                min="15"
                                max="480"
                                step="15"
                            />
                            <div
                                v-if="form.errors.duration_minutes"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.duration_minutes }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="appointment_type"
                                >Appointment Type</Label
                            >
                            <Select v-model="form.appointment_type">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="consultation"
                                        >Consultation</SelectItem
                                    >
                                    <SelectItem value="emergency"
                                        >Emergency</SelectItem
                                    >
                                    <SelectItem value="follow_up"
                                        >Follow-up</SelectItem
                                    >
                                    <SelectItem value="procedure"
                                        >Procedure</SelectItem
                                    >
                                    <SelectItem value="checkup"
                                        >Check-up</SelectItem
                                    >
                                    <SelectItem value="telemedicine"
                                        >Telemedicine</SelectItem
                                    >
                                    <SelectItem value="screening"
                                        >Screening</SelectItem
                                    >
                                    <SelectItem value="therapy"
                                        >Therapy</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                            <div
                                v-if="form.errors.appointment_type"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.appointment_type }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-lg border p-4">
                        <h3 class="font-medium">Procedure Details</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="is_hormone_test"
                                    :checked="form.is_hormone_test"
                                    @update:checked="
                                        form.is_hormone_test = $event
                                    "
                                />
                                <Label for="is_hormone_test"
                                    >Hormone Test</Label
                                >
                            </div>

                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="is_tvs"
                                    :checked="form.is_tvs"
                                    @update:checked="form.is_tvs = $event"
                                />
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
                                <Checkbox
                                    id="is_beta_hcg"
                                    :checked="form.is_beta_hcg"
                                    @update:checked="form.is_beta_hcg = $event"
                                />
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
