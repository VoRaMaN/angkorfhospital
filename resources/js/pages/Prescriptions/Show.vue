<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';

interface Props {
    prescription: {
        id: number;
        patient_id: number;
        patient_name: string;
        doctor_id: number;
        doctor_name: string;
        medication_id: number;
        medication_name: string;
        dosage: string;
        frequency: string;
        duration: string;
        instructions: string;
        status: string;
        prescribed_date: string;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Prescriptions',
        href: '/prescriptions',
    },
    {
        title: 'Details',
        href: '#',
    },
];
</script>

<template>
    <Head title="Prescription Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/prescriptions">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Prescription Details</h1>
                    <p class="text-muted-foreground">View prescription information</p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="`/prescriptions/${props.prescription.id}/edit`">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Patient</dt>
                            <dd class="text-sm font-medium">{{ props.prescription.patient_name }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Doctor</dt>
                            <dd class="text-sm">{{ props.prescription.doctor_name }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Medication</dt>
                            <dd class="text-sm">{{ props.prescription.medication_name }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd class="text-sm">
                                <Badge :variant="props.prescription.status === 'active' ? 'default' : 'secondary'">
                                    {{ props.prescription.status }}
                                </Badge>
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Dosage</dt>
                            <dd class="text-sm">{{ props.prescription.dosage }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Frequency</dt>
                            <dd class="text-sm">{{ props.prescription.frequency }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Duration</dt>
                            <dd class="text-sm">{{ props.prescription.duration }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Prescribed Date</dt>
                            <dd class="text-sm">{{ new Date(props.prescription.prescribed_date).toLocaleDateString() }}</dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt class="text-sm font-medium text-muted-foreground">Instructions</dt>
                            <dd class="text-sm">{{ props.prescription.instructions || 'No special instructions' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Created</dt>
                            <dd class="text-sm">{{ new Date(props.prescription.created_at).toLocaleString() }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Last Updated</dt>
                            <dd class="text-sm">{{ new Date(props.prescription.updated_at).toLocaleString() }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
