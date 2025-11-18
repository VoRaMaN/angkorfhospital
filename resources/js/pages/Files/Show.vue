<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit as patientEdit } from '@/routes/patient-files';
import { edit as staffEdit } from '@/routes/staff-files';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Download, Edit } from 'lucide-vue-next';
import { computed } from 'vue';
import { useAuth } from '@/composables/useAuth';

interface Props {
    title: string;
    indexRoute: string;
    item: Record<string, any>;
    isPatient?: boolean;
    fileExists?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    isPatient: false,
});

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: props.indexRoute,
    },
    {
        title: 'View',
        href: '#',
    },
];

const downloadUrl = computed(() => {
    const base = props.isPatient ? 'patient-files' : 'staff-files';
    return `/${base}/${props.item.id}/download`;
});

const inlineUrl = computed(
    () => `${downloadUrl.value}?inline=1&t=${new Date().getTime()}`,
);

const editUrl = computed(() => {
    const editRoute = props.isPatient
        ? patientEdit(props.item.id)
        : staffEdit(props.item.id);
    return editRoute.url;
});

const isImage = computed(() =>
    props.item.file?.mime_type?.startsWith('image/'),
);
const isPdf = computed(() => props.item.file?.mime_type === 'application/pdf');
</script>

<template>
    <Head :title="`View ${title.slice(0, -1)}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_files')"
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
                        View {{ title.slice(0, -1) }}
                    </h1>
                    <p class="text-muted-foreground">
                        View {{ title.slice(0, -1).toLowerCase() }} content
                    </p>
                </div>
                <div class="ml-auto flex gap-2">
                    <Button v-if="fileExists" variant="outline" as-child>
                        <a :href="downloadUrl" target="_blank">
                            <Download class="size-4" />
                            Download
                        </a>
                    </Button>
                    <Button v-if="editUrl && hasPermission('edit_files')" variant="outline" as-child>
                        <Link :href="editUrl">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <div class="rounded-lg border bg-card p-6">
                    <div
                        v-if="isImage && fileExists"
                        class="flex justify-center"
                    >
                        <img
                            :src="inlineUrl"
                            :alt="item.file.name"
                            class="max-h-96 max-w-full object-contain"
                        />
                    </div>
                    <div
                        v-else-if="isImage && !fileExists"
                        class="flex justify-center"
                    >
                        <div
                            class="flex h-48 w-full max-w-md items-center justify-center rounded bg-gray-200 text-gray-500"
                        >
                            File not found - No Preview Available
                        </div>
                    </div>
                    <div
                        v-else-if="isPdf && fileExists"
                        class="flex justify-center"
                    >
                        <iframe :src="inlineUrl" class="h-96 w-full border" />
                    </div>
                    <div
                        v-else-if="isPdf && !fileExists"
                        class="flex justify-center"
                    >
                        <div
                            class="flex h-96 w-full items-center justify-center rounded bg-gray-200 text-gray-500"
                        >
                            File not found - Cannot display PDF
                        </div>
                    </div>
                    <div v-else class="py-8 text-center">
                        <p class="text-muted-foreground">
                            This file type cannot be previewed inline.
                        </p>
                        <Button v-if="fileExists" class="mt-4" as-child>
                            <a :href="downloadUrl" target="_blank">
                                <Download class="mr-2 size-4" />
                                Download File
                            </a>
                        </Button>
                        <p v-else class="mt-4 text-gray-500">File not found</p>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div>
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                File Name
                            </dt>
                            <dd class="text-sm">
                                {{ item.file?.name || 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                File Size
                            </dt>
                            <dd class="text-sm">
                                {{
                                    item.file?.size
                                        ? `${item.file.size} bytes`
                                        : 'N/A'
                                }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                MIME Type
                            </dt>
                            <dd class="text-sm">
                                {{ item.file?.mime_type || 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Type
                            </dt>
                            <dd class="text-sm">{{ item.type || 'N/A' }}</dd>
                        </div>
                        <div v-if="item.patient">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Patient
                            </dt>
                            <dd class="text-sm">{{ item.patient.name }}</dd>
                        </div>
                        <div v-if="item.staff">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Staff
                            </dt>
                            <dd class="text-sm">{{ item.staff.name }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view files.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
