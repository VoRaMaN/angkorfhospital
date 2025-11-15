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
    show,
    updateStatus as updateStatusRoute,
} from '@/routes/appointments';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, Plus } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    appointments: Array<{
        id: number;
        patient: { user: { name: string } };
        staff: { user: { name: string } };
        appointment_date_time: string;
        duration_minutes: number;
        appointment_type: string;
        status: string;
    }>;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appointments',
        href: '#',
    },
];

const confirmModalOpen = ref(false);
const selectedAppointment = ref<any>(null);
const selectedNewStatus = ref('');

const updateStatus = async (appointment: any, newStatus: string) => {
    selectedAppointment.value = appointment;
    selectedNewStatus.value = newStatus;
    confirmModalOpen.value = true;
};

const confirmStatusUpdate = async () => {
    if (!selectedAppointment.value || !selectedNewStatus.value) return;

    try {
        const route = updateStatusRoute(selectedAppointment.value.id);
        const response = await fetch(route.url, {
            method: route.method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':
                    (
                        document.querySelector(
                            'meta[name="csrf-token"]',
                        ) as HTMLMetaElement
                    )?.content || '',
            },
            body: JSON.stringify({ status: selectedNewStatus.value }),
        });

        if (response.ok) {
            confirmModalOpen.value = false;
            selectedAppointment.value = null;
            selectedNewStatus.value = '';
            window.location.reload(); // Refresh to show updated status
        } else {
            alert('Failed to update appointment status');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        alert('An error occurred while updating the status');
    }
};

const cancelStatusUpdate = () => {
    confirmModalOpen.value = false;
    selectedAppointment.value = null;
    selectedNewStatus.value = '';
};
</script>

<template>
    <Head title="Appointments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
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
                    <Button
                        as-child
                        v-if="hasPermission('create_appointments')"
                    >
                        <Link :href="create().url">
                            <Plus class="size-4" />
                            Create Appointment
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Staff</TableHead>
                            <TableHead>Date</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Duration</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="appointment in props.appointments"
                            :key="appointment.id"
                        >
                            <TableCell>{{
                                appointment.patient.user.name
                            }}</TableCell>
                            <TableCell>{{
                                appointment.staff.user.name
                            }}</TableCell>
                            <TableCell>{{
                                new Date(
                                    appointment.appointment_date_time,
                                ).toLocaleString()
                            }}</TableCell>
                            <TableCell>
                                <Badge variant="outline">
                                    {{
                                        appointment?.appointment_type.replace(
                                            '_',
                                            ' ',
                                        )
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell
                                >{{
                                    appointment.duration_minutes
                                }}min</TableCell
                            >
                            <TableCell>
                                <Badge
                                    :variant="
                                        appointment.status === 'completed'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        appointment.status
                                            .charAt(0)
                                            .toUpperCase() +
                                        appointment.status.slice(1)
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="show(appointment.id).url"
                                            >View</Link
                                        >
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                        v-if="
                                            hasPermission('edit_appointments')
                                        "
                                    >
                                        <Link :href="edit(appointment.id).url"
                                            >Edit</Link
                                        >
                                    </Button>

                                    <!-- Status-specific action buttons -->
                                    <template
                                        v-if="
                                            appointment.status ===
                                                'scheduled' &&
                                            hasPermission('edit_appointments')
                                        "
                                    >
                                        <Button
                                            variant="default"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'confirmed',
                                                )
                                            "
                                        >
                                            Confirm
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'cancelled',
                                                )
                                            "
                                        >
                                            Cancel
                                        </Button>
                                    </template>

                                    <template
                                        v-else-if="
                                            appointment.status ===
                                                'confirmed' &&
                                            hasPermission('edit_appointments')
                                        "
                                    >
                                        <Button
                                            variant="default"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'arrived',
                                                )
                                            "
                                        >
                                            Arrived
                                        </Button>
                                        <Button
                                            variant="default"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'completed',
                                                )
                                            "
                                        >
                                            Complete
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'cancelled',
                                                )
                                            "
                                        >
                                            Cancel
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'no_show',
                                                )
                                            "
                                        >
                                            No Show
                                        </Button>
                                    </template>

                                    <template
                                        v-else-if="
                                            appointment.status === 'arrived' &&
                                            hasPermission('edit_appointments')
                                        "
                                    >
                                        <Button
                                            variant="default"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'in_progress',
                                                )
                                            "
                                        >
                                            Start
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'cancelled',
                                                )
                                            "
                                        >
                                            Cancel
                                        </Button>
                                    </template>

                                    <template
                                        v-else-if="
                                            appointment.status ===
                                                'in_progress' &&
                                            hasPermission('edit_appointments')
                                        "
                                    >
                                        <Button
                                            variant="default"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'completed',
                                                )
                                            "
                                        >
                                            Complete
                                        </Button>
                                    </template>

                                    <template
                                        v-else-if="
                                            appointment.status === 'no-show' &&
                                            hasPermission('edit_appointments')
                                        "
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'scheduled',
                                                )
                                            "
                                        >
                                            Reschedule
                                        </Button>
                                    </template>

                                    <template
                                        v-else-if="
                                            appointment.status ===
                                                'cancelled' &&
                                            hasPermission('edit_appointments')
                                        "
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'scheduled',
                                                )
                                            "
                                        >
                                            Reschedule
                                        </Button>
                                    </template>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.appointments.length === 0">
                            <TableCell
                                colspan="7"
                                class="text-center text-muted-foreground"
                            >
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
