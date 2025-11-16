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
import { processPage } from '@/routes/medical-orders';
import { show } from '@/routes/visits';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, Clock, Eye, Play, User } from 'lucide-vue-next';

interface Props {
    visits: Array<{
        id: number;
        patient: {
            user: {
                name: string;
            };
        };
        appointment: any;
        visit_date_time: string;
        status: string;
        notes?: string;
        created_at: string;
        medical_orders: Array<{
            id: number;
            status: string;
        }>;
    }>;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'My Visits to Process',
        href: '/doctors/my-to-be-process-visits',
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'awaiting_assignment':
            return 'bg-orange-100 text-orange-800';
        case 'assigned':
            return 'bg-purple-100 text-purple-800';
        case 'in_progress':
            return 'bg-blue-100 text-blue-800';
        case 'awaiting_accountant':
            return 'bg-cyan-100 text-cyan-800';
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="My Visits to Process" />

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">My Visits to Process</h1>
                    <p class="text-muted-foreground">
                        Process your assigned patient visits
                    </p>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Visit Date & Time</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="visit in visits" :key="visit.id">
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <div>
                                        <div class="font-medium">
                                            {{ visit.patient.user.name }}
                                        </div>
                                        <div v-if="visit.appointment" class="text-sm text-muted-foreground">
                                            From appointment
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <div>
                                        <div>
                                            {{
                                                new Date(
                                                    visit.visit_date_time,
                                                ).toLocaleDateString()
                                            }}
                                        </div>
                                        <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                            <Clock class="h-3 w-3" />
                                            {{
                                                new Date(
                                                    visit.visit_date_time,
                                                ).toLocaleTimeString()
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge :class="getStatusColor(visit.status)">
                                    {{ visit.status.replace('_', ' ') }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="show(visit.id).url">
                                        <Eye class="mr-1 h-4 w-4" />
                                        View Details
                                        </Link>
                                    </Button>
                                    <Button v-if="visit.medical_orders.length > 0" variant="outline"
                                        class="border-green-600 text-green-600" size="sm" as-child>
                                        <Link :href="processPage(
                                            visit.medical_orders[0].id,
                                        ).url
                                            ">
                                        <Play class="mr-1 h-4 w-4" />
                                        Process Visit
                                        </Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
