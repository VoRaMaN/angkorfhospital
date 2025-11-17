<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import {
    confirmProcessed,
    edit,
    index,
} from '@/routes/medical-orders';
import { show as showMedicalRecord } from '@/routes/medical-records';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Activity,
    ChevronDown,
    ChevronRight,
    Edit,
    FileText,
    FlaskConical,
    Package,
    Pill,
    Play,
    Scan,
    Syringe,
    UserCheck,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface OrderItem {
    id: number;
    item_type: string;
    item_name: string;
    details?: string;
    dosage?: string;
    frequency?: string;
    route?: string;
    quantity_required: number;
    selling_price?: number;
    status: string;
    status_label: string;
    notes?: string;
    completed_at?: string;
    inventory_item?: {
        id: number;
        item_name: string;
        quantity: number;
    };
}

interface Props {
    medicalOrder: {
        id: number;
        patient_id: number;
        patient_name: string;
        staff_id: number;
        staff_name: string;
        order_details: string;
        status: string;
        status_label: string;
        priority: string;
        priority_label: string;
        notes?: string;
        ordered_at: string;
        completed_at?: string;
        created_at: string;
        updated_at: string;
        medical_record_id?: number;
        order_items: OrderItem[];
    };
    labPanels: {
        id: number;
        name: string;
        description: string;
        price: number;
        items: {
            id: number;
            item_name: string;
            quantity_required: number;
            notes?: string;
        }[];
    }[];
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Orders',
        href: index().url,
    },
    {
        title: 'Processing Order',
        href: '#',
    },
];

const expandedItems = ref<Set<number>>(new Set());

const toggleItemExpansion = (itemId: number) => {
    const newExpanded = new Set(expandedItems.value);
    if (newExpanded.has(itemId)) {
        newExpanded.delete(itemId);
    } else {
        newExpanded.add(itemId);
    }
    expandedItems.value = newExpanded;
};

const hasAdditionalInfo = (item: OrderItem) => {
    return !!(
        item.details ||
        item.notes ||
        item.dosage ||
        item.frequency ||
        item.route
    );
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        processed: 'bg-orange-100 text-orange-800',
        completed: 'bg-green-100 text-green-800',
        cancel: 'bg-gray-100 text-gray-800',
        rejected: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getPriorityColor = (priority: string) => {
    const colors: Record<string, string> = {
        routine: 'bg-gray-100 text-gray-800',
        urgent: 'bg-orange-100 text-orange-800',
        stat: 'bg-red-100 text-red-800',
    };
    return colors[priority] || 'bg-gray-100 text-gray-800';
};

const getItemTypeIcon = (type: string) => {
    const icons: Record<string, any> = {
        lab: FlaskConical,
        procedure: Syringe,
        imaging: Scan,
        consultation: UserCheck,
        therapy: Activity,
        supply: Package,
    };
    return icons[type] || Package;
};

const getItemTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        lab: 'Lab Test',
        procedure: 'Procedure',
        imaging: 'Imaging',
        consultation: 'Consultation',
        therapy: 'Therapy',
        supply: 'Supply',
    };
    return labels[type] || type;
};

const groupedOrderItems = computed(() => {
    const groups: Record<string, { item: OrderItem; panelName?: string }[]> =
        {};

    props.medicalOrder.order_items.forEach((item) => {
        let groupKey = item.item_type;
        let panelName: string | undefined;

        // For lab items, group by panel name instead of just "lab"
        if (item.item_type === 'lab' && item.inventory_item) {
            // Find which panel contains this lab item
            const panel = props.labPanels.find((p) =>
                p.items.some(
                    (panelItem) => panelItem.id === item.inventory_item!.id,
                ),
            );
            if (panel) {
                groupKey = `lab-${panel.id}`;
                panelName = panel.name;
            }
        }

        if (!groups[groupKey]) {
            groups[groupKey] = [];
        }
        groups[groupKey].push({ item, panelName });
    });

    return groups;
});

