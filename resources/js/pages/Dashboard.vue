<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as patientsIndex, show } from '@/routes/patients';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    AlertTriangle,
    ArrowUpRight,
    Calendar,
    CalendarDays,
    FileText,
    MoreHorizontal,
    Phone,
    Search,
    Stethoscope,
    Users,
    X,
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
    phoneSearch?: string;
    dobSearch?: string;
    patients?: any[];
    recentPatients?: any[];
}

const props = defineProps<Props>();

const searchValue = ref(props.search || '');
const phoneValue = ref(props.phoneSearch || '');
const dobValue = ref(props.dobSearch || '');
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
    const hasQuery = searchValue.value.trim() || phoneValue.value.trim() || dobValue.value.trim();
    if (!hasQuery && !props.search && !props.phoneSearch && !props.dobSearch) return;
    loading.value = true;
    router.visit(dashboard().url, {
        method: 'get',
        data: {
            search: searchValue.value,
            phone: phoneValue.value,
            dob: dobValue.value,
        },
        only: ['patients', 'search', 'phoneSearch', 'dobSearch'],
        preserveState: true,
        onFinish: () => (loading.value = false),
    });
};

const clearSearch = () => {
    searchValue.value = '';
    phoneValue.value = '';
    dobValue.value = '';
    loading.value = true;
    router.visit(dashboard().url, {
        method: 'get',
        only: ['patients', 'search', 'phoneSearch', 'dobSearch'],
        preserveState: true,
        onFinish: () => (loading.value = false),
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
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">

            <!-- Page Title -->
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
                <p class="text-sm text-muted-foreground">Welcome back! Here's what's happening today.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">

                <!-- Total Patients -->
                <Card class="overflow-hidden">
                    <CardContent class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 shadow-lg shadow-blue-200/60 dark:shadow-blue-900/40">
                                    <Users class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-muted-foreground">Total Patients</p>
                                    <p class="mt-0.5 text-2xl font-bold">{{ stats.total_patients }}</p>
                                    <p class="text-xs text-muted-foreground">Registered patients</p>
                                </div>
                            </div>
                            <MoreHorizontal class="h-4 w-4 shrink-0 text-muted-foreground/50" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Total Staff -->
                <Card class="overflow-hidden">
                    <CardContent class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-400 to-purple-600 shadow-lg shadow-purple-200/60 dark:shadow-purple-900/40">
                                    <Stethoscope class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-muted-foreground">Total Staff</p>
                                    <p class="mt-0.5 text-2xl font-bold">{{ stats.total_staff }}</p>
                                    <p class="text-xs text-muted-foreground">Active staff members</p>
                                </div>
                            </div>
                            <MoreHorizontal class="h-4 w-4 shrink-0 text-muted-foreground/50" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Today's Appointments -->
                <Card class="overflow-hidden">
                    <CardContent class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-green-600 shadow-lg shadow-green-200/60 dark:shadow-green-900/40">
                                    <CalendarDays class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-muted-foreground">Today's Appointments</p>
                                    <p class="mt-0.5 text-2xl font-bold">{{ stats.todays_appointments }}</p>
                                    <p class="text-xs text-muted-foreground">Scheduled for today</p>
                                </div>
                            </div>
                            <MoreHorizontal class="h-4 w-4 shrink-0 text-muted-foreground/50" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Pending Orders -->
                <Card class="overflow-hidden">
                    <CardContent class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-orange-200/60 dark:shadow-orange-900/40">
                                    <FileText class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-muted-foreground">Pending Orders</p>
                                    <p class="mt-0.5 text-2xl font-bold">{{ stats.pending_medical_orders }}</p>
                                    <p class="text-xs text-muted-foreground">Awaiting completion</p>
                                </div>
                            </div>
                            <MoreHorizontal class="h-4 w-4 shrink-0 text-muted-foreground/50" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Low Stock Items -->
                <Card class="overflow-hidden">
                    <CardContent class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-400 to-rose-600 shadow-lg shadow-red-200/60 dark:shadow-red-900/40">
                                    <AlertTriangle class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-muted-foreground">Low Stock Items</p>
                                    <p class="mt-0.5 text-2xl font-bold">{{ stats.low_stock_items }}</p>
                                    <p class="text-xs text-muted-foreground">Need restocking</p>
                                </div>
                            </div>
                            <MoreHorizontal class="h-4 w-4 shrink-0 text-muted-foreground/50" />
                        </div>
                    </CardContent>
                </Card>

            </div>

            <!-- Search Patients -->
            <Card>
                <CardHeader class="pb-3">
                    <div class="flex items-center gap-2">
                        <Search class="h-4 w-4 text-muted-foreground" />
                        <CardTitle>Search Patients</CardTitle>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <!-- Name / ID / Phone -->
                        <div class="relative flex-1">
                            <input
                                v-model="searchValue"
                                type="text"
                                placeholder="Search by name, ID, or phone..."
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 pr-9 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                @keyup.enter="performSearch"
                            />
                            <Search class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground/50" />
                        </div>
                        <!-- Phone number -->
                        <div class="relative w-full sm:w-44">
                            <input
                                v-model="phoneValue"
                                type="text"
                                placeholder="Phone number"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 pr-9 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                @keyup.enter="performSearch"
                            />
                            <Phone class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground/50" />
                        </div>
                        <!-- Date of birth -->
                        <div class="relative w-full sm:w-44">
                            <input
                                v-model="dobValue"
                                type="text"
                                placeholder="Date of birth"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 pr-9 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                @keyup.enter="performSearch"
                            />
                            <Calendar class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground/50" />
                        </div>
                        <!-- Actions -->
                        <div class="flex gap-2">
                            <Button :disabled="loading" class="bg-primary" @click="performSearch">
                                <span v-if="loading">Searching...</span>
                                <span v-else>Search</span>
                            </Button>
                            <Button
                                v-if="searchValue || phoneValue || dobValue || props.patients"
                                variant="ghost"
                                class="gap-1 text-muted-foreground"
                                @click="clearSearch"
                            >
                                <X class="h-3.5 w-3.5" />
                                Clear
                            </Button>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div v-if="props.patients !== null" class="mt-4">
                        <div v-if="loading" class="flex flex-col items-center gap-2 py-10">
                            <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-primary"></div>
                            <p class="text-sm text-muted-foreground">Searching...</p>
                        </div>
                        <template v-else>
                            <Table v-if="props.patients && props.patients.length > 0">
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>ID</TableHead>
                                        <TableHead>Name</TableHead>
                                        <TableHead>Phone</TableHead>
                                        <TableHead>DOB</TableHead>
                                        <TableHead class="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="patient in props.patients"
                                        :key="patient.id"
                                        class="cursor-pointer hover:bg-muted/40"
                                        @click="router.visit(show({ query: { patient: patient.id } }).url)"
                                    >
                                        <TableCell class="font-mono text-xs">{{ patient.id }}</TableCell>
                                        <TableCell class="font-medium">
                                            {{ patient.title ? patient.title + ' ' : '' }}{{ patient.name }} {{ patient.surname }}
                                        </TableCell>
                                        <TableCell>
                                            <span class="flex items-center gap-1.5 text-muted-foreground">
                                                <Phone class="h-3.5 w-3.5" />
                                                {{ patient.mobile_phone || patient.home_phone || 'N/A' }}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <span class="flex items-center gap-1.5 text-muted-foreground">
                                                <Calendar class="h-3.5 w-3.5" />
                                                {{ formatDOB(patient) }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                class="border rounded-lg"
                                                @click.stop="router.visit(show({ query: { patient: patient.id } }).url)"
                                            >
                                                View
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                            <p v-else-if="props.patients && props.patients.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                                No patients found matching your search.
                            </p>
                        </template>
                    </div>
                </CardContent>
            </Card>

            <!-- Recent Patients -->
            <Card v-if="props.recentPatients && props.recentPatients.length > 0">
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Users class="h-4 w-4 text-muted-foreground" />
                            <CardTitle>Recent Patients</CardTitle>
                        </div>
                        <Link
                            :href="patientsIndex().url"
                            class="flex items-center gap-1 text-sm font-medium text-primary hover:underline"
                        >
                            View all patients
                            <ArrowUpRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="pl-6">ID</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Phone</TableHead>
                                <TableHead>DOB</TableHead>
                                <TableHead class="pr-6 text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="patient in props.recentPatients"
                                :key="patient.id"
                                class="cursor-pointer hover:bg-muted/40"
                                @click="router.visit(show({ query: { patient: patient.id } }).url)"
                            >
                                <TableCell class="pl-6 font-mono text-xs">{{ patient.id }}</TableCell>
                                <TableCell class="font-medium">
                                    {{ patient.title ? patient.title + ' ' : '' }}{{ patient.name }} {{ patient.surname }}
                                </TableCell>
                                <TableCell>
                                    <span class="flex items-center gap-1.5 text-muted-foreground">
                                        <Phone class="h-3.5 w-3.5" />
                                        {{ patient.mobile_phone || patient.home_phone || 'N/A' }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <span class="flex items-center gap-1.5 text-muted-foreground">
                                        <Calendar class="h-3.5 w-3.5" />
                                        {{ formatDOB(patient) }}
                                    </span>
                                </TableCell>
                                <TableCell class="pr-6 text-right">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="border rounded-lg"
                                        @click.stop="router.visit(show({ query: { patient: patient.id } }).url)"
                                    >
                                        View
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>

