<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, show } from '@/routes/billings';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { DollarSign, Edit, Eye, Plus, Search, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    billings: {
        id: number;
        patient_id: number;
        patient_name: string;
        appointment_id?: number;
        total_amount: number;
        paid_amount: number;
        outstanding_amount: number;
        status: string;
        billing_date: string;
        due_date: string;
        created_at: string;
    }[];
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billings',
        href: '/billings',
    },
];

// Format currency
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const getStatusVariant = (status: string) => {
    switch (status.toLowerCase()) {
        case 'paid':
            return 'default';
        case 'pending':
            return 'secondary';
        case 'overdue':
            return 'destructive';
        case 'partial':
            return 'outline';
        default:
            return 'secondary';
    }
};
</script>

<template>
    <Head title="Billings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Billings</h1>
                    <p class="text-muted-foreground">
                        Manage patient billing records
                    </p>
                </div>
                <div class="ml-auto">
                    <Button as-child>
                        <Link :href="create().url">
                            <Plus class="size-4" />
                            Add Billing
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
                        placeholder="Search billings..."
                        class="pl-9"
                    />
                </div>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Total Amount</TableHead>
                            <TableHead>Paid Amount</TableHead>
                            <TableHead>Outstanding</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Billing Date</TableHead>
                            <TableHead>Due Date</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="billing in props.billings"
                            :key="billing.id"
                        >
                            <TableCell class="font-medium">{{
                                billing.patient_name
                            }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-1">
                                    <DollarSign
                                        class="size-4 text-muted-foreground"
                                    />
                                    {{ formatCurrency(billing.total_amount) }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-1">
                                    <DollarSign class="size-4 text-green-600" />
                                    {{ formatCurrency(billing.paid_amount) }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-1">
                                    <DollarSign class="size-4 text-red-600" />
                                    {{
                                        formatCurrency(
                                            billing.outstanding_amount,
                                        )
                                    }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="getStatusVariant(billing.status)"
                                >
                                    {{ billing.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{
                                new Date(
                                    billing.billing_date,
                                ).toLocaleDateString()
                            }}</TableCell>
                            <TableCell>{{
                                new Date(billing.due_date).toLocaleDateString()
                            }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="show(billing.id).url">
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="edit(billing.id).url">
                                            <Edit class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm">
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.billings.length === 0">
                            <TableCell
                                colspan="8"
                                class="text-center text-muted-foreground"
                            >
                                No billings found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
