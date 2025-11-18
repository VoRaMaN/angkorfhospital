<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import PatientFilesTab from '@/pages/Patients/PatientFilesTab.vue';
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
        patient_files?: Array<any>;
        medical_orders?: Array<any>;
        medical_records?: Array<{
            id: number;
            diagnosis: string;
            treatment: string;
            notes: string;
            date_of_service: string;
            created_at: string;
        }>;
        medical_orders_data?: Array<{
            id: number;
            type: string;
            order_details: string;
            status: string;
            priority: string;
            ordered_at: string;
            completed_at: string | null;
            staff_name: string | null;
            notes: string | null;
        }>;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

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
        <div v-if="hasPermission('view_patients')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/patients">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Patient Details</h1>
                    <p class="text-muted-foreground">
                        View patient information
                    </p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child v-if="hasPermission('edit_patients')">
                        <Link :href="`/patients/${props.patient.id}/edit`">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <Tabs default-value="details" class="w-full">
                    <TabsList class="grid w-full grid-cols-4">
                        <TabsTrigger value="details"
                            >Patient Details</TabsTrigger
                        >
                        <TabsTrigger value="files">Files</TabsTrigger>
                        <TabsTrigger value="medical-orders"
                            >Medical Orders</TabsTrigger
                        >
                        <TabsTrigger value="medical-records"
                            >Medical Records</TabsTrigger
                        >
                    </TabsList>

                    <TabsContent value="details" class="mt-6">
                        <div class="rounded-lg border bg-card p-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Full Name
                                    </dt>
                                    <dd class="text-sm">
                                        {{
                                            props.patient.user?.name ||
                                            `${props.patient.first_name}
                                        ${props.patient.last_name}`
                                        }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Email
                                    </dt>
                                    <dd class="text-sm">
                                        {{
                                            props.patient.user?.email ||
                                            'No email account'
                                        }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        First Name
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.first_name }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Last Name
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.last_name }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Date of Birth
                                    </dt>
                                    <dd class="text-sm">
                                        {{
                                            new Date(
                                                props.patient.date_of_birth,
                                            ).toLocaleDateString()
                                        }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Gender
                                    </dt>
                                    <dd class="text-sm">
                                        <Badge variant="secondary">
                                            {{
                                                props.patient.gender
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                props.patient.gender.slice(1)
                                            }}
                                        </Badge>
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Phone Number
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.phone_number }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Email
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.email || 'No email' }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Address
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.address }}
                                    </dd>
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <dt
                                        class="text-sm font-medium text-muted-foreground"
                                    >
                                        Insurance Information
                                    </dt>
                                    <dd class="text-sm">
                                        {{
                                            props.patient.insurance_info ||
                                            'N/A'
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
                                                props.patient.created_at,
                                            ).toLocaleString()
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
                                                props.patient.updated_at,
                                            ).toLocaleString()
                                        }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </TabsContent>

                    <TabsContent value="files" class="mt-6">
                        <PatientFilesTab :patient="props.patient" />
                    </TabsContent>

                    <TabsContent value="medical-orders" class="mt-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold">Medical Orders</h3>
                                    <p class="text-sm text-muted-foreground">
                                        All medical orders and their current status
                                    </p>
                                </div>
                            </div>

                            <div v-if="!props.patient.medical_orders_data || props.patient.medical_orders_data.length === 0"
                                 class="rounded-lg border bg-card p-8 text-center">
                                <p class="text-muted-foreground">No medical orders found.</p>
                            </div>

                            <div v-else class="space-y-4">
                                <div v-for="order in props.patient.medical_orders_data"
                                     :key="order.id"
                                     class="rounded-lg border bg-card p-6">
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Order Date
                                            </dt>
                                            <dd class="text-sm">
                                                {{ new Date(order.ordered_at).toLocaleString() }}
                                            </dd>
                                        </div>

                                        <div class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Status
                                            </dt>
                                            <dd class="text-sm">
                                                <Badge :variant="order.status === 'completed' ? 'default' : order.status === 'processing' ? 'secondary' : 'outline'">
                                                    {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                                                </Badge>
                                            </dd>
                                        </div>

                                        <div v-if="order.priority" class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Priority
                                            </dt>
                                            <dd class="text-sm">
                                                <Badge variant="destructive" v-if="order.priority === 'high'">
                                                    High Priority
                                                </Badge>
                                                <Badge variant="secondary" v-else>
                                                    {{ order.priority.charAt(0).toUpperCase() + order.priority.slice(1) }}
                                                </Badge>
                                            </dd>
                                        </div>

                                        <div v-if="order.staff_name" class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Ordered By
                                            </dt>
                                            <dd class="text-sm">
                                                {{ order.staff_name }}
                                            </dd>
                                        </div>
                                    </div>

                                    <div class="mt-4 space-y-4">
                                        <div class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Order Details
                                            </dt>
                                            <dd class="text-sm whitespace-pre-line">
                                                {{ order.order_details }}
                                            </dd>
                                        </div>

                                        <div v-if="order.notes" class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Notes
                                            </dt>
                                            <dd class="text-sm whitespace-pre-line">
                                                {{ order.notes }}
                                            </dd>
                                        </div>

                                        <div v-if="order.completed_at" class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Completed At
                                            </dt>
                                            <dd class="text-sm">
                                                {{ new Date(order.completed_at).toLocaleString() }}
                                            </dd>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-4 border-t">
                                        <p class="text-xs text-muted-foreground">
                                            Order ID: #{{ order.id }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabsContent>

                    <TabsContent value="medical-records" class="mt-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold">Medical Records</h3>
                                    <p class="text-sm text-muted-foreground">
                                        Complete medical history and treatment records
                                    </p>
                                </div>
                            </div>

                            <div v-if="!props.patient.medical_records || props.patient.medical_records.length === 0"
                                 class="rounded-lg border bg-card p-8 text-center">
                                <p class="text-muted-foreground">No medical records found.</p>
                            </div>

                            <div v-else class="space-y-4">
                                <div v-for="record in props.patient.medical_records"
                                     :key="record.id"
                                     class="rounded-lg border bg-card p-6">
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Date of Service
                                            </dt>
                                            <dd class="text-sm">
                                                {{ new Date(record.date_of_service).toLocaleDateString() }}
                                            </dd>
                                        </div>

                                        <div class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Record ID
                                            </dt>
                                            <dd class="text-sm font-mono">
                                                #{{ record.id }}
                                            </dd>
                                        </div>
                                    </div>

                                    <div class="mt-4 space-y-4">
                                        <div v-if="record.diagnosis" class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Diagnosis
                                            </dt>
                                            <dd class="text-sm whitespace-pre-line">
                                                {{ record.diagnosis }}
                                            </dd>
                                        </div>

                                        <div v-if="record.treatment" class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Treatment
                                            </dt>
                                            <dd class="text-sm whitespace-pre-line">
                                                {{ record.treatment }}
                                            </dd>
                                        </div>

                                        <div v-if="record.notes" class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Notes
                                            </dt>
                                            <dd class="text-sm whitespace-pre-line">
                                                {{ record.notes }}
                                            </dd>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-4 border-t">
                                        <p class="text-xs text-muted-foreground">
                                            Created: {{ new Date(record.created_at).toLocaleString() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabsContent>
                </Tabs>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to view patient details.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
