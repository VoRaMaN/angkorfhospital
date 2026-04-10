<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatDate } from '@/lib/utils';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, show } from '@/routes/medical-records';
import { medicalRecordReport } from '@/routes/medical-orders';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Edit, Eye, Plus, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Props {
    medicalRecords: {
        id: number;
        patient_id: number;
        patient_name: string;
        doctor_id: number;
        doctor_name: string;
        diagnosis: string;
        treatment: string;
        notes: string;
        date_of_service: string;
        created_at: string;
    }[];
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search);
let searchTimeout: number | null = null;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Records',
        href: '/medical-records',
    },
];

// Debounced search function
const performSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        router.get('/medical-records', {
            search: searchQuery.value,
            page: 1, // Reset to first page when searching
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300); // 300ms debounce
};

// Watch for search query changes
watch(searchQuery, () => {
    performSearch();
});

const deleteRecord = (id: number) => {
    if (confirm('Are you sure you want to delete this medical record?')) {
        router.delete(destroy(id).url);
    }
};
</script>

<template>
    <Head title="Medical Records" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Medical Records</h1>
                    <p class="text-muted-foreground">
                        Manage patient medical records
                    </p>
                </div>
                <div class="ml-auto">
                    <Button as-child>
                        <Link :href="create().url">
                            <Plus class="size-4" />
                            Add Record
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative max-w-sm flex-1">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search records..."
                        class="pl-9"
                    />
                </div>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Doctor</TableHead>
                            <TableHead>Diagnosis</TableHead>
                            <TableHead>Visit Date</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="record in props.medicalRecords"
                            :key="record.id"
                        >
                            <TableCell class="font-medium">{{
                                record.patient_name
                            }}</TableCell>
                            <TableCell>{{ record.doctor_name }}</TableCell>
                            <TableCell>
                                <span
                                    class="block max-w-[200px] truncate"
                                    :title="record.diagnosis"
                                >
                                    {{ record.diagnosis }}
                                </span>
                            </TableCell>
                            <TableCell>{{
                                record.date_of_service
                                    ? formatDate(record.date_of_service)
                                    : 'N/A'
                            }}</TableCell>
                            <TableCell>{{
                                formatDate(record.created_at)
                            }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="show(record.id).url">
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <a :href="medicalRecordReport(record.id).url" target="_blank">
                                            <Download class="size-4" />
                                        </a>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="edit(record.id).url">
                                            <Edit class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" @click="deleteRecord(record.id)">
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.medicalRecords.length === 0">
                            <TableCell
                                colspan="6"
                                class="text-center text-muted-foreground"
                            >
                                No medical records found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
