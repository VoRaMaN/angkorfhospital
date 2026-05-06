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
import { Label } from '@/components/ui/label';
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
import { assignProcess, show } from '@/routes/visits';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    Calendar,
    Clock,
    Eye,
    Loader2,
    User,
    UserCheck,
} from 'lucide-vue-next';

import SearchableSelect from '@/components/SearchableSelect.vue';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';

interface Visit {
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
}

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
    staff: Array<{
        id: number;
        name: string;
    }>;
}

const props = defineProps<Props>();
const { hasPermission } = useAuth();

const staffOptions = computed(() =>
    props.staff.map((s) => ({ value: s.id.toString(), label: s.name })),
);

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

// Modal state
const showAssignModal = ref(false);
const selectedVisit = ref<Visit | null>(null);

// Form for assigning staff
const assignForm = useForm({
    staff_id: '',
});

// Functions
const openAssignModal = (visit: Visit) => {
    selectedVisit.value = visit;
    assignForm.reset();
    showAssignModal.value = true;
};

const assignVisit = () => {
    if (!selectedVisit.value) return;

    assignForm.patch(assignProcess(selectedVisit.value.id).url, {
        onSuccess: () => {
            showAssignModal.value = false;
            selectedVisit.value = null;
            toast.success("Doctor assigned successfully!");
            // Refresh the page or update the list
            router.reload();
        },
    });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':             return 'bg-amber-100 text-amber-700 border border-amber-200';
        case 'awaiting_assignment': return 'bg-orange-100 text-orange-700 border border-orange-200';
        case 'assigned':            return 'bg-violet-100 text-violet-700 border border-violet-200';
        case 'in_progress':         return 'bg-sky-100 text-sky-700 border border-sky-200';
        case 'awaiting_accountant': return 'bg-teal-100 text-teal-700 border border-teal-200';
        case 'completed':           return 'bg-emerald-100 text-emerald-700 border border-emerald-200';
        case 'cancelled':           return 'bg-rose-100 text-rose-700 border border-rose-200';
        default:                    return 'bg-slate-100 text-slate-500 border border-slate-200';
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="My Visits" />

        <div v-if="hasPermission('view_visits')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">My Visits</h1>
                    <p class="text-muted-foreground">
                        View and manage your assigned patient visits
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
                                            {{
                                                visit.patient?.user?.name ||
                                                'Unknown Patient'
                                            }}
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
                                        {{ visit.visit_date_time }}
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
                                    <Button v-if="
                                        visit.status ===
                                        'awaiting_assignment' &&
                                        hasPermission('assign_visits')
                                    " variant="outline" size="sm" class="border-blue-600 text-blue-600"
                                        @click="openAssignModal(visit)">
                                        <UserCheck class="mr-1 h-4 w-4" />
                                        Select Doctor
                                    </Button>
                                </div>
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
                        <SearchableSelect v-model="assignForm.staff_id" :options="staffOptions"
                            placeholder="Select doctor" class="col-span-3" />
                    </div>
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showAssignModal = false">
                        Cancel
                    </Button>
                    <Button type="button" @click="assignVisit" :disabled="assignForm.processing">
                        <Loader2 v-if="assignForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Assign & Process
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
    <Toaster />
</template>
