<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatDate, formatDateTime } from '@/lib/utils';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    edit as inventoryEdit,
    index as inventoryIndex,
} from '@/routes/inventory';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';

interface Props {
    item: {
        id: number;
        item_name: string;
        description: string;
        category: string;
        barcode: string;
        quantity: number;
        unit: string;
        dose_unit: string;
        total_per_box: number;
        minimum_stock: number;
        type_of_supply: string;
        unit_price: number;
        selling_price: number;
        supplier: string;
        location: string;
        expiry_date: string;
        alert_days: number;
        notes: string;
        status: string;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: inventoryIndex().url,
    },
    {
        title: 'Details',
        href: '#',
    },
];
</script>

<template>
    <Head title="Inventory Item Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_inventories')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="inventoryIndex().url">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Inventory Item Details</h1>
                    <p class="text-muted-foreground">View item information</p>
                </div>
                <div class="ml-auto">
                    <Button v-if="hasPermission('edit_inventories')" variant="outline" as-child>
                        <Link :href="inventoryEdit(props.item.id).url">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Item Name
                            </dt>
                            <dd class="text-sm">{{ props.item.item_name }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Status
                            </dt>
                            <dd class="text-sm">
                                <Badge
                                    :variant="
                                        props.item.status === 'Low Stock'
                                            ? 'destructive'
                                            : 'default'
                                    "
                                >
                                    {{ props.item.status }}
                                </Badge>
                            </dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Description
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.item.description ||
                                    'No description provided'
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Quantity
                            </dt>
                            <dd class="text-sm">
                                {{ props.item.quantity }}
                                <span v-if="props.item.type_of_supply === 'rx_medicine'">Tablets</span>
                                <span v-else>{{ props.item.unit }}</span>
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Minimum Stock
                            </dt>
                            <dd class="text-sm">
                                {{ props.item.minimum_stock }}
                                <span v-if="props.item.type_of_supply === 'rx_medicine'">Tablets</span>
                                <span v-else>{{ props.item.unit }}</span>
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Type of Supply
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.item.type_of_supply
                                        .replace('_', ' ')
                                        .replace(/\b\w/g, (l) =>
                                            l.toUpperCase(),
                                        )
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Category
                            </dt>
                            <dd class="text-sm">{{ props.item.category || '—' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Supplier
                            </dt>
                            <dd class="text-sm">{{ props.item.supplier || '—' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Barcode
                            </dt>
                            <dd class="text-sm">{{ props.item.barcode || '—' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Unit Price
                            </dt>
                            <dd class="text-sm">
                                ${{ Number(props.item.unit_price ?? 0).toFixed(2) }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Selling Price
                            </dt>
                            <dd class="text-sm font-semibold">
                                ${{ Number(props.item.selling_price ?? 0).toFixed(2) }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Dose Unit
                            </dt>
                            <dd class="text-sm">{{ props.item.dose_unit || '—' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Total per Box
                            </dt>
                            <dd class="text-sm">{{ props.item.total_per_box ?? '—' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Expiry Date
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.item.expiry_date
                                        ? formatDate(props.item.expiry_date)
                                        : '—'
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Alert Days
                            </dt>
                            <dd class="text-sm">{{ props.item.alert_days ?? '—' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Location
                            </dt>
                            <dd class="text-sm">{{ props.item.location || '—' }}</dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Notes
                            </dt>
                            <dd class="text-sm">{{ props.item.notes || '—' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Created
                            </dt>
                            <dd class="text-sm">
                                {{
                                    formatDateTime(props.item.created_at)
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
                                    formatDateTime(props.item.updated_at)
                                }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view inventory details.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
