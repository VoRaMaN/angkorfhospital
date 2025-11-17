<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
import { create, edit, show } from '@/routes/billings';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { DollarSign, Edit, Eye, Plus, Search } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    billings: {
        id: number;
        patient_id: number;
        patient_name: string;
        appointment_id?: number;
        visit_id?: number;
        medical_order_id?: number;
        total_amount: number;
        paid_amount: number;
        outstanding_amount: number;
        status: string;
        billing_date: string;
        due_date: string;
        created_at: string;
    }[];
    filters: {
        search: string;
        status: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search);
const statusFilter = ref(props.filters.status || '');

const isDialogOpen = ref(false);
const selectedBilling = ref<Props['billings'][0] | null>(null);
const selectedStatus = ref('');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billings',
        href: '/billings',
    },
];

// Format currency
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const getStatusVariant = (status: string) => {
    switch (status.toLowerCase()) {
        case 'paid':
            return 'default';
        case 'pending':
            return 'secondary';
        case 'overdue':
            return 'destructive';
        case 'written_off':
            return 'outline';
        case 'cancelled':
            return 'secondary';
        default:
            return 'secondary';
    }
};

const updateStatus = (billingId: number, newStatus: string) => {
    router.patch(`/billings/${billingId}/status`, { status: newStatus }, {
        preserveScroll: true,
        onSuccess: () => {
            isDialogOpen.value = false;
        },
    });
};

const getAvailableStatuses = (currentStatus: string) => {
    const allStatuses = ['pending', 'paid', 'overdue', 'partial', 'written_off', 'cancelled'];
    return allStatuses.filter(status => status !== currentStatus);
};

const performSearch = () => {
    router.visit('/billings', {
        data: {
            search: searchQuery.value,
            status: statusFilter.value,
        },
        preserveState: true,
    });
};

const openStatusDialog = (billing: Props['billings'][0]) => {
    selectedBilling.value = billing;
    selectedStatus.value = '';
    isDialogOpen.value = true;
};
</script>

<template>

    <Head title="Billings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Billings</h1>
                    <p class="text-muted-foreground">
                        Manage patient billing records
                    </p>
                </div>
                <div class="ml-auto">
                    <Button as-child>
                        <Link :href="create().url">
                        <Plus class="size-4" />
                        Add Billing
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative max-w-sm flex-1">
                    <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="searchQuery" placeholder="Search billings..." class="pl-9"
                        @keyup.enter="performSearch" />
                </div>
                <Button @click="performSearch" variant="outline">
                    Search
                </Button>
            </div>

            <!-- Status Filter Buttons -->
            <div class="flex flex-wrap gap-2">
                <Button variant="outline" size="sm" :class="{ 'bg-primary text-primary-foreground': !statusFilter }"
                    as-child>
                    <Link href="/billings">All</Link>
                </Button>
                <Button variant="outline" size="sm"
                    :class="{ 'bg-primary text-primary-foreground': statusFilter === 'pending' }" as-child>
                    <Link href="/billings?status=pending">Pending</Link>
                </Button>
                <Button variant="outline" size="sm"
                    :class="{ 'bg-primary text-primary-foreground': statusFilter === 'paid' }" as-child>
                    <Link href="/billings?status=paid">Paid</Link>
                </Button>
                <Button variant="outline" size="sm"
                    :class="{ 'bg-primary text-primary-foreground': statusFilter === 'overdue' }" as-child>
                    <Link href="/billings?status=overdue">Overdue</Link>
                </Button>
                <Button variant="outline" size="sm"
                    :class="{ 'bg-primary text-primary-foreground': statusFilter === 'partial' }" as-child>
                    <Link href="/billings?status=partial">Partial</Link>
                </Button>
                <Button variant="outline" size="sm"
                    :class="{ 'bg-primary text-primary-foreground': statusFilter === 'written_off' }" as-child>
                    <Link href="/billings?status=written_off">Written Off</Link>
                </Button>
                <Button variant="outline" size="sm"
                    :class="{ 'bg-primary text-primary-foreground': statusFilter === 'cancelled' }" as-child>
                    <Link href="/billings?status=cancelled">Cancelled</Link>
                </Button>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Appointment</TableHead>
                            <TableHead>Visit</TableHead>
                            <TableHead>Medical Order</TableHead>
                            <TableHead>Total Amount</TableHead>
                            <TableHead>Paid Amount</TableHead>
                            <TableHead>Outstanding</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Billing Date</TableHead>
                            <TableHead>Due Date</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="billing in props.billings" :key="billing.id">
                            <TableCell class="font-medium">{{
                                billing.patient_name
                                }}</TableCell>
                            <TableCell>{{ billing.appointment_id ? 'Yes' : 'No' }}</TableCell>
                            <TableCell>{{ billing.visit_id ? 'Yes' : 'No' }}</TableCell>
                            <TableCell>{{ billing.medical_order_id ? 'Yes' : 'No' }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-1">
                                    <DollarSign class="size-4 text-muted-foreground" />
                                    {{ formatCurrency(billing.total_amount) }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-1">
                                    <DollarSign class="size-4 text-green-600" />
                                    {{ formatCurrency(billing.paid_amount) }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-1">
                                    <DollarSign class="size-4 text-red-600" />
                                    {{
                                        formatCurrency(
                                            billing.outstanding_amount,
                                        )
                                    }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge :variant="getStatusVariant(billing.status)">
                                    {{ billing.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{
                                new Date(
                                    billing.billing_date,
                                ).toLocaleDateString()
                            }}</TableCell>
                            <TableCell>{{
                                new Date(billing.due_date).toLocaleDateString()
                                }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="show(billing.id).url">
                                        <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="edit(billing.id).url">
                                        <Edit class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" @click="openStatusDialog(billing)">
                                        Update Status
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.billings.length === 0">
                            <TableCell colspan="11" class="text-center text-muted-foreground">
                                No billings found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>

    <Dialog v-model:open="isDialogOpen">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Update Billing Status</DialogTitle>
            </DialogHeader>
            <div v-if="selectedBilling" class="py-4">
                <p>Current Status: <Badge :variant="getStatusVariant(selectedBilling.status)">{{ selectedBilling.status
                        }}</Badge>
                </p>
                <div class="mt-4">
                    <label for="status-select" class="block text-sm font-medium">New Status</label>
                    <Select v-model="selectedStatus">
                        <SelectTrigger>
                            <SelectValue placeholder="Select new status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="status in getAvailableStatuses(selectedBilling.status)" :key="status"
                                :value="status" class="capitalize">
                                {{ status.replace('_', ' ') }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter>
                <Button variant="outline" @click="isDialogOpen = false">Cancel</Button>
                <Button @click="updateStatus(selectedBilling!.id, selectedStatus)"
                    :disabled="!selectedStatus">Update</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
