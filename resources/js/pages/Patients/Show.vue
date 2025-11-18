<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import PatientFilesTab from '@/pages/Patients/PatientFilesTab.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Printer, X } from 'lucide-vue-next';

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

const showPrintPreview = ref(false);
const stickerHtml = ref('');

const printMedicalStickers = () => {
    const patientName = props.patient.user?.name || `${props.patient.first_name} ${props.patient.last_name}`;
    const patientDOB = new Date(props.patient.date_of_birth).toLocaleDateString('en-US');
    const patientId = `P${props.patient.id.toString().padStart(6, '0')}`;
    const clinicName = 'CynoSys Clinic'; // You can make this configurable
    const dos = new Date().toLocaleDateString('en-US'); // Current date as DOS

    stickerHtml.value = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Sticker Labels</title>
    <script src="https://cdn.tailwindcss.com"></${'script'}>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        .sticker-grid-wrapper {
            display: grid;
            grid-template-columns: repeat(2, 4.0in);
            grid-auto-rows: 1.0in;
            gap: 10px;
            width: max-content;
            padding: 5px;
            background-color: white;
        }

        .sticker-label {
            width: 4.0in;
            height: 1.0in;
            box-sizing: border-box;
            border: 1px dashed #cccccc;
            padding: 8px;
            background-color: white;
            color: black;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .text-xs-sticker { font-size: 0.65rem; }
        .text-sm-sticker { font-size: 0.75rem; }
        .text-md-sticker { font-size: 0.9rem; }
        .text-lg-sticker { font-size: 1.1rem; }

        @media print {
            /* Page setup */
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            /* Hide everything except the sticker content */
            body * {
                visibility: hidden;
            }

            .sticker-grid-wrapper,
            .sticker-grid-wrapper * {
                visibility: visible;
            }

            /* Reset body for print */
            body {
                background-color: white !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: auto !important;
            }

            /* Sticker grid layout for print */
            .sticker-grid-wrapper {
                position: static !important;
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                grid-auto-rows: 1in !important;
                gap: 0.125in !important; /* 1/8 inch gap between stickers */
                width: 100% !important;
                max-width: 7.5in !important; /* Account for page margins */
                margin: 0 auto !important;
                padding: 0 !important;
                background-color: white !important;
                border: none !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
            }

            /* Individual sticker styling for print */
            .sticker-label {
                width: 100% !important;
                height: 1in !important;
                box-sizing: border-box !important;
                border: 1px solid #000000 !important; /* Black border for better visibility on print */
                padding: 6px !important;
                background-color: white !important;
                color: black !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
                box-shadow: none !important;
                border-radius: 0 !important; /* Remove rounded corners for print */
                overflow: hidden !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Typography adjustments for print */
            .text-xs-sticker { font-size: 8pt !important; }
            .text-sm-sticker { font-size: 9pt !important; }
            .text-md-sticker { font-size: 11pt !important; }
            .text-lg-sticker { font-size: 12pt !important; }

            /* Ensure all text is black for print */
            .sticker-label * {
                color: black !important;
                background-color: transparent !important;
            }

            /* Font weight adjustments for better print visibility */
            .font-bold { font-weight: bold !important; }
            .font-extrabold { font-weight: 800 !important; }
            .font-semibold { font-weight: 600 !important; }

            /* Spacing adjustments */
            .pb-2 { padding-bottom: 4px !important; }
            .mb-2 { margin-bottom: 4px !important; }
            .space-y-1 > * + * { margin-top: 2px !important; }

            /* Flexbox adjustments for print */
            .flex { display: flex !important; }
            .justify-between { justify-content: space-between !important; }
            .items-center { align-items: center !important; }
            .flex-grow { flex-grow: 1 !important; }
            .flex-direction-column { flex-direction: column !important; }

            /* Prevent page breaks within stickers */
            .sticker-label,
            .sticker-label * {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Ensure proper text rendering */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="sticker-grid-wrapper">
        ${Array.from({ length: 10 }, () => `
        <div class="sticker-label rounded-md">
            <div class="flex justify-between items-center pb-2 mb-2">
                <span class="text-md-sticker font-bold text-gray-700">${clinicName}</span>
                <span class="text-md-sticker font-semibold">DOS: ${dos}</span>
            </div>
            <div class="flex-grow space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-md-sticker font-bold uppercase text-gray-700">Patient:</span>
                    <span class="text-lg-sticker font-extrabold text-gray-900">${patientName}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-md-sticker font-bold uppercase text-gray-700">DOB:</span>
                    <span class="text-lg-sticker font-semibold text-gray-800">${patientDOB}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-md-sticker font-bold uppercase text-gray-700">Patient ID:</span>
                    <span class="text-lg-sticker font-semibold text-gray-800">${patientId}</span>
                </div>
            </div>
        </div>
        `).join('')}
    </div>
</body>
</html>`;

    showPrintPreview.value = true;
};

const executePrint = () => {
    window.print();
};

const closePrintPreview = () => {
    showPrintPreview.value = false;
};

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
                    <h1 class="text-2xl font-bold">Patient Details</h1>
                    <p class="text-muted-foreground">
                        View patient information
                    </p>
                </div>
                <div class="ml-auto flex gap-2">
                    <Button variant="outline" @click="printMedicalStickers">
                        <Printer class="size-4" />
                        Print Stickers
                    </Button>
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
                        <TabsTrigger value="details">Patient Details</TabsTrigger>
                        <TabsTrigger value="files">Files</TabsTrigger>
                        <TabsTrigger value="medical-orders">Medical Orders</TabsTrigger>
                        <TabsTrigger value="medical-records">Medical Records</TabsTrigger>
                    </TabsList>

                    <TabsContent value="details" class="mt-6">
                        <div class="rounded-lg border bg-card p-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <dt class="text-sm font-medium text-muted-foreground">
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
                                    <dt class="text-sm font-medium text-muted-foreground">
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
                                    <dt class="text-sm font-medium text-muted-foreground">
                                        First Name
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.first_name }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt class="text-sm font-medium text-muted-foreground">
                                        Last Name
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.last_name }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt class="text-sm font-medium text-muted-foreground">
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
                                    <dt class="text-sm font-medium text-muted-foreground">
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
                                    <dt class="text-sm font-medium text-muted-foreground">
                                        Phone Number
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.phone_number }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt class="text-sm font-medium text-muted-foreground">
                                        Email
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.email || 'No email' }}
                                    </dd>
                                </div>

                                <div class="space-y-2">
                                    <dt class="text-sm font-medium text-muted-foreground">
                                        Address
                                    </dt>
                                    <dd class="text-sm">
                                        {{ props.patient.address }}
                                    </dd>
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <dt class="text-sm font-medium text-muted-foreground">
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
                                    <dt class="text-sm font-medium text-muted-foreground">
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
                                    <dt class="text-sm font-medium text-muted-foreground">
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
                                                    :variant="order.status === 'completed' ? 'default' : order.status === 'processing' ? 'secondary' : 'outline'">
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
                                                    {{ order.priority.charAt(0).toUpperCase() + order.priority.slice(1)
                                                    }}
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

        <!-- Print Preview Modal -->
        <div v-if="showPrintPreview" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
            @click.self="closePrintPreview">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold">Print Preview - Patient Stickers</h3>
                    <div class="flex gap-2">
                        <Button variant="outline" @click="executePrint">
                            <Printer class="size-4 mr-2" />
                            Print
                        </Button>
                        <Button variant="outline" @click="closePrintPreview">
                            <X class="size-4 mr-2" />
                            Close
                        </Button>
                    </div>
                </div>
                <div class="p-6 overflow-auto max-h-[calc(90vh-80px)]">
                    <div class="text-center mb-4 text-sm text-muted-foreground">
                        This preview shows how the stickers will appear when printed. Use Ctrl+P (or Cmd+P on Mac) to
                        print.
                    </div>
                    <div v-html="stickerHtml" class="border rounded bg-gray-50 p-4"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
