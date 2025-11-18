<script setup lang="ts">
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
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';

interface Props {
    items: Array<Record<string, any>>;
    title: string;
    createRoute?: string;
    showRoute?: string;
    editRoute?: string;
    deleteRoute?: string;
}

const props = withDefaults(defineProps<Props>(), {
    createRoute: '',
    showRoute: '',
    editRoute: '',
    deleteRoute: '',
});

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: '#',
    },
];
</script>

<template>
    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_files')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ title }}</h1>
                    <p class="text-muted-foreground">
                        Manage your {{ title.toLowerCase() }}
                    </p>
                </div>
                <Button v-if="createRoute && hasPermission('create_files')" as-child>
                    <Link :href="createRoute">
                        <Plus class="size-4" />
                        Create {{ title.slice(0, -1) }}
                    </Link>
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead
                                v-for="key in Object.keys(items[0] || {})"
                                :key="key"
                            >
                                {{ key.charAt(0).toUpperCase() + key.slice(1) }}
                            </TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in items" :key="item.id">
                            <TableCell
                                v-for="value in Object.values(item)"
                                :key="value"
                            >
                                {{ value }}
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button
                                        v-if="showRoute"
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                showRoute.replace(
                                                    ':id',
                                                    item.id,
                                                )
                                            "
                                            >View</Link
                                        >
                                    </Button>
                                    <Button
                                        v-if="editRoute && hasPermission('edit_files')"
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                editRoute.replace(
                                                    ':id',
                                                    item.id,
                                                )
                                            "
                                            >Edit</Link
                                        >
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.items.length === 0">
                            <TableCell
                                colspan="6"
                                class="text-center text-muted-foreground"
                            >
                                No files found
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
                    You don't have permission to view files.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
