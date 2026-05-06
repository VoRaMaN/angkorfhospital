<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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
import {
    create as labPanelCreate,
    edit as labPanelEdit,
    index as labPanelIndex,
    show as labPanelShow,
} from '@/routes/lab-panels';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { FlaskConical, Plus, Search, Clock, AlertCircle, CheckCircle2, ClipboardEdit, FileText, Eye } from 'lucide-vue-next';

import { computed, ref, watch, reactive } from 'vue';
import { useAuth } from '@/composables/useAuth';
import OPUReportDialog from './OPUReportDialog.vue';

interface LabItem {
    id: number;
    item_name: string;
    details: string;
    status: string;
    status_label: string;
    result_value: string | null;
    result_unit: string | null;
    result_notes: string | null;
}

interface ActiveLabOrder {
    id: number;
    patient_id: string | null;
    patient_name: string;
    patient_id_card: string | null;
    patient_dob: string | null;
    patient_phone: string | null;
    staff_name: string;
    staff_id: number | null;
    status: string;
    status_label: string;
    priority: string;
    priority_label: string;
    ordered_at: string;
    opu_report_id: number | null;
    lab_items: LabItem[];
}

interface Props {
    labPanels: {
        data: Array<{
            id: number;
            name: string;
            description: string;
            price: number;
            is_active: boolean;
            inventory_items_count: number;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        current_page: number;
        last_page: number;
    };
    activeLabOrders: ActiveLabOrder[];
    categories: string[];
    currentStaff: { id: number; name: string } | null;
    filters: {
        search: string;
        category: string;
        status: string;
        lab_start_date: string;
        lab_end_date: string;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lab Panels',
        href: '#',
    },
];

const labStartDate = ref(props.filters.lab_start_date || new Date().toISOString().slice(0, 10));
const labEndDate = ref(props.filters.lab_end_date || new Date().toISOString().slice(0, 10));

const searchLabOrders = () => {
    router.get(
        labPanelIndex().url,
        { ...props.filters, lab_start_date: labStartDate.value, lab_end_date: labEndDate.value },
        { preserveState: true, replace: true },
    );
};
const searchQuery = ref(props.filters.search);
const selectedStatus = ref(props.filters.status || null);

watch(searchQuery, (value) => {
    updateFilter('search', value);
});

watch(selectedStatus, (value) => {
    updateFilter('status', value || '');
});

const updateFilter = (key: string, value: string) => {
    router.get(
        labPanelIndex().url,
        { ...props.filters, [key]: value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const paginationLinks = computed(() => {
    return props.labPanels.links.filter((link) => link.url);
});

const statusBadgeClass = (status: string) => {
    switch (status) {
        case 'pending':    return 'bg-amber-100 text-amber-700 border border-amber-200';
        case 'processing': return 'bg-sky-100 text-sky-700 border border-sky-200';
        case 'completed':  return 'bg-emerald-100 text-emerald-700 border border-emerald-200';
        default:           return 'bg-slate-100 text-slate-500 border border-slate-200';
    }
};

const priorityBadgeClass = (priority: string) => {
    switch (priority) {
        case 'urgent': return 'bg-orange-100 text-orange-700 border border-orange-200';
        case 'high':   return 'bg-orange-100 text-orange-700 border border-orange-200';
        case 'stat':   return 'bg-rose-100 text-rose-700 border border-rose-200';
        case 'normal': return 'bg-blue-100 text-blue-600 border border-blue-200';
        case 'routine':return 'bg-blue-100 text-blue-600 border border-blue-200';
        case 'low':    return 'bg-slate-100 text-slate-500 border border-slate-200';
        default:       return 'bg-slate-100 text-slate-500 border border-slate-200';
    }
};

const activeResultForm = ref<number | null>(null);
// Store form data per item id
const resultForms = reactive<Record<number, { result_value: string; result_unit: string; result_notes: string }>>({})

const openResultForm = (item: LabItem) => {
    activeResultForm.value = item.id;
    // Always refresh so editing re-populates with current saved values
    resultForms[item.id] = {
        result_value: item.result_value ?? '',
        result_unit: item.result_unit ?? '',
        result_notes: item.result_notes ?? '',
    };
};

const saveResult = (orderId: number, itemId: number) => {
    const form = resultForms[itemId];
    router.patch(`/medical-orders/${orderId}/items/${itemId}/lab-result`, form, {
        preserveScroll: true,
        onSuccess: () => { activeResultForm.value = null; },
    });
};

// ─── OPU Report Dialog ────────────────────────────────────────────────────────
const opuDialogOpen = ref(false);
const opuOrderContext = ref<ActiveLabOrder | null>(null);
const opuExistingReport = ref<any>(null);

const openOPUDialog = async (order: ActiveLabOrder) => {
    opuOrderContext.value = order;
    opuExistingReport.value = null;
    if (order.opu_report_id) {
        // Load existing report
        try {
            const resp = await fetch(`/opu-reports/order/${order.id}`);
            // Inertia returns HTML — we use a simple JSON fetch via the search-patients pattern
            // Instead, we pass the data inline via the opu_report_id existence signal
            // Full data will be loaded from the server via Inertia visit for view
        } catch {/* ignore */}
    }
    opuDialogOpen.value = true;
};

const onOPUSaved = () => {
    router.reload({ only: ['activeLabOrders'], preserveScroll: true });
};

const viewOPUReport = (orderId: number) => {
    router.visit(`/opu-reports/order/${orderId}`);
};
</script>

<template>
    <Head title="Lab Panels" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_lab_panels')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Lab Panels</h1>
                    <p class="text-muted-foreground">
                        Manage laboratory test panels and their required
                        supplies
                    </p>
                </div>
                <Button v-if="hasPermission('create_lab_panels')" as-child>
                    <Link :href="labPanelCreate().url">
                        <Plus class="size-4" />
                        Add Panel
                    </Link>
                </Button>
            </div>

            <!-- Active Lab Orders - Lab Staff Reminder -->
            <div class="rounded-xl border-2 border-blue-200 bg-blue-50/50 p-4 dark:border-blue-900 dark:bg-blue-950/30">
                <!-- Date Range Filter -->
                <div class="mb-4 flex flex-wrap items-end gap-3">
                    <div>
                        <Label class="text-xs text-muted-foreground">From</Label>
                        <Input v-model="labStartDate" type="date" class="mt-1 h-8 w-36 text-sm" />
                    </div>
                    <div>
                        <Label class="text-xs text-muted-foreground">To</Label>
                        <Input v-model="labEndDate" type="date" class="mt-1 h-8 w-36 text-sm" />
                    </div>
                    <Button size="sm" class="h-8" @click="searchLabOrders">
                        <Search class="mr-1.5 size-3.5" />
                        Search
                    </Button>
                    <Button
                        size="sm" variant="ghost" class="h-8 text-xs"
                        @click="labStartDate = new Date().toISOString().slice(0, 10); labEndDate = new Date().toISOString().slice(0, 10); searchLabOrders()"
                    >
                        Today
                    </Button>
                </div>

                <div class="mb-4 flex items-center justify-between">
                    <h2 class="flex items-center gap-2 text-lg font-semibold">
                        <span class="relative flex size-3">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex size-3 rounded-full bg-blue-500"></span>
                        </span>
                        Lab Orders
                        <Badge variant="secondary" class="ml-1">{{ props.activeLabOrders.length }}</Badge>
                    </h2>
                    <p class="text-xs text-muted-foreground flex items-center gap-1">
                        <Clock class="size-3" />
                        Showing {{ labStartDate === labEndDate ? labStartDate : labStartDate + ' â†’ ' + labEndDate }}
                    </p>
                </div>
                <div v-if="props.activeLabOrders.length === 0" class="rounded-xl border border-dashed p-6 text-center text-muted-foreground">
                    <FlaskConical class="mx-auto mb-2 size-8 opacity-40" />
                    <p class="text-sm">No lab orders found for this date range</p>
                </div>
                <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="order in props.activeLabOrders"
                        :key="order.id"
                    >
                    <Card
                        :class="[
                            'relative transition-shadow hover:shadow-md hover:border-blue-400',
                            order.priority === 'urgent' || order.priority === 'stat'
                                ? 'border-red-300 dark:border-red-800'
                                : ''
                        ]"
                    >
                        <div v-if="order.priority === 'urgent' || order.priority === 'stat'"
                            class="absolute top-0 right-0 left-0 h-1 rounded-t-xl bg-red-500"
                        />
                        <CardHeader class="pb-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <CardTitle class="text-base">{{ order.patient_name }}</CardTitle>
                                    <div class="mt-1 space-y-0.5 text-xs text-muted-foreground">
                                        <p v-if="order.patient_id_card">ID: {{ order.patient_id_card }}</p>
                                        <p v-if="order.patient_dob">DOB: {{ order.patient_dob }}</p>
                                        <p v-if="order.patient_phone">Tel: {{ order.patient_phone }}</p>
                                    </div>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        Order #{{ order.id }} &middot; {{ order.ordered_at }}
                                    </p>
                                </div>
                                <div class="flex gap-1.5">
                                    <Badge :class="priorityBadgeClass(order.priority)">
                                        <AlertCircle v-if="order.priority === 'urgent' || order.priority === 'stat'" class="mr-1 size-3" />
                                        {{ order.priority_label }}
                                    </Badge>
                                    <Badge :class="statusBadgeClass(order.status)">
                                        {{ order.status_label }}
                                    </Badge>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Ordered by Dr. {{ order.staff_name }}
                            </p>
                            <!-- OPU Report button -->
                            <div class="mt-2 flex gap-2">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    class="h-7 gap-1 border-blue-200 bg-blue-50 px-2 text-xs text-blue-700 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-300"
                                    @click.stop="openOPUDialog(order)"
                                >
                                    <FileText class="size-3" />
                                    {{ order.opu_report_id ? 'Edit OPU Report' : 'Input OPU Report' }}
                                </Button>
                                <Button
                                    v-if="order.opu_report_id"
                                    size="sm"
                                    variant="ghost"
                                    class="h-7 gap-1 px-2 text-xs text-muted-foreground"
                                    @click.stop="viewOPUReport(order.id)"
                                >
                                    <Eye class="size-3" />
                                    View
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="mb-2 text-xs font-medium text-muted-foreground uppercase tracking-wide">Lab Items ({{ order.lab_items.length }})</p>
                            <div class="space-y-3">
                                <div
                                    v-for="item in order.lab_items"
                                    :key="item.id"
                                    :class="[
                                        'rounded-md border px-3 py-2 text-sm',
                                        item.status === 'completed' ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950/50' : '',
                                        item.status === 'processing' ? 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950/50' : '',
                                        item.status === 'pending' ? 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950/50' : '',
                                    ]"
                                >
                                    <!-- Item header row -->
                                    <div class="flex items-center justify-between gap-2">
                                        <div>
                                            <p class="font-medium">{{ item.item_name }}</p>
                                            <p v-if="item.details" class="text-xs text-muted-foreground">{{ item.details }}</p>
                                            <!-- Show saved result -->
                                            <p v-if="item.result_value && item.status === 'completed'" class="mt-1 text-xs font-semibold text-green-700 dark:text-green-400">
                                                Result: {{ item.result_value }}<span v-if="item.result_unit"> {{ item.result_unit }}</span>
                                            </p>
                                            <p v-if="item.result_notes && item.status === 'completed'" class="text-xs text-muted-foreground italic">{{ item.result_notes }}</p>
                                        </div>
                                        <div class="flex shrink-0 items-center gap-1.5">
                                            <Badge :class="[statusBadgeClass(item.status), 'text-xs']">
                                                {{ item.status_label }}
                                            </Badge>
                                            <Button
                                                v-if="item.status !== 'completed'"
                                                size="sm"
                                                variant="outline"
                                                class="h-7 px-2 text-xs"
                                                @click.stop="openResultForm(item)"
                                            >
                                                <ClipboardEdit class="mr-1 size-3" />
                                                Input Result
                                            </Button>
                                            <Button
                                                v-else
                                                size="sm"
                                                variant="ghost"
                                                class="h-7 px-2 text-xs text-muted-foreground"
                                                @click.stop="openResultForm(item)"
                                            >
                                                <ClipboardEdit class="mr-1 size-3" />
                                                Edit
                                            </Button>
                                        </div>
                                    </div>

                                    <!-- Inline result form -->
                                    <div
                                        v-if="activeResultForm === item.id"
                                        class="mt-3 space-y-2 border-t pt-3"
                                        @click.stop
                                    >
                                        <div class="flex gap-2">
                                            <div class="flex-1">
                                                <Label class="text-xs">Result Value</Label>
                                                <Input
                                                    v-model="resultForms[item.id].result_value"
                                                    placeholder="e.g. 5.2, Negative, Normal"
                                                    class="mt-1 h-8 text-sm"
                                                />
                                            </div>
                                            <div class="w-24">
                                                <Label class="text-xs">Unit</Label>
                                                <Input
                                                    v-model="resultForms[item.id].result_unit"
                                                    placeholder="g/dL, mmolâ€¦"
                                                    class="mt-1 h-8 text-sm"
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <Label class="text-xs">Notes (optional)</Label>
                                            <Textarea
                                                v-model="resultForms[item.id].result_notes"
                                                placeholder="Additional notesâ€¦"
                                                class="mt-1 min-h-[60px] text-sm"
                                            />
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                class="h-7 text-xs"
                                                @click.stop="activeResultForm = null"
                                            >
                                                Cancel
                                            </Button>
                                            <Button
                                                size="sm"
                                                class="h-7 bg-green-600 text-xs hover:bg-green-700"
                                                @click.stop="saveResult(order.id, item.id)"
                                            >
                                                <CheckCircle2 class="mr-1 size-3" />
                                                Save & Complete
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center">
                <div class="flex-1">
                    <div class="relative">
                        <Search
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchQuery"
                            placeholder="Search lab panels..."
                            class="pl-9"
                        />
                    </div>
                </div>
                <div class="flex gap-2">
                    <Select v-model="selectedStatus">
                        <SelectTrigger class="w-40">
                            <SelectValue placeholder="All Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="inactive">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Price</TableHead>
                            <TableHead>Items</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="labPanel in props.labPanels.data"
                            :key="labPanel.id"
                            class="cursor-pointer hover:bg-muted/50"
                            @click="router.visit(labPanelShow(labPanel.id).url)"
                        >
                            <TableCell>{{ labPanel.name }}</TableCell>
                            <TableCell>{{ labPanel.description }}</TableCell>
                            <TableCell>${{ labPanel.price }}</TableCell>
                            <TableCell
                                >{{
                                    labPanel.inventory_items_count
                                }}
                                items</TableCell
                            >
                            <TableCell>
                                <Badge
                                    :variant="
                                        labPanel.is_active
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        labPanel.is_active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell @click.stop>
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                labPanelShow(labPanel.id).url
                                            "
                                            >View</Link
                                        >
                                    </Button>
                                    <Button
                                        v-if="hasPermission('edit_lab_panels')"
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                labPanelEdit(labPanel.id).url
                                            "
                                            >Edit</Link
                                        >
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.labPanels.length === 0">
                            <TableCell
                                colspan="6"
                                class="text-center text-muted-foreground"
                            >
                                No lab panels found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                class="flex justify-center gap-2"
                v-if="props.labPanels.last_page > 1"
            >
                <Button
                    v-for="link in paginationLinks"
                    :key="link.label"
                    :variant="link.active ? 'default' : 'outline'"
                    as-child
                >
                    <Link :href="link.url!">{{ link.label }}</Link>
                </Button>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view lab panels.
                </p>
            </div>
        </div>
    </AppLayout>

    <!-- OPU Report Dialog -->
    <OPUReportDialog
        v-model="opuDialogOpen"
        :order-id="opuOrderContext?.id ?? 0"
        :female-patient-id="opuOrderContext?.patient_id ?? null"
        :female-patient-name="opuOrderContext?.patient_name ?? ''"
        :female-patient-dob="opuOrderContext?.patient_dob ?? null"
        :doctor-staff-id="opuOrderContext?.staff_id ?? null"
        :doctor-staff-name="opuOrderContext?.staff_name ?? null"
        :existing-report="opuExistingReport"
        @saved="onOPUSaved"
    />
</template>
