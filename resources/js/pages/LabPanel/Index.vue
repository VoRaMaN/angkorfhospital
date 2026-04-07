<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
    create as labPanelCreate,
    edit as labPanelEdit,
    index as labPanelIndex,
    show as labPanelShow,
} from '@/routes/lab-panels';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { FlaskConical, Plus, Search, Clock, AlertCircle } from 'lucide-vue-next';

import { computed, ref, watch } from 'vue';
import { useAuth } from '@/composables/useAuth';

interface LabItem {
    id: number;
    item_name: string;
    details: string;
    status: string;
    status_label: string;
}

interface ActiveLabOrder {
    id: number;
    patient_name: string;
    staff_name: string;
    status: string;
    status_label: string;
    priority: string;
    priority_label: string;
    ordered_at: string;
    lab_items: LabItem[];
}

interface Props {
    labPanels: {
        data: Array<{
            id: number;
            name: string;
            description: string;
            price: number;
            is_active: boolean;
            inventory_items_count: number;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        current_page: number;
        last_page: number;
    };
    activeLabOrders: ActiveLabOrder[];
    categories: string[];
    filters: {
        search: string;
        category: string;
        status: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lab Panels',
        href: '#',
    },
];

const searchQuery = ref(props.filters.search);
const selectedStatus = ref(props.filters.status || null);

watch(searchQuery, (value) => {
    updateFilter('search', value);
});

watch(selectedStatus, (value) => {
    updateFilter('status', value || '');
});

const updateFilter = (key: string, value: string) => {
    router.get(
        labPanelIndex().url,
        { ...props.filters, [key]: value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const paginationLinks = computed(() => {
    return props.labPanels.links.filter((link) => link.url);
});

const statusBadgeVariant = (status: string) => {
    switch (status) {
        case 'pending': return 'secondary' as const;
        case 'processing': return 'default' as const;
        case 'completed': return 'outline' as const;
        default: return 'secondary' as const;
    }
};

const priorityBadgeVariant = (priority: string) => {
    switch (priority) {
        case 'urgent': return 'destructive' as const;
        case 'high': return 'destructive' as const;
        case 'normal': return 'default' as const;
        case 'low': return 'secondary' as const;
        default: return 'secondary' as const;
    }
};

const { hasPermission } = useAuth();
</script>

<template>
    <Head title="Lab Panels" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_lab_panels')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Lab Panels</h1>
                    <p class="text-muted-foreground">
                        Manage laboratory test panels and their required
                        supplies
                    </p>
                </div>
                <Button v-if="hasPermission('create_lab_panels')" as-child>
                    <Link :href="labPanelCreate().url">
                        <Plus class="size-4" />
                        Add Panel
                    </Link>
                </Button>
            </div>

            <!-- Active Lab Orders - Lab Staff Reminder -->
            <div v-if="props.activeLabOrders.length > 0" class="rounded-xl border-2 border-blue-200 bg-blue-50/50 p-4 dark:border-blue-900 dark:bg-blue-950/30">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="flex items-center gap-2 text-lg font-semibold">
                        <span class="relative flex size-3">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex size-3 rounded-full bg-blue-500"></span>
                        </span>
                        Lab Orders In Progress
                        <Badge variant="secondary" class="ml-1">{{ props.activeLabOrders.length }}</Badge>
                    </h2>
                    <p class="text-xs text-muted-foreground flex items-center gap-1">
                        <Clock class="size-3" />
                        Auto-refreshes on page load
                    </p>
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="order in props.activeLabOrders" :key="order.id"
                        :class="[
                            'relative transition-shadow hover:shadow-md',
                            order.priority === 'urgent' || order.priority === 'stat'
                                ? 'border-red-300 dark:border-red-800'
                                : ''
                        ]"
                    >
                        <div v-if="order.priority === 'urgent' || order.priority === 'stat'"
                            class="absolute top-0 right-0 left-0 h-1 rounded-t-xl bg-red-500"
                        />
                        <CardHeader class="pb-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <CardTitle class="text-base">{{ order.patient_name }}</CardTitle>
                                    <p class="mt-0.5 text-xs text-muted-foreground">
                                        Order #{{ order.id }} &middot; {{ order.ordered_at }}
                                    </p>
                                </div>
                                <div class="flex gap-1.5">
                                    <Badge :variant="priorityBadgeVariant(order.priority)">
                                        <AlertCircle v-if="order.priority === 'urgent' || order.priority === 'stat'" class="mr-1 size-3" />
                                        {{ order.priority_label }}
                                    </Badge>
                                    <Badge :variant="statusBadgeVariant(order.status)">
                                        {{ order.status_label }}
                                    </Badge>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Ordered by Dr. {{ order.staff_name }}
                            </p>
                        </CardHeader>
                        <CardContent>
                            <p class="mb-2 text-xs font-medium text-muted-foreground uppercase tracking-wide">Lab Items ({{ order.lab_items.length }})</p>
                            <div class="space-y-2">
                                <div
                                    v-for="item in order.lab_items"
                                    :key="item.id"
                                    :class="[
                                        'flex items-center justify-between rounded-md border px-3 py-2 text-sm',
                                        item.status === 'processing' ? 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950/50' : '',
                                        item.status === 'pending' ? 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950/50' : '',
                                    ]"
                                >
                                    <div>
                                        <p class="font-medium">{{ item.item_name }}</p>
                                        <p v-if="item.details" class="text-xs text-muted-foreground">{{ item.details }}</p>
                                    </div>
                                    <Badge :variant="statusBadgeVariant(item.status)" class="text-xs">
                                        {{ item.status_label }}
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <div v-else class="rounded-xl border border-dashed p-6 text-center text-muted-foreground">
                <FlaskConical class="mx-auto mb-2 size-8 opacity-40" />
                <p class="text-sm">No active lab orders at the moment</p>
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
                            placeholder="Search lab panels..."
                            class="pl-9"
                        />
                    </div>
                </div>
                <div class="flex gap-2">
                    <Select v-model="selectedStatus">
                        <SelectTrigger class="w-40">
                            <SelectValue placeholder="All Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="inactive">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Price</TableHead>
                            <TableHead>Items</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="labPanel in props.labPanels.data"
                            :key="labPanel.id"
                            class="cursor-pointer hover:bg-muted/50"
                            @click="router.visit(labPanelShow(labPanel.id).url)"
                        >
                            <TableCell>{{ labPanel.name }}</TableCell>
                            <TableCell>{{ labPanel.description }}</TableCell>
                            <TableCell>${{ labPanel.price }}</TableCell>
                            <TableCell
                                >{{
                                    labPanel.inventory_items_count
                                }}
                                items</TableCell
                            >
                            <TableCell>
                                <Badge
                                    :variant="
                                        labPanel.is_active
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        labPanel.is_active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell @click.stop>
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                labPanelShow(labPanel.id).url
                                            "
                                            >View</Link
                                        >
                                    </Button>
                                    <Button
                                        v-if="hasPermission('edit_lab_panels')"
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                labPanelEdit(labPanel.id).url
                                            "
                                            >Edit</Link
                                        >
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.labPanels.length === 0">
                            <TableCell
                                colspan="6"
                                class="text-center text-muted-foreground"
                            >
                                No lab panels found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                class="flex justify-center gap-2"
                v-if="props.labPanels.last_page > 1"
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
                    You don't have permission to view lab panels.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
