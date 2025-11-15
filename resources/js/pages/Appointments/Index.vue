<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { create, show, edit, updateStatus as updateStatusRoute } from '@/routes/appointments';
import { Plus } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

interface Props {
    appointments: Array<{
        id: number;
        patient: { user: { name: string } };
        staff: { user: { name: string } };
        appointment_date_time: string;
        status: string;
    }>;
}

const props = defineProps<Props>();

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
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Appointments</h1>
                    <p class="text-muted-foreground">Manage your appointments</p>
                </div>
                <Button as-child>
                    <Link :href="create().url">
                    <Plus class="size-4" />
                    Create Appointment
                    </Link>
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Staff</TableHead>
                            <TableHead>Date</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="appointment in props.appointments" :key="appointment.id">
                            <TableCell>{{ appointment.patient.user.name }}</TableCell>
                            <TableCell>{{ appointment.staff.user.name }}</TableCell>
                            <TableCell>{{ new Date(appointment.appointment_date_time).toLocaleString() }}</TableCell>
                            <TableCell>
                                <Badge :variant="appointment.status === 'completed' ? 'default' : 'secondary'">
                                    {{ appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1) }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="show(appointment.id).url">View</Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="edit(appointment.id).url">Edit</Link>
                                    </Button>

                                    <!-- Status-specific action buttons -->
                                    <template v-if="appointment.status === 'scheduled'">
                                        <Button variant="default" size="sm"
                                            @click="updateStatus(appointment, 'confirmed')">
                                            Confirm
                                        </Button>
                                        <Button variant="destructive" size="sm"
                                            @click="updateStatus(appointment, 'cancelled')">
                                            Cancel
                                        </Button>
                                    </template>

                                    <template v-else-if="appointment.status === 'confirmed'">
                                        <Button variant="default" size="sm"
                                            @click="updateStatus(appointment, 'completed')">
                                            Complete
                                        </Button>
                                        <Button variant="destructive" size="sm"
                                            @click="updateStatus(appointment, 'cancelled')">
                                            Cancel
                                        </Button>
                                        <Button variant="outline" size="sm"
                                            @click="updateStatus(appointment, 'no-show')">
                                            No Show
                                        </Button>
                                    </template>

                                    <template v-else-if="appointment.status === 'no-show'">
                                        <Button variant="outline" size="sm"
                                            @click="updateStatus(appointment, 'scheduled')">
                                            Reschedule
                                        </Button>
                                    </template>

                                    <template v-else-if="appointment.status === 'cancelled'">
                                        <Button variant="outline" size="sm"
                                            @click="updateStatus(appointment, 'scheduled')">
                                            Reschedule
                                        </Button>
                                    </template>
                                </div>
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
                        Are you sure you want to update the status of this appointment to "{{ selectedNewStatus }}"?
                        <br><br>
                        <strong>Patient:</strong> {{ selectedAppointment?.patient?.user?.name || 'Unknown' }}
                        <br>
                        <strong>Current Status:</strong> {{ selectedAppointment?.status || 'Unknown' }}
                        <br>
                        <strong>New Status:</strong> {{ selectedNewStatus }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="cancelStatusUpdate">
                        Cancel
                    </Button>
                    <Button @click="confirmStatusUpdate">
                        Confirm
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>