const getItemTypeDisplayName = (type: string, panelName?: string) => {
    if (panelName) {
        return panelName;
    }

    const names: Record<string, string> = {
        lab: 'Lab Tests',
        procedure: 'Procedures',
        imaging: 'Imaging',
        supply: 'Supplies',
    };
    return names[type] || type;
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const getItemPrice = (item: OrderItem) => {
    if (item.selling_price) {
        return item.selling_price * item.quantity_required;
    }
    return 0;
};

const orderTotalPrice = computed(() => {
    return props.medicalOrder.order_items.reduce((total, item) => {
        return total + getItemPrice(item);
    }, 0);
});

const orderSummary = computed(() => {
    const summary = {
        total: props.medicalOrder.order_items.length,
        lab: 0,
        rx_medicine: 0,
        procedure: 0,
        imaging: 0,
        supply: 0,
    };

    props.medicalOrder.order_items.forEach((item) => {
        if (item.item_type === 'lab') summary.lab++;
        else if (item.item_type === 'rx_medicine') summary.rx_medicine++;
        else if (item.item_type === 'procedure') summary.procedure++;
        else if (item.item_type === 'imaging') summary.imaging++;
        else if (item.item_type === 'supply') summary.supply++;
    });

    return summary;
});

const processOrder = () => {
    if (confirm('Are you sure you want to mark this medical order as processed?')) {
        router.patch(confirmProcessed(props.medicalOrder.id).url, {}, {
            onSuccess: () => {
                // Redirect or refresh handled by Inertia
            },
        });
    }
};
</script>

<template>

    <Head title="Processing Medical Order" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_medical_orders')"
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">
                        Processing Medical Order
                    </h1>
                    <p class="text-muted-foreground">
                        Review order details and process the order
                    </p>
                </div>
                <div class="ml-auto flex gap-2">
                    <Button v-if="medicalOrder.medical_record_id" variant="outline" as-child>
                        <Link :href="showMedicalRecord(
                            medicalOrder.medical_record_id,
                        ).url
                            ">
                        <FileText class="mr-2 size-4" />
                        View Medical Record
                        </Link>
                    </Button>
                    <Button v-if="
                        medicalOrder.status === 'processing' &&
                        hasPermission('process_medical_orders')
                    " variant="default" @click="processOrder">
                        <Play class="mr-2 size-4" />
                        Confirm Processed
                    </Button>
                    <Button v-if="hasPermission('edit_medical_orders')" variant="outline" as-child>
                        <Link :href="edit(medicalOrder.id).url">
                        <Edit class="size-4" />
                        Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Order Information Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Order Information</CardTitle>
                        <CardDescription>Basic details about this medical
                            order</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Patient
                                </dt>
                                <dd class="text-sm font-medium">
                                    {{ medicalOrder.patient_name }}
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Ordering Staff
                                </dt>
                                <dd class="text-sm font-medium">
                                    {{ medicalOrder.staff_name }}
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Status
                                </dt>
                                <dd class="text-sm">
                                    <Badge :class="getStatusColor(medicalOrder.status)
                                        ">
                                        {{ medicalOrder.status_label }}
                                    </Badge>
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Priority
                                </dt>
                                <dd class="text-sm">
                                    <Badge :class="getPriorityColor(
                                        medicalOrder.priority,
                                    )
                                        ">
                                        {{ medicalOrder.priority_label }}
                                    </Badge>
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Ordered At
                                </dt>
                                <dd class="text-sm">
                                    {{ medicalOrder.ordered_at }}
                                </dd>
                            </div>

                            <div v-if="medicalOrder.completed_at" class="space-y-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Completed At
                                </dt>
                                <dd class="text-sm">
                                    {{ medicalOrder.completed_at }}
                                </dd>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Order Details
                                </dt>
                                <dd class="text-sm">
                                    {{ medicalOrder.order_details }}
                                </dd>
                            </div>

                            <div v-if="medicalOrder.notes" class="space-y-2 md:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Notes
                                </dt>
                                <dd class="text-sm">
                                    {{ medicalOrder.notes }}
                                </dd>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Order Summary -->
                <Card v-if="orderSummary.total > 0"
                    class="border-green-200 bg-green-50/50 dark:border-green-800 dark:bg-green-950/20">
                    <CardContent class="pt-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Package class="size-5 text-green-600 dark:text-green-400" />
                                <span class="font-medium text-green-800 dark:text-green-200">Order Summary</span>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-green-800 dark:text-green-200">
                                    {{ orderSummary.total }}
                                </div>
                                <div class="text-sm text-green-600 dark:text-green-400">
                                    {{ formatPrice(orderTotalPrice) }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-5 gap-4 text-sm">
                            <div v-if="orderSummary.lab > 0" class="flex items-center gap-1">
                                <FlaskConical class="size-4 text-blue-600" />
                                <span>{{ orderSummary.lab }} Lab</span>
                            </div>
                            <div v-if="orderSummary.rx_medicine > 0" class="flex items-center gap-1">
                                <Pill class="size-4 text-purple-600" />
                                <span>{{ orderSummary.rx_medicine }} RX</span>
                            </div>
                            <div v-if="orderSummary.procedure > 0" class="flex items-center gap-1">
                                <Syringe class="size-4 text-orange-600" />
                                <span>{{ orderSummary.procedure }} Proc</span>
                            </div>
                            <div v-if="orderSummary.imaging > 0" class="flex items-center gap-1">
                                <Scan class="size-4 text-red-600" />
                                <span>{{ orderSummary.imaging }} Imaging</span>
                            </div>
                            <div v-if="orderSummary.supply > 0" class="flex items-center gap-1">
                                <Package class="size-4 text-gray-600" />
                                <span>{{ orderSummary.supply }} Supply</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Order Items Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Order Items ({{
                            medicalOrder.order_items.length
                        }})</CardTitle>
                        <CardDescription>Lab tests, procedures, imaging, and
                            supplies</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="medicalOrder.order_items.length === 0"
                            class="py-12 text-center text-muted-foreground">
                            <p>No items in this order</p>
                        </div>

                        <div v-else class="space-y-6">
                            <div v-for="[type, items] in Object.entries(
                                groupedOrderItems,
                            )" :key="type" class="space-y-3">
                                <!-- Group Header -->
                                <div class="flex items-center gap-3 border-b pb-2">
                                    <component :is="getItemTypeIcon(
                                        items[0]?.panelName
                                            ? 'lab'
                                            : type,
                                    )
                                        " class="size-5 text-primary" />
                                    <h3 class="text-lg font-semibold">
                                        {{
                                            getItemTypeDisplayName(
                                                type,
                                                items[0]?.panelName,
                                            )
                                        }}
                                    </h3>
                                    <Badge variant="secondary" class="ml-auto">{{ items.length }}</Badge>
                                </div>

                                <!-- Items in this group -->
                                <div class="ml-8 space-y-2">
                                    <div v-for="itemData in items" :key="itemData.item.id"
                                        class="overflow-hidden rounded-lg border">
                                        <!-- Collapsible Header -->
                                        <div class="flex cursor-pointer items-center justify-between p-4 transition-colors hover:bg-muted/50"
                                            @click="
                                                toggleItemExpansion(
                                                    itemData.item.id,
                                                )
                                                ">
                                            <div class="flex items-center gap-3">
                                                <component :is="getItemTypeIcon(
                                                    itemData.item
                                                        .item_type,
                                                )
                                                    " class="size-6 text-primary" />
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-medium">{{
                                                            itemData.item
                                                                .item_name
                                                        }}</span>
                                                        <Badge variant="outline" class="text-xs">
                                                            {{
                                                                getItemTypeLabel(
                                                                    itemData
                                                                        .item
                                                                        .item_type,
                                                                )
                                                            }}
                                                        </Badge>
                                                        <Badge v-if="
                                                            hasAdditionalInfo(
                                                                itemData.item,
                                                            )
                                                        " variant="secondary" class="text-xs">
                                                            Has Details
                                                        </Badge>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="text-right">
                                                    <div class="text-sm font-medium">
                                                        {{
                                                            formatPrice(
                                                                getItemPrice(
                                                                    itemData.item,
                                                                ),
                                                            )
                                                        }}
                                                    </div>
                                                    <div v-if="
                                                        itemData.item
                                                            .quantity_required >
                                                        1
                                                    " class="text-xs text-muted-foreground">
                                                        {{
                                                            itemData.item
                                                                .quantity_required
                                                        }}
                                                        ×
                                                        {{
                                                            formatPrice(
                                                                itemData.item
                                                                    .selling_price ||
                                                                0,
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                                <Badge :class="getStatusColor(
                                                    itemData.item
                                                        .status,
                                                )
                                                    ">
                                                    {{
                                                        itemData.item
                                                            .status_label
                                                    }}
                                                </Badge>
                                                <component :is="expandedItems.has(
                                                    itemData.item.id,
                                                )
                                                    ? ChevronDown
                                                    : ChevronRight
                                                    " class="size-4 text-muted-foreground" />
                                            </div>
                                        </div>

                                        <!-- Collapsible Content -->
                                        <div v-if="
                                            expandedItems.has(
                                                itemData.item.id,
                                            )
                                        " class="space-y-3 border-t bg-muted/20 px-4 py-3">
                                            <!-- Details -->
                                            <div v-if="itemData.item.details" class="text-sm">
                                                <span class="text-muted-foreground">Details:</span>
                                                <p class="mt-1">
                                                    {{ itemData.item.details }}
                                                </p>
                                            </div>

                                            <!-- Medical Details -->
                                            <div v-if="
                                                itemData.item.dosage ||
                                                itemData.item.frequency ||
                                                itemData.item.route
                                            " class="grid gap-4 text-sm md:grid-cols-3">
                                                <div v-if="itemData.item.dosage">
                                                    <span class="text-muted-foreground">Dosage:</span>
                                                    <span class="ml-1 font-medium">{{
                                                        itemData.item.dosage
                                                    }}</span>
                                                </div>
                                                <div v-if="
                                                    itemData.item.frequency
                                                ">
                                                    <span class="text-muted-foreground">Frequency:</span>
                                                    <span class="ml-1 font-medium">{{
                                                        itemData.item
                                                            .frequency
                                                    }}</span>
                                                </div>
                                                <div v-if="itemData.item.route">
                                                    <span class="text-muted-foreground">Route:</span>
                                                    <span class="ml-1 font-medium">{{
                                                        itemData.item.route
                                                    }}</span>
                                                </div>
                                            </div>

                                            <!-- Quantity & Inventory -->
                                            <div class="flex items-center gap-6 text-sm">
                                                <div>
                                                    <span class="text-muted-foreground">Quantity:</span>
                                                    <span class="ml-1 font-medium">{{
                                                        itemData.item
                                                            .quantity_required
                                                    }}</span>
                                                </div>
                                                <div v-if="
                                                    itemData.item
                                                        .completed_at
                                                ">
                                                    <span class="text-muted-foreground">Completed:</span>
                                                    <span class="ml-1 font-medium">{{
                                                        itemData.item
                                                            .completed_at
                                                    }}</span>
                                                </div>
                                            </div>

                                            <!-- Notes -->
                                            <div v-if="itemData.item.notes" class="text-sm">
                                                <span class="text-muted-foreground">Notes:</span>
                                                <p class="mt-1 italic">
                                                    {{ itemData.item.notes }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Metadata Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Record Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Created At
                                </dt>
                                <dd class="text-sm">
                                    {{
                                        new Date(
                                            medicalOrder.created_at,
                                        ).toLocaleString()
                                    }}
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt class="text-sm font-medium text-muted-foreground">
                                    Last Updated
                                </dt>
                                <dd class="text-sm">
                                    {{
                                        new Date(
                                            medicalOrder.updated_at,
                                        ).toLocaleString()
                                    }}
                                </dd>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
        <div v-else class="flex h-full flex-1 items-center justify-center">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">
                    Access Denied
                </h2>
                <p class="text-muted-foreground">
                    You do not have permission to view medical orders.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
