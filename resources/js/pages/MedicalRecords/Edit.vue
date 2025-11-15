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
import { index } from '@/routes/medical-records';

interface Props {
    record: {
        id: number;
        appointment_id: number;
        diagnosis: string;
        treatment: string;
        notes: string;
        visit_date: string;
    };
    appointments: {
        id: number;
        patient_name: string;
        doctor_name: string;
        date: string;
        time: string;
    }[];
}

const props = defineProps<Props>();

const form = useForm({
    appointment_id: props.record.appointment_id.toString(),
    diagnosis: props.record.diagnosis,
    treatment: props.record.treatment,
    notes: props.record.notes,
    date_of_service: props.record.visit_date.split('T')[0], // Format for date input
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Records',
        href: '/medical-records',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const submit = () => {
    form.put(`/medical-records/${props.record.id}`, {
        onSuccess: () => {
            // Success handled by Inertia
        },
    });
};
</script>

<template>
    <Head title="Edit Medical Record" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="index().url">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Medical Record</h1>
                    <p class="text-muted-foreground">Update medical record information</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="submit" class="space-y-6 rounded-lg border bg-card p-6">
                    <div class="space-y-2">
                        <Label for="appointment_id">Appointment</Label>
                        <Select v-model="form.appointment_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Select appointment" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="appointment in props.appointments" :key="appointment.id" :value="appointment.id.toString()">
                                    {{ appointment.patient_name }} - {{ appointment.doctor_name }} ({{ appointment.date }} {{ appointment.time }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.appointment_id" class="text-sm text-destructive">
                            {{ form.errors.appointment_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="date_of_service">Date of Service</Label>
                        <Input
                            id="date_of_service"
                            v-model="form.date_of_service"
                            type="date"
                            required
                        />
                        <div v-if="form.errors.date_of_service" class="text-sm text-destructive">
                            {{ form.errors.date_of_service }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="diagnosis">Diagnosis</Label>
                        <Textarea
                            id="diagnosis"
                            v-model="form.diagnosis"
                            placeholder="Enter diagnosis..."
                            rows="3"
                        />
                        <div v-if="form.errors.diagnosis" class="text-sm text-destructive">
                            {{ form.errors.diagnosis }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="treatment">Treatment</Label>
                        <Textarea
                            id="treatment"
                            v-model="form.treatment"
                            placeholder="Enter treatment plan..."
                            rows="3"
                        />
                        <div v-if="form.errors.treatment" class="text-sm text-destructive">
                            {{ form.errors.treatment }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="notes">Notes</Label>
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            placeholder="Additional notes..."
                            rows="3"
                        />
                        <div v-if="form.errors.notes" class="text-sm text-destructive">
                            {{ form.errors.notes }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="size-4" />
                            {{ form.processing ? 'Updating...' : 'Update Record' }}
                        </Button>
                        <Button type="button" variant="outline" as-child>
                            <a :href="index().url">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
