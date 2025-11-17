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
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    completeItem,
    index,
    processAndBill as processAndBillRoute,
    sendBack,
} from '@/routes/medical-orders';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    Activity,
    ArrowLeft,
    CheckCircle,
    CheckCircle2,
    FlaskConical,
    Package,
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
        order_items: OrderItem[];
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();
const page = usePage();

// Check for flash messages
const flashError = computed(() => (page.props as any)?.flash?.error);
const flashSuccess = computed(() => (page.props as any)?.flash?.success);

// Create a local reactive copy of the medical order data
const medicalOrder = ref({
    ...props.medicalOrder,
    order_items: [...props.medicalOrder.order_items],
});

const expandedItems = ref<Set<number>>(new Set());
const showCompleteDialog = ref(false);
const showItemCompleteDialog = ref(false);
const showSuccessDialog = ref(false);
const showCompleteAllDialog = ref(false);
const showSendBackDialog = ref(false);
const successMessage = ref('');
const sendBackReason = ref('');
const itemToComplete = ref<OrderItem | null>(null);
const completingOrder = ref(false);
const completingItem = ref(false);
const completingAllItems = ref(false);

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

const orderSummary = computed(() => {
    const summary = {
        total: medicalOrder.value.order_items.length,
        completed: 0,
        pending: 0,
        lab: 0,
        rx_medicine: 0,
        procedure: 0,
        imaging: 0,
        supply: 0,
    };

    medicalOrder.value.order_items.forEach((item) => {
        if (item.status === 'completed') summary.completed++;
        else summary.pending++;

        if (item.item_type === 'lab') summary.lab++;
        else if (item.item_type === 'rx_medicine') summary.rx_medicine++;
        else if (item.item_type === 'procedure') summary.procedure++;
        else if (item.item_type === 'imaging') summary.imaging++;
        else if (item.item_type === 'supply') summary.supply++;
    });

    return summary;
});

const allItemsCompleted = computed(() => {
    return orderSummary.value.pending === 0;
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Orders',
        href: index().url,
    },
    {
        title: allItemsCompleted.value ? 'Process Bill' : 'Complete Order',
        href: '#',
    },
];

const confirmProcessBill = () => {
    showCompleteDialog.value = true;
};

const processBill = () => {
    completingOrder.value = true;
    router.patch(
        processAndBillRoute(medicalOrder.value.id).url,
        {},
        {
            onSuccess: () => {
                completingOrder.value = false;
                showCompleteDialog.value = false;
                successMessage.value =
                    'Medical order has been processed and billed successfully!';
                showSuccessDialog.value = true;
            },
            onError: (errors) => {
                completingOrder.value = false;
                console.error('Process bill error:', errors);
                // Show a generic error message
                successMessage.value = 'Failed to process bill. Please check the order status and try again.';
                showSuccessDialog.value = true;
            },
        },
    );
};

const confirmCompleteItem = (item: OrderItem) => {
    itemToComplete.value = item;
    showItemCompleteDialog.value = true;
};

const confirmCompleteAllItems = () => {
    showCompleteAllDialog.value = true;
};

const completeAllItems = async () => {
    completingAllItems.value = true;
    showCompleteAllDialog.value = false;

    const pendingItems = medicalOrder.value.order_items.filter(
        (item) => item.status !== 'completed',
    );
    let completedCount = 0;

    try {
        // Complete all pending items sequentially
        for (const item of pendingItems) {
            await new Promise((resolve, reject) => {
                router.patch(
                    completeItem({
                        medical_order: medicalOrder.value.id,
                        item: item.id,
                    }).url,
                    {},
                    {
                        onSuccess: () => {
                            // Update the item status locally
                            const itemIndex =
                                medicalOrder.value.order_items.findIndex(
                                    (orderItem) => orderItem.id === item.id,
                                );
                            if (itemIndex !== -1) {
                                medicalOrder.value.order_items[
                                    itemIndex
                                ].status = 'completed';
                                medicalOrder.value.order_items[
                                    itemIndex
                                ].status_label = 'Completed';
                                medicalOrder.value.order_items[
                                    itemIndex
                                ].completed_at = new Date()
                                    .toISOString()
                                    .slice(0, 19)
                                    .replace('T', ' ');
                            }
                            completedCount++;
                            resolve(true);
                        },
                        onError: () => {
                            reject(
                                new Error(
                                    `Failed to complete ${item.item_name}`,
                                ),
                            );
                        },
                    },
                );
            });
        }

        successMessage.value = `Order completed successfully!`;
        showSuccessDialog.value = true;
    } catch (error) {
        console.error('Error completing all items:', error);
        successMessage.value = `Failed to complete order. ${completedCount} items were completed successfully.`;
        showSuccessDialog.value = true;
    } finally {
        completingAllItems.value = false;
    }
};

