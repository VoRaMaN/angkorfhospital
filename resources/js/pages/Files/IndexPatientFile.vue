<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, show } from '@/routes/patient-files';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useAuth } from '@/composables/useAuth';
interface Props {
    items: Array<Record<string, any>>;
    title: string;
    createRoute?: string;
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const searchQuery = ref(props.filters.search || '');
let searchTimeout: number | null = null;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: '#',
    },
];

// Debounced search function
const performSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        router.get('/patient-files', {
            search: searchQuery.value,
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
</script>

<template>

    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_files')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ title }}</h1>
                    <p class="text-muted-foreground">
                        Manage your {{ title.toLowerCase() }}
                    </p>
                </div>
                <Button v-if="createRoute && hasPermission('create_files')" as-child>
                    <Link :href="create().url">
                    <Plus class="size-4" />
                    Create {{ title.slice(0, -1) }}
                    </Link>
                </Button>
            </div>

            <!-- Search -->
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="searchQuery" placeholder="Search patient files..." class="pl-10" />
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>File Name</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Size</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in items" :key="item.id">
                            <TableCell>{{ item.patient.name }}</TableCell>
                            <TableCell>{{ item.file.name }}</TableCell>
                            <TableCell>{{ item.type }}</TableCell>
                            <TableCell>{{ item.file.size }}</TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="show(item.id).url">View</Link>
                                    </Button>
                                    <Button v-if="hasPermission('edit_files')" variant="outline" size="sm" as-child>
                                        <Link :href="edit(item.id).url">Edit</Link>
                                    </Button>
                                </div>
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
                    You don't have permission to view patient files.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
