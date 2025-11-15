<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    patients: Array<{ id: number; user: { name: string } }>;
    staff: Array<{ id: number; user: { name: string }; role: { name: string } }>;
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
    reason_for_visit: '',
});
</script>

<template>
    <Head title="Create Appointment" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/appointments">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Create Appointment</h1>
                    <p class="text-muted-foreground">Schedule a new appointment</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="form.post('/appointments')" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="patient_id">Patient</Label>
                        <Select v-model="form.patient_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Select a patient" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="patient in props.patients" :key="patient.id" :value="patient.id.toString()">
                                    {{ patient.user?.name || 'Unknown Patient' }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.patient_id" class="text-sm text-destructive">
                            {{ form.errors.patient_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="staff_id">Staff</Label>
                        <Select v-model="form.staff_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Select staff" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="staffMember in props.staff" :key="staffMember.id" :value="staffMember.id.toString()">
                                    {{ staffMember.user?.name || 'Unknown Staff' }} ({{ staffMember.role?.name || 'Staff' }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.staff_id" class="text-sm text-destructive">
                            {{ form.errors.staff_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="appointment_date_time">Appointment Date & Time</Label>
                        <Input
                            id="appointment_date_time"
                            v-model="form.appointment_date_time"
                            type="datetime-local"
                        />
                        <div v-if="form.errors.appointment_date_time" class="text-sm text-destructive">
                            {{ form.errors.appointment_date_time }}
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
                        <div v-if="form.errors.reason_for_visit" class="text-sm text-destructive">
                            {{ form.errors.reason_for_visit }}
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