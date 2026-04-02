<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Calendar, Download, FileText, Search } from 'lucide-vue-next';
import { ref } from 'vue';
import { index as medicineReportIndex, exportMethod as medicineReportExport } from '@/routes/medicine-report';

interface Medicine {
    medicine_name: string;
    quantity: number;
    type: string;
    date: string;
    unit_price: number;
    selling_price: number;
    total_cost: number;
}

interface MedicineUsage {
    patient_id: number;
    patient_name: string;
    medicines: Medicine[];
    total_medicines: number;
    total_cost: number;
}

interface Props {
    medicineUsage: MedicineUsage[];
    filters: {
        start_date: string;
        end_date: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medicine Report',
        href: '#',
    },
];

const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');

const searchReport = () => {
    router.get(
        medicineReportIndex().url,
        {
            start_date: startDate.value,
            end_date: endDate.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
};

const exportAll = () => {
    window.location.href = medicineReportExport({ query: {
        start_date: startDate.value,
        end_date: endDate.value,
    }}).url;
};

const exportPatient = (patientId: number) => {
    window.location.href = medicineReportExport({ query: {
        start_date: startDate.value,
        end_date: endDate.value,
        patient_id: patientId,
    }}).url;
};

const formatPrice = (price: number) => {
    return `$${price.toFixed(2)}`;
};
</script>

<template>
    <AppLayout title="Medicine Report" :breadcrumbs="breadcrumbs">
        <Head title="Medicine Report" />

        <div class="container mx-auto space-y-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Medicine Report</h1>
                    <p class="text-muted-foreground">
                        Track medicine usage by patients over time
                    </p>
                </div>
            </div>

            <!-- Date Range Filter -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Calendar class="h-5 w-5" />
                        Select Date Range
                    </CardTitle>
                    <CardDescription>
                        Choose a date range to view medicine usage report
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <Label for="start_date">From Date</Label>
                            <Input
                                id="start_date"
                                v-model="startDate"
                                type="date"
                                class="mt-1"
                            />
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <Label for="end_date">To Date</Label>
                            <Input
                                id="end_date"
                                v-model="endDate"
                                type="date"
                                class="mt-1"
                            />
                        </div>
                        <div class="flex gap-2">
                            <Button @click="searchReport" :disabled="!startDate || !endDate">
                                <Search class="mr-2 h-4 w-4" />
                                Search
                            </Button>
                            <Button
                                v-if="medicineUsage.length > 0"
                                @click="exportAll"
                                variant="outline"
                            >
                                <Download class="mr-2 h-4 w-4" />
                                Export All
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Results -->
            <div v-if="!startDate || !endDate" class="rounded-lg border bg-muted/50 p-12 text-center">
                <FileText class="mx-auto h-12 w-12 text-muted-foreground" />
                <h3 class="mt-4 text-lg font-semibold">No Date Range Selected</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Please select a date range and click search to view the medicine report.
                </p>
            </div>

            <div v-else-if="medicineUsage.length === 0" class="rounded-lg border bg-muted/50 p-12 text-center">
                <FileText class="mx-auto h-12 w-12 text-muted-foreground" />
                <h3 class="mt-4 text-lg font-semibold">No Medicine Usage Found</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    No medicine usage records found for the selected date range.
                </p>
            </div>

            <div v-else class="space-y-6">
                <div class="rounded-lg border bg-card p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Patients</p>
                            <p class="text-2xl font-bold">{{ medicineUsage.length }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Total Cost</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ formatPrice(medicineUsage.reduce((sum, p) => sum + p.total_cost, 0)) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Total Medicines</p>
                            <p class="text-2xl font-bold">
                                {{ medicineUsage.reduce((sum, p) => sum + p.total_medicines, 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                <Card v-for="usage in medicineUsage" :key="usage.patient_id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>{{ usage.patient_name }}</CardTitle>
                                <CardDescription>
                                    {{ usage.medicines.length }} medicine(s) | Total: {{ formatPrice(usage.total_cost) }}
                                </CardDescription>
                            </div>
                            <Button @click="exportPatient(usage.patient_id)" variant="outline" size="sm">
                                <Download class="mr-2 h-4 w-4" />
                                Export
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Medicine Name</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead class="text-right">Quantity</TableHead>
                                    <TableHead class="text-right">Unit Price</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                    <TableHead>Date</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(medicine, idx) in usage.medicines" :key="idx">
                                    <TableCell class="font-medium">{{ medicine.medicine_name }}</TableCell>
                                    <TableCell>
                                        <Badge variant="secondary">{{ medicine.type }}</Badge>
                                    </TableCell>
                                    <TableCell class="text-right">{{ medicine.quantity }}</TableCell>
                                    <TableCell class="text-right">{{ formatPrice(medicine.selling_price) }}</TableCell>
                                    <TableCell class="text-right font-semibold text-green-600">
                                        {{ formatPrice(medicine.total_cost) }}
                                    </TableCell>
                                    <TableCell>{{ medicine.date }}</TableCell>
                                </TableRow>
                                <TableRow class="bg-muted/50 font-semibold">
                                    <TableCell colspan="2">Total</TableCell>
                                    <TableCell class="text-right">{{ usage.total_medicines }}</TableCell>
                                    <TableCell></TableCell>
                                    <TableCell class="text-right text-green-600">
                                        {{ formatPrice(usage.total_cost) }}
                                    </TableCell>
                                    <TableCell></TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
