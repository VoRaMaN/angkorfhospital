<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatDate } from '@/lib/utils';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import InventoryEditSheet from '@/components/InventoryEditSheet.vue';
import type { InventoryItem } from '@/components/InventoryEditSheet.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    create as inventoryCreate,
    show as inventoryShow,
} from '@/routes/inventory';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { FileEdit, Plus } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    labSupplies: Array<InventoryItem>;
    filters: {
        search: string;
        status: string;
    };
}

const props = defineProps<Props>();

const editSheetOpen = ref(false);
const selectedItem = ref<InventoryItem | null>(null);

const openEditSheet = (item: InventoryItem) => {
    selectedItem.value = item;
    editSheetOpen.value = true;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lab Inventory',
        href: '#',
    },
];
</script>

<template>
    <Head title="Lab Inventory" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Lab Inventory</h1>
                    <p class="text-muted-foreground">
                        Manage laboratory supplies and equipment
                    </p>
                </div>
                <Button as-child>
                    <Link :href="inventoryCreate().url">
                        <Plus class="size-4" />
                        Add Item
                    </Link>
                </Button>
            </div>

            <!-- Filters removed - this page shows all lab supplies -->

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Item Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Unit Price</TableHead>
                            <TableHead>Quantity</TableHead>
                            <TableHead>Expiry Date</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="item in props.labSupplies"
                            :key="item.id"
                        >
                            <TableCell class="font-medium">{{ item.item_name }}</TableCell>
                            <TableCell class="max-w-[200px] truncate">{{ item.description }}</TableCell>
                            <TableCell>${{ Number(item.unit_price ?? 0).toFixed(2) }}</TableCell>
                            <TableCell
                                >{{ item.quantity }} {{ item.unit }}</TableCell
                            >
                            <TableCell>
                                {{ item.expiry_date ? formatDate(item.expiry_date) : '—' }}
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="
                                        item.quantity <= item.minimum_stock
                                            ? 'destructive'
                                            : 'default'
                                    "
                                >
                                    {{ item.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="inventoryShow(item.id).url"
                                            >View</Link
                                        >
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        @click="openEditSheet(item)"
                                    >
                                        <FileEdit class="mr-1 size-3" />
                                        Edit Details
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <InventoryEditSheet
                :item="selectedItem"
                v-model:open="editSheetOpen"
            />
        </div>
    </AppLayout>
</template>
