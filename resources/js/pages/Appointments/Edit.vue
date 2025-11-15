<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FormControl, FormItem, FormLabel, FormMessage, FormField } from '@/components/ui/form';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    appointment: {
        id: number;
        patient_id: number;
        staff_id: number;
        appointment_date_time: string;
        status: string;
    };
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
        title: 'Edit',
        href: '#',
    },
];

const form = useForm({
    patient_id: props.appointment.patient_id.toString(),
    staff_id: props.appointment.staff_id.toString(),
    appointment_date_time: props.appointment.appointment_date_time,
    status: props.appointment.status,
});
</script>

<template>
    <Head title="Edit Appointment" />

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
                    <h1 class="text-2xl font-bold">Edit Appointment</h1>
                    <p class="text-muted-foreground">Update appointment information</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form :action="`/appointments/${props.appointment.id}`" method="POST" class="space-y-6">
                    <input type="hidden" name="_method" value="PUT" />

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Patient</label>
                        <Select v-model="form.patient_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Select a patient" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="patient in props.patients" :key="patient.id" :value="patient.id.toString()">
                                    {{ patient.user.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Staff</label>
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
                    </div>

                    <FormField v-slot="{ componentField }" name="appointment_date_time">
                        <FormItem>
                            <FormLabel>Appointment Date & Time</FormLabel>
                            <FormControl>
                                <Input v-bind="componentField" type="datetime-local" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Status</label>
                        <Select v-model="form.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Select status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="scheduled">Scheduled</SelectItem>
                                <SelectItem value="confirmed">Confirmed</SelectItem>
                                <SelectItem value="completed">Completed</SelectItem>
                                <SelectItem value="cancelled">Cancelled</SelectItem>
                                <SelectItem value="no-show">No Show</SelectItem>
                            </SelectContent>
                        </Select>
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
        </div>
    </AppLayout>
</template>