<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, DollarSign, Calendar, User, FileText } from 'lucide-vue-next';

interface Props {
    billing: {
        id: number;
        patient_id: number;
        patient_name: string;
        appointment_id?: number;
        appointment_date?: string;
        total_amount: number;
        paid_amount: number;
        outstanding_amount: number;
        status: string;
        billing_date: string;
        due_date: string;
        description: string;
        notes: string;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billings',
        href: '/billings',
    },
    {
        title: 'Details',
        href: '#',
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
    <Head title="Billing Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/billings">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Billing Details</h1>
                    <p class="text-muted-foreground">View billing information</p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="`/billings/${props.billing.id}/edit`">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl space-y-6">
                <!-- Status and Summary Cards -->
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="rounded-lg border bg-card p-4">
                        <div class="flex items-center gap-2">
                            <DollarSign class="size-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm text-muted-foreground">Total Amount</p>
                                <p class="text-2xl font-bold">{{ formatCurrency(props.billing.total_amount) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg border bg-card p-4">
                        <div class="flex items-center gap-2">
                            <DollarSign class="size-5 text-green-600" />
                            <div>
                                <p class="text-sm text-muted-foreground">Paid Amount</p>
                                <p class="text-2xl font-bold text-green-600">{{ formatCurrency(props.billing.paid_amount) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg border bg-card p-4">
                        <div class="flex items-center gap-2">
                            <DollarSign class="size-5 text-red-600" />
                            <div>
                                <p class="text-sm text-muted-foreground">Outstanding</p>
                                <p class="text-2xl font-bold text-red-600">{{ formatCurrency(props.billing.outstanding_amount) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg border bg-card p-4">
                        <div class="flex items-center gap-2">
                            <Badge :variant="getStatusVariant(props.billing.status)" class="text-lg px-3 py-1">
                                {{ props.billing.status }}
                            </Badge>
                        </div>
                    </div>
                </div>

                <!-- Main Details -->
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <User class="size-4" />
                                Patient
                            </dt>
                            <dd class="text-sm font-medium">{{ props.billing.patient_name }}</dd>
                        </div>

                        <div v-if="props.billing.appointment_id" class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <Calendar class="size-4" />
                                Related Appointment
                            </dt>
                            <dd class="text-sm">{{ new Date(props.billing.appointment_date!).toLocaleDateString() }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <Calendar class="size-4" />
                                Billing Date
                            </dt>
                            <dd class="text-sm">{{ new Date(props.billing.billing_date).toLocaleDateString() }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <Calendar class="size-4" />
                                Due Date
                            </dt>
                            <dd class="text-sm">{{ new Date(props.billing.due_date).toLocaleDateString() }}</dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <FileText class="size-4" />
                                Description
                            </dt>
                            <dd class="text-sm">{{ props.billing.description || 'No description provided' }}</dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <FileText class="size-4" />
                                Notes
                            </dt>
                            <dd class="text-sm">{{ props.billing.notes || 'No additional notes' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Created</dt>
                            <dd class="text-sm">{{ new Date(props.billing.created_at).toLocaleString() }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Last Updated</dt>
                            <dd class="text-sm">{{ new Date(props.billing.updated_at).toLocaleString() }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
