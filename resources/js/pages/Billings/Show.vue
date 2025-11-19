<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { report as reportRoute } from '@/routes/billings';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    DollarSign,
    Download,
    Edit,
    FileText,
    User,
} from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';

interface Props {
    billing: {
        id: number;
        patient_name: string;
        appointment_id?: number;
        visit_id?: number;
        medical_order_id?: number;
        amount: number;
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

const { hasPermission } = useAuth();

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
        default:
            return 'secondary';
    }
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
                <div class="ml-auto">
                    <Button v-if="hasPermission('edit_billings')" variant="outline" as-child>
                        <Link :href="`/billings/${props.billing.id}/edit`">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <a
                            :href="reportRoute(props.billing.id).url"
                            target="_blank"
                        >
                            <Download class="size-4" />
                            Download Report
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
                                    {{
                                        formatCurrency(
                                            props.billing.amount,
                                        )
                                    }}
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
                                    new Date(
                                        props.billing.billing_date,
                                    ).toLocaleDateString()
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
                                    new Date(
                                        props.billing.created_at,
                                    ).toLocaleString()
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
                                    new Date(
                                        props.billing.updated_at,
                                    ).toLocaleString()
                                }}
                            </dd>
                        </div>
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
                        <div class="border-t pt-4 mt-6">
                            <div class="flex items-center justify-between">
                                <div class="text-lg font-semibold">Total Amount</div>
                                <div class="text-2xl font-bold">
                                    {{ formatCurrency(props.costBreakdown.total) }}
                                </div>
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
