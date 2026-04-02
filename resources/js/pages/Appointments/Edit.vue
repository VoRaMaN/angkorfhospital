<script setup lang="ts">
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
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Props {
    appointment: {
        id: number;
        patient_id: number;
        staff_id: number;
        appointment_date_time: string;
        duration_minutes: number;
        appointment_type: string;
        status: string;
        reason_for_visit?: string;
        notes?: string;
        is_hormone_test?: boolean;
        is_tvs?: boolean;
        opu_time?: string;
        et_fet_time?: string;
        is_beta_hcg?: boolean;
    };
    patients: Array<{ id: number; name: string }>;
    staff: Array<{
        id: number;
        user: { name: string };
        role: { name: string };
    }>;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appointments',
        href: '/appointments',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

// Parse the appointment datetime from backend (format: YYYY-MM-DDTHH:mm)
const parseDateTime = (dateTimeStr: string) => {
    const date = new Date(dateTimeStr);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return {
        date: `${day}/${month}/${year}`,
        time: `${hours}:${minutes}`
    };
};

const { date: initialDate, time: initialTime } = parseDateTime(props.appointment.appointment_date_time);
const appointmentDate = ref(initialDate);
const appointmentTime = ref(initialTime);

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
    patient_id: props.appointment.patient_id.toString(),
    staff_id: props.appointment.staff_id.toString(),
    appointment_date_time: '',
    duration_minutes: props.appointment.duration_minutes?.toString() || '30',
    appointment_type: props.appointment.appointment_type || 'consultation',
    status: props.appointment.status,
    reason_for_visit: props.appointment.reason_for_visit || '',
    notes: props.appointment.notes || '',
    is_hormone_test: props.appointment.is_hormone_test || false,
    is_tvs: props.appointment.is_tvs || false,
    opu_time: props.appointment.opu_time || '',
    et_fet_time: props.appointment.et_fet_time || '',
    is_beta_hcg: props.appointment.is_beta_hcg || false,
});
</script>

<template>
    <Head title="Edit Appointment" />

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
                    <h1 class="text-2xl font-bold">Edit Appointment</h1>
                    <p class="text-muted-foreground">
                        Update appointment information
                    </p>
                </div>
            </div>

            <div class="max-w-2xl" v-if="hasPermission('edit_appointments')">
                <form
                    @submit.prevent="() => {
                        form.appointment_date_time = combinedDateTime;
                        form.put(`/appointments/${props.appointment.id}`);
                    }"
                    class="space-y-6"
                >
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Patient</label>
                        <SearchableSelect
                            v-model="form.patient_id"
                            :options="
                                props.patients.map((p) => ({
                                    value: p.id.toString(),
                                    label: p.name,
                                }))
                            "
                            placeholder="Select a patient"
                            search-placeholder="Search patients..."
                            empty-text="No patients found."
                        />
                        <div
                            v-if="form.errors.patient_id"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.patient_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Staff</label>
                        <SearchableSelect
                            v-model="form.staff_id"
                            :options="
                                props.staff.map((s) => ({
                                    value: s.id.toString(),
                                    label: `${s.user?.name || 'Unknown Staff'} (${s.role?.name || 'Staff'})`,
                                }))
                            "
                            placeholder="Select staff"
                            search-placeholder="Search staff..."
                            empty-text="No staff found."
                        />
                        <div
                            v-if="form.errors.staff_id"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.staff_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium"
                            >Appointment Date & Time</label
                        >
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Input
                                    v-model="appointmentDate"
                                    type="text"
                                    placeholder="DD/MM/YYYY"
                                    pattern="\d{2}/\d{2}/\d{4}"
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
                            <label class="text-sm font-medium"
                                >Duration (minutes)</label
                            >
                            <Input
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
                            <label class="text-sm font-medium"
                                >Appointment Type</label
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
                        <label class="text-sm font-medium"
                            >Reason for Visit</label
                        >
                        <Textarea
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
                        <label class="text-sm font-medium"
                            >Additional Notes</label
                        >
                        <Textarea
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

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Status</label>
                        <Select v-model="form.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Select status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="scheduled"
                                    >Scheduled</SelectItem
                                >
                                <SelectItem value="confirmed"
                                    >Confirmed</SelectItem
                                >
                                <SelectItem value="arrived">Arrived</SelectItem>
                                <SelectItem value="in_progress"
                                    >In Progress</SelectItem
                                >
                                <SelectItem value="completed"
                                    >Completed</SelectItem
                                >
                                <SelectItem value="cancelled"
                                    >Cancelled</SelectItem
                                >
                                <SelectItem value="no_show">No Show</SelectItem>
                                <SelectItem value="rescheduled"
                                    >Rescheduled</SelectItem
                                >
                            </SelectContent>
                        </Select>
                        <div
                            v-if="form.errors.status"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.status }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Update Appointment
                        </Button>
                        <Button variant="outline" as-child>
                            <a href="/appointments">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
            <div v-else class="max-w-2xl text-center">
                <p class="text-muted-foreground">
                    You do not have permission to edit appointments.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
