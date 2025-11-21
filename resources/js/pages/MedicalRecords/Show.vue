<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index, report as reportRoute } from '@/routes/medical-records';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Download } from 'lucide-vue-next';

interface Props {
    medicalRecord: {
        id: number;
        patient_id: number;
        patient_name: string;
        doctor_id: number;
        doctor_name: string;
        diagnosis: string;
        treatment: string;
        notes: string;
        date_of_service: string;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Records',
        href: '/medical-records',
    },
    {
        title: 'Details',
        href: '#',
    },
];
</script>

<template>
    <Head title="Medical Record Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="index().url">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Medical Record Details</h1>
                    <p class="text-muted-foreground">
                        View medical record information
                    </p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="edit(props.medicalRecord.id).url">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <a
                            :href="reportRoute(props.medicalRecord.id).url"
                            target="_blank"
                        >
                            <Download class="size-4" />
                            Download Report
                        </a>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <div class="rounded-lg border bg-card p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Patient
                            </dt>
                            <dd class="text-sm">
                                {{ props.medicalRecord.patient_name }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Doctor
                            </dt>
                            <dd class="text-sm">
                                {{ props.medicalRecord.doctor_name }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Date of Service
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.medicalRecord.date_of_service
                                        ? new Date(
                                              props.medicalRecord.date_of_service,
                                          ).toLocaleDateString()
                                        : 'N/A'
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Created
                            </dt>
                            <dd class="text-sm">
                                {{
                                    new Date(
                                        props.medicalRecord.created_at,
                                    ).toLocaleString()
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Diagnosis
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.medicalRecord.diagnosis ||
                                    'No diagnosis recorded'
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Treatment
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.medicalRecord.treatment ||
                                    'No treatment recorded'
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Notes
                            </dt>
                            <dd class="text-sm">
                                {{
                                    props.medicalRecord.notes ||
                                    'No additional notes'
                                }}
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Last Updated
                            </dt>
                            <dd class="text-sm">
                                {{
                                    new Date(
                                        props.medicalRecord.updated_at,
                                    ).toLocaleString()
                                }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
