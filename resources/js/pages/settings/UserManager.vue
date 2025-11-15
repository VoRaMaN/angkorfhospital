<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
import { create, edit, show } from '@/routes/settings/user-management';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Edit,
    Eye,
    Plus,
    Search,
    Trash2,
    UserCheck,
    UserX,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    users: {
        id: number;
        name: string;
        email: string;
        type: 'staff' | 'doctor' | 'patient';
        role_name?: string;
        department_name?: string;
        status: 'active' | 'inactive';
        email_verified_at: string | null;
        created_at: string;
    }[];
    filters: {
        search: string;
        type: string;
        status: string;
    };
    stats: {
        total_users: number;
        active_users: number;
        staff_count: number;
        doctor_count: number;
        patient_count: number;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search);
const typeFilter = ref(props.filters.type);
const statusFilter = ref(props.filters.status);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
    {
        title: 'User Management',
        href: '/settings/users',
    },
];

// Computed filtered users
const filteredUsers = computed(() => {
    return props.users.filter((user) => {
        const matchesSearch =
            !searchQuery.value ||
            user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            user.email.toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesType = !typeFilter.value || user.type === typeFilter.value;
        const matchesStatus =
            !statusFilter.value || user.status === statusFilter.value;

        return matchesSearch && matchesType && matchesStatus;
    });
});

const updateFilters = () => {
    router.get(
        '/settings/users',
        {
            search: searchQuery.value,
            type: typeFilter.value,
            status: statusFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const toggleUserStatus = (userId: number) => {
    router.post(
        `/settings/users/${userId}/toggle-status`,
        {},
        {
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">User Management</h1>
                    <p class="text-muted-foreground">
                        Manage all users in the system
                    </p>
                </div>
                <div class="ml-auto">
                    <Button as-child>
                        <Link :href="create().url">
                            <Plus class="size-4" />
                            Add User
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-2xl font-bold">
                        {{ props.stats.total_users }}
                    </div>
                    <p class="text-sm text-muted-foreground">Total Users</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-2xl font-bold">
                        {{ props.stats.active_users }}
                    </div>
                    <p class="text-sm text-muted-foreground">Active Users</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-2xl font-bold">
                        {{ props.stats.staff_count }}
                    </div>
                    <p class="text-sm text-muted-foreground">Staff</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-2xl font-bold">
                        {{ props.stats.doctor_count }}
                    </div>
                    <p class="text-sm text-muted-foreground">Doctors</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-2xl font-bold">
                        {{ props.stats.patient_count }}
                    </div>
                    <p class="text-sm text-muted-foreground">Patients</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative max-w-sm flex-1">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search users..."
                        class="pl-9"
                        @input="updateFilters"
                    />
                </div>

                <Select
                    v-model="typeFilter"
                    @update:model-value="updateFilters"
                >
                    <SelectTrigger class="w-40">
                        <SelectValue placeholder="All Types" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Types</SelectItem>
                        <SelectItem value="staff">Staff</SelectItem>
                        <SelectItem value="doctor">Doctor</SelectItem>
                        <SelectItem value="patient">Patient</SelectItem>
                    </SelectContent>
                </Select>

                <Select
                    v-model="statusFilter"
                    @update:model-value="updateFilters"
                >
                    <SelectTrigger class="w-40">
                        <SelectValue placeholder="All Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Status</SelectItem>
                        <SelectItem value="active">Active</SelectItem>
                        <SelectItem value="inactive">Inactive</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Users Table -->
            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Role/Department</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Verified</TableHead>
                            <TableHead class="w-[150px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in filteredUsers" :key="user.id">
                            <TableCell class="font-medium">{{
                                user.name
                            }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>
                                <Badge
                                    :variant="
                                        user.type === 'doctor'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{ user.type }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <span v-if="user.role_name">{{
                                    user.role_name
                                }}</span>
                                <span v-else-if="user.department_name">{{
                                    user.department_name
                                }}</span>
                                <span v-else class="text-muted-foreground"
                                    >-</span
                                >
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="
                                        user.status === 'active'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{ user.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    v-if="user.email_verified_at"
                                    variant="outline"
                                >
                                    <UserCheck class="mr-1 size-3" />
                                    Verified
                                </Badge>
                                <Badge v-else variant="destructive">
                                    <UserX class="mr-1 size-3" />
                                    Unverified
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="show(user.id).url">
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="edit(user.id).url">
                                            <Edit class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="toggleUserStatus(user.id)"
                                        :title="
                                            user.status === 'active'
                                                ? 'Deactivate User'
                                                : 'Activate User'
                                        "
                                    >
                                        <UserX
                                            v-if="user.status === 'active'"
                                            class="size-4"
                                        />
                                        <UserCheck v-else class="size-4" />
                                    </Button>
                                    <Button variant="ghost" size="sm">
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="filteredUsers.length === 0">
                            <TableCell
                                colspan="7"
                                class="text-center text-muted-foreground"
                            >
                                No users found matching your criteria
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
