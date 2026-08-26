<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import { formatDate } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, FlaskConical, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface LabResultRow {
    key: string;
    type: string;
    type_label: string;
    patient_name: string;
    patient_hn: string | null;
    medical_order_id: number | null;
    result_date: string | null;
    created_at: string | null;
    view_url: string | null;
}

interface Props {
    results: {
        data: LabResultRow[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        current_page: number;
        last_page: number;
        total: number;
    };
    filters: {
        search: string;
        type: string | null;
        start_date: string | null;
        end_date: string | null;
        direction: 'asc' | 'desc';
    };
    typeOptions: Record<string, string>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Lab Panels', href: '/lab-panels' },
    { title: 'Lab Results', href: '#' },
];

const searchQuery = ref(props.filters.search || '');
const typeFilter = ref(props.filters.type || '');
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');
const direction = ref<'asc' | 'desc'>(props.filters.direction || 'desc');

const performSearch = () => {
    router.get(
        '/lab-results',
        {
            search: searchQuery.value || undefined,
            type: typeFilter.value || undefined,
            start_date: startDate.value || undefined,
            end_date: endDate.value || undefined,
            direction: direction.value,
        },
        { preserveState: true, replace: true },
    );
};

let debounceTimer: ReturnType<typeof setTimeout> | null = null;
watch(searchQuery, () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(performSearch, 500);
});

watch([typeFilter, startDate, endDate], performSearch);

const toggleDateSort = () => {
    direction.value = direction.value === 'desc' ? 'asc' : 'desc';
    performSearch();
};

const clearFilters = () => {
    searchQuery.value = '';
    typeFilter.value = '';
    startDate.value = '';
    endDate.value = '';
    direction.value = 'desc';
    performSearch();
};

const paginationLinks = () => props.results.links.filter((link) => link.url);
</script>

<template>
    <Head title="Lab Results" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Lab Results</h1>
                    <p class="text-muted-foreground">
                        Completed lab results across every report type
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/lab-panels">Back to Lab Panels</Link>
                </Button>
            </div>

            <div class="rounded-lg border bg-card p-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="space-y-1.5 lg:col-span-2">
                        <Label for="search">Search</Label>
                        <div class="relative">
                            <Search class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                            <Input
                                id="search"
                                v-model="searchQuery"
                                placeholder="Patient name, HN, or order #"
                                class="pl-8"
                            />
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <Label>Report Type</Label>
                        <Select v-model="typeFilter">
                            <SelectTrigger>
                                <SelectValue placeholder="All types" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">All types</SelectItem>
                                <SelectItem v-for="(label, key) in props.typeOptions" :key="key" :value="key">
                                    {{ label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="space-y-1.5">
                        <Label for="start_date">From</Label>
                        <Input id="start_date" v-model="startDate" type="date" />
                    </div>
                    <div class="space-y-1.5">
                        <Label for="end_date">To</Label>
                        <Input id="end_date" v-model="endDate" type="date" />
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">{{ props.results.total }} result(s)</p>
                    <Button variant="ghost" size="sm" @click="clearFilters">Clear filters</Button>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>HN</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Result Date</TableHead>
                            <TableHead>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 hover:text-foreground"
                                    @click="toggleDateSort"
                                >
                                    Recorded
                                    <ArrowDown v-if="direction === 'desc'" class="h-3.5 w-3.5" />
                                    <ArrowUp v-else class="h-3.5 w-3.5" />
                                </button>
                            </TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="row in props.results.data" :key="row.key">
                            <TableCell class="font-medium">{{ row.patient_name }}</TableCell>
                            <TableCell>{{ row.patient_hn ?? '—' }}</TableCell>
                            <TableCell>
                                <Badge variant="outline">{{ row.type_label }}</Badge>
                            </TableCell>
                            <TableCell>{{ row.result_date ? formatDate(row.result_date) : '—' }}</TableCell>
                            <TableCell>{{ row.created_at ? formatDate(row.created_at) : '—' }}</TableCell>
                            <TableCell>
                                <Button v-if="row.view_url" variant="outline" size="sm" as-child>
                                    <a :href="row.view_url" target="_blank">View</a>
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.results.data.length === 0">
                            <TableCell colspan="6" class="py-10 text-center text-muted-foreground">
                                <FlaskConical class="mx-auto mb-2 h-8 w-8 opacity-40" />
                                No completed lab results match these filters.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="flex justify-center gap-2" v-if="props.results.last_page > 1">
                <Button
                    v-for="link in paginationLinks()"
                    :key="link.label"
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                    as-child
                >
                    <Link :href="link.url!" preserve-state>{{ link.label }}</Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
