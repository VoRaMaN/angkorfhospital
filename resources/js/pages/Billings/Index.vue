<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatDate } from '@/lib/utils';
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
import { create, edit, letter, show } from '@/routes/billings';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { DollarSign, Edit, Eye, Plus, Printer, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useAuth } from '@/composables/useAuth';

interface Props {
    billings: {
        id: number;
        bill_no: string;
        patient_id: string | number;
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
        start_date?: string;
        end_date?: string;
    };
    overdueCount: number;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const searchQuery = ref(props.filters.search);
const statusFilter = ref(props.filters.status || '');
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');

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

const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
        case 'paid':        return 'bg-emerald-100 text-emerald-700 border border-emerald-200';
        case 'pending':     return 'bg-amber-100 text-amber-700 border border-amber-200';
        case 'overdue':     return 'bg-rose-100 text-rose-700 border border-rose-200';
        case 'partial':     return 'bg-indigo-100 text-indigo-700 border border-indigo-200';
        case 'written_off': return 'bg-slate-100 text-slate-500 border border-slate-200';
        case 'cancelled':   return 'bg-slate-100 text-slate-500 border border-slate-200';
        default:            return 'bg-slate-100 text-slate-500 border border-slate-200';
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
    const allStatuses = ['pending', 'paid', 'overdue', 'partial', 'cancelled'];
    return allStatuses.filter(status => status !== currentStatus);
};

const performSearch = () => {
    router.visit('/billings', {
        data: {
            search: searchQuery.value,
            status: statusFilter.value,
            start_date: startDate.value || undefined,
            end_date: endDate.value || undefined,
        },
        preserveState: true,
    });
};

// Debounce search for smooth typing
const debounceDelay = 500;
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
watch(searchQuery, () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        performSearch();
    }, debounceDelay);
});

watch([startDate, endDate, statusFilter], () => {
    // Instant search when date or status changes
    performSearch();
});

const clearDates = () => {
    startDate.value = '';
    endDate.value = '';
    performSearch();
};

const openStatusDialog = (billing: Props['billings'][0]) => {
    selectedBilling.value = billing;
    selectedStatus.value = '';
    isDialogOpen.value = true;
};

const exportBillings = () => {
    const params = new URLSearchParams();
    if (searchQuery.value) params.append('search', searchQuery.value);
    if (statusFilter.value) params.append('status', statusFilter.value);
    if (startDate.value) params.append('start_date', startDate.value);
    if (endDate.value) params.append('end_date', endDate.value);

    const url = `/billings-export?${params.toString()}`;
    window.open(url, '_blank');
};
</script>

<template>

    <Head title="Billings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_billings')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Billings</h1>
                    <p class="text-muted-foreground">
                        Manage patient billing records
                    </p>
                </div>
                <div class="ml-auto">
                    <Button v-if="hasPermission('create_billings')" as-child>
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
                        data-enter-nav-skip @keyup.enter="performSearch" />
                </div>
                <Button @click="performSearch" variant="outline">
                    Search
                </Button>
                <div class="flex gap-2 items-center">
                    <Input v-model="startDate" type="date" class="max-w-[200px]" placeholder="Start date" />
                    <Input v-model="endDate" type="date" class="max-w-[200px]" placeholder="End date" />
                    <Button variant="ghost" size="sm" @click="clearDates">Clear Dates</Button>
                    <Button variant="outline" size="sm" @click="exportBillings" v-if="hasPermission('view_billings')">
                        Export to CSV
                    </Button>
                </div>
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
                    :class="{ 'bg-destructive text-destructive-foreground': statusFilter === 'overdue', 'bg-primary text-primary-foreground': statusFilter !== 'overdue' && statusFilter === 'overdue' }" as-child>
                    <Link href="/billings?status=overdue" class="flex items-center gap-1">
                        Overdue
                        <span v-if="props.overdueCount > 0" class="inline-flex items-center justify-center size-5 rounded-full bg-red-500 text-white text-xs font-bold">{{ props.overdueCount > 9 ? '9+' : props.overdueCount }}</span>
                    </Link>
                </Button>
                <Button variant="outline" size="sm"
                    :class="{ 'bg-primary text-primary-foreground': statusFilter === 'partial' }" as-child>
                    <Link href="/billings?status=partial">Partial</Link>
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
                            <TableHead>Bill No</TableHead>
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
                            <TableCell class="font-medium">{{ billing.bill_no }}</TableCell>
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
                                <Badge :class="getStatusColor(billing.status)">
                                    {{ billing.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{
                                formatDate(billing.billing_date)
                            }}</TableCell>
                            <TableCell>{{
                                formatDate(billing.due_date)
                                }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="show(billing.id).url">
                                        <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button v-if="hasPermission('edit_billings')" variant="ghost" size="sm" as-child>
                                        <Link :href="edit(billing.id).url">
                                        <Edit class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <a
                                            :href="letter(billing.id).url"
                                            target="_blank"
                                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3"
                                        >
                                            <Printer class="size-4" />
                                            Print
                                        </a>
                                    </Button>
                                    <Button variant="ghost" size="sm" @click="openStatusDialog(billing)">
                                        Update Status
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.billings.length === 0">
                            <TableCell colspan="12" class="text-center text-muted-foreground">
                                No billings found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view billings.
                </p>
            </div>
        </div>
    </AppLayout>

    <Dialog v-model:open="isDialogOpen">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Update Billing Status</DialogTitle>
            </DialogHeader>
            <div v-if="selectedBilling" class="py-4">
                <p>Current Status: <Badge :class="getStatusColor(selectedBilling.status)">{{ selectedBilling.status
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
