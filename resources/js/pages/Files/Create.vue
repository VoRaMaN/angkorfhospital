<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import FileForm from './FileForm.vue';
import { useAuth } from '@/composables/useAuth';

interface Props {
    title: string;
    indexRoute: string;
    patients?: Array<Record<string, any>>;
    staff?: Array<Record<string, any>>;
    typeOptions?: Record<string, string>;
    currentStaff?: Record<string, any>;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: props.indexRoute,
    },
    {
        title: 'Create',
        href: '#',
    },
];
</script>

<template>
    <Head :title="`Create ${title.slice(0, -1)}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('create_files')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="indexRoute">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">
                        Create {{ title.slice(0, -1) }}
                    </h1>
                    <p class="text-muted-foreground">
                        Add a new {{ title.slice(0, -1).toLowerCase() }}
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <FileForm
                    mode="create"
                    :index-route="indexRoute"
                    :patients="patients"
                    :staff="staff"
                    :type-options="typeOptions"
                    :current-staff="currentStaff"
                />
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to create files.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
