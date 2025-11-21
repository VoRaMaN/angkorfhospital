<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { show } from '@/routes/patients';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    AlertTriangle,
    CalendarDays,
    FileText,
    Stethoscope,
    Users,
} from 'lucide-vue-next';

interface Props {
    stats?: {
        total_patients: number;
        total_staff: number;
        todays_appointments: number;
        pending_medical_orders: number;
        low_stock_items: number;
    };
    search?: string;
    patients?: any[];
    recentPatients?: any[];
}

const props = defineProps<Props>();

const searchValue = ref(props.search || '');
const loading = ref(false);

const stats = props.stats || {
    total_patients: 0,
    total_staff: 0,
    todays_appointments: 0,
    pending_medical_orders: 0,
    low_stock_items: 0,
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const performSearch = () => {
    if (searchValue.value.trim() === '' && !props.search) return;
    loading.value = true;
    router.visit(dashboard().url, {
        method: 'get',
        data: { search: searchValue.value },
        only: ['patients', 'search'],
        preserveState: true,
        onFinish: () => loading.value = false,
    });
};

const formatDOB = (patient: any) => {
    if (!patient.date_of_birth_day || !patient.date_of_birth_month || !patient.date_of_birth_year) {
        return 'N/A';
    }
    const day = String(patient.date_of_birth_day).padStart(2, '0');
    const month = String(patient.date_of_birth_month).padStart(2, '0');
    const year = patient.date_of_birth_year;
    return `${day}/${month}/${year}`;
};
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Patients</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.total_patients }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Registered patients
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Staff</CardTitle>
                        <Stethoscope class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.total_staff }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Active staff members
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Today's Appointments</CardTitle>
                        <CalendarDays class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.todays_appointments }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Scheduled for today
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Orders</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.pending_medical_orders }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Awaiting completion
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Low Stock Items</CardTitle>
                        <AlertTriangle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.low_stock_items }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Need restocking
                        </p>
                    </CardContent>
                </Card>
            </div>
            <!-- Search Patients -->
            <Card>
                <CardHeader>
                    <CardTitle>Search Patients</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Phone</TableHead>
                                <TableHead>DOB</TableHead>
                                <TableHead class="w-[100px]">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <!-- Search Row -->
                            <TableRow>
                                <TableCell colspan="5" class="p-4">
                                    <div class="flex gap-2">
                                        <input
                                            v-model="searchValue"
                                            type="text"
                                            placeholder="Search by name, ID, phone..."
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            @keyup.enter="performSearch"
                                        />
                                        <button
                                            type="button"
                                            :disabled="loading"
                                            @click="performSearch"
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                                        >
                                            <span v-if="loading">Searching...</span>
                                            <span v-else>Search</span>
                                        </button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <!-- Loading Row -->
                            <TableRow v-if="loading">
                                <TableCell colspan="5" class="text-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                                    <p class="mt-2 text-sm text-muted-foreground">Searching...</p>
                                </TableCell>
                            </TableRow>
                            <!-- Results Rows -->
                            <TableRow v-else-if="props.patients && props.patients.length > 0" v-for="patient in props.patients" :key="patient.id">
                                <TableCell>{{ patient.id }}</TableCell>
                                <TableCell class="font-medium">
                                    <button class="text-primary hover:underline" @click="router.visit(show({ query: { patient: patient.id } }).url)">
                                        {{ patient.name }}
                                    </button>
                                </TableCell>
                                <TableCell>{{ patient.mobile_phone || patient.home_phone || 'N/A' }}</TableCell>
                                <TableCell>{{ formatDOB(patient) }}</TableCell>
                                <TableCell>
                                    <button class="text-primary hover:underline" @click="router.visit(show({ query: { patient: patient.id } }).url)">
                                        View
                                    </button>
                                </TableCell>
                            </TableRow>
                            <!-- No Results Row -->
                            <TableRow v-else-if="!loading && props.patients && props.patients.length === 0">
                                <TableCell colspan="5" class="text-center py-8 text-muted-foreground">
                                    No patients found matching your search.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Recent Patients -->
            <Card v-if="props.recentPatients && props.recentPatients.length > 0">
                <CardHeader>
                    <CardTitle>Recent Patients</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Phone</TableHead>
                                <TableHead>DOB</TableHead>
                                <TableHead class="w-[100px]">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="patient in props.recentPatients" :key="patient.id">
                                <TableCell>{{ patient.id }}</TableCell>
                                <TableCell class="font-medium">
                                    <button class="text-primary hover:underline" @click="router.visit(show({ query: { patient: patient.id } }).url)">
                                        {{ patient.name }}
                                    </button>
                                </TableCell>
                                <TableCell>{{ patient.mobile_phone || patient.home_phone || 'N/A' }}</TableCell>
                                <TableCell>{{ formatDOB(patient) }}</TableCell>
                                <TableCell>
                                    <button class="text-primary hover:underline" @click="router.visit(show({ query: { patient: patient.id } }).url)">
                                        View
                                    </button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
