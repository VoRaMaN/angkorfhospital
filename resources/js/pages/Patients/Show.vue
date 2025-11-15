<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';

interface Props {
    patient: {
        id: number;
        user: { name: string; email: string };
        first_name: string;
        last_name: string;
        date_of_birth: string;
        gender: string;
        address: string;
        phone_number: string;
        email?: string;
        insurance_info: string;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Patients',
        href: '/patients',
    },
    {
        title: 'Details',
        href: '#',
    },
];
</script>

<template>
    <Head title="Patient Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/patients">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Patient Details</h1>
                    <p class="text-muted-foreground">View patient information</p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="`/patients/${props.patient.id}/edit`">
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
                            <dt class="text-sm font-medium text-muted-foreground">Full Name</dt>
                            <dd class="text-sm">{{ props.patient.user?.name || `${props.patient.first_name} ${props.patient.last_name}` }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Email</dt>
                            <dd class="text-sm">{{ props.patient.user?.email || 'No email account' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">First Name</dt>
                            <dd class="text-sm">{{ props.patient.first_name }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Last Name</dt>
                            <dd class="text-sm">{{ props.patient.last_name }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Date of Birth</dt>
                            <dd class="text-sm">{{ new Date(props.patient.date_of_birth).toLocaleDateString() }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Gender</dt>
                            <dd class="text-sm">
                                <Badge variant="secondary">
                                    {{ props.patient.gender.charAt(0).toUpperCase() + props.patient.gender.slice(1) }}
                                </Badge>
                            </dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Phone Number</dt>
                            <dd class="text-sm">{{ props.patient.phone_number }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Email</dt>
                            <dd class="text-sm">{{ props.patient.email || 'No email' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Address</dt>
                            <dd class="text-sm">{{ props.patient.address }}</dd>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <dt class="text-sm font-medium text-muted-foreground">Insurance Information</dt>
                            <dd class="text-sm">{{ props.patient.insurance_info || 'N/A' }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Created</dt>
                            <dd class="text-sm">{{ new Date(props.patient.created_at).toLocaleString() }}</dd>
                        </div>

                        <div class="space-y-2">
                            <dt class="text-sm font-medium text-muted-foreground">Last Updated</dt>
                            <dd class="text-sm">{{ new Date(props.patient.updated_at).toLocaleString() }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>