<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
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
import { create, edit, show } from '@/routes/staff';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Eye, Plus, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useAuth } from '@/composables/useAuth';

interface Props {
    staff: {
        id: number;
        user_id: number;
        name: string;
        email: string;
        role_name: string;
        department_name: string;
        hire_date: string;
        status: string;
        created_at: string;
        can_edit: boolean;
        can_delete: boolean;
    }[];
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search);
let searchTimeout: number | null = null;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Staff',
        href: '/staff',
    },
];

// Debounced search function
const performSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        router.get('/staff', {
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
    <Head title="Staff" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_staff')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Staff</h1>
                    <p class="text-muted-foreground">Manage hospital staff</p>
                </div>
                <div class="ml-auto">
                    <Button v-if="hasPermission('create_staff')" as-child>
                        <Link :href="create().url">
                            <Plus class="size-4" />
                            Add Staff
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
                        placeholder="Search staff..."
                        class="pl-9"
                    />
                </div>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Role</TableHead>
                            <TableHead>Department</TableHead>
                            <TableHead>Hire Date</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="member in props.staff"
                            :key="member.id"
                        >
                            <TableCell class="font-medium">{{
                                member.name
                            }}</TableCell>
                            <TableCell>{{ member.email }}</TableCell>
                            <TableCell>
                                <Badge variant="outline">{{
                                    member.role_name
                                }}</Badge>
                            </TableCell>
                            <TableCell>{{ member.department_name }}</TableCell>
                            <TableCell>{{
                                formatDate(member.hire_date)
                            }}</TableCell>
                            <TableCell>
                                <Badge
                                    :variant="
                                        member.status === 'active'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{ member.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="show(member.id).url">
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link v-if="member.can_edit" :href="edit(member.id).url">
                                            <Edit class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button v-if="member.can_delete" variant="ghost" size="sm">
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.staff.length === 0">
                            <TableCell
                                colspan="7"
                                class="text-center text-muted-foreground"
                            >
                                No staff found
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
                    You don't have permission to view staff.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
