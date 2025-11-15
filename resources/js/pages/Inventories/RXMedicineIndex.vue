<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
    show as inventoryShow,
} from '@/routes/inventory';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

interface Props {
    rxMedicines: Array<{
        id: number;
        item_name: string;
        description: string;
        quantity: number;
        unit: string;
        minimum_stock: number;
        type_of_supply: string;
        status: string;
    }>;
    filters: {
        search: string;
        status: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'RX Medicine',
        href: '#',
    },
];
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
                <Button as-child>
                    <Link :href="inventoryCreate().url">
                        <Plus class="size-4" />
                        Add Item
                    </Link>
                </Button>
            </div>

            <!-- Filters removed - this page shows all RX medicines -->

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Item Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Quantity</TableHead>
                            <TableHead>Unit</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="item in props.rxMedicines"
                            :key="item.id"
                        >
                            <TableCell>{{ item.item_name }}</TableCell>
                            <TableCell>{{ item.description }}</TableCell>
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
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
