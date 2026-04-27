<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { letter as letterRoute } from '@/routes/billings';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, DollarSign, Printer, Save } from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';

interface Props {
    billing: {
        id: number;
        appointment_id?: number;
        visit_id?: number;
        medical_order_id?: number;
        amount: number;
        status: string;
        billing_date: string;
        notes: string;
    };
    appointments: {
        id: number;
        label: string;
    }[];
    visits: {
        id: number;
        label: string;
    }[];
    medicalOrders: {
        id: number;
        label: string;
    }[];
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const form = useForm({
    appointment_id: props.billing.appointment_id?.toString() || '',
    visit_id: props.billing.visit_id?.toString() || '',
    medical_order_id: props.billing.medical_order_id?.toString() || '',
    amount: props.billing.amount.toString(),
    status: props.billing.status,
    billing_date: props.billing.billing_date.split('T')[0],
    notes: props.billing.notes,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billings',
        href: '/billings',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

// Calculate outstanding amount
// const outstandingAmount = computed(() => {
//     const total = parseFloat(form.amount) || 0;
//     return total;
// });

const submit = () => {
    form.put(`/billings/${props.billing.id}`, {
        onSuccess: () => {
            // Success handled by Inertia
        },
    });
};
</script>

<template>
    <Head title="Edit Billing" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('edit_billings')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/billings">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Billing</h1>
                    <p class="text-muted-foreground">
                        Update billing information
                    </p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <a :href="letterRoute(props.billing.id).url" target="_blank">
                            <Printer class="size-4" />
                            Print Receipt
                        </a>
                    </Button>
                </div>
            </div>

            <div class="max-w-2xl">
                <form
                    @submit.prevent="submit"
                    class="space-y-6 rounded-lg border bg-card p-6"
                >
                    <div class="space-y-2">
                        <Label for="appointment_id">Appointment (Optional)</Label>
                        <SearchableSelect
                            v-model="form.appointment_id"
                            :options="props.appointments.map(a => ({ value: a.id.toString(), label: a.label }))"
                            placeholder="Select appointment"
                            search-placeholder="Search appointments..."
                        />
                        <div
                            v-if="form.errors.appointment_id"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.appointment_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="visit_id">Visit (Optional)</Label>
                        <SearchableSelect
                            v-model="form.visit_id"
                            :options="props.visits.map(v => ({ value: v.id.toString(), label: v.label }))"
                            placeholder="Select visit"
                            search-placeholder="Search visits..."
                        />
                        <div
                            v-if="form.errors.visit_id"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.visit_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="medical_order_id">Medical Order (Optional)</Label>
                        <SearchableSelect
                            v-model="form.medical_order_id"
                            :options="props.medicalOrders.map(o => ({ value: o.id.toString(), label: o.label }))"
                            placeholder="Select medical order"
                            search-placeholder="Search medical orders..."
                        />
                        <div
                            v-if="form.errors.medical_order_id"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.medical_order_id }}
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="amount">Amount</Label>
                            <div class="relative">
                                <DollarSign
                                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                />
                                <Input
                                    id="amount"
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="pl-9"
                                    required
                                />
                            </div>
                            <div
                                v-if="form.errors.amount"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.amount }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="status">Status</Label>
                            <Select v-model="form.status">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="pending">Pending</SelectItem>
                                    <SelectItem value="paid">Paid</SelectItem>
                                    <SelectItem value="overdue">Overdue</SelectItem>
                                    <SelectItem value="partial">Partial</SelectItem>
                                    <SelectItem value="cancelled">Cancelled</SelectItem>
                                </SelectContent>
                            </Select>
                            <div
                                v-if="form.errors.status"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.status }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="billing_date">Billing Date</Label>
                        <Input
                            id="billing_date"
                            v-model="form.billing_date"
                            type="date"
                            required
                        />
                        <div
                            v-if="form.errors.billing_date"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.billing_date }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="notes">Notes</Label>
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            placeholder="Additional notes..."
                            rows="2"
                        />
                        <div
                            v-if="form.errors.notes"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.notes }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="size-4" />
                            {{
                                form.processing
                                    ? 'Updating...'
                                    : 'Update Billing'
                            }}
                        </Button>
                        <Button type="button" variant="outline" as-child>
                            <a href="/billings">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to edit billings.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
