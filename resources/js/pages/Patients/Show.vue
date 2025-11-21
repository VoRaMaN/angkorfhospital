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
        id: string;
        user?: { name: string; email: string };
        title?: string;
        name: string;
        surname: string;
        khmer_china_name?: string;
        khmer_china_surname?: string;
        date_of_birth_day: number;
        date_of_birth_month: number;
        date_of_birth_year: number;
        gender: string;
        id_card_or_passport?: string;
        marital_status?: string;
        nationality: string;
        religion?: string;
        race?: string;

        // Address
        address?: string;
        building_village?: string;
        moo?: string;
        soi?: string;
        road?: string;
        sub_district?: string;
        district?: string;
        province?: string;
        zip_code?: string;

        // Contact
        home_phone?: string;
        mobile_phone?: string;
        email?: string;
        occupation?: string;
        company_name?: string;
        company_phone?: string;

        // Emergency Contact
        emergency_contact_name?: string;
        emergency_contact_relationship?: string;
        emergency_contact_description_other?: string;
        emergency_contact_address_same_as_patient?: boolean;
        emergency_contact_address?: string;
        emergency_contact_road?: string;
        emergency_contact_sub_district?: string;
        emergency_contact_district?: string;
        emergency_contact_province?: string;
        emergency_contact_zip_code?: string;
        emergency_contact_home_phone?: string;
        emergency_contact_mobile_phone?: string;
        emergency_contact_email?: string;

        // Payment
        payment_method?: string;
        contract_name?: string;
        insurance_name?: string;
        staff_id?: number;
        patient_type?: string;

        staff?: { id: number; name: string };

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
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/patients">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">
                        {{ patient.title }} {{ patient.name }} {{ patient.surname }}
                    </h1>
                    <p class="text-muted-foreground">
                        View patient information
                    </p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="`/patients/edit?patient=${props.patient.id}`">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-4xl">
                <Tabs default-value="details" class="w-full">
                    <TabsList class="grid w-full grid-cols-4">
                        <TabsTrigger value="details">Patient Details</TabsTrigger>
                        <TabsTrigger value="files">Files</TabsTrigger>
                        <TabsTrigger value="medical-orders">Medical Orders</TabsTrigger>
                        <TabsTrigger value="medical-records">Medical Records</TabsTrigger>
                    </TabsList>

                    <TabsContent value="details" class="mt-6 space-y-6">
                        
                        <!-- Personal Information -->
                        <div class="rounded-lg border p-4">
                            <h3 class="mb-4 flex items-center text-lg font-medium">
                                <User class="mr-2 size-5" />
                                Personal Information
                            </h3>
                            <div class="rounded-md border">
                                <table class="w-full">
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium w-1/3">Title</td>
                                            <td class="p-4">{{ patient.title || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">First Name</td>
                                            <td class="p-4">{{ patient.name }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Last Name</td>
                                            <td class="p-4">{{ patient.surname }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Khmer/China Name</td>
                                            <td class="p-4">{{ patient.khmer_china_name || 'N/A' }} {{ patient.khmer_china_surname || '' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Date of Birth</td>
                                            <td class="p-4">{{ patient.date_of_birth_day }}/{{ patient.date_of_birth_month }}/{{ patient.date_of_birth_year }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Gender</td>
                                            <td class="p-4 capitalize">{{ patient.gender }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">ID Card/Passport</td>
                                            <td class="p-4">{{ patient.id_card_or_passport || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Marital Status</td>
                                            <td class="p-4">{{ patient.marital_status || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Nationality</td>
                                            <td class="p-4">{{ patient.nationality }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Religion</td>
                                            <td class="p-4">{{ patient.religion || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Race</td>
                                            <td class="p-4">{{ patient.race || 'N/A' }}</td>
                                        </tr>
                                        <tr v-if="patient.user">
                                            <td class="p-4 font-medium">User Account</td>
                                            <td class="p-4">{{ patient.user.name }} ({{ patient.user.email }})</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="rounded-lg border p-4">
                            <h3 class="mb-4 text-lg font-medium">Contact Information</h3>
                            <div class="rounded-md border">
                                <table class="w-full">
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium w-1/3">Home Phone</td>
                                            <td class="p-4">{{ patient.home_phone || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Mobile Phone</td>
                                            <td class="p-4">{{ patient.mobile_phone || 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4 font-medium">Email</td>
                                            <td class="p-4">{{ patient.email || 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="rounded-lg border p-4">
                            <h3 class="mb-4 text-lg font-medium">Address</h3>
                            <div class="rounded-md border">
                                <table class="w-full">
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium w-1/3">Address</td>
                                            <td class="p-4">{{ patient.address || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Building/Village</td>
                                            <td class="p-4">{{ patient.building_village || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Moo</td>
                                            <td class="p-4">{{ patient.moo || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Soi</td>
                                            <td class="p-4">{{ patient.soi || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Road</td>
                                            <td class="p-4">{{ patient.road || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Sub-district</td>
                                            <td class="p-4">{{ patient.sub_district || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">District</td>
                                            <td class="p-4">{{ patient.district || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Province</td>
                                            <td class="p-4">{{ patient.province || 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4 font-medium">Zip Code</td>
                                            <td class="p-4">{{ patient.zip_code || 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Employment -->
                        <div class="rounded-lg border p-4">
                            <h3 class="mb-4 text-lg font-medium">Employment</h3>
                            <div class="rounded-md border">
                                <table class="w-full">
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium w-1/3">Occupation</td>
                                            <td class="p-4">{{ patient.occupation || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Company Name</td>
                                            <td class="p-4">{{ patient.company_name || 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4 font-medium">Company Phone</td>
                                            <td class="p-4">{{ patient.company_phone || 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="rounded-lg border p-4">
                            <h3 class="mb-4 text-lg font-medium">Emergency Contact</h3>
                            <div class="rounded-md border">
                                <table class="w-full">
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium w-1/3">Name</td>
                                            <td class="p-4">{{ patient.emergency_contact_name || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Relationship</td>
                                            <td class="p-4">{{ patient.emergency_contact_relationship || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Other Relationship</td>
                                            <td class="p-4">{{ patient.emergency_contact_description_other || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Same as Patient Address</td>
                                            <td class="p-4">{{ patient.emergency_contact_address_same_as_patient ? 'Yes' : 'No' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Address</td>
                                            <td class="p-4">{{ patient.emergency_contact_address || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Road</td>
                                            <td class="p-4">{{ patient.emergency_contact_road || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Sub-district</td>
                                            <td class="p-4">{{ patient.emergency_contact_sub_district || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">District</td>
                                            <td class="p-4">{{ patient.emergency_contact_district || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Province</td>
                                            <td class="p-4">{{ patient.emergency_contact_province || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Zip Code</td>
                                            <td class="p-4">{{ patient.emergency_contact_zip_code || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Home Phone</td>
                                            <td class="p-4">{{ patient.emergency_contact_home_phone || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Mobile Phone</td>
                                            <td class="p-4">{{ patient.emergency_contact_mobile_phone || 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4 font-medium">Email</td>
                                            <td class="p-4">{{ patient.emergency_contact_email || 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Insurance & Payment -->
                        <div class="rounded-lg border p-4">
                            <h3 class="mb-4 text-lg font-medium">Insurance & Payment</h3>
                            <div class="rounded-md border">
                                <table class="w-full">
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium w-1/3">Payment Method</td>
                                            <td class="p-4 capitalize">{{ patient.payment_method || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Contract Name</td>
                                            <td class="p-4">{{ patient.contract_name || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Insurance Name</td>
                                            <td class="p-4">{{ patient.insurance_name || 'N/A' }}</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-4 font-medium">Referring Doctor</td>
                                            <td class="p-4">{{ patient.staff?.name || 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4 font-medium">Patient Type</td>
                                            <td class="p-4">{{ patient.patient_type || 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                <div v-for="order in props.patient.medical_orders_data" :key="order.id"
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
                                                <Badge
                                                    :variant="order.status === 'Completed' ? 'default' : order.status === 'Processing' ? 'secondary' : 'outline'">
                                                    {{ order.status }}
                                                </Badge>
                                            </dd>
                                        </div>

                                        <div v-if="order.priority" class="space-y-2">
                                            <dt class="text-sm font-medium text-muted-foreground">
                                                Priority
                                            </dt>
                                            <dd class="text-sm">
                                                <Badge variant="destructive" v-if="order.priority === 'STAT'">
                                                    {{ order.priority }}
                                                </Badge>
                                                <Badge variant="secondary" v-else>
                                                    {{ order.priority }}
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
                                <div v-for="record in props.patient.medical_records" :key="record.id"
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
