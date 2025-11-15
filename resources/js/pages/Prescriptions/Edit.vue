<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

interface Props {
    prescription: {
        id: number;
        patient_id: number;
        doctor_id: number;
        medication_id: number;
        dosage: string;
        frequency: string;
        duration: string;
        instructions: string;
        prescribed_date: string;
    };
    patients: {
        id: number;
        name: string;
    }[];
    doctors: {
        id: number;
        name: string;
    }[];
    medications: {
        id: number;
        name: string;
    }[];
}

const props = defineProps<Props>();

const form = useForm({
    patient_id: props.prescription.patient_id.toString(),
    doctor_id: props.prescription.doctor_id.toString(),
    medication_id: props.prescription.medication_id.toString(),
    dosage: props.prescription.dosage,
    frequency: props.prescription.frequency,
    duration: props.prescription.duration,
    instructions: props.prescription.instructions,
    prescribed_date: props.prescription.prescribed_date.split('T')[0],
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Prescriptions',
        href: '/prescriptions',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const frequencyOptions = [
    'Once daily',
    'Twice daily',
    'Three times daily',
    'Four times daily',
    'Every 4 hours',
    'Every 6 hours',
    'Every 8 hours',
    'Every 12 hours',
    'As needed',
    'Before meals',
    'After meals',
    'With meals',
];

const submit = () => {
    form.put(`/prescriptions/${props.prescription.id}`, {
        onSuccess: () => {
            // Success handled by Inertia
        },
    });
};
</script>

<template>
    <Head title="Edit Prescription" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/prescriptions">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Prescription</h1>
                    <p class="text-muted-foreground">Update prescription information</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="submit" class="space-y-6 rounded-lg border bg-card p-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="patient_id">Patient</Label>
                            <Select v-model="form.patient_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select patient" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="patient in props.patients" :key="patient.id" :value="patient.id.toString()">
                                        {{ patient.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.patient_id" class="text-sm text-destructive">
                                {{ form.errors.patient_id }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="doctor_id">Doctor</Label>
                            <Select v-model="form.doctor_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select doctor" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="doctor in props.doctors" :key="doctor.id" :value="doctor.id.toString()">
                                        {{ doctor.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.doctor_id" class="text-sm text-destructive">
                                {{ form.errors.doctor_id }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="medication_id">Medication</Label>
                        <Select v-model="form.medication_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Select medication" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="medication in props.medications" :key="medication.id" :value="medication.id.toString()">
                                    {{ medication.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.medication_id" class="text-sm text-destructive">
                            {{ form.errors.medication_id }}
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="dosage">Dosage</Label>
                            <Input
                                id="dosage"
                                v-model="form.dosage"
                                placeholder="e.g., 500mg, 10ml"
                                required
                            />
                            <div v-if="form.errors.dosage" class="text-sm text-destructive">
                                {{ form.errors.dosage }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="frequency">Frequency</Label>
                            <Select v-model="form.frequency">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select frequency" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="freq in frequencyOptions" :key="freq" :value="freq">
                                        {{ freq }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.frequency" class="text-sm text-destructive">
                                {{ form.errors.frequency }}
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="duration">Duration</Label>
                            <Input
                                id="duration"
                                v-model="form.duration"
                                placeholder="e.g., 7 days, 2 weeks"
                                required
                            />
                            <div v-if="form.errors.duration" class="text-sm text-destructive">
                                {{ form.errors.duration }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="prescribed_date">Prescribed Date</Label>
                            <Input
                                id="prescribed_date"
                                v-model="form.prescribed_date"
                                type="date"
                                required
                            />
                            <div v-if="form.errors.prescribed_date" class="text-sm text-destructive">
                                {{ form.errors.prescribed_date }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="instructions">Instructions</Label>
                        <Textarea
                            id="instructions"
                            v-model="form.instructions"
                            placeholder="Special instructions for the patient..."
                            rows="3"
                        />
                        <div v-if="form.errors.instructions" class="text-sm text-destructive">
                            {{ form.errors.instructions }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="size-4" />
                            {{ form.processing ? 'Updating...' : 'Update Prescription' }}
                        </Button>
                        <Button type="button" variant="outline" as-child>
                            <a href="/prescriptions">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
