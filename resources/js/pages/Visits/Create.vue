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
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { computed } from 'vue';

interface Patient {
    id: number;
    user: {
        name: string;
    };
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
    patients: Patient[];
    staff: Staff[];
    appointments: Appointment[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visits',
        href: '/visits',
    },
    {
        title: 'Create Visit',
        href: '#',
    },
];

const form = useForm<{
    appointment_id: string | null;
    patient_id: string;
    staff_id: string | null;
    visit_date_time: string;
    status: string;
    notes: string;
}>({
    appointment_id: null,
    patient_id: '',
    staff_id: null,
    visit_date_time: '',
    status: 'pending',
    notes: '',
});

const patientOptions = computed(() => [
    { value: 'null', label: 'Select a patient' },
    ...props.patients.map((p) => ({
        value: p.id.toString(),
        label: p.user.name,
    })),
]);
const staffOptions = computed(() => [
    { value: 'null', label: 'Unassigned' },
    ...props.staff.map((s) => ({ value: s.id.toString(), label: s.user.name })),
]);

const patientValue = computed({
    get: () => form.patient_id || 'null',
    set: (value) => {
        form.patient_id = value === 'null' ? '' : value;
    },
});

const staffValue = computed({
    get: () => (form.staff_id ? String(form.staff_id) : 'null'),
    set: (value) => {
        form.staff_id = value === 'null' ? null : value;
    },
});

const submit = () => {
    // Transform form data before submission
    const transformedData = {
        appointment_id:
            form.appointment_id === 'none' ? null : form.appointment_id,
        patient_id: form.patient_id === '' ? null : form.patient_id,
        staff_id: form.staff_id,
        visit_date_time: form.visit_date_time,
        status: form.status,
        notes: form.notes,
    };

    form.transform(() => transformedData).post('/visits', {
        onSuccess: () => {
            // Handle success
        },
    });
};
</script>

<template>
    <Head title="Create Visit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
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
                    <h1 class="text-2xl font-bold">Create Visit</h1>
                    <p class="text-muted-foreground">
                        Create a new patient visit
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="submit">
                    <Card>
                        <CardHeader>
                            <CardTitle>Visit Information</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="space-y-2">
                                <Label for="appointment_id"
                                    >Related Appointment (Optional)</Label
                                >
                                <Select v-model="form.appointment_id">
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Select an appointment"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="none"
                                            >None</SelectItem
                                        >
                                        <SelectItem
                                            v-for="appointment in appointments"
                                            :key="appointment.id"
                                            :value="appointment.id.toString()"
                                        >
                                            #{{ appointment.id }} -
                                            {{ appointment.patient.user.name }}
                                            -
                                            {{
                                                new Date(
                                                    appointment.appointment_date_time,
                                                ).toLocaleDateString()
                                            }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="space-y-2">
                                <Label for="patient_id">Patient *</Label>
                                <SearchableSelect
                                    v-model="patientValue"
                                    :options="patientOptions"
                                    placeholder="Select a patient"
                                />
                                <div
                                    v-if="form.errors.patient_id"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.patient_id }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="staff_id"
                                    >Assigned Staff (Optional)</Label
                                >
                                <SearchableSelect
                                    v-model="staffValue"
                                    :options="staffOptions"
                                    placeholder="Select staff member (optional)"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="visit_date_time"
                                    >Visit Date & Time *</Label
                                >
                                <Input
                                    id="visit_date_time"
                                    v-model="form.visit_date_time"
                                    type="datetime-local"
                                    required
                                />
                                <div
                                    v-if="form.errors.visit_date_time"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.visit_date_time }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="status">Status *</Label>
                                <Select v-model="form.status" required>
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Select status"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="pending"
                                            >Pending</SelectItem
                                        >
                                        <SelectItem value="in_progress"
                                            >In Progress</SelectItem
                                        >
                                        <SelectItem value="completed"
                                            >Completed</SelectItem
                                        >
                                        <SelectItem value="cancelled"
                                            >Cancelled</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <div
                                    v-if="form.errors.status"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.status }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="notes">Notes</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    placeholder="Additional notes about the visit..."
                                    rows="3"
                                />
                                <div
                                    v-if="form.errors.notes"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.notes }}
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Button variant="outline" as-child>
                                    <Link href="/visits">Cancel</Link>
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    <Save class="size-4" />
                                    Create Visit
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
