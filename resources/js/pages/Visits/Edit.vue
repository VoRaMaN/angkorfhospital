<script setup lang="ts">
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { computed } from 'vue';

interface Visit {
    id: number;
    appointment_id: number | null;
    patient_id: number;
    staff_id: number | null;
    visit_date_time: string;
    status: string;
    notes: string | null;
}

interface Patient {
    id: number;
    name: string;
}

interface Staff {
    id: number;
    user: {
        name: string;
    };
}

interface Appointment {
    id: number;
    patient: Patient;
    staff: Staff | null;
    appointment_date_time: string;
    status: string;
}

interface Props {
    visit: Visit;
    patients: Patient[];
    staff: Staff[];
    appointments: Appointment[];
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visits',
        href: '/visits',
    },
    {
        title: `Edit Visit #${props.visit.id}`,
        href: '#',
    },
];

const form = useForm<{
    appointment_id: string | null;
    patient_id: string;
    staff_id: string;
    visit_date_time: string;
    status: string;
    notes: string;
}>({
    appointment_id: props.visit.appointment_id?.toString() ?? null,
    patient_id: props.visit.patient_id.toString(),
    staff_id: props.visit.staff_id?.toString() ?? '',
    visit_date_time: new Date(props.visit.visit_date_time).toISOString().slice(0, 16),
    status: props.visit.status,
    notes: props.visit.notes ?? '',
});

const patientOptions = computed(() =>
    props.patients.map((patient) => ({
        value: patient.id.toString(),
        label: patient.name,
    }))
);

const staffOptions = computed(() => [
    { value: '', label: 'Unassigned' },
    ...props.staff.map((staffMember) => ({
        value: staffMember.id.toString(),
        label: staffMember.user.name,
    })),
]);

const appointmentOptions = computed(() => [
    { value: 'none', label: 'None' },
    ...props.appointments.map((appointment) => ({
        value: appointment.id.toString(),
        label: `#${appointment.id} - ${appointment.patient.name} - ${new Date(appointment.appointment_date_time).toLocaleDateString()}`,
    })),
]);

const patientValue = computed({
    get: () => form.patient_id,
    set: (value: string) => {
        form.patient_id = value;
    },
});

const staffValue = computed({
    get: () => form.staff_id,
    set: (value: string) => {
        form.staff_id = value;
    },
});

const submit = () => {
    const data = {
        appointment_id: form.appointment_id === 'none' || !form.appointment_id ? null : form.appointment_id,
        patient_id: form.patient_id,
        staff_id: form.staff_id === '' ? null : form.staff_id,
        visit_date_time: form.visit_date_time,
        status: form.status,
        notes: form.notes || null,
    };

    form.transform(() => data).put(`/visits/${props.visit.id}`, {
        onSuccess: () => {
            // Success handled by Inertia
        },
    });
};
</script>

<template>
    <Head title="Edit Visit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            v-if="hasPermission('edit_visits')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <Link href="/visits">
                        <ArrowLeft class="size-4" />
                        Back to Visits
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Visit</h1>
                    <p class="text-muted-foreground">
                        Update the details for this visit.
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="submit">
                    <Card>
                        <CardHeader>
                            <CardTitle>Visit Details</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <!-- Related Appointment -->
                            <div class="space-y-2">
                                <Label for="appointment_id">Related Appointment (Optional)</Label>
                                <Select v-model="form.appointment_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select an appointment" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in appointmentOptions"
                                            :key="option.value"
                                            :value="option.value"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.appointment_id" class="text-sm text-red-600">
                                    {{ form.errors.appointment_id }}
                                </p>
                            </div>

                            <!-- Patient -->
                            <div class="space-y-2">
                                <Label for="patient_id">Patient *</Label>
                                <SearchableSelect
                                    v-model="patientValue"
                                    :options="patientOptions"
                                    placeholder="Select a patient"
                                />
                                <p v-if="form.errors.patient_id" class="text-sm text-red-600">
                                    {{ form.errors.patient_id }}
                                </p>
                            </div>

                            <!-- Assigned Staff -->
                            <div class="space-y-2">
                                <Label for="staff_id">Assigned Doctor (Optional)</Label>
                                <SearchableSelect
                                    v-model="staffValue"
                                    :options="staffOptions"
                                    placeholder="Select doctor member"
                                />
                                <p v-if="form.errors.staff_id" class="text-sm text-red-600">
                                    {{ form.errors.staff_id }}
                                </p>
                            </div>

                            <!-- Visit Date & Time -->
                            <div class="space-y-2">
                                <Label for="visit_date_time">Visit Date & Time *</Label>
                                <Input
                                    id="visit_date_time"
                                    v-model="form.visit_date_time"
                                    type="datetime-local"
                                    required
                                />
                                <p v-if="form.errors.visit_date_time" class="text-sm text-red-600">
                                    {{ form.errors.visit_date_time }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div class="space-y-2">
                                <Label for="status">Status *</Label>
                                <Select v-model="form.status" required>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="pending">Pending</SelectItem>
                                        <SelectItem value="in_progress">In Progress</SelectItem>
                                        <SelectItem value="completed">Completed</SelectItem>
                                        <SelectItem value="cancelled">Cancelled</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.status" class="text-sm text-red-600">
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <!-- Notes -->
                            <div class="space-y-2">
                                <Label for="notes">Notes</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    placeholder="Additional notes about the visit..."
                                    rows="3"
                                />
                                <p v-if="form.errors.notes" class="text-sm text-red-600">
                                    {{ form.errors.notes }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" as-child>
                                    <Link href="/visits">Cancel</Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    <Save class="size-4 mr-2" />
                                    Update Visit
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </div>
        <div v-else class="flex h-full flex-1 items-center justify-center">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You do not have permission to edit visits.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
