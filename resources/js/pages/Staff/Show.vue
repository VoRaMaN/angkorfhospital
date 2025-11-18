<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';
import StaffFilesTab from './StaffFilesTab.vue';
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
        contact_number: string;
        status: string;
        created_at: string;
        updated_at: string;
        staffFiles?: Array<any>;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Staff',
        href: '/staff',
    },
    {
        title: 'Details',
        href: '#',
    },
];

const { hasPermission } = useAuth();
</script>

<template>
    <Head title="Staff Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_staff')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/staff">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Staff Details</h1>
                    <p class="text-muted-foreground">View staff information</p>
                </div>
                <div class="ml-auto">
                    <Button v-if="hasPermission('edit_staff')" variant="outline" as-child>
                        <Link :href="`/staff/${props.staff.id}/edit`">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <Tabs default-value="details" class="w-full">
                    <TabsList class="grid w-full grid-cols-2">
                        <TabsTrigger value="details">Staff Details</TabsTrigger>
                        <TabsTrigger value="files">Files</TabsTrigger>
                    </TabsList>

                    <TabsContent value="details" class="mt-6">
                        <div class="rounded-lg border bg-card p-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Full Name
                                    </dt>
                                    <dd class="text-sm font-medium">
                                        {{ props.staff.name }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Email
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.staff.email }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Role
                                    </dt>
                                    <dd class="text-sm">
                                        <Badge variant="outline">{{
                                            props.staff.role_name
                                        }}</Badge>
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Department
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.staff.department_name }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Hire Date
                                    </dt>
                                    <dd class="text-sm">
                                        {{
                                            new Date(
                                                props.staff.hire_date,
                                            ).toLocaleDateString()
                                        }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Status
                                    </dt>
                                    <dd class="text-sm">
                                        <Badge
                                            :variant="
                                                props.staff.status === 'active'
                                                    ? 'default'
                                                    : 'secondary'
                                            "
                                        >
                                            {{ props.staff.status }}
                                        </Badge>
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Phone
                                    </dt>
                                    <dd class="text-sm">
                                        {{
                                            props.staff.contact_number ||
                                            'Not provided'
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
                                                props.staff.created_at,
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
                                                props.staff.updated_at,
                                            ).toLocaleString()
                                        }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </TabsContent>

                    <TabsContent value="files" class="mt-6">
                        <StaffFilesTab :staff="props.staff" />
                    </TabsContent>
                </Tabs>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view staff details.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
