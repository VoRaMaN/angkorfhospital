<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';

interface Props {
    appointment: {
        id: number;
        patient: { user?: { name: string; email?: string } | null; first_name: string; last_name: string; email?: string };
        staff: { user: { name: string }; role: { name: string } };
        appointment_date_time: string;
        status: string;
        reason_for_visit?: string;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appointments',
        href: '/appointments',
    },
    {
        title: 'Details',
        href: '#',
    },
];
</script>

<template>
    <Head title="Appointment Details" />

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
                    <h1 class="text-2xl font-bold">Appointment Details</h1>
                    <p class="text-muted-foreground">View appointment information</p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="`/appointments/${props.appointment.id}/edit`">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Patient</dt>
                            <dd class="text-sm">{{ props.appointment.patient.user?.name || `${props.appointment.patient.first_name} ${props.appointment.patient.last_name}` }}</dd>
                        </div>

                        <div class="space-y-2" v-if="props.appointment.patient.user?.email || props.appointment.patient.email">
                            <dt class="text-sm font-medium text-muted-foreground">Patient Email</dt>
                            <dd class="text-sm">{{ props.appointment.patient.user?.email || props.appointment.patient.email }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Staff</dt>
                            <dd class="text-sm">{{ props.appointment.staff.user.name }} ({{ props.appointment.staff.role.name }})</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Appointment Date & Time</dt>
                            <dd class="text-sm">{{ new Date(props.appointment.appointment_date_time).toLocaleString() }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd class="text-sm">
                                <Badge :variant="props.appointment.status === 'completed' ? 'default' : 'secondary'">
                                    {{ props.appointment.status.charAt(0).toUpperCase() + props.appointment.status.slice(1) }}
                                </Badge>
                            </dd>
                        </div>

                        <div class="space-y-2" v-if="props.appointment.reason_for_visit">
                            <dt class="text-sm font-medium text-muted-foreground">Reason for Visit</dt>
                            <dd class="text-sm">{{ props.appointment.reason_for_visit }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Created</dt>
                            <dd class="text-sm">{{ new Date(props.appointment.created_at).toLocaleString() }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>