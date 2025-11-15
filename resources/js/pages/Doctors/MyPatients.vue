<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { show } from '@/routes/patients';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, Mail, Phone } from 'lucide-vue-next';

interface Props {
    patients: Array<{
        id: number;
        name: string;
        email: string;
        phone?: string;
        date_of_birth: string;
        last_visit?: string;
        total_appointments: number;
    }>;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'My Patients',
        href: '/doctors/my-patients',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="My Patients" />

        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">My Patients</h1>
                    <p class="text-muted-foreground">
                        View and manage patients assigned to you
                    </p>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Contact</TableHead>
                            <TableHead>Date of Birth</TableHead>
                            <TableHead>Last Visit</TableHead>
                            <TableHead>Total Appointments</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="patient in patients" :key="patient.id">
                            <TableCell>
                                <div class="font-medium">
                                    {{ patient.name }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="space-y-1">
                                    <div
                                        class="flex items-center gap-1 text-sm"
                                    >
                                        <Mail
                                            class="h-3 w-3 text-muted-foreground"
                                        />
                                        {{ patient.email }}
                                    </div>
                                    <div
                                        v-if="patient.phone"
                                        class="flex items-center gap-1 text-sm text-muted-foreground"
                                    >
                                        <Phone class="h-3 w-3" />
                                        {{ patient.phone }}
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                {{
                                    new Date(
                                        patient.date_of_birth,
                                    ).toLocaleDateString()
                                }}
                            </TableCell>
                            <TableCell>
                                <div
                                    v-if="patient.last_visit"
                                    class="flex items-center gap-1"
                                >
                                    <Calendar
                                        class="h-3 w-3 text-muted-foreground"
                                    />
                                    {{
                                        new Date(
                                            patient.last_visit,
                                        ).toLocaleDateString()
                                    }}
                                </div>
                                <span v-else class="text-muted-foreground"
                                    >Never</span
                                >
                            </TableCell>
                            <TableCell>
                                <Badge variant="secondary">
                                    {{ patient.total_appointments }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="show(patient.id).url">
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
