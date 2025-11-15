<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import RoleForm from './RoleForm.vue';

interface Props {
    available_permissions: {
        id: number;
        name: string;
        group: string;
    }[];
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
        title: 'Create',
        href: '#',
    },
];

const handleSuccess = () => {
    // Redirect handled by Inertia
};
</script>

<template>
    <Head title="Create Role" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/settings/roles">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Create Role</h1>
                    <p class="text-muted-foreground">Add a new role with permissions</p>
                </div>
            </div>

            <div class="max-w-4xl">
                <RoleForm
                    action="/settings/roles"
                    method="post"
                    :available_permissions="props.available_permissions"
                    @success="handleSuccess"
                />
            </div>
        </div>
    </AppLayout>
</template>
