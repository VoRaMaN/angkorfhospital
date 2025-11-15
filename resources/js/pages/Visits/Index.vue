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
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { assignProcess, create, edit, show, update } from '@/routes/visits';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    Edit,
    Eye,
    Loader2,
    Plus,
    Search,
    UserCheck,
    X,
} from 'lucide-vue-next';

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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visits',
        href: '#',
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
            // Refresh the page or update the list
            window.location.reload();
        },
    });
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
                    // Refresh the page or update the list
                    window.location.reload();
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
    <Head title="Visits" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Visits</h1>
                    <p class="text-muted-foreground">
                        Manage patient visits and their associated medical
                        orders
                    </p>
                </div>
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="size-4" />
                        New Visit
                    </Link>
                </Button>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative max-w-sm flex-1">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
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
                                <div
                                    v-if="visit.appointment"
                                    class="text-sm text-muted-foreground"
                                >
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
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="show(visit.id).url">
                                            <Eye class="size-4" />
                                            View
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="edit(visit.id).url">
                                            <Edit class="size-4" />
                                            Edit
                                        </Link>
                                    </Button>
                                    <Button
                                        v-if="visit.status === 'pending'"
                                        variant="default"
                                        size="sm"
                                        @click="openAssignModal(visit)"
                                    >
                                        <UserCheck class="size-4" />
                                        Assign
                                    </Button>
                                    <Button
                                        v-if="
                                            visit.status === 'pending' ||
                                            visit.status === 'in_progress'
                                        "
                                        variant="destructive"
                                        size="sm"
                                        @click="cancelVisit(visit)"
                                    >
                                        <X class="size-4" />
                                        Cancel
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.visits.length === 0">
                            <TableCell
                                colspan="5"
                                class="text-center text-muted-foreground"
                            >
                                No visits found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- Assign Staff Modal -->
        <Dialog v-model:open="showAssignModal">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Assign Staff to Visit</DialogTitle>
                    <DialogDescription>
                        Select a staff member to assign to this visit. This will
                        also initiate the medical order process.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="staff" class="text-right"> Staff </Label>
                        <Select v-model="assignForm.staff_id">
                            <SelectTrigger class="col-span-3">
                                <SelectValue
                                    placeholder="Select staff member"
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
</template>
