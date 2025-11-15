<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';

interface Props {
    title: string;
    indexRoute: string;
    editRoute?: string;
    item: Record<string, any>;
}

const props = withDefaults(defineProps<Props>(), {
    editRoute: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: props.indexRoute,
    },
    {
        title: 'Details',
        href: '#',
    },
];
</script>

<template>
    <Head :title="`${title.slice(0, -1)} Details`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="indexRoute">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">{{ title.slice(0, -1) }} Details</h1>
                    <p class="text-muted-foreground">View {{ title.slice(0, -1).toLowerCase() }} information</p>
                </div>
                <div class="ml-auto">
                    <Button v-if="editRoute" variant="outline" as-child>
                        <Link :href="editRoute.replace(':id', item.id)">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div v-for="[key, value] in Object.entries(item)" :key="key" class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">
                                {{ key.charAt(0).toUpperCase() + key.slice(1).replace(/([A-Z])/g, ' $1') }}
                            </dt>
                            <dd class="text-sm">
                                <Badge v-if="typeof value === 'boolean'" :variant="value ? 'default' : 'secondary'">
                                    {{ value ? 'Yes' : 'No' }}
                                </Badge>
                                <span v-else>{{ value || 'N/A' }}</span>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
