<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    role: {
        id: number;
        name: string;
        description: string;
        permissions: {
            id: number;
            name: string;
            group: string;
        }[];
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
    {
        title: 'Roles',
        href: '/settings/roles',
    },
    {
        title: 'Details',
        href: '#',
    },
];

// Group permissions by their group for display
const groupedPermissions = computed(() => {
    const groups: Record<string, typeof props.role.permissions> = {};
    props.role.permissions.forEach((permission) => {
        if (!groups[permission.group]) {
            groups[permission.group] = [];
        }
        groups[permission.group].push(permission);
    });
    return groups;
});
</script>

<template>
    <Head title="Role Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/settings/roles">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Role Details</h1>
                    <p class="text-muted-foreground">
                        View role information and permissions
                    </p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="`/settings/roles/${props.role.id}/edit`">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Role Name
                            </dt>
                            <dd class="text-sm font-medium">
                                {{ props.role.name }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Permissions Count
                            </dt>
                            <dd class="text-sm">
                                <Badge variant="outline"
                                    >{{
                                        props.role.permissions.length
                                    }}
                                    permissions</Badge
                                >
                            </dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Description
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.role.description ||
                                    'No description provided'
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Created
                            </dt>
                            <dd class="text-sm">
                                {{
                                    new Date(
                                        props.role.created_at,
                                    ).toLocaleString()
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Last Updated
                            </dt>
                            <dd class="text-sm">
                                {{
                                    new Date(
                                        props.role.updated_at,
                                    ).toLocaleString()
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Permissions
                            </dt>
                            <dd class="text-sm">
                                <div
                                    v-if="
                                        Object.keys(groupedPermissions)
                                            .length === 0
                                    "
                                    class="text-muted-foreground"
                                >
                                    No permissions assigned
                                </div>
                                <div v-else class="space-y-4">
                                    <div
                                        v-for="(
                                            permissions, group
                                        ) in groupedPermissions"
                                        :key="group"
                                        class="space-y-2"
                                    >
                                        <h4
                                            class="text-sm font-medium tracking-wide text-muted-foreground uppercase"
                                        >
                                            {{ group }}
                                        </h4>
                                        <div class="flex flex-wrap gap-2">
                                            <Badge
                                                v-for="permission in permissions"
                                                :key="permission.id"
                                                variant="secondary"
                                            >
                                                {{ permission.name }}
                                            </Badge>
                                        </div>
                                    </div>
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
