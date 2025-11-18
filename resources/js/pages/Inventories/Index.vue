<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
    create as inventoryCreate,
    edit as inventoryEdit,
    index as inventoryIndex,
    show as inventoryShow,
} from '@/routes/inventory';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search } from 'lucide-vue-next';

import { computed, ref, watch } from 'vue';
import { useAuth } from '@/composables/useAuth';

interface Props {
    inventories: {
        data: Array<{
            id: number;
            item_name: string;
            description: string;
            quantity: number;
            unit: string;
            minimum_stock: number;
            type_of_supply: string;
            status: string;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        current_page: number;
        last_page: number;
    };
    typesOfSupply: Array<{
        value: string;
        label: string;
    }>;
    filters: {
        search: string;
        type_of_supply: string;
        status: string;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: '#',
    },
];

const searchQuery = ref(props.filters.search);
const selectedType = ref(props.filters.type_of_supply || null);
const selectedStatus = ref(props.filters.status || null);

watch(searchQuery, (value) => {
    updateFilter('search', value);
});

watch(selectedType, (value) => {
    updateFilter('type_of_supply', value || '');
});

watch(selectedStatus, (value) => {
    updateFilter('status', value || '');
});

const updateFilter = (key: string, value: string) => {
    router.get(
        inventoryIndex().url,
        { ...props.filters, [key]: value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const paginationLinks = computed(() => {
    return props.inventories.links.filter((link) => link.url);
});
</script>

<template>
    <Head title="Inventory" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_inventories')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Inventory</h1>
                    <p class="text-muted-foreground">
                        Manage medical supplies and equipment
                    </p>
                </div>
                <Button v-if="hasPermission('create_inventories')" as-child>
                    <Link :href="inventoryCreate().url">
                        <Plus class="size-4" />
                        Add Item
                    </Link>
                </Button>
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
                            placeholder="Search inventory..."
                            class="pl-9"
                        />
                    </div>
                </div>
                <div class="flex gap-2">
                    <Select v-model="selectedType">
                        <SelectTrigger class="w-48">
                            <SelectValue placeholder="All Types" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="type in props.typesOfSupply"
                                :key="type.value"
                                :value="type.value"
                            >
                                {{ type.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-model="selectedStatus">
                        <SelectTrigger class="w-40">
                            <SelectValue placeholder="All Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="in_stock">In Stock</SelectItem>
                            <SelectItem value="low_stock">Low Stock</SelectItem>
                            <SelectItem value="out_of_stock"
                                >Out of Stock</SelectItem
                            >
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Item Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Type of Supply</TableHead>
                            <TableHead>Quantity</TableHead>
                            <TableHead>Unit</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="item in props.inventories.data"
                            :key="item.id"
                        >
                            <TableCell>{{ item.item_name }}</TableCell>
                            <TableCell>{{ item.description }}</TableCell>
                            <TableCell>{{
                                item.type_of_supply
                                    .replace(/_/g, ' ')
                                    .replace(/\b\w/g, (l: string) =>
                                        l.toUpperCase(),
                                    )
                            }}</TableCell>
                            <TableCell
                                >{{ item.quantity }} {{ item.unit }}</TableCell
                            >
                            <TableCell>{{ item.unit }}</TableCell>
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
                                        v-if="hasPermission('edit_inventories')"
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="inventoryEdit(item.id).url"
                                            >Edit</Link
                                        >
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.inventories.data.length === 0">
                            <TableCell
                                colspan="7"
                                class="text-center text-muted-foreground"
                            >
                                No inventory items found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                class="flex justify-center gap-2"
                v-if="props.inventories.last_page > 1"
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
                    You don't have permission to view inventory.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
