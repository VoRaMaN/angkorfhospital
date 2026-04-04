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
import { Skeleton } from '@/components/ui/skeleton';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as activityLogIndex } from '@/routes/activity-log';
import { type BreadcrumbItem } from '@/types';
import { Deferred, Head, Link, router, usePoll } from '@inertiajs/vue3';
import { Activity, Circle, Download, Filter, RotateCcw, User } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface ActivityLogEntry {
    id: number;
    user_id: number | null;
    action: string;
    subject_type: string | null;
    subject_id: number | null;
    description: string;
    properties: Record<string, unknown> | null;
    ip_address: string | null;
    user_agent: string | null;
    created_at: string;
    user: {
        id: number;
        name: string;
        email: string;
    } | null;
}

interface OnlineUser {
    id: number;
    name: string;
    email: string;
    last_active_at: string;
}

interface Props {
    activityLogs: {
        data: ActivityLogEntry[];
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        current_page: number;
        last_page: number;
        total: number;
    };
    onlineUsers: OnlineUser[];
    users: Array<{ id: number; name: string }>;
    subjectTypes: Array<{ value: string; label: string }>;
    filters: {
        user_id?: string;
        action?: string;
        subject_type?: string;
        date_from?: string;
        date_to?: string;
    };
}

const props = defineProps<Props>();

// Poll online users every 30 seconds
usePoll(30000, { only: ['onlineUsers'] });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Activity Log', href: activityLogIndex().url },
];

const filterUserId = ref(props.filters.user_id || '');
const filterAction = ref(props.filters.action || '');
const filterSubjectType = ref(props.filters.subject_type || '');
const filterDateFrom = ref(props.filters.date_from || '');
const filterDateTo = ref(props.filters.date_to || '');

