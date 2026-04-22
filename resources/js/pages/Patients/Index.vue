<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, show } from '@/routes/patients';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search, Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const navigateToPatient = (patientId: string) => {
    router.visit(show({ query: { patient: patientId } }).url);
};

interface Props {
    patients: {
        data: Array<{
            id: string;
            user: { name: string; email: string } | null;
            full_name: string;
            name: string;
            surname: string;
            date_of_birth_day: number;
            date_of_birth_month: number;
            date_of_birth_year: number;
            gender: string;
            mobile_phone: string;
            home_phone: string;
        }>;
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters: {
        search: string;
        show_all: boolean;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const searchQuery = ref(props.filters.search || '');
const showAll = ref(props.filters.show_all || false);
let searchTimeout: number | null = null;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Patients',
        href: '#',
    },
];

// Debounced search function
const performSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        router.get('/patients', {
            search: searchQuery.value,
            show_all: showAll.value ? '1' : undefined,
            page: 1, // Reset to first page when searching
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300); // 300ms debounce
};

// Toggle show all patients
const toggleShowAll = () => {
    showAll.value = !showAll.value;
    router.get('/patients', {
        search: searchQuery.value,
        show_all: showAll.value ? '1' : undefined,
        page: 1,
    }, {
        preserveState: true,
        replace: true,
    });
};

// Watch for search query changes
watch(searchQuery, () => {
    performSearch();
});

const hasSearch = computed(() => searchQuery.value.trim() !== '' || showAll.value);
</script>

<template>

    <Head title="Patients" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_patients')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Patients</h1>
                    <p class="text-muted-foreground">Manage patient records</p>
                </div>
                <Button as-child v-if="hasPermission('create_patients')">
                    <Link :href="create().url">
                    <Plus class="size-4" />
                    Add Patient
                    </Link>
                </Button>
            </div>

            <!-- Search and Filters -->
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search patients..."
                        class="pl-10"
                    />
                </div>
                <Button
                    variant="outline"
                    size="sm"
                    @click="toggleShowAll"
                    class="gap-2"
                >
                    <component :is="showAll ? Eye : EyeOff" class="size-4" />
                    {{ showAll ? 'Hide Inactive' : 'Show All' }}
                </Button>
                <div class="text-sm text-muted-foreground">
                    {{ props.patients.total }} patient{{ props.patients.total !== 1 ? 's' : '' }} found
                </div>
            </div>

            <div class="rounded-md border">
                <Table v-if="hasSearch">
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Date of Birth</TableHead>
                            <TableHead>Gender</TableHead>
                            <TableHead>Phone</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="patient in props.patients.data"
                            :key="patient.id"
                            class="cursor-pointer hover:bg-muted/50"
                            @click="navigateToPatient(patient.id)"
                        >
                            <TableCell class="font-mono text-sm">{{ patient.id }}</TableCell>
                            <TableCell>
                                <span class="font-medium">
                                    {{ patient.full_name }}
                                </span>
                            </TableCell>
                            <TableCell>{{ patient.user?.email || 'No Email' }}</TableCell>
                            <TableCell>{{ patient.date_of_birth_day }}/{{ patient.date_of_birth_month }}/{{ patient.date_of_birth_year }}</TableCell>
                            <TableCell>{{ patient.gender }}</TableCell>
                            <TableCell>{{ patient.mobile_phone || patient.home_phone }}</TableCell>
                            <TableCell @click.stop>
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="show({ query: { patient: patient.id } }).url"
                                            >View</Link
                                        >
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="edit({ query: { patient: patient.id } }).url"
                                            >Edit</Link
                                        >
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.patients.data.length === 0">
                            <TableCell
                                colspan="7"
                                class="text-center text-muted-foreground"
                            >
                                No patients found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <div v-else class="flex flex-col items-center justify-center py-16 text-muted-foreground gap-2">
                    <Search class="size-10 opacity-30" />
                    <p class="text-sm">Search for a patient to view results</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="props.patients.last_page > 1" class="flex items-center justify-between">
                <div class="text-sm text-muted-foreground">
                    Showing {{ (props.patients.current_page - 1) * props.patients.per_page + 1 }} to {{ Math.min(props.patients.current_page * props.patients.per_page, props.patients.total) }} of {{ props.patients.total }} results
                </div>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="props.patients.current_page === 1"
                        @click="router.get('/patients', { search: searchQuery, show_all: showAll ? '1' : undefined, page: props.patients.current_page - 1 }, { preserveState: true })"
                    >
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="props.patients.current_page === props.patients.last_page"
                        @click="router.get('/patients', { search: searchQuery, show_all: showAll ? '1' : undefined, page: props.patients.current_page + 1 }, { preserveState: true })"
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>
        <div v-else class="flex h-full flex-1 items-center justify-center">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">
                    Access Denied
                </h2>
                <p class="text-muted-foreground">
                    You do not have permission to view patients.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
