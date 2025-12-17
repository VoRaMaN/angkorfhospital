<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { Toaster } from '@/components/ui/sonner';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { assignProcess, update } from '@/routes/visits';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    Edit,
    FileText,
    Loader2,
    Stethoscope,
    Trash2,
    User,
    UserCheck,
    X,
} from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { ref } from 'vue';

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
    medical_orders?: any[];
}

interface Props {
    visit: Visit;
    staff: Array<{
        id: number;
        name: string;
    }>;
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
    medical_orders?: any[];
}

interface Props {
    visit: Visit;
    staff: Array<{
        id: number;
        name: string;
    }>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visits',
        href: '/visits',
    },
    {
        title: 'Visit Details',
        href: '#',
    },
];

// Modal state
const showAssignModal = ref(false);

// Form for assigning staff
const assignForm = useForm({
    staff_id: '',
});

// Functions
const openAssignModal = () => {
    assignForm.reset();
    showAssignModal.value = true;
};

const assignVisit = () => {
    assignForm.patch(assignProcess(props.visit.id).url, {
        onSuccess: () => {
            showAssignModal.value = false;
            toast.success("Doctor assigned successfully!");
            // Refresh the page or update the list
            router.reload();
        },
    });
};

const cancelVisit = () => {
    if (confirm('Are you sure you want to cancel this visit?')) {
        router.patch(
            update(props.visit.id).url,
            {
                status: 'cancelled',
            },
            {
                onSuccess: () => {
                    toast.success("Visit cancelled successfully!");
                    // Refresh the page or update the list
                    router.reload();
                },
            },
        );
    }
};

const deleteVisit = () => {
    if (confirm('Are you sure you want to delete this visit? This action cannot be undone.')) {
        router.delete(
            `/visits/${props.visit.id}`,
            {
                onSuccess: () => {
                    toast.success("Visit deleted successfully!");
                    router.visit('/visits');
                },
            },
        );
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'in_progress':
            return 'bg-blue-100 text-blue-800';
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
    <Head title="Visit Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_visits')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <Link href="/visits">
                        <ArrowLeft class="size-4" />
                        Back to Visits
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Visit Details</h1>
                    <p class="text-muted-foreground">
                        View visit information and associated medical orders
                    </p>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <div class="flex gap-2">
                        <Button
                            v-if="visit.status === 'pending'"
                            variant="default"
                            size="sm"
                            @click="openAssignModal"
                        >
                            <UserCheck class="mr-1 size-4" />
                            Assign
                        </Button>
                        <Button
                            v-if="
                                (visit.status === 'pending' ||
                                visit.status === 'in_progress') &&
                                hasPermission('cancel_visits')
                            "
                            variant="destructive"
                            size="sm"
                            @click="cancelVisit"
                        >
                            <X class="mr-1 size-4" />
                            Cancel
                        </Button>
                        <Button
                            v-if="hasPermission('delete_visits')"
                            variant="destructive"
                            size="sm"
                            @click="deleteVisit"
                        >
                            <Trash2 class="mr-1 size-4" />
                            Delete
                        </Button>
                    </div>
                    <Button variant="outline" as-child v-if="hasPermission('edit_visits')">
                        <Link :href="`/visits/${visit.id}/edit`">
                            <Edit class="size-4" />
                            Edit Visit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Visit Information -->
                <div class="md:col-span-2">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Calendar class="size-5" />
                                Visit Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <div
                                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                                    >
                                        <User class="size-4" />
                                        Patient
                                    </div>
                                    <div class="text-sm">
                                        {{ visit.patient?.user?.name || `${visit.patient?.name || ''} ${visit.patient?.surname || ''}`.trim() || 'Unknown Patient' }}
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div
                                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                                    >
                                        <Stethoscope class="size-4" />
                                        Assigned Doctor
                                    </div>
                                    <div class="text-sm">
                                        {{
                                            visit.staff?.user.name ||
                                            'Unassigned'
                                        }}
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div
                                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                                    >
                                        <Calendar class="size-4" />
                                        Visit Date & Time
                                    </div>
                                    <div class="text-sm">
                                        {{
                                            new Date(
                                                visit.visit_date_time,
                                            ).toLocaleDateString()
                                        }}
                                        <div class="text-muted-foreground">
                                            {{
                                                new Date(
                                                    visit.visit_date_time,
                                                ).toLocaleTimeString()
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Status
                                    </div>
                                    <Badge
                                        :class="getStatusColor(visit.status)"
                                    >
                                        {{ visit.status.replace('_', ' ') }}
                                    </Badge>
                                </div>
                            </div>

                            <Separator />

                            <div v-if="visit.appointment" class="space-y-2">
                                <div
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Related Appointment
                                </div>
                                <div class="text-sm">
                                    Appointment #{{ visit.appointment.id }} -
                                    {{
                                        new Date(
                                            visit.appointment.appointment_date_time,
                                        ).toLocaleDateString()
                                    }}
                                </div>
                            </div>

                            <div v-if="visit.notes" class="space-y-2">
                                <div
                                    class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                                >
                                    <FileText class="size-4" />
                                    Notes
                                </div>
                                <div class="text-sm">{{ visit.notes }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Medical Orders -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle>Medical Orders</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="
                                    visit.medical_orders &&
                                    visit.medical_orders.length > 0
                                "
                                class="space-y-2"
                            >
                                <div
                                    v-for="order in visit.medical_orders"
                                    :key="order.id"
                                    class="flex items-center justify-between"
                                >
                                    <div class="text-sm">
                                        <div class="font-medium">
                                            {{ order.order_details }}
                                        </div>
                                        <div class="text-muted-foreground">
                                            {{ order.status }}
                                        </div>
                                    </div>
                                    <Badge variant="outline" class="text-xs">
                                        {{ order.priority }}
                                    </Badge>
                                </div>
                            </div>
                            <div v-else class="text-sm text-muted-foreground">
                                No medical orders associated with this visit
                                yet.
                            </div>
                        </CardContent>
                    </Card>
                </div>
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

        <!-- Assign Staff Modal -->
        <Dialog v-model:open="showAssignModal">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Select Doctor for Visit</DialogTitle>
                    <DialogDescription>
                        Select a doctor to assign to this visit. This will
                        also initiate the medical order process.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="staff" class="text-right"> Doctor </Label>
                        <Select v-model="assignForm.staff_id">
                            <SelectTrigger class="col-span-3">
                                <SelectValue
                                    placeholder="Select doctor"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="staff in props.staff"
                                    :key="staff.id"
                                    :value="staff.id.toString()"
                                >
                                    {{ staff.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="showAssignModal = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        @click="assignVisit"
                        :disabled="assignForm.processing"
                    >
                        <Loader2
                            v-if="assignForm.processing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        Assign & Process
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
    <Toaster />
</template>