const completeItemAction = () => {
    if (!itemToComplete.value) return;

    completingItem.value = true;
    router.patch(
        completeItem({
            medical_order: medicalOrder.value.id,
            item: itemToComplete.value.id,
        }).url,
        {},
        {
            onSuccess: () => {
                completingItem.value = false;
                showItemCompleteDialog.value = false;

                // Update the item status locally instead of reloading
                const item = itemToComplete.value;
                if (item) {
                    const itemIndex = medicalOrder.value.order_items.findIndex(
                        (orderItem) => orderItem.id === item.id,
                    );
                    if (itemIndex !== -1) {
                        medicalOrder.value.order_items[itemIndex].status =
                            'completed';
                        medicalOrder.value.order_items[itemIndex].status_label =
                            'Completed';
                        medicalOrder.value.order_items[itemIndex].completed_at =
                            new Date()
                                .toISOString()
                                .slice(0, 19)
                                .replace('T', ' ');
                    }
                }

                successMessage.value = `"${item!.item_name}" has been completed successfully!`;
                showSuccessDialog.value = true;
                itemToComplete.value = null;
            },
            onError: () => {
                completingItem.value = false;
            },
        },
    );
};

const sendBackOrder = () => {
    if (!sendBackReason.value.trim()) {
        alert('Please provide a reason for sending back the order.');
        return;
    }

    if (confirm('Are you sure you want to send this medical order back for revision?')) {
        router.patch(sendBack(medicalOrder.value.id).url, {
            reason: sendBackReason.value.trim(),
        }, {
            onSuccess: () => {
                showSendBackDialog.value = false;
                sendBackReason.value = '';
                // Reload the page to reflect the updated item statuses
                window.location.reload();
            },
        });
    }
};
</script>

