<script setup lang="ts">
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

interface Props {
    patients: Array<{ id: number; name: string }>;
    staff: Array<{
        id: number;
        user: { name: string };
        role: { name: string };
    }>;
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

const form = useForm({
    patient_id: '',
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
                <form
                    @submit.prevent="form.post('/appointments')"
                    class="space-y-6"
                >
                    <div class="space-y-2">
                        <Label for="patient_id">Patient</Label>
                        <SearchableSelect
                            v-model="form.patient_id"
                            :options="
                                props.patients.map((p) => ({
                                    value: p.id.toString(),
                                    label: p?.name || 'Unknown Patient',
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
                        <Label for="staff_id">Staff</Label>
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
                        <Label for="appointment_date_time"
                            >Appointment Date & Time</Label
                        >
                        <Input
                            id="appointment_date_time"
                            v-model="form.appointment_date_time"
                            type="datetime-local"
                        />
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
                        <Label for="reason_for_visit">Reason for Visit</Label>
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
                </form>
            </div>
        </div>
    </AppLayout>
</template>
