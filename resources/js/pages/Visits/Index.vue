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
import { Edit, Eye, Plus, Search, X, Calendar, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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
    doctor: {
        id: number;
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
    doctors: Array<{
        id: number;
        name: string;
    }>;
    filters: {
        search: string;
        date: string;
        patient?: string;
    };
    patientName?: string;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const searchQuery = ref(props.filters.search || '');
const selectedDate = ref(props.filters.date || '');
let searchTimeout: number | null = null;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visits',
        href: '#',
    },
];

// Debounced search function
const performSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        router.get('/visits', {
            search: searchQuery.value,
            date: selectedDate.value,
            patient: props.filters.patient, // Preserve patient filter
            page: 1, // Reset to first page when searching
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300); // 300ms debounce
};

// Watch for search query changes
watch(searchQuery, () => {
    performSearch();
});

// Watch for date changes
watch(selectedDate, () => {
    router.get('/visits', {
        search: searchQuery.value,
        date: selectedDate.value,
        patient: props.filters.patient, // Preserve patient filter
        page: 1,
    }, {
        preserveState: true,
        replace: true,
    });
});

const clearDate = () => {
    selectedDate.value = new Date().toISOString().split('T')[0]; // Reset to today
};

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

const assignDoctor = (visit: Visit, doctorId: number) => {
    router.patch(`/visits/${visit.id}/assign-doctor`, {
        doctor_id: doctorId,
    }, {
        preserveScroll: true,
    });
};

const deleteVisit = (visit: Visit) => {
    if (confirm('Are you sure you want to delete this visit? This action cannot be undone.')) {
        router.delete(
            `/visits/${visit.id}`,
            {
                onSuccess: () => {
                    toast.success("Visit deleted successfully!");
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

    <Head title="Visits" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_visits')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        {{ props.filters.patient ? `Visit History - ${props.patientName || 'Patient'}` : 'Visits' }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ props.filters.patient ? 'All visits for this patient' : 'Manage patient visits and their associated medical orders' }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button v-if="props.filters.patient" variant="outline" as-child>
                        <a :href="`/patients/show?patient=${props.filters.patient}`">
                            Back to Patient
                        </a>
                    </Button>
                    <Button as-child v-if="hasPermission('create_visits')">
                        <Link :href="create().url">
                        <Plus class="size-4" />
                        New Visit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative max-w-sm flex-1">
                    <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="searchQuery" placeholder="Search visits..." class="pl-9" />
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <Calendar class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="selectedDate"
                            type="date"
                            class="pl-9 w-48"
                        />
                    </div>
                    <Button
                        v-if="selectedDate !== new Date().toISOString().split('T')[0]"
                        variant="ghost"
                        size="icon"
                        @click="clearDate"
                        title="Reset to today"
                    >
                        <X class="size-4" />
                    </Button>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Staff</TableHead>
                            <TableHead>Doctor</TableHead>
                            <TableHead>Visit Date</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="visit in visits" :key="visit.id">
                            <TableCell>
                                <div class="font-medium">
                                    {{ visit.patient?.user?.name || `${visit.patient?.name || ''} ${visit.patient?.surname || ''}`.trim() || 'Unknown Patient' }}
                                </div>
                                <div v-if="visit.appointment" class="text-sm text-muted-foreground">
                                    From appointment
                                </div>
                            </TableCell>
                            <TableCell>
                                {{ visit.staff?.user.name || 'Unassigned' }}
                            </TableCell>
                            <TableCell>
                                <select
                                    v-if="visit.status !== 'completed' && visit.status !== 'cancelled' && hasPermission('assign_visits')"
                                    :value="visit.doctor?.id || ''"
                                    @change="assignDoctor(visit, Number(($event.target as HTMLSelectElement).value))"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">Select Doctor</option>
                                    <option
                                        v-for="doctor in doctors"
                                        :key="doctor.id"
                                        :value="doctor.id"
                                    >
                                        {{ doctor.name }}
                                    </option>
                                </select>
                                <span v-else>
                                    {{ visit.doctor?.user.name || 'Not Assigned' }}
                                </span>
                            </TableCell>
                            <TableCell>
                                {{ visit.visit_date_time }}
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
                                    <Button v-if="hasPermission('delete_visits')"
                                        variant="destructive" size="sm" @click="deleteVisit(visit)">
                                        <Trash2 class="size-4" />
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.visits.length === 0">
                            <TableCell colspan="6" class="text-center text-muted-foreground">
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