<template>

    <Head :title="allItemsCompleted ? 'Process Bill' : 'Complete Order'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('edit_medical_orders') || hasPermission('view_medical_orders')"
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            
            <!-- Flash Messages -->
            <div v-if="flashError" class="rounded-md border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/20">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            Error
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <p>{{ flashError }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-if="flashSuccess" class="rounded-md border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950/20">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                            Success
                        </h3>
                        <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                            <p>{{ flashSuccess }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">{{ allItemsCompleted ? 'Process Bill' : 'Complete Order' }}</h1>
                    <p class="text-muted-foreground">
                        {{ allItemsCompleted ? 'Mark items as completed and process the bill' : 'Complete the remaining order items' }}
                    </p>
                </div>
                <div class="ml-auto flex gap-2">
                    <Button v-if="hasPermission('process_medical_orders')"
                        variant="destructive"
                        @click="showSendBackDialog = true"
                    >
                        <ArrowLeft class="mr-2 size-4" />
                        Send Back
                    </Button>
                    <Button v-if="orderSummary.pending > 0" variant="outline" @click="confirmCompleteAllItems"
                        :disabled="completingAllItems || completingItem">
                        <CheckCircle class="mr-2 size-4" />
                        {{
                            completingAllItems
                                ? 'Completing Order...'
                                : `Complete Order (${orderSummary.pending})`
                        }}
                    </Button>
                    <Button variant="default" @click="confirmProcessBill"
                        :disabled="!allItemsCompleted || completingOrder">
                        <CheckCircle2 class="mr-2 size-4" />
                        {{
                            completingOrder ? 'Processing...' : 'Process Bill'
                        }}
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

                <!-- Progress Summary -->
                <Card class="border-blue-200 bg-blue-50/50 dark:border-blue-800 dark:bg-blue-950/20">
                    <CardContent class="pt-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <CheckCircle class="size-5 text-blue-600 dark:text-blue-400" />
                                <span class="font-medium text-blue-800 dark:text-blue-200">Completion Progress</span>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                    {{ orderSummary.completed }} /
                                    {{ orderSummary.total }}
                                </div>
                                <div class="text-sm text-blue-600 dark:text-blue-400">
                                    {{
                                        Math.round(
                                            (orderSummary.completed /
                                                orderSummary.total) *
                                            100,
                                        )
                                    }}% Complete
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="h-2 w-full rounded-full bg-gray-200">
                                <div class="h-2 rounded-full bg-blue-600 transition-all duration-300" :style="{
                                    width: `${(orderSummary.completed / orderSummary.total) * 100}%`,
                                }"></div>
                            </div>
                        </div>
                        <div v-if="!allItemsCompleted" class="mt-3 text-sm text-blue-600 dark:text-blue-400">
                            Complete all {{ orderSummary.pending }} remaining
                            items to process the bill
                        </div>
                    </CardContent>
                </Card>

                <!-- Order Items Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Order Items ({{
                            medicalOrder.order_items.length
                        }})</CardTitle>
                        <CardDescription>Mark individual items as completed</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="medicalOrder.order_items.length === 0"
                            class="py-12 text-center text-muted-foreground">
                            <p>No items in this order</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="item in medicalOrder.order_items" :key="item.id"
                                class="overflow-hidden rounded-lg border">
                                <!-- Item Header -->
                                <div class="flex items-center justify-between p-4 transition-colors hover:bg-muted/50">
                                    <div class="flex items-center gap-3">
                                        <component :is="getItemTypeIcon(item.item_type)
                                            " class="size-6 text-primary" />
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium">{{
                                                    item.item_name
                                                    }}</span>
                                                <Badge variant="outline" class="text-xs">
                                                    {{
                                                        getItemTypeLabel(
                                                            item.item_type,
                                                        )
                                                    }}
                                                </Badge>
                                                <Badge v-if="
                                                    hasAdditionalInfo(item)
                                                " variant="secondary" class="text-xs">
                                                    Has Details
                                                </Badge>
                                            </div>
                                            <div v-if="item.details" class="mt-1 text-sm text-muted-foreground">
                                                {{ item.details }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <div class="text-sm font-medium">
                                                {{
                                                    formatPrice(
                                                        getItemPrice(item),
                                                    )
                                                }}
                                            </div>
                                            <div v-if="
                                                item.quantity_required > 1
                                            " class="text-xs text-muted-foreground">
                                                {{ item.quantity_required }} ×
                                                {{
                                                    formatPrice(
                                                        item.selling_price || 0,
                                                    )
                                                }}
                                            </div>
                                        </div>
                                        <Badge :class="getStatusColor(item.status)">
                                            {{ item.status_label }}
                                        </Badge>
                                        <Button v-if="item.status !== 'completed'" size="sm"
                                            @click="confirmCompleteItem(item)" class="ml-2" :disabled="completingItem">
                                            <CheckCircle class="mr-1 size-4" />
                                            {{
                                                completingItem
                                                    ? 'Completing...'
                                                    : 'Complete'
                                            }}
                                        </Button>
                                    </div>
                                </div>

                                <!-- Item Details (if expanded) -->
                                <div v-if="expandedItems.has(item.id)" class="space-y-3 border-t bg-muted/20 px-4 py-3">
                                    <!-- Medical Details -->
                                    <div v-if="
                                        item.dosage ||
                                        item.frequency ||
                                        item.route
                                    " class="grid gap-4 text-sm md:grid-cols-3">
                                        <div v-if="item.dosage">
                                            <span class="text-muted-foreground">Dosage:</span>
                                            <span class="ml-1 font-medium">{{
                                                item.dosage
                                                }}</span>
                                        </div>
                                        <div v-if="item.frequency">
                                            <span class="text-muted-foreground">Frequency:</span>
                                            <span class="ml-1 font-medium">{{
                                                item.frequency
                                                }}</span>
                                        </div>
                                        <div v-if="item.route">
                                            <span class="text-muted-foreground">Route:</span>
                                            <span class="ml-1 font-medium">{{
                                                item.route
                                                }}</span>
                                        </div>
                                    </div>

                                    <!-- Quantity & Completion Info -->
                                    <div class="flex items-center gap-6 text-sm">
                                        <div>
                                            <span class="text-muted-foreground">Quantity:</span>
                                            <span class="ml-1 font-medium">{{
                                                item.quantity_required
                                                }}</span>
                                        </div>
                                        <div v-if="item.completed_at">
                                            <span class="text-muted-foreground">Completed:</span>
                                            <span class="ml-1 font-medium">{{
                                                item.completed_at
                                                }}</span>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div v-if="item.notes" class="text-sm">
                                        <span class="text-muted-foreground">Notes:</span>
                                        <p class="mt-1 italic">
                                            {{ item.notes }}
                                        </p>
                                    </div>
                                </div>
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
                    You do not have permission to complete medical orders.
                </p>
            </div>
        </div>

        <!-- Complete Order Dialog -->
        <Dialog v-model:open="showCompleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Process Bill</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to process the bill for this medical order?
                        <br /><br />
                        <strong>All {{ orderSummary.total }} items have been
                            completed.</strong>
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showCompleteDialog = false"
                        :disabled="completingOrder">Cancel</Button>
                    <Button @click="processBill" :disabled="completingOrder">
                        <CheckCircle2 class="mr-2 size-4" />
                        {{
                            completingOrder ? 'Processing...' : 'Process Bill'
                        }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Complete Item Dialog -->
        <Dialog v-model:open="showItemCompleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Complete Order Item</DialogTitle>
                    <DialogDescription>
                        Mark "{{ itemToComplete?.item_name }}" as completed?
                        <br /><br />
                        <strong>This will update the item status to
                            "Completed".</strong>
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showItemCompleteDialog = false"
                        :disabled="completingItem">Cancel</Button>
                    <Button @click="completeItemAction" :disabled="completingItem">
                        <CheckCircle class="mr-2 size-4" />
                        {{ completingItem ? 'Completing...' : 'Complete Item' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Complete All Items Dialog -->
        <Dialog v-model:open="showCompleteAllDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Complete Order</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to complete this order?
                        <br /><br />
                        <strong>This will mark all {{ orderSummary.pending }} pending items as completed.</strong>
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showCompleteAllDialog = false"
                        :disabled="completingAllItems">Cancel</Button>
                    <Button @click="completeAllItems" :disabled="completingAllItems">
                        <CheckCircle class="mr-2 size-4" />
                        {{
                            completingAllItems
                                ? 'Completing Order...'
                                : `Complete Order`
                        }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Success Dialog -->
        <Dialog v-model:open="showSuccessDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <CheckCircle2 class="size-5 text-green-600" />
                        Success
                    </DialogTitle>
                    <DialogDescription>
                        {{ successMessage }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button @click="showSuccessDialog = false">OK</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Send Back Dialog -->
        <Dialog v-model:open="showSendBackDialog">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Send Order Back for Revision</DialogTitle>
                    <DialogDescription>
                        Provide a reason for sending this medical order back. The order will be returned to pending status for corrections.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="send-back-reason" class="text-right">
                            Reason *
                        </Label>
                        <Textarea
                            id="send-back-reason"
                            v-model="sendBackReason"
                            placeholder="Please explain why this order needs revision..."
                            rows="4"
                        />
                    </div>
                </div>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="showSendBackDialog = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        @click="sendBackOrder"
                        :disabled="!sendBackReason.trim()"
                    >
                        Send Back
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