const applyFilters = () => {
    router.get(
        activityLogIndex().url,
        {
            user_id: filterUserId.value || undefined,
            action: filterAction.value || undefined,
            subject_type: filterSubjectType.value || undefined,
            date_from: filterDateFrom.value || undefined,
            date_to: filterDateTo.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const clearFilters = () => {
    filterUserId.value = '';
    filterAction.value = '';
    filterSubjectType.value = '';
    filterDateFrom.value = '';
    filterDateTo.value = '';
    router.get(activityLogIndex().url, {}, { preserveState: true, replace: true });
};

// Debounce date filter changes
let filterTimeout: number | null = null;
watch([filterUserId, filterAction, filterSubjectType], () => {
    if (filterTimeout) clearTimeout(filterTimeout);
    filterTimeout = setTimeout(applyFilters, 300);
});

const paginationLinks = computed(() => {
    return props.activityLogs.links.filter((link) => link.url);
});

const actionVariant = (action: string) => {
    switch (action) {
        case 'created':
            return 'default';
        case 'updated':
            return 'secondary';
        case 'deleted':
            return 'destructive';
        default:
            return 'outline';
    }
};

const timeAgo = (dateStr: string) => {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours}h ago`;

    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return `${diffDays}d ago`;

    return date.toLocaleDateString();
};

const formatDateTime = (dateStr: string) => {
    return new Date(dateStr).toLocaleString();
};

const subjectLabel = (type: string | null) => {
    if (!type) return '-';
    const parts = type.split('\\');
    return parts[parts.length - 1];
};

const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (filterUserId.value) params.set('user_id', filterUserId.value);
    if (filterAction.value) params.set('action', filterAction.value);
    if (filterSubjectType.value) params.set('subject_type', filterSubjectType.value);
    if (filterDateFrom.value) params.set('date_from', filterDateFrom.value);
    if (filterDateTo.value) params.set('date_to', filterDateTo.value);
    const qs = params.toString();
    return `/activity-log/export${qs ? '?' + qs : ''}`;
});
</script>

<template>
    <Head title="Activity Log" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Activity Log</h1>
                    <p class="text-muted-foreground">Monitor system activity and user sessions</p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" size="sm" as="a" :href="exportUrl">
                        <Download class="mr-1 size-4" />
                        Export CSV
                    </Button>
                </div>
            </div>

            <!-- Who's Online -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-lg">
                        <Circle class="size-3 fill-green-500 text-green-500" />
                        Online Now ({{ props.onlineUsers?.length ?? 0 }})
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Deferred data="onlineUsers">
                        <template #fallback>
                            <div class="flex flex-wrap gap-3">
                                <div v-for="n in 3" :key="n" class="flex items-center gap-2 rounded-lg border bg-card px-3 py-2">
                                    <Skeleton class="size-8 rounded-full" />
                                    <div class="space-y-1">
                                        <Skeleton class="h-4 w-20" />
                                        <Skeleton class="h-3 w-14" />
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div v-if="props.onlineUsers && props.onlineUsers.length > 0" class="flex flex-wrap gap-3">
                            <div
                                v-for="onlineUser in props.onlineUsers"
                                :key="onlineUser.id"
                                class="flex items-center gap-2 rounded-lg border bg-card px-3 py-2"
                            >
                                <div class="flex size-8 items-center justify-center rounded-full bg-primary/10">
                                    <User class="size-4 text-primary" />
                                </div>
                                <div class="text-sm">
                                    <div class="font-medium">{{ onlineUser.name }}</div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ timeAgo(onlineUser.last_active_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">No users currently online</p>
                    </Deferred>
                </CardContent>
            </Card>

            <!-- Filters -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-lg">
                        <Filter class="size-4" />
                        Filters
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Deferred :data="['users', 'subjectTypes']">
                        <template #fallback>
                            <div class="flex flex-wrap items-end gap-3">
                                <div v-for="n in 5" :key="n" class="min-w-[140px] space-y-1">
                                    <Skeleton class="h-3 w-12" />
                                    <Skeleton class="h-9 w-full" />
                                </div>
                            </div>
                        </template>
                        <div class="flex flex-wrap items-end gap-3">
                            <div class="min-w-[160px]">
                                <label class="mb-1 block text-xs text-muted-foreground">User</label>
                                <Select v-model="filterUserId">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All users" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All users</SelectItem>
                                        <SelectItem
                                            v-for="user in props.users"
                                            :key="user.id"
                                            :value="String(user.id)"
                                        >
                                            {{ user.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="min-w-[140px]">
                                <label class="mb-1 block text-xs text-muted-foreground">Action</label>
                                <Select v-model="filterAction">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All actions" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All actions</SelectItem>
                                        <SelectItem value="created">Created</SelectItem>
                                        <SelectItem value="updated">Updated</SelectItem>
                                        <SelectItem value="deleted">Deleted</SelectItem>
                                        <SelectItem value="login">Login</SelectItem>
                                        <SelectItem value="logout">Logout</SelectItem>
                                        <SelectItem value="failed_login">Failed Login</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="min-w-[160px]">
                                <label class="mb-1 block text-xs text-muted-foreground">Model</label>
                                <Select v-model="filterSubjectType">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All models" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All models</SelectItem>
                                        <SelectItem
                                            v-for="st in props.subjectTypes"
                                            :key="st.value"
                                            :value="st.value"
                                        >
                                            {{ st.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="min-w-[150px]">
                                <label class="mb-1 block text-xs text-muted-foreground">From</label>
                                <Input
                                    v-model="filterDateFrom"
                                    type="date"
                                    @change="applyFilters"
                                />
                            </div>

                            <div class="min-w-[150px]">
                                <label class="mb-1 block text-xs text-muted-foreground">To</label>
                                <Input
                                    v-model="filterDateTo"
                                    type="date"
                                    @change="applyFilters"
                                />
                            </div>

                            <Button variant="outline" size="sm" @click="clearFilters">
                                <RotateCcw class="mr-1 size-3" />
                                Clear
                            </Button>
                        </div>
                    </Deferred>
                </CardContent>
            </Card>

            <!-- Activity Log Table -->
            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>User</TableHead>
                            <TableHead>Action</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Model</TableHead>
                            <TableHead>IP Address</TableHead>
                            <TableHead>Time</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="log in props.activityLogs.data"
                            :key="log.id"
                        >
                            <TableCell class="font-medium">
                                {{ log.user?.name ?? 'System' }}
                            </TableCell>
                            <TableCell>
                                <Badge :variant="actionVariant(log.action)">
                                    {{ log.action }}
                                </Badge>
                            </TableCell>
                            <TableCell class="max-w-sm truncate">
                                {{ log.description }}
                            </TableCell>
                            <TableCell>
                                <Badge variant="outline">
                                    {{ subjectLabel(log.subject_type) }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-muted-foreground">
                                {{ log.ip_address ?? '-' }}
                            </TableCell>
                            <TableCell class="whitespace-nowrap text-muted-foreground" :title="formatDateTime(log.created_at)">
                                {{ timeAgo(log.created_at) }}
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.activityLogs.data.length === 0">
                            <TableCell colspan="6" class="text-center text-muted-foreground">
                                No activity logs found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Total: {{ props.activityLogs.total }} entries
                </p>
                <div
                    class="flex gap-2"
                    v-if="props.activityLogs.last_page > 1"
                >
                    <Button
                        v-for="link in paginationLinks"
                        :key="link.label"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        as-child
                    >
                        <Link :href="link.url!" v-html="link.label" />
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
