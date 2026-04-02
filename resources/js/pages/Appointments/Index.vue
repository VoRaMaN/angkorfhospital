<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    calendar,
    create,
    edit,
    letter,
    show,
} from '@/routes/appointments';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Calendar, Download, Plus, Printer, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Props {
    appointments: Array<{
        id: number;
        patient: {
            user: { name: string };
            mobile_phone: string | null;
        };
        staff: { user: { name: string } };
        appointment_date_time: string;
        duration_minutes: number;
        appointment_type: string;
        status: string;
    }>;
    filters: {
        search: string;
        from?: string | null;
        to?: string | null;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const searchQuery = ref(props.filters.search || '');
const dateFrom = ref<string>(props.filters.from ?? '');
const dateTo = ref<string>(props.filters.to ?? '');
const clearDates = () => {
    dateFrom.value = '';
    dateTo.value = '';
    performSearch();
};
let searchTimeout: number | null = null;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appointments',
        href: '#',
    },
];

// Debounced search function
const performSearch = (immediate = false) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    if (immediate) {
        router.get('/appointments', {
            search: searchQuery.value,
            page: 1, // Reset to first page when searching
            from: dateFrom.value || undefined,
            to: dateTo.value || undefined,
        }, {
            preserveState: true,
            replace: true,
        });
        return;
    }

    searchTimeout = setTimeout(() => {
        router.get('/appointments', {
            search: searchQuery.value,
            page: 1, // Reset to first page when searching
            from: dateFrom.value || undefined,
            to: dateTo.value || undefined,
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

// Do not auto-search on date change; user will click Search

const confirmModalOpen = ref(false);
const selectedAppointment = ref<any>(null);
const selectedNewStatus = ref('');

const updateStatus = async (appointment: any, newStatus: string) => {
    selectedAppointment.value = appointment;
    selectedNewStatus.value = newStatus;
    confirmModalOpen.value = true;
};

const confirmStatusUpdate = () => {
    if (!selectedAppointment.value || !selectedNewStatus.value) return;

    // Extract numeric ID from appointment (handle both numeric and string formats)
    let appointmentId = selectedAppointment.value.id;
    if (typeof appointmentId === 'string' && appointmentId.includes('/')) {
        // If format is like "25/000002", parse the number after the slash to remove leading zeros
        appointmentId = parseInt(appointmentId.split('/')[1]);
    }

    router.patch(
        `/appointments/${appointmentId}/status`,
        {
            status: selectedNewStatus.value,
        },
        {
            onSuccess: () => {
                confirmModalOpen.value = false;
                selectedAppointment.value = null;
                selectedNewStatus.value = '';
            },
            onError: () => {
                alert('Failed to update appointment status');
            },
        },
    );
};

const cancelStatusUpdate = () => {
    confirmModalOpen.value = false;
    selectedAppointment.value = null;
    selectedNewStatus.value = '';
};

// Helper function to extract the correct ID from appointment
const getAppointmentId = (id: string | number): number => {
    if (typeof id === 'string' && id.includes('/')) {
        // If format is like "25/000002", parse the number after the slash to remove leading zeros
        return parseInt(id.split('/')[1]);
    }
    return typeof id === 'string' ? parseInt(id) : id;
};

const exportAppointments = () => {
    const params = new URLSearchParams();
    if (searchQuery.value) params.append('search', searchQuery.value);
    if (dateFrom.value) params.append('from', dateFrom.value);
    if (dateTo.value) params.append('to', dateTo.value);

    const url = `/appointments-export?${params.toString()}`;
    window.open(url, '_blank');
};
</script>

<template>

    <Head title="Appointments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Appointments</h1>
                    <p class="text-muted-foreground">
                        Manage your appointments
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button as-child variant="outline">
                        <Link :href="calendar().url">
                        <Calendar class="mr-2 size-4" />
                        Calendar View
                        </Link>
                    </Button>
                    <Button as-child v-if="hasPermission('create_appointments')">
                        <Link :href="create().url">
                        <Plus class="size-4" />
                        Create Appointment
                        </Link>
                    </Button>
                    <Button variant="outline" @click="exportAppointments" v-if="hasPermission('view_appointments')">
                        <Download class="size-4" />
                        Export to CSV
                    </Button>
                </div>
            </div>

            <!-- Search + Date Range Filters Grouped -->
            <div class="flex items-center gap-4 w-full">
                <div class="relative flex-1 min-w-0">
                    <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="searchQuery" placeholder="Search appointments..." class="pl-10"
                        @keyup.enter="performSearch(true)" />
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <label class="text-sm text-muted-foreground">From</label>
                    <div class="flex flex-col min-w-[150px]">
                        <Input type="date" v-model="dateFrom" />
                    </div>
                    <label class="text-sm text-muted-foreground">To</label>
                    <div class="flex flex-col min-w-[150px]">
                        <Input type="date" v-model="dateTo" />
                    </div>
                    <div class="flex items-center">
                        <Button variant="ghost" size="sm" @click="clearDates">
                            Clear
                        </Button>
                        <Button class="ml-2" size="sm" @click="performSearch(true)">
                            Search
                        </Button>
                    </div>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Time</TableHead>
                            <TableHead>ID</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Patient Name</TableHead>
                            <TableHead>Mobile Number</TableHead>
                            <TableHead>Doctor</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="appointment in props.appointments" :key="appointment.id">
                            <TableCell>{{
                                new Date(appointment.appointment_date_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                            }}</TableCell>
                            <TableCell class="font-mono">{{ appointment.id }}</TableCell>
                            <TableCell>
                                <Badge :variant="
                                        appointment.status === 'completed'
                                            ? 'default'
                                            : 'secondary'
                                    ">
                                    {{
                                    appointment.status
                                    .charAt(0)
                                    .toUpperCase() +
                                    appointment.status.slice(1)
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ appointment.patient?.user?.name || 'Unknown Patient' }}</TableCell>
                            <TableCell>{{ appointment.patient.mobile_phone || 'N/A' }}</TableCell>
                            <TableCell>{{ appointment.staff?.user?.name || 'Unassigned' }}</TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="show(getAppointmentId(appointment.id)).url">View</Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child v-if="
                                            hasPermission('edit_appointments')
                                        ">
                                        <Link :href="edit(getAppointmentId(appointment.id)).url">Edit</Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child>
                                        <a :href="letter(getAppointmentId(appointment.id)).url" target="_blank"
                                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                                            <Printer class="size-4" />
                                            Print
                                        </a>
                                    </Button>

                                    <!-- Status-specific action buttons -->
                                    <template v-if="
                                            appointment.status ===
                                                'confirmed' &&
                                            hasPermission('edit_appointments')
                                        ">
                                        <Button variant="default" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'arrived',
                                                )
                                            ">
                                            Arrived
                                        </Button>
                                        <Button variant="default" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'completed',
                                                )
                                            ">
                                            Complete
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'cancelled',
                                                )
                                            ">
                                            Cancel
                                        </Button>
                                        <Button variant="outline" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'no_show',
                                                )
                                            ">
                                            No Show
                                        </Button>
                                    </template>

                                    <template v-else-if="
                                            appointment.status === 'arrived' &&
                                            hasPermission('edit_appointments')
                                        ">
                                        <Button variant="default" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'in_progress',
                                                )
                                            ">
                                            Start
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'cancelled',
                                                )
                                            ">
                                            Cancel
                                        </Button>
                                    </template>

                                    <template v-else-if="
                                            appointment.status ===
                                                'in_progress' &&
                                            hasPermission('edit_appointments')
                                        ">
                                        <Button variant="default" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'completed',
                                                )
                                            ">
                                            Complete
                                        </Button>
                                    </template>

                                    <template v-else-if="
                                            appointment.status === 'no-show' &&
                                            hasPermission('edit_appointments')
                                        ">
                                        <Button variant="outline" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'scheduled',
                                                )
                                            ">
                                            Reschedule
                                        </Button>
                                    </template>

                                    <template v-else-if="
                                            appointment.status ===
                                                'cancelled' &&
                                            hasPermission('edit_appointments')
                                        ">
                                        <Button variant="outline" size="sm" @click="
                                                updateStatus(
                                                    appointment,
                                                    'scheduled',
                                                )
                                            ">
                                            Reschedule
                                        </Button>
                                    </template>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.appointments.length === 0">
                            <TableCell colspan="7" class="text-center text-muted-foreground">
                                No appointments found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <Dialog v-model:open="confirmModalOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirm Status Update</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to update the status of this
                        appointment to "{{ selectedNewStatus }}"? <br /><br />
                        <strong>Patient:</strong>
                        {{
                        selectedAppointment?.patient?.user?.name ||
                        'Unknown'
                        }}
                        <br />
                        <strong>Current Status:</strong>
                        {{ selectedAppointment?.status || 'Unknown' }}
                        <br />
                        <strong>New Status:</strong> {{ selectedNewStatus }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="cancelStatusUpdate">
                        Cancel
                    </Button>
                    <Button @click="confirmStatusUpdate"> Confirm </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
