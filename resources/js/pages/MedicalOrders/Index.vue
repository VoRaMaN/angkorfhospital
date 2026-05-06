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
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, show } from '@/routes/medical-orders';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Calendar, Plus, Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';

const navigateToOrder = (orderId: number) => {
    router.visit(show(orderId).url);
};

interface OrderItem {
    item_type: string;
    item_name: string;
    status: string;
}

interface Props {
    medicalOrders: Array<{
        id: number;
        patient_name: string;
        staff_name: string;
        order_type: string;
        order_type_label: string;
        order_details: string;
        status: string;
        status_label: string;
        priority: string;
        priority_label: string;
        ordered_at: string;
        completed_at?: string;
        items_count: number;
        order_items: OrderItem[];
    }>;
    filters: {
        search: string;
        date: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    medicalOrders: () => [],
    filters: () => ({ search: '', date: '' }),
});

const { hasPermission } = useAuth();

const searchQuery = ref(props.filters.search || '');
const selectedDate = ref(props.filters.date || '');
let searchTimeout: number | null = null;

const performSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get('/medical-orders', {
            search: searchQuery.value,
            date: selectedDate.value,
            page: 1,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
};

watch(searchQuery, () => {
    performSearch();
});

watch(selectedDate, () => {
    router.get('/medical-orders', {
        search: searchQuery.value,
        date: selectedDate.value,
        page: 1,
    }, {
        preserveState: true,
        replace: true,
    });
});

const clearDate = () => {
    selectedDate.value = new Date().toISOString().split('T')[0];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Orders',
        href: '#',
    },
];

const getOrderTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        lab: 'bg-blue-100 text-blue-800',
        procedure: 'bg-green-100 text-green-800',
        referral: 'bg-purple-100 text-purple-800',
        therapy: 'bg-orange-100 text-orange-800',
        imaging: 'bg-cyan-100 text-cyan-800',
        consultation: 'bg-indigo-100 text-indigo-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        pending:    'bg-amber-100 text-amber-700 border border-amber-200',
        processing: 'bg-sky-100 text-sky-700 border border-sky-200',
        processed:  'bg-indigo-100 text-indigo-700 border border-indigo-200',
        completed:  'bg-emerald-100 text-emerald-700 border border-emerald-200',
        cancel:     'bg-slate-100 text-slate-500 border border-slate-200',
        rejected:   'bg-rose-100 text-rose-700 border border-rose-200',
    };
    return colors[status] || 'bg-slate-100 text-slate-500 border border-slate-200';
};

const getPriorityColor = (priority: string) => {
    const colors: Record<string, string> = {
        routine: 'bg-blue-100 text-blue-600 border border-blue-200',
        urgent:  'bg-orange-100 text-orange-700 border border-orange-200',
        stat:    'bg-rose-100 text-rose-700 border border-rose-200',
    };
    return colors[priority] || 'bg-slate-100 text-slate-500 border border-slate-200';
};
</script>

<template>
    <Head title="Medical Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Medical Orders</h1>
                    <p class="text-muted-foreground">
                        Manage medical orders for patients
                    </p>
                </div>
                <Button as-child v-if="hasPermission('create_medical_orders')">
                    <Link :href="create().url">
                        <Plus class="size-4" />
                        Create Medical Order
                    </Link>
                </Button>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative max-w-sm flex-1">
                    <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="searchQuery" placeholder="Search medical orders..." class="pl-9" />
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <Calendar class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="selectedDate"
                            type="date"
                            class="pl-9 w-48"
                        />
                    </div>
                    <Button
                        v-if="selectedDate !== new Date().toISOString().split('T')[0]"
                        variant="ghost"
                        size="icon"
                        @click="clearDate"
                        title="Reset to today"
                    >
                        <X class="size-4" />
                    </Button>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Ordered By</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Order Items</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Priority</TableHead>
                            <TableHead>Ordered At</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="order in medicalOrders"
                            :key="order.id"
                            class="cursor-pointer hover:bg-muted/50"
                            @click="navigateToOrder(order.id)"
                        >
                            <TableCell>{{ order.patient_name || 'Unknown Patient' }}</TableCell>
                            <TableCell>{{ order.staff_name || 'Unknown Staff' }}</TableCell>
                            <TableCell>
                                <Badge
                                    :class="getOrderTypeColor(order.order_type)"
                                >
                                    {{ order.order_type_label }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium"
                                        >{{ order.items_count }} item(s)</span
                                    >
                                    <div
                                        v-if="order.order_items.length > 0"
                                        class="flex flex-wrap gap-1"
                                    >
                                        <Badge
                                            v-for="(
                                                item, idx
                                            ) in order.order_items.slice(0, 3)"
                                            :key="idx"
                                            variant="outline"
                                            class="text-xs"
                                        >
                                            {{ item.item_type }}:
                                            {{ item.item_name }}
                                        </Badge>
                                        <Badge
                                            v-if="order.order_items.length > 3"
                                            variant="outline"
                                            class="text-xs"
                                        >
                                            +{{ order.order_items.length - 3 }}
                                            more
                                        </Badge>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge :class="getStatusColor(order.status)">
                                    {{ order.status_label }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :class="getPriorityColor(order.priority)"
                                >
                                    {{ order.priority_label }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ order.ordered_at }}</TableCell>
                            <TableCell @click.stop>
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                        v-if="hasPermission('view_medical_orders')"
                                    >
                                        <Link :href="show(order.id).url"
                                            >View</Link
                                        >
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                        v-if="
                                            hasPermission('edit_medical_orders')
                                        "
                                    >
                                        <Link :href="edit(order.id).url"
                                            >Edit</Link
                                        >
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.medicalOrders.length === 0">
                            <TableCell
                                colspan="8"
                                class="text-center text-muted-foreground"
                            >
                                No medical orders found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
