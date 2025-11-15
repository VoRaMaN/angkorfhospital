<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
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
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Edit, Eye, Plus, Search, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    roles: {
        id: number;
        name: string;
        description: string;
        permissions_count: number;
        created_at: string;
    }[];
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
    {
        title: 'Roles',
        href: '/settings/roles',
    },
];
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Roles</h1>
                    <p class="text-muted-foreground">
                        Manage user roles and permissions
                    </p>
                </div>
                <div class="ml-auto">
                    <Button as-child>
                        <Link href="/settings/roles/create">
                            <Plus class="size-4" />
                            Add Role
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative max-w-sm flex-1">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search roles..."
                        class="pl-9"
                    />
                </div>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Permissions</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="role in props.roles" :key="role.id">
                            <TableCell class="font-medium">{{
                                role.name
                            }}</TableCell>
                            <TableCell>{{
                                role.description || 'No description'
                            }}</TableCell>
                            <TableCell>
                                <Badge variant="outline"
                                    >{{
                                        role.permissions_count
                                    }}
                                    permissions</Badge
                                >
                            </TableCell>
                            <TableCell>{{
                                new Date(role.created_at).toLocaleDateString()
                            }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link
                                            :href="`/settings/roles/${role.id}`"
                                        >
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link
                                            :href="`/settings/roles/${role.id}/edit`"
                                        >
                                            <Edit class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm">
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.roles.length === 0">
                            <TableCell
                                colspan="5"
                                class="text-center text-muted-foreground"
                            >
                                No roles found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
