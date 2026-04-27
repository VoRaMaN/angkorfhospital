<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertTriangle, Download, FileEdit, Plus, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';

interface RxMedicineItem extends InventoryItem {
    original_quantity: number | null;
}

interface Props {
    rxMedicines: Array<RxMedicineItem>;
    filters: {
        search: string;
        status: string;
    };
    counts: {
        expired: number;
        low_stock: number;
        out_of_stock: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'RX Medicine',
        href: '#',
    },
];

const searchQuery = ref(props.filters.search || '');
const activeStatus = ref(props.filters.status || '');
const editSheetOpen = ref(false);
const selectedItem = ref<RxMedicineItem | null>(null);

const openEditSheet = (item: RxMedicineItem) => {
    selectedItem.value = item;
    editSheetOpen.value = true;
};

const applyFilter = (status: string) => {
    activeStatus.value = activeStatus.value === status ? '' : status;
    router.get(
        route('inventory.rx-medicine'),
        { search: searchQuery.value, status: activeStatus.value },
        { preserveState: true, replace: true },
    );
};

const performSearch = useDebounceFn(() => {
    router.get(
        route('inventory.rx-medicine'),
        { search: searchQuery.value, status: activeStatus.value },
        {
            preserveState: true,
            replace: true,
        }
    );
}, 300);

watch(searchQuery, () => {
    performSearch();
});

const getStatusBadgeVariant = (status: string) => {
    if (status === 'Out of Stock') return 'destructive';
    if (status === 'Low Stock') return 'warning';
    return 'default';
};
</script>

<template>
    <Head title="RX Medicine" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">RX Medicine</h1>
                    <p class="text-muted-foreground">
                        Manage prescription medicines inventory
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Export Report -->
                    <Button variant="outline" as-child>
                        <a :href="route('inventory.rx-medicine.export')">
                            <Download class="size-4" />
                            Export Report
                        </a>
                    </Button>
                    <!-- Alert filter buttons -->
                    <Button
                        :variant="activeStatus === 'expired' ? 'default' : 'outline'"
                        :class="activeStatus === 'expired' ? 'bg-purple-600 hover:bg-purple-700 text-white' : 'border-purple-400 text-purple-700 hover:bg-purple-50 dark:border-purple-600 dark:text-purple-400'"
                        @click="applyFilter('expired')"
                    >
                        <AlertTriangle class="size-4" />
                        Expired {{ counts.expired }} items
                    </Button>
                    <Button
                        :variant="activeStatus === 'low_stock' ? 'default' : 'outline'"
                        :class="activeStatus === 'low_stock' ? 'bg-orange-500 hover:bg-orange-600 text-white' : 'border-orange-400 text-orange-600 hover:bg-orange-50 dark:border-orange-600 dark:text-orange-400'"
                        @click="applyFilter('low_stock')"
                    >
                        <AlertTriangle class="size-4" />
                        Low Stock {{ counts.low_stock }} items
                    </Button>
                    <Button
                        :variant="activeStatus === 'out_of_stock' ? 'default' : 'outline'"
                        :class="activeStatus === 'out_of_stock' ? 'bg-red-600 hover:bg-red-700 text-white' : 'border-red-400 text-red-600 hover:bg-red-50 dark:border-red-600 dark:text-red-400'"
                        @click="applyFilter('out_of_stock')"
                    >
                        <AlertTriangle class="size-4" />
                        No stock {{ counts.out_of_stock }} items
                    </Button>
                    <!-- Add Item -->
                    <Button as-child>
                        <Link :href="inventoryCreate().url">
                            <Plus class="size-4" />
                            Add Item
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Search -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <Search
                        class="absolute left-2 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search medicines..."
                        class="pl-8"
                    />
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Item Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Unit Price</TableHead>
                            <TableHead>Selling Price</TableHead>
                            <TableHead>Original Quantity</TableHead>
                            <TableHead>Remaining</TableHead>
                            <TableHead>Expiry Date</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="item in props.rxMedicines"
                            :key="item.id"
                        >
                            <TableCell class="font-medium">{{ item.item_name }}</TableCell>
                            <TableCell class="max-w-[200px] truncate">{{ item.description }}</TableCell>
                            <TableCell>${{ Number(item.unit_price ?? 0).toFixed(2) }}</TableCell>
                            <TableCell class="font-semibold">${{ Number(item.selling_price ?? 0).toFixed(2) }}</TableCell>
                            <TableCell class="text-muted-foreground">
                                {{ item.original_quantity ?? item.quantity }} {{ item.unit }}
                            </TableCell>
                            <TableCell>
                                {{ item.quantity }} {{ item.unit }}
                            </TableCell>
                            <TableCell>
                                {{ item.expiry_date ? formatDate(item.expiry_date) : 'â€”' }}
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="
                                        item.quantity <= 0
                                            ? 'destructive'
                                            : item.quantity <= item.minimum_stock
                                              ? 'outline'
                                              : 'default'
                                    "
                                    :class="
                                        item.quantity <= 0
                                            ? 'bg-red-100 text-red-700 border-red-300'
                                            : item.quantity <= item.minimum_stock
                                              ? 'bg-orange-100 text-orange-700 border-orange-300'
                                              : 'bg-green-100 text-green-700 border-green-300'
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
