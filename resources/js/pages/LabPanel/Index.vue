<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search } from 'lucide-vue-next';
import { index as labPanelIndex, create as labPanelCreate, show as labPanelShow, edit as labPanelEdit } from '@/routes/lab-panels';

import { ref, watch, computed } from 'vue';

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
    router.get(labPanelIndex().url, { ...props.filters, [key]: value }, {
        preserveState: true,
        replace: true,
    });
};

const paginationLinks = computed(() => {
    return props.labPanels.links.filter(link => link.url);
});
</script>

<template>

    <Head title="Lab Panels" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Lab Panels</h1>
                    <p class="text-muted-foreground">Manage laboratory test panels and their required supplies</p>
                </div>
                <Button as-child>
                    <Link :href="labPanelCreate().url">
                    <Plus class="size-4" />
                    Add Panel
                    </Link>
                </Button>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center">
                <div class="flex-1">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Search lab panels..." class="pl-9" />
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
                        <TableRow v-for="labPanel in props.labPanels.data" :key="labPanel.id">
                            <TableCell>{{ labPanel.name }}</TableCell>
                            <TableCell>{{ labPanel.description }}</TableCell>
                            <TableCell>${{ labPanel.price }}</TableCell>
                            <TableCell>{{ labPanel.inventory_items_count }} items</TableCell>
                            <TableCell>
                                <Badge :variant="labPanel.is_active ? 'default' : 'secondary'">
                                    {{ labPanel.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="labPanelShow(labPanel.id).url">View</Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="labPanelEdit(labPanel.id).url">Edit</Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center gap-2" v-if="props.labPanels.last_page > 1">
                <Button v-for="link in paginationLinks" :key="link.label" :variant="link.active ? 'default' : 'outline'"
                    as-child>
                    <Link :href="link.url!">{{ link.label }}</Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>