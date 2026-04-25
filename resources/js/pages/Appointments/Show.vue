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
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDateTime } from '@/lib/utils';
import { updateStatus as updateStatusRoute } from '@/routes/appointments';
import { report as reportRoute } from '@/routes/appointments';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Download } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    appointment: {
        id: number;
        patient: {
            user?: { name: string; email?: string } | null;
            name: string;
            surname: string;
            email?: string;
        };
        staff: { user: { name: string }; role: { name: string } };
        appointment_date_time: string;
        duration_minutes: number;
        appointment_type: string;
        status: string;
        reason_for_visit?: string;
        notes?: string;
        is_hormone_test?: boolean;
        is_tvs?: boolean;
        opu_time?: string | null;
        et_fet_time?: string | null;
        is_beta_hcg?: boolean;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

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

const confirmModalOpen = ref(false);
const selectedNewStatus = ref('');

const updateStatus = (newStatus: string) => {
    selectedNewStatus.value = newStatus;
    confirmModalOpen.value = true;
};

const confirmStatusUpdate = () => {
    router.patch(
        updateStatusRoute(props.appointment.id).url,
        {
            status: selectedNewStatus.value,
        },
        {
            onSuccess: () => {
                confirmModalOpen.value = false;
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
    selectedNewStatus.value = '';
};

// Helper functions for colors
const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        scheduled:
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        confirmed:
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        arrived:
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        in_progress:
            'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        completed:
            'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        no_show:
            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        rescheduled:
            'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        consultation:
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        emergency: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        follow_up:
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        procedure:
            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        checkup:
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        telemedicine:
            'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
        screening:
            'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
        therapy:
            'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Appointment Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/appointments">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Appointment Details</h1>
                    <p class="text-muted-foreground">
                        View appointment information
                    </p>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <div
                        class="flex gap-2"
                        v-if="hasPermission('edit_appointments')"
                    >
                        <template
                            v-if="props.appointment.status === 'scheduled'"
                        >
                            <Button
                                variant="default"
                                size="sm"
                                @click="updateStatus('confirmed')"
                                >Confirm</Button
                            >
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="updateStatus('cancelled')"
                                >Cancel</Button
                            >
                        </template>

                        <template
                            v-else-if="props.appointment.status === 'confirmed'"
                        >
                            <Button
                                variant="default"
                                size="sm"
                                @click="updateStatus('completed')"
                                >Complete</Button
                            >
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="updateStatus('cancelled')"
                                >Cancel</Button
                            >
                            <Button
                                variant="outline"
                                size="sm"
                                @click="updateStatus('no-show')"
                                >No Show</Button
                            >
                        </template>

                        <template
                            v-else-if="props.appointment.status === 'cancelled'"
                        >
                            <Button
                                variant="outline"
                                size="sm"
                                @click="updateStatus('scheduled')"
                                >Reschedule</Button
                            >
                        </template>

                        <template
                            v-else-if="props.appointment.status === 'no-show'"
                        >
                            <Button
                                variant="outline"
                                size="sm"
                                @click="updateStatus('scheduled')"
                                >Reschedule</Button
                            >
                        </template>
                    </div>
                    <Button
                        variant="outline"
                        as-child
                        v-if="hasPermission('edit_appointments')"
                    >
                        <Link
                            :href="`/appointments/${props.appointment.id}/edit`"
                        >
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button
                        variant="outline"
                        as-child
                    >
                        <a
                            :href="reportRoute(props.appointment.id).url"
                            target="_blank"
                        >
                            <Download class="size-4" />
                            Download Report
                        </a>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Patient
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.appointment.patient.user?.name ||
                                    `${props.appointment.patient.name} ${props.appointment.patient.surname}`
                                }}
                            </dd>
                        </div>

                        <div
                            class="space-y-2"
                            v-if="
                                props.appointment.patient.user?.email ||
                                props.appointment.patient.email
                            "
                        >
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Patient Email
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.appointment.patient.user?.email ||
                                    props.appointment.patient.email
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Staff
                            </dt>
                            <dd class="text-sm">
                                {{ props.appointment.staff.user.name }} ({{
                                    props.appointment.staff.role.name
                                }})
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Appointment Date & Time
                            </dt>
                            <dd class="text-sm">
                                {{
                                    formatDateTime(props.appointment.appointment_date_time)
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Duration
                            </dt>
                            <dd class="text-sm">
                                {{ props.appointment.duration_minutes }} minutes
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Type
                            </dt>
                            <dd class="text-sm">
                                <Badge
                                    :class="
                                        getTypeColor(
                                            props.appointment.appointment_type,
                                        )
                                    "
                                >
                                    {{
                                        props.appointment.appointment_type.replace(
                                            '_',
                                            ' ',
                                        )
                                    }}
                                </Badge>
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Status
                            </dt>
                            <dd class="text-sm">
                                <Badge
                                    :class="
                                        getStatusColor(props.appointment.status)
                                    "
                                >
                                    {{
                                        props.appointment.status.replace(
                                            '_',
                                            ' ',
                                        )
                                    }}
                                </Badge>
                            </dd>
                        </div>

                        <div
                            class="space-y-2"
                            v-if="props.appointment.reason_for_visit"
                        >
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Reason for Visit
                            </dt>
                            <dd class="text-sm">
                                {{ props.appointment.reason_for_visit }}
                            </dd>
                        </div>

                        <div class="space-y-2" v-if="props.appointment.notes">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Additional Notes
                            </dt>
                            <dd class="text-sm">
                                {{ props.appointment.notes }}
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Procedure Details -->
                <div class="rounded-lg border bg-card p-6 mt-4">
                    <h2 class="text-lg font-semibold mb-4">Procedure Details</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Hormone Test</dt>
                            <dd class="text-sm">{{ props.appointment.is_hormone_test ? 'Yes' : 'No' }}</dd>
                        </div>
                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">TVS</dt>
                            <dd class="text-sm">{{ props.appointment.is_tvs ? 'Yes' : 'No' }}</dd>
                        </div>
                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">OPU Time</dt>
                            <dd class="text-sm">{{ props.appointment.opu_time || '—' }}</dd>
                        </div>
                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">ET/FET Time</dt>
                            <dd class="text-sm">{{ props.appointment.et_fet_time || '—' }}</dd>
                        </div>
                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Beta HCG</dt>
                            <dd class="text-sm">{{ props.appointment.is_beta_hcg ? 'Yes' : 'No' }}</dd>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-6 mt-4">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Created
                            </dt>
                            <dd class="text-sm">
                                {{
                                    formatDateTime(props.appointment.created_at)
                                }}
                            </dd>
                        </div>
                    </div>
                </div>
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
                            props.appointment.patient.user?.name ||
                            `${props.appointment.patient.name} ${props.appointment.patient.surname}`
                        }}
                        <br />
                        <strong>Current Status:</strong>
                        {{ props.appointment.status }}
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
