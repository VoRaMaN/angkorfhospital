<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Toaster } from '@/components/ui/sonner';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, show, update } from '@/routes/visits';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Eye, Plus, Search, X } from 'lucide-vue-next';
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
    staff: Array<{
        id: number;
        name: string;
    }>;
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

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
    staff: Array<{
        id: number;
        name: string;
    }>;
    filters: {
        search: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visits',
        href: '#',
    },
];

const cancelVisit = (visit: Visit) => {
    if (confirm('Are you sure you want to cancel this visit?')) {
        router.patch(
            update(visit.id).url,
            {
                status: 'cancelled',
            },
            {
                onSuccess: () => {
                    toast.success("Visit cancelled successfully!");
                    router.reload();
                },
            },
        );
    }
};

const notifyStaff = (visit: Visit) => {
    router.patch(
        `/visits/${visit.id}/notify-staff`,
        {},
        {
            onSuccess: () => {
                toast.success("Nurse notified successfully!");
                router.reload();
            },
        },
    );
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

    <Head title="Visits" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_visits')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Visits</h1>
                    <p class="text-muted-foreground">
                        Manage patient visits and their associated medical
                        orders
                    </p>
                </div>
                <Button as-child v-if="hasPermission('create_visits')">
                    <Link :href="create().url">
                    <Plus class="size-4" />
                    New Visit
                    </Link>
                </Button>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative max-w-sm flex-1">
                    <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input placeholder="Search visits..." class="pl-9" />
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Staff</TableHead>
                            <TableHead>Visit Date</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="visit in visits" :key="visit.id">
                            <TableCell>
                                <div class="font-medium">
                                    {{ visit.patient.user.name }}
                                </div>
                                <div v-if="visit.appointment" class="text-sm text-muted-foreground">
                                    From appointment
                                </div>
                            </TableCell>
                            <TableCell>
                                {{ visit.staff?.user.name || 'Unassigned' }}
                            </TableCell>
                            <TableCell>
                                {{
                                    new Date(
                                        visit.visit_date_time,
                                    ).toLocaleDateString()
                                }}
                                <div class="text-sm text-muted-foreground">
                                    {{
                                        new Date(
                                            visit.visit_date_time,
                                        ).toLocaleTimeString()
                                    }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge :class="getStatusColor(visit.status)">
                                    {{ visit.status.replace('_', ' ') }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child v-if="hasPermission('view_visits')">
                                        <Link :href="show(visit.id).url">
                                        <Eye class="size-4" />
                                        View
                                        </Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child v-if="hasPermission('edit_visits')">
                                        <Link :href="edit(visit.id).url">
                                        <Edit class="size-4" />
                                        Edit
                                        </Link>
                                    </Button>
                                    <Button v-if="visit.status === 'pending' && hasPermission('notify_visits')"
                                        variant="outline" size="sm" @click="notifyStaff(visit)">
                                        Send To Nurse
                                    </Button>
                                    <Button v-if="
                                        (visit.status === 'pending' ||
                                            visit.status === 'in_progress') &&
                                        hasPermission('cancel_visits')
                                    " variant="destructive" size="sm" @click="cancelVisit(visit)">
                                        <X class="size-4" />
                                        Cancel
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.visits.length === 0">
                            <TableCell colspan="5" class="text-center text-muted-foreground">
                                No visits found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
        <div v-else class="flex h-full flex-1 items-center justify-center">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">
                    Access Denied
                </h2>
                <p class="text-muted-foreground">
                    You do not have permission to view visits.
                </p>
            </div>
        </div>
    </AppLayout>
    <Toaster />
</template>
