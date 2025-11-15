<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, DollarSign } from 'lucide-vue-next';
import { computed, watch } from 'vue';

interface Props {
    patients: {
        id: number;
        name: string;
    }[];
    appointments?: {
        id: number;
        appointment_date: string;
        patient: { name: string };
    }[];
}

const props = defineProps<Props>();

const form = useForm({
    patient_id: '',
    appointment_id: '',
    total_amount: '',
    paid_amount: '0',
    billing_date: new Date().toISOString().split('T')[0],
    due_date: '',
    description: '',
    notes: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billings',
        href: '/billings',
    },
    {
        title: 'Create',
        href: '#',
    },
];

// Calculate outstanding amount
const outstandingAmount = computed(() => {
    const total = parseFloat(form.total_amount) || 0;
    const paid = parseFloat(form.paid_amount) || 0;
    return Math.max(0, total - paid);
});

// Set default due date to 30 days from billing date
watch(() => form.billing_date, (newDate: string) => {
    if (newDate && !form.due_date) {
        const dueDate = new Date(newDate);
        dueDate.setDate(dueDate.getDate() + 30);
        form.due_date = dueDate.toISOString().split('T')[0];
    }
});

const submit = () => {
    form.post('/billings', {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create Billing" />

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
                    <h1 class="text-2xl font-bold">Create Billing</h1>
                    <p class="text-muted-foreground">Add a new billing record</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="submit" class="space-y-6 rounded-lg border bg-card p-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="patient_id">Patient</Label>
                            <Select v-model="form.patient_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select patient" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="patient in props.patients" :key="patient.id" :value="patient.id.toString()">
                                        {{ patient.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.patient_id" class="text-sm text-destructive">
                                {{ form.errors.patient_id }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="appointment_id">Related Appointment (Optional)</Label>
                            <Select v-model="form.appointment_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select appointment" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">No appointment</SelectItem>
                                    <SelectItem v-for="appointment in props.appointments" :key="appointment.id" :value="appointment.id.toString()">
                                        {{ appointment.patient.name }} - {{ new Date(appointment.appointment_date).toLocaleDateString() }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.appointment_id" class="text-sm text-destructive">
                                {{ form.errors.appointment_id }}
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="total_amount">Total Amount</Label>
                            <div class="relative">
                                <DollarSign class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="total_amount"
                                    v-model="form.total_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="pl-9"
                                    required
                                />
                            </div>
                            <div v-if="form.errors.total_amount" class="text-sm text-destructive">
                                {{ form.errors.total_amount }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="paid_amount">Paid Amount</Label>
                            <div class="relative">
                                <DollarSign class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="paid_amount"
                                    v-model="form.paid_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="pl-9"
                                />
                            </div>
                            <div v-if="form.errors.paid_amount" class="text-sm text-destructive">
                                {{ form.errors.paid_amount }}
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-muted rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Outstanding Amount:</span>
                            <span class="text-lg font-bold text-red-600">
                                ${{ outstandingAmount.toFixed(2) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="billing_date">Billing Date</Label>
                            <Input
                                id="billing_date"
                                v-model="form.billing_date"
                                type="date"
                                required
                            />
                            <div v-if="form.errors.billing_date" class="text-sm text-destructive">
                                {{ form.errors.billing_date }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="due_date">Due Date</Label>
                            <Input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                required
                            />
                            <div v-if="form.errors.due_date" class="text-sm text-destructive">
                                {{ form.errors.due_date }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            placeholder="Billing description..."
                            rows="3"
                        />
                        <div v-if="form.errors.description" class="text-sm text-destructive">
                            {{ form.errors.description }}
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
                        <div v-if="form.errors.notes" class="text-sm text-destructive">
                            {{ form.errors.notes }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="size-4" />
                            {{ form.processing ? 'Creating...' : 'Create Billing' }}
                        </Button>
                        <Button type="button" variant="outline" as-child>
                            <a href="/billings">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
