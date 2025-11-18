<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { processPage, sendBack, completePage } from '@/routes/medical-orders';
import { show } from '@/routes/visits';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Calendar, Clock, Eye, Play, User } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    visits: Array<{
        id: number;
        patient: {
            user: {
                name: string;
            };
        };
        appointment: any;
        visit_date_time: string;
        status: string;
        notes?: string;
        created_at: string;
        medical_orders: Array<{
            id: number;
            status: string;
        }>;
    }>;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const showSendBackDialog = ref(false);
const selectedOrder = ref<number | null>(null);
const reason = ref('');

const sendBackOrder = () => {
    if (selectedOrder.value && reason.value.trim()) {
        router.patch(sendBack(selectedOrder.value).url, { reason: reason.value }, {
            onSuccess: () => {
                showSendBackDialog.value = false;
                reason.value = '';
                selectedOrder.value = null;
            }
        });
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'My Visits to Process',
        href: '/doctors/my-to-be-process-visits',
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'awaiting_assignment':
            return 'bg-orange-100 text-orange-800';
        case 'assigned':
            return 'bg-purple-100 text-purple-800';
        case 'in_progress':
            return 'bg-blue-100 text-blue-800';
        case 'sent_back':
            return 'bg-red-100 text-red-800';
        case 'awaiting_accountant':
            return 'bg-cyan-100 text-cyan-800';
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
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="My Visits to Process" />

        <div v-if="hasPermission('view_visits')" class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">My Visits to Process</h1>
                    <p class="text-muted-foreground">
                        Process your assigned patient visits
                    </p>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Visit Date & Time</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="visit in visits" :key="visit.id">
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <div>
                                        <div class="font-medium">
                                            {{ visit.patient.user.name }}
                                        </div>
                                        <div v-if="visit.appointment" class="text-sm text-muted-foreground">
                                            From appointment
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <div>
                                        <div>
                                            {{
                                                new Date(
                                                    visit.visit_date_time,
                                                ).toLocaleDateString()
                                            }}
                                        </div>
                                        <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                            <Clock class="h-3 w-3" />
                                            {{
                                                new Date(
                                                    visit.visit_date_time,
                                                ).toLocaleTimeString()
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge :class="getStatusColor(visit.status)">
                                    {{ visit.status.replace('_', ' ') }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="show(visit.id).url">
                                            <Eye class="mr-1 h-4 w-4" />
                                            View Details
                                        </Link>
                                    </Button>
                                    <Button
                                        v-if="visit.medical_orders.length > 0 && (visit.status === 'assigned' || visit.status === 'in_progress' || visit.status === 'sent_back') && hasPermission('process_medical_orders')"
                                        variant="outline" class="border-green-600 text-green-600" size="sm" as-child>
                                        <Link :href="processPage(
                                            visit.medical_orders[0].id,
                                        ).url
                                            ">
                                        <Play class="mr-1 h-4 w-4" />
                                        Process Visit
                                        </Link>
                                    </Button>
                                    <Button
                                        v-if="visit.medical_orders.length > 0 && visit.status === 'awaiting_accountant' && hasPermission('send_back_medical_orders')"
                                        variant="outline" class="border-red-600 text-red-600" size="sm"
                                        @click="selectedOrder = visit.medical_orders[0].id; showSendBackDialog = true">
                                        Send Back
                                    </Button>
                                    <Button
                                        v-if="visit.medical_orders.length > 0 && visit.status === 'awaiting_accountant' && hasPermission('complete_medical_orders')"
                                        variant="outline" class="border-blue-600 text-blue-600" size="sm" as-child>
                                        <Link :href="completePage(visit.medical_orders[0].id).url">
                                            Complete Visit
                                        </Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
        <div v-else class="flex h-full flex-1 items-center justify-center">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">
                    Access Denied
                </h2>
                <p class="text-muted-foreground">
                    You do not have permission to view visits.
                </p>
            </div>
        </div>

        <Dialog v-model:open="showSendBackDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Send Back for Revision</DialogTitle>
                    <DialogDescription>
                        Provide a reason for sending this medical order back for revision.
                    </DialogDescription>
                </DialogHeader>
                <Textarea v-model="reason" placeholder="Enter reason..." />
                <DialogFooter>
                    <Button variant="outline" @click="showSendBackDialog = false">Cancel</Button>
                    <Button @click="sendBackOrder" :disabled="!reason.trim()">Send Back</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
