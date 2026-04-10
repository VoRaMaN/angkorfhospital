<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { formatDateTime } from '@/lib/utils';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';

interface Props {
    department: {
        id: number;
        name: string;
        description: string;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
    {
        title: 'Details',
        href: '#',
    },
];
import { useAuth } from '@/composables/useAuth';

const { hasPermission } = useAuth();
</script>

<template>
    <Head title="Department Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_departments')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/departments">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Department Details</h1>
                    <p class="text-muted-foreground">
                        View department information
                    </p>
                </div>
                <div class="ml-auto">
                    <Button v-if="hasPermission('edit_departments')" variant="outline" as-child>
                        <Link
                            :href="`/departments/${props.department.id}/edit`"
                        >
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
                                Department Name
                            </dt>
                            <dd class="text-sm">{{ props.department.name }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Created
                            </dt>
                            <dd class="text-sm">
                                {{
                                    formatDateTime(props.department.created_at)
                                }}
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
                                    props.department.description ||
                                    'No description provided'
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
                                    formatDateTime(props.department.updated_at)
                                }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view department details.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
