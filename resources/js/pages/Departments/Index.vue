<script setup lang="ts">
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
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, show } from '@/routes/departments';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useAuth } from '@/composables/useAuth';

interface Props {
    departments: Array<{
        id: number;
        name: string;
        description: string;
        created_at: string;
    }>;
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
let searchTimeout: number | null = null;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '#',
    },
];

// Debounced search function
const performSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        router.get('/departments', {
            search: searchQuery.value,
            page: 1, // Reset to first page when searching
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300); // 300ms debounce
};

// Watch for search query changes
watch(searchQuery, () => {
    performSearch();
});

const { hasPermission } = useAuth();
</script>

<template>
    <Head title="Departments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_departments')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Departments</h1>
                    <p class="text-muted-foreground">
                        Manage hospital departments
                    </p>
                </div>
                <Button v-if="hasPermission('create_departments')" as-child>
                    <Link :href="create().url">
                        <Plus class="size-4" />
                        Add Department
                    </Link>
                </Button>
            </div>

            <!-- Search -->
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search departments..."
                        class="pl-10"
                    />
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="department in props.departments"
                            :key="department.id"
                        >
                            <TableCell>{{ department.name }}</TableCell>
                            <TableCell>{{ department.description }}</TableCell>
                            <TableCell>{{
                                formatDate(department.created_at)
                            }}</TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="show(department.id).url"
                                            >View</Link
                                        >
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="edit(department.id).url"
                                            >Edit</Link
                                        >
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.departments.length === 0">
                            <TableCell
                                colspan="4"
                                class="text-center text-muted-foreground"
                            >
                                No departments found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view departments.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
