<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Calendar, User, Stethoscope, FileText } from 'lucide-vue-next';

interface Visit {
    id: number;
    patient: {
        user: {
            name: string;
        };
    };
    staff: {
        user: {
            name: string;
        };
    } | null;
    appointment: any;
    visit_date_time: string;
    status: string;
    notes: string;
    created_at: string;
    medical_orders?: any[];
}

interface Props {
    visit: Visit;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visits',
        href: '/visits',
    },
    {
        title: 'Visit Details',
        href: '#',
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'in_progress':
            return 'bg-blue-100 text-blue-800';
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Visit Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <Link href="/visits">
                        <ArrowLeft class="size-4" />
                        Back to Visits
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Visit Details</h1>
                    <p class="text-muted-foreground">View visit information and associated medical orders</p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="`/visits/${visit.id}/edit`">
                            <Edit class="size-4" />
                            Edit Visit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Visit Information -->
                <div class="md:col-span-2">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Calendar class="size-5" />
                                Visit Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                                        <User class="size-4" />
                                        Patient
                                    </div>
                                    <div class="text-sm">{{ visit.patient.user.name }}</div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                                        <Stethoscope class="size-4" />
                                        Assigned Staff
                                    </div>
                                    <div class="text-sm">{{ visit.staff?.user.name || 'Unassigned' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                                        <Calendar class="size-4" />
                                        Visit Date & Time
                                    </div>
                                    <div class="text-sm">
                                        {{ new Date(visit.visit_date_time).toLocaleDateString() }}
                                        <div class="text-muted-foreground">
                                            {{ new Date(visit.visit_date_time).toLocaleTimeString() }}
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="text-sm font-medium text-muted-foreground">Status</div>
                                    <Badge :class="getStatusColor(visit.status)">
                                        {{ visit.status.replace('_', ' ') }}
                                    </Badge>
                                </div>
                            </div>

                            <Separator />

                            <div v-if="visit.appointment" class="space-y-2">
                                <div class="text-sm font-medium text-muted-foreground">Related Appointment</div>
                                <div class="text-sm">
                                    Appointment #{{ visit.appointment.id }} -
                                    {{ new Date(visit.appointment.appointment_date_time).toLocaleDateString() }}
                                </div>
                            </div>

                            <div v-if="visit.notes" class="space-y-2">
                                <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                                    <FileText class="size-4" />
                                    Notes
                                </div>
                                <div class="text-sm">{{ visit.notes }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Medical Orders -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle>Medical Orders</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="visit.medical_orders && visit.medical_orders.length > 0" class="space-y-2">
                                <div v-for="order in visit.medical_orders" :key="order.id" class="flex items-center justify-between">
                                    <div class="text-sm">
                                        <div class="font-medium">{{ order.order_details }}</div>
                                        <div class="text-muted-foreground">{{ order.status }}</div>
                                    </div>
                                    <Badge variant="outline" class="text-xs">
                                        {{ order.priority }}
                                    </Badge>
                                </div>
                            </div>
                            <div v-else class="text-sm text-muted-foreground">
                                No medical orders associated with this visit yet.
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
