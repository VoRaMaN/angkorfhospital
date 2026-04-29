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
    rxMedicine as rxMedicineRoute,
    show as inventoryShow,
} from '@/routes/inventory';
import { exportMethod as rxMedicineExportRoute } from '@/routes/inventory/rx-medicine';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertTriangle, Download, FileEdit, Plus, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';

interface RxMedicineItem extends InventoryItem {
    original_quantity: number | null;
}

interface Props {
    rxMedicines: Array<RxMedicineItem>;
    filters: {
        search: string;
        status: string;
        date_from: string;
        date_to: string;
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
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const editSheetOpen = ref(false);
const selectedItem = ref<RxMedicineItem | null>(null);

const openEditSheet = (item: RxMedicineItem) => {
    selectedItem.value = item;
    editSheetOpen.value = true;
};

const navigate = (params: Record<string, string>) => {
    router.get(
        rxMedicineRoute.url(),
        params,
        { preserveState: true, replace: true },
    );
};

const currentParams = () => ({
    search: searchQuery.value,
    status: activeStatus.value,
    date_from: dateFrom.value,
    date_to: dateTo.value,
});

const applyFilter = (status: string) => {
    activeStatus.value = activeStatus.value === status ? '' : status;
    navigate(currentParams());
};

const performSearch = useDebounceFn(() => {
    navigate(currentParams());
}, 300);

watch(searchQuery, () => {
    performSearch();
});

watch([dateFrom, dateTo], () => {
    navigate(currentParams());
});

const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (searchQuery.value) { params.set('search', searchQuery.value); }
    if (activeStatus.value) { params.set('status', activeStatus.value); }
    if (dateFrom.value) { params.set('date_from', dateFrom.value); }
    if (dateTo.value) { params.set('date_to', dateTo.value); }
    const qs = params.toString();
    return rxMedicineExportRoute.url() + (qs ? '?' + qs : '');
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
                        <a :href="exportUrl" target="_blank">
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

            <!-- Search + Date Range -->
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
                <div class="flex items-center gap-2">
                    <span class="text-muted-foreground text-sm whitespace-nowrap">Expiry date:</span>
                    <Input
                        v-model="dateFrom"
                        type="date"
                        class="w-40"
                        placeholder="From"
                    />
                    <span class="text-muted-foreground text-sm">�</span>
                    <Input
                        v-model="dateTo"
                        type="date"
                        class="w-40"
                        placeholder="To"
                    />
                    <Button
                        v-if="dateFrom || dateTo"
                        variant="ghost"
                        size="sm"
                        @click="dateFrom = ''; dateTo = ''"
                    >
                        Clear
                    </Button>
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
                                {{ item.expiry_date ? formatDate(item.expiry_date) : '—' }}
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
