<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatDate, formatDateTime } from '@/lib/utils';
import AppLayout from '@/layouts/AppLayout.vue';
import { letter as letterRoute, receive as receiveRoute } from '@/routes/billings';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    CheckCircle,
    DollarSign,
    FileText,
    Inbox,
    Printer,
    RotateCcw,
    User,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { useAuth } from '@/composables/useAuth';

interface Props {
    billing: {
        id: number;
        bill_no: string;
        patient_name: string;
        appointment_id?: number;
        visit_id?: number;
        medical_order_id?: number;
        amount: number;
        discount_amount: number;
        status: string;
        billing_date: string;
        notes: string;
        created_at: string;
        updated_at: string;
    };
    costBreakdown?: {
        groups: Array<{
            name: string;
            type: string;
            panel_name?: string;
            items: Array<{
                id: number;
                item_name: string;
                item_type: string;
                quantity: number;
                unit_price: number;
                total: number;
                details?: string;
            }>;
            subtotal: number;
            item_count: number;
        }>;
        total: number;
    };
}

const props = defineProps<Props>();

const { hasPermission, isAdmin } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billings',
        href: '/billings',
    },
    {
        title: 'Details',
        href: '#',
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
        case 'partial':
            return 'outline';
        case 'written_off':
            return 'destructive';
        case 'cancelled':
            return 'outline';
        case 'revision':
            return 'secondary';
        case 'revised':
            return 'outline';
        default:
            return 'secondary';
    }
};

const subtotal = computed(() => props.billing.amount);
const discountAmount = computed(() => props.billing.discount_amount ?? 0);
const finalAmount = computed(() => subtotal.value - discountAmount.value);
const discountPercent = computed(() =>
    subtotal.value > 0 ? (discountAmount.value / subtotal.value) * 100 : 0,
);

const discountForm = ref({
    discount_amount: (props.billing.discount_amount ?? 0).toString(),
});
const discountPercInput = ref(
    subtotal.value > 0
        ? (((props.billing.discount_amount ?? 0) / subtotal.value) * 100).toFixed(2)
        : '0',
);
const savingDiscount = ref(false);

const onDiscountUsdInput = (e: Event) => {
    const val = parseFloat((e.target as HTMLInputElement).value) || 0;
    discountForm.value.discount_amount = val.toString();
    discountPercInput.value =
        subtotal.value > 0 ? ((val / subtotal.value) * 100).toFixed(2) : '0';
};

const onDiscountPercInput = (e: Event) => {
    const pct = parseFloat((e.target as HTMLInputElement).value) || 0;
    const usd = (subtotal.value * pct) / 100;
    discountForm.value.discount_amount = usd.toFixed(2);
    discountPercInput.value = pct.toString();
};

const saveDiscount = () => {
    savingDiscount.value = true;
    router.patch(
        `/billings/${props.billing.id}/discount`,
        { discount_amount: parseFloat(discountForm.value.discount_amount) || 0 },
        {
            preserveState: false,
            onFinish: () => {
                savingDiscount.value = false;
            },
        },
    );
};

const sendingBack = ref(false);

const completePayment = () => {
    if (confirm('Are you sure you want to mark this billing as paid? This will complete the payment and move the order to history.')) {
        router.patch(`/billings/${props.billing.id}/complete-payment`, {}, {
            preserveState: false,
            preserveScroll: false,
        });
    }
};

const receiveRevised = () => {
    router.patch(receiveRoute(props.billing.id).url, {}, {
        preserveState: false,
        preserveScroll: false,
    });
};

const sendBackToNurse = () => {
    sendingBack.value = true;
    router.patch(`/billings/${props.billing.id}/send-back-to-nurse`, {}, {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => {
            sendingBack.value = false;
        },
    });
};

</script>

