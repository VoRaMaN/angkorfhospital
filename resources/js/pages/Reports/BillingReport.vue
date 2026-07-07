<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Calendar, Download, FileText, TrendingUp } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { index as billingReportIndex, exportMethod as billingReportExport } from '@/routes/billing-report';

interface Billing {
    id: number;
    bill_no: string;
    billing_date: string;
    amount: number;
    status: string;
    payment_method: string;
}

interface PatientBilling {
    patient_id: number;
    patient_name: string;
    total_amount: number;
    bill_count: number;
    billings: Billing[];
}

interface Summary {
    total_revenue: number;
    total_bills: number;
    average_bill: number;
    paid_count: number;
    unpaid_count: number;
}

interface Props {
    billingData: PatientBilling[];
    summary: Summary;
    filters: {
        start_date: string;
        end_date: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billing Report',
        href: '#',
    },
];

const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');
const groupBy = ref('day');

const searchReport = () => {
    router.get(
        billingReportIndex().url,
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

const exportReport = () => {
    window.open(billingReportExport({
        query: {
            start_date: startDate.value,
            end_date: endDate.value,
            group_by: groupBy.value,
            ...(effectiveStatus.value !== 'all' ? { status: effectiveStatus.value } : {}),
        },
    }).url, '_blank');
};

type BadgeVariant = 'default' | 'secondary' | 'destructive' | 'outline';

const getStatusColor = (status: string): BadgeVariant => {
    const colors: Record<string, BadgeVariant> = {
        paid: 'default',
        pending: 'secondary',
        overdue: 'destructive',
    };
    return colors[status] || 'secondary';
};

const statusLabels: Record<string, string> = {
    paid: 'Paid',
    pending: 'Pending',
    overdue: 'Overdue',
    partial: 'Partial',
    written_off: 'Written Off',
    cancelled: 'Cancelled',
    revision: 'Revision',
    revised: 'Revised',
    sent_to_account: 'Sent to Account',
};

const statusOrder = ['paid', 'pending', 'overdue', 'partial', 'written_off', 'cancelled', 'revision', 'revised', 'sent_to_account'];

const activeStatus = ref('all');

const statusTabs = computed(() => {
    const counts: Record<string, number> = {};
    let total = 0;
    for (const patient of props.billingData) {
        for (const billing of patient.billings) {
            counts[billing.status] = (counts[billing.status] || 0) + 1;
            total++;
        }
    }
    const tabs = [{ value: 'all', label: `All (${total})` }];
    for (const status of statusOrder) {
        if (counts[status]) {
            tabs.push({ value: status, label: `${statusLabels[status] ?? status} (${counts[status]})` });
        }
    }
    return tabs;
});

// Fall back to "all" if the selected status vanished after a new report was generated
const effectiveStatus = computed(() =>
    statusTabs.value.some((t) => t.value === activeStatus.value) ? activeStatus.value : 'all',
);

const filteredBillingData = computed(() => {
    if (effectiveStatus.value === 'all') {
        return props.billingData;
    }
    return props.billingData
        .map((patient) => {
            const billings = patient.billings.filter((b) => b.status === effectiveStatus.value);
            return {
                ...patient,
                billings,
                bill_count: billings.length,
                total_amount: billings.reduce((sum, b) => sum + b.amount, 0),
            };
        })
        .filter((patient) => patient.billings.length > 0);
});

// Summary cards follow the active tab: on "Paid" only paid money/counts, etc.
const displaySummary = computed<Summary>(() => {
    if (effectiveStatus.value === 'all') {
        return props.summary;
    }
    const bills = filteredBillingData.value.flatMap((patient) => patient.billings);
    const revenue = bills.reduce((sum, b) => sum + b.amount, 0);
    return {
        total_revenue: revenue,
        total_bills: bills.length,
        average_bill: bills.length ? revenue / bills.length : 0,
        paid_count: bills.filter((b) => b.status === 'paid').length,
        unpaid_count: bills.filter((b) => ['pending', 'overdue'].includes(b.status)).length,
    };
});
</script>

<template>
    <Head title="Billing Report" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Billing Report</h1>
                    <p class="text-muted-foreground">View billing records grouped by patient with summary statistics</p>
                </div>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Calendar class="size-5" />
                        Date Range Filter
                    </CardTitle>
                    <CardDescription>Select date range to view billing records</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="start_date">Start Date</Label>
                                <Input
                                    id="start_date"
                                    v-model="startDate"
                                    type="date"
                                    placeholder="Start Date"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="end_date">End Date</Label>
                                <Input id="end_date" v-model="endDate" type="date" placeholder="End Date" />
                            </div>
                            <div class="flex items-end">
                                <Button @click="searchReport" class="w-full">
                                    <FileText class="mr-2 size-4" />
                                    Generate Report
                                </Button>
                            </div>
                        </div>

                        <!-- Export Section -->
                        <div v-if="billingData.length > 0" class="flex items-end gap-4 border-t pt-4">
                            <div class="flex-1 space-y-2">
                                <Label for="group_by">Group Export By</Label>
                                <Select v-model="groupBy">
                                    <SelectTrigger id="group_by">
                                        <SelectValue placeholder="Select grouping" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="day">Day (Detailed)</SelectItem>
                                        <SelectItem value="month">Month</SelectItem>
                                        <SelectItem value="year">Year</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <Button @click="exportReport" variant="outline">
                                <Download class="mr-2 size-4" />
                                Export to CSV
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Summary Statistics -->
            <div v-if="billingData.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Revenue</CardDescription>
                        <CardTitle class="text-2xl">${{ displaySummary.total_revenue.toFixed(2) }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Bills</CardDescription>
                        <CardTitle class="text-2xl">{{ displaySummary.total_bills }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Average Bill</CardDescription>
                        <CardTitle class="text-2xl">${{ displaySummary.average_bill.toFixed(2) }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Paid Bills</CardDescription>
                        <CardTitle class="text-2xl text-green-600">{{ displaySummary.paid_count }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Unpaid Bills</CardDescription>
                        <CardTitle class="text-2xl text-red-600">{{ displaySummary.unpaid_count }}</CardTitle>
                    </CardHeader>
                </Card>
            </div>

            <!-- Results -->
            <div v-if="billingData.length > 0" class="space-y-6">
                <!-- Status Tabs -->
                <Tabs v-model="activeStatus">
                    <TabsList>
                        <TabsTrigger v-for="tab in statusTabs" :key="tab.value" :value="tab.value">
                            {{ tab.label }}
                        </TabsTrigger>
                    </TabsList>
                </Tabs>

                <!-- No bills for selected status -->
                <Card v-if="filteredBillingData.length === 0">
                    <CardContent class="flex flex-col items-center justify-center py-12">
                        <TrendingUp class="size-12 text-muted-foreground" />
                        <p class="mt-4 text-lg font-medium">No bills with this status</p>
                        <p class="text-sm text-muted-foreground">Select another tab to see results</p>
                    </CardContent>
                </Card>

                <!-- Patient Groups -->
                <Card v-for="patient in filteredBillingData" :key="patient.patient_id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>{{ patient.patient_name }}</CardTitle>
                                <CardDescription>
                                    Patient ID: {{ patient.patient_id }} • {{ patient.bill_count }} bill(s)
                                </CardDescription>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-muted-foreground">Total Amount</div>
                                <div class="text-2xl font-bold">${{ patient.total_amount.toFixed(2) }}</div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Bill No</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Amount</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Payment Method</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="billing in patient.billings" :key="billing.id">
                                    <TableCell class="font-medium">{{ billing.bill_no }}</TableCell>
                                    <TableCell>{{ billing.billing_date }}</TableCell>
                                    <TableCell>${{ billing.amount.toFixed(2) }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="getStatusColor(billing.status)">
                                            {{ billing.status.toUpperCase() }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>{{ billing.payment_method }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>

            <!-- No Results -->
            <Card v-else-if="startDate && endDate">
                <CardContent class="flex flex-col items-center justify-center py-12">
                    <TrendingUp class="size-12 text-muted-foreground" />
                    <p class="mt-4 text-lg font-medium">No billing records found</p>
                    <p class="text-sm text-muted-foreground">
                        Try adjusting your date range to see results
                    </p>
                </CardContent>
            </Card>

            <!-- Initial State -->
            <Card v-else>
                <CardContent class="flex flex-col items-center justify-center py-12">
                    <Calendar class="size-12 text-muted-foreground" />
                    <p class="mt-4 text-lg font-medium">Select Date Range</p>
                    <p class="text-sm text-muted-foreground">
                        Choose start and end dates to generate billing report
                    </p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
