<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';
import { index as inventoryIndex, edit as inventoryEdit } from '@/routes/inventory';

interface Props {
    item: {
        id: number;
        item_name: string;
        description: string;
        quantity: number;
        unit: string;
        minimum_stock: number;
        type_of_supply: string;
        supplier: string;
        cost_per_unit: number;
        expiry_date: string;
        status: string;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
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
                    <Button variant="outline" as-child>
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
                            <dt class="text-sm font-medium text-muted-foreground">Item Name</dt>
                            <dd class="text-sm">{{ props.item.item_name }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd class="text-sm">
                                <Badge :variant="props.item.status === 'Low Stock' ? 'destructive' : 'default'">
                                    {{ props.item.status }}
                                </Badge>
                            </dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt class="text-sm font-medium text-muted-foreground">Description</dt>
                            <dd class="text-sm">{{ props.item.description || 'No description provided' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Quantity</dt>
                            <dd class="text-sm">{{ props.item.quantity }} {{ props.item.unit }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Minimum Stock</dt>
                            <dd class="text-sm">{{ props.item.minimum_stock }} {{ props.item.unit }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Type of Supply</dt>
                            <dd class="text-sm">{{ props.item.type_of_supply.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Supplier</dt>
                            <dd class="text-sm">{{ props.item.supplier }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Cost per Unit</dt>
                            <dd class="text-sm">${{ props.item.cost_per_unit }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Expiry Date</dt>
                            <dd class="text-sm">{{ props.item.expiry_date ? new Date(props.item.expiry_date).toLocaleDateString() : 'N/A' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Created</dt>
                            <dd class="text-sm">{{ new Date(props.item.created_at).toLocaleString() }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Last Updated</dt>
                            <dd class="text-sm">{{ new Date(props.item.updated_at).toLocaleString() }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>