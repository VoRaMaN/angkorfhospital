<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { show } from '@/routes/appointments';
import { Calendar, Clock, User } from 'lucide-vue-next';

interface Props {
    appointments: Array<{
        id: number;
        patient: {
            id: number;
            name: string;
            date_of_birth: string;
        };
        appointment_date: string;
        appointment_time: string;
        status: string;
        notes?: string;
    }>;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'My Appointments',
        href: '/doctors/my-appointments',
    },
];

const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
        case 'scheduled':
            return 'bg-blue-100 text-blue-800';
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        case 'no-show':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="My Appointments" />

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">My Appointments</h1>
                    <p class="text-muted-foreground">View and manage your scheduled appointments</p>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Date & Time</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="appointment in appointments" :key="appointment.id">
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <div>
                                        <div class="font-medium">{{ appointment.patient.name }}</div>
                                        <div class="text-sm text-muted-foreground">
                                            DOB: {{ new Date(appointment.patient.date_of_birth).toLocaleDateString() }}
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <div>
                                        <div>{{ new Date(appointment.appointment_date).toLocaleDateString() }}</div>
                                        <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                            <Clock class="h-3 w-3" />
                                            {{ appointment.appointment_time }}
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge :class="getStatusColor(appointment.status)">
                                    {{ appointment.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="show(appointment.id).url">
                                        View Details
                                    </Link>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
