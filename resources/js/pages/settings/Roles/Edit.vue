<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import RoleForm from './RoleForm.vue';

interface Props {
    role: {
        id: number;
        name: string;
        description: string;
        permissions: number[];
    };
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
        title: 'Edit',
        href: '#',
    },
];

const handleSuccess = () => {
    // Redirect handled by Inertia
};
</script>

<template>

    <Head title="Edit Role" />

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
                    <h1 class="text-2xl font-bold">Edit Role</h1>
                    <p class="text-muted-foreground">Update role information and permissions</p>
                </div>
            </div>

            <div class="max-w-4xl">
                <RoleForm :action="`/settings/roles/${props.role.id}`" method="put" :initial-name="props.role.name"
                    :initial-description="props.role.description" :initial-permissions="props.role.permissions"
                    :available_permissions="props.available_permissions" @success="handleSuccess" />
            </div>
        </div>
    </AppLayout>
</template>
