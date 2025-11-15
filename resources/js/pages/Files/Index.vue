<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ title }}</h1>
                    <p class="text-muted-foreground">Manage your {{ title.toLowerCase() }}</p>
                </div>
                <Button v-if="createRoute" as-child>
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
                            <TableHead v-for="key in Object.keys(items[0] || {})" :key="key">
                                {{ key.charAt(0).toUpperCase() + key.slice(1) }}
                            </TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in items" :key="item.id">
                            <TableCell v-for="value in Object.values(item)" :key="value">
                                {{ value }}
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button v-if="showRoute" variant="outline" size="sm" as-child>
                                        <Link :href="showRoute.replace(':id', item.id)">View</Link>
                                    </Button>
                                    <Button v-if="editRoute" variant="outline" size="sm" as-child>
                                        <Link :href="editRoute.replace(':id', item.id)">Edit</Link>
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
