<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatDate, formatTime } from '@/lib/utils';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Toaster } from '@/components/ui/sonner';
import AppLayout from '@/layouts/AppLayout.vue';
import { show } from '@/routes/visits';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Calendar, Clock, Eye, Trash2, User } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

interface Visit {
    id: number;
    patient: {
        user: {
            name: string;
        };
    };
    staff: {
        user: {
            name: string;
        };
    } | null;
    appointment: any;
    visit_date_time: string;
    status: string;
    notes: string;
    created_at: string;
    medical_orders: Array<{
        id: number;
        status: string;
    }>;
}

interface Props {
    visits: Visit[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'My Visits',
        href: '/doctors/my-visits',
    },
];

const deleteVisit = (visit: Visit) => {
    if (confirm('Are you sure you want to delete this visit? This action cannot be undone.')) {
        router.delete(
            `/visits/${visit.id}`,
            {
                onSuccess: () => {
                    toast.success('Visit deleted successfully!');
                },
            },
        );
    }
};

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
        <Head title="My Visits" />

        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">My Visits</h1>
                    <p class="text-muted-foreground">
                        View and manage visits assigned to you
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
                            <TableHead>Medical Orders</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="visits.length === 0">
                            <TableCell colspan="5" class="py-8 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <Calendar
                                        class="h-8 w-8 text-muted-foreground"
                                    />
                                    <div class="text-lg font-medium">
                                        No visits assigned
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        You don't have any visits assigned to
                                        you at this time.
                                    </div>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-for="visit in visits"
                            :key="visit.id"
                        >
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <User
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div>
                                        <div class="font-medium">
                                            {{ visit.patient?.user?.name || `${visit.patient?.name || ''} ${visit.patient?.surname || ''}`.trim() || 'Unknown Patient' }}
                                        </div>
                                        <div
                                            v-if="visit.appointment"
                                            class="text-sm text-muted-foreground"
                                        >
                                            From appointment
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Calendar
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div>
                                        <div>
                                            {{
                                                formatDate(visit.visit_date_time)
                                            }}
                                        </div>
                                        <div
                                            class="flex items-center gap-1 text-sm text-muted-foreground"
                                        >
                                            <Clock class="h-3 w-3" />
                                            {{
                                                formatTime(visit.visit_date_time)
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
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-for="order in visit.medical_orders"
                                        :key="order.id"
                                        variant="outline"
                                        :class="getStatusColor(order.status)"
                                    >
                                        {{ order.status }}
                                    </Badge>
                                </div>
                                <div
                                    v-if="visit.medical_orders.length === 0"
                                    class="text-sm text-muted-foreground"
                                >
                                    No orders
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="show(visit.id).url">
                                            <Eye class="size-4" />
                                            View Details
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteVisit(visit)"
                                    >
                                        <Trash2 class="size-4" />
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
    <Toaster />
</template>