<template>
    <Head title="Billing Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_billings')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/billings">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Billing Details</h1>
                    <p class="text-muted-foreground">
                        View billing information
                    </p>
                </div>
                <div class="ml-auto flex gap-2">
                    <!-- Process: only for users who can process medical orders (not billing-only users) -->
                    <Button
                        v-if="hasPermission('process_medical_orders') && props.billing.medical_order_id && props.billing.status !== 'paid'"
                        variant="outline"
                        as-child
                    >
                        <Link :href="`/medical-orders/${props.billing.medical_order_id}/process`">
                            <FileText class="size-4" />
                            Process
                        </Link>
                    </Button>
                    <!-- Receive: billing user acknowledges billing sent from nurse -->
                    <Button
                        v-if="hasPermission('edit_billings') && (props.billing.status === 'revised' || props.billing.status === 'sent_to_account')"
                        variant="default"
                        class="bg-blue-600 hover:bg-blue-700 text-white"
                        @click="receiveRevised"
                    >
                        <Inbox class="size-4" />
                        Receive
                    </Button>
                    <!-- Finish: mark as paid (not when awaiting receipt or in revision flow) -->
                    <Button
                        v-if="hasPermission('edit_billings') && props.billing.status !== 'paid' && props.billing.status !== 'revision' && props.billing.status !== 'revised' && props.billing.status !== 'sent_to_account'"
                        variant="default"
                        @click="completePayment"
                    >
                        <CheckCircle class="size-4" />
                        Finish
                    </Button>
                    <!-- Send Back: when sent_to_account or pending (billing user can reject at either stage) -->
                    <Button
                        v-if="hasPermission('edit_billings') && props.billing.medical_order_id && ['sent_to_account', 'pending'].includes(props.billing.status)"
                        variant="outline"
                        class="border-amber-300 text-amber-700 hover:bg-amber-50 dark:border-amber-700 dark:text-amber-400 dark:hover:bg-amber-950/20"
                        :disabled="sendingBack"
                        @click="sendBackToNurse"
                    >
                        <RotateCcw class="size-4" />
                        {{ sendingBack ? 'Sending...' : 'Send Back' }}
                    </Button>
                    <!-- View bill (receipt PDF) -->
                    <Button variant="outline" as-child>
                        <a :href="letterRoute(props.billing.id).url" target="_blank">
                            <Printer class="size-4" />
                            View Bill
                        </a>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl space-y-6">
                <!-- Status and Summary Cards -->
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border bg-card p-4">
                        <div class="flex items-center gap-2">
                            <DollarSign class="size-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm text-muted-foreground">
                                    Amount
                                </p>
                                <p class="text-2xl font-bold">
                                    {{ formatCurrency(finalAmount) }}
                                </p>
                                <p v-if="discountAmount > 0" class="text-sm text-green-600 dark:text-green-400">
                                    Discount: -{{ formatCurrency(discountAmount) }} ({{ discountPercent.toFixed(1) }}%)
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg border bg-card p-4">
                        <div class="flex items-center gap-2">
                            <Badge
                                :variant="
                                    getStatusVariant(props.billing.status)
                                "
                                class="px-3 py-1 text-lg"
                            >
                                {{ props.billing.status }}
                            </Badge>
                        </div>
                    </div>
                </div>

                <!-- Main Details -->
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <FileText class="size-4" />
                                Bill No
                            </dt>
                            <dd class="text-sm font-medium">
                                {{ props.billing.bill_no }}
                            </dd>
                        </div>
                        <div class="space-y-2">
                            <dt
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <User class="size-4" />
                                Patient
                            </dt>
                            <dd class="text-sm font-medium">
                                {{ props.billing.patient_name }}
                            </dd>
                        </div>

                        <div
                            v-if="props.billing.appointment_id"
                            class="space-y-2"
                        >
                            <dt
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <Calendar class="size-4" />
                                Related Appointment
                            </dt>
                            <dd class="text-sm">
                                Yes
                            </dd>
                        </div>

                        <div
                            v-if="props.billing.visit_id"
                            class="space-y-2"
                        >
                            <dt
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <Calendar class="size-4" />
                                Related Visit
                            </dt>
                            <dd class="text-sm">
                                Yes
                            </dd>
                        </div>

                        <div
                            v-if="props.billing.medical_order_id"
                            class="space-y-2"
                        >
                            <dt
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <FileText class="size-4" />
                                Related Medical Order
                            </dt>
                            <dd class="text-sm">
                                Yes
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <Calendar class="size-4" />
                                Billing Date
                            </dt>
                            <dd class="text-sm">
                                {{
                                    formatDate(props.billing.billing_date)
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <FileText class="size-4" />
                                Notes
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.billing.notes || 'No additional notes'
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Created
                            </dt>
                            <dd class="text-sm">
                                {{
                                    formatDateTime(props.billing.created_at)
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Last Updated
                            </dt>
                            <dd class="text-sm">
                                {{
                                    formatDateTime(props.billing.updated_at)
                                }}
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Discount Section -->
                <div v-if="hasPermission('edit_billings') && props.billing.status !== 'paid'" class="rounded-lg border bg-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Discount</h3>
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium">Discount (USD)</label>
                            <input
                                type="number"
                                min="0"
                                :max="props.billing.amount"
                                step="0.01"
                                :value="discountForm.discount_amount"
                                @input="onDiscountUsdInput"
                                class="flex h-9 w-36 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                placeholder="0.00"
                            />
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium">Discount (%)</label>
                            <input
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                :value="discountPercInput"
                                @input="onDiscountPercInput"
                                class="flex h-9 w-28 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                placeholder="0.00"
                            />
                        </div>
                        <Button @click="saveDiscount" :disabled="savingDiscount" size="sm">
                            {{ savingDiscount ? 'Saving...' : 'Save Discount' }}
                        </Button>
                    </div>
                    <div v-if="discountAmount > 0" class="mt-4 rounded-md bg-green-50 dark:bg-green-950/20 p-3 text-sm">
                        <span class="text-green-700 dark:text-green-400 font-medium">Applied: -{{ formatCurrency(discountAmount) }} ({{ discountPercent.toFixed(2) }}%)</span>
                        <span class="text-muted-foreground ml-2">→ Net total: {{ formatCurrency(finalAmount) }}</span>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div v-if="props.costBreakdown" class="rounded-lg border bg-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Cost Breakdown</h3>
                    <div class="space-y-6">
                        <div
                            v-for="group in props.costBreakdown.groups"
                            :key="group.name"
                            class="rounded-lg border bg-muted/20 p-4"
                        >
                            <!-- Group Header -->
                            <div class="flex items-center justify-between border-b pb-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="text-lg font-semibold">
                                        {{ group.name }}
                                    </div>
                                    <Badge variant="secondary">
                                        {{ group.item_count }} item{{ group.item_count !== 1 ? 's' : '' }}
                                    </Badge>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold">
                                        {{ formatCurrency(group.subtotal) }}
                                    </div>
                                    <div class="text-sm text-muted-foreground">Subtotal</div>
                                </div>
                            </div>

                            <!-- Group Items -->
                            <div class="space-y-2">
                                <div
                                    v-for="item in group.items"
                                    :key="item.id"
                                    class="flex items-center justify-between py-2 px-3 rounded-md bg-background"
                                >
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <div class="font-medium">{{ item.item_name }}</div>
                                            <div v-if="item.details" class="text-sm text-muted-foreground">
                                                {{ item.details }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium">{{ formatCurrency(item.total) }}</div>
                                        <div v-if="item.quantity > 1" class="text-sm text-muted-foreground">
                                            {{ item.quantity }} × {{ formatCurrency(item.unit_price) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="border-t pt-4 mt-6 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-muted-foreground">Subtotal</div>
                                <div class="text-sm">{{ formatCurrency(props.costBreakdown.total) }}</div>
                            </div>
                            <div v-if="discountAmount > 0" class="flex items-center justify-between text-green-600 dark:text-green-400">
                                <div class="text-sm">Discount ({{ discountPercent.toFixed(2) }}%)</div>
                                <div class="text-sm">-{{ formatCurrency(discountAmount) }}</div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t">
                                <div class="text-lg font-semibold">Total Amount</div>
                                <div class="text-2xl font-bold">{{ formatCurrency(finalAmount) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view billing details.
                </p>
            </div>
        </div>

    </AppLayout>
</template>
