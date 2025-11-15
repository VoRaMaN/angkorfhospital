<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { assignProcess, update } from '@/routes/visits';
import { Plus, Search, Eye, Edit, UserCheck, X, Loader2 } from 'lucide-vue-next';

import { ref } from 'vue';

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
    medical_orders: Array<{
        id: number;
        status: string;
    }>;
}

interface Props {
    visits: Visit[];
    staff: Array<{
        id: number;
        name: string;
    }>;
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visits',
        href: '#',
    },
];

// Modal state
const showAssignModal = ref(false);
const selectedVisit = ref<Visit | null>(null);

// Form for assigning staff
const assignForm = useForm({
    staff_id: '',
});

// Functions
const openAssignModal = (visit: Visit) => {
    selectedVisit.value = visit;
    assignForm.reset();
    showAssignModal.value = true;
};

const assignVisit = () => {
    if (!selectedVisit.value) return;

    assignForm.patch(assignProcess(selectedVisit.value.id).url, {
        onSuccess: () => {
            showAssignModal.value = false;
            selectedVisit.value = null;
            // Refresh the page or update the list
            window.location.reload();
        },
    });
};

const cancelVisit = (visit: Visit) => {
    if (confirm('Are you sure you want to cancel this visit?')) {
        router.patch(update(visit.id).url, {
            status: 'cancelled',
        }, {
            onSuccess: () => {
                // Refresh the page or update the list
                window.location.reload();
            },
        });
    }
};

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

    <Head title="Visits" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Visits</h1>
                    <p class="text-muted-foreground">Manage patient visits and their associated medical orders</p>
                </div>
                <Button as-child>
                    <Link href="/visits/create">
                    <Plus class="size-4" />
                    New Visit
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>All Visits</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="relative flex-1 max-w-sm">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Search visits..." class="pl-8" />
                        </div>
                    </div>

                    <div class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="h-12 px-4 text-left align-middle font-medium">Patient</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium">Staff</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium">Visit Date</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="visit in visits" :key="visit.id" class="border-b">
                                    <td class="p-4 align-middle">
                                        <div class="font-medium">{{ visit.patient.user.name }}</div>
                                        <div v-if="visit.appointment" class="text-sm text-muted-foreground">
                                            From appointment
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ visit.staff?.user.name || 'Unassigned' }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ new Date(visit.visit_date_time).toLocaleDateString() }}
                                        <div class="text-sm text-muted-foreground">
                                            {{ new Date(visit.visit_date_time).toLocaleTimeString() }}
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <Badge :class="getStatusColor(visit.status)">
                                            {{ visit.status.replace('_', ' ') }}
                                        </Badge>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <div class="flex items-center space-x-2">
                                            <Button variant="ghost" size="sm" as-child>
                                                <Link :href="`/visits/${visit.id}`">
                                                <Eye class="size-4 mr-1" />
                                                View
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="sm" as-child>
                                                <Link :href="`/visits/${visit.id}/edit`">
                                                <Edit class="size-4 mr-1" />
                                                Edit
                                                </Link>
                                            </Button>
                                            <Button v-if="visit.status === 'pending'" variant="default" size="sm"
                                                @click="openAssignModal(visit)">
                                                <UserCheck class="size-4 mr-1" />
                                                Assign
                                            </Button>
                                            <Button v-if="visit.status === 'pending' || visit.status === 'in_progress'"
                                                variant="destructive" size="sm" @click="cancelVisit(visit)">
                                                <X class="size-4 mr-1" />
                                                Cancel
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Assign Staff Modal -->
        <Dialog v-model:open="showAssignModal">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Assign Staff to Visit</DialogTitle>
                    <DialogDescription>
                        Select a staff member to assign to this visit. This will also initiate the medical order
                        process.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="staff" class="text-right">
                            Staff
                        </Label>
                        <Select v-model="assignForm.staff_id">
                            <SelectTrigger class="col-span-3">
                                <SelectValue placeholder="Select staff member" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="staff in props.staff" :key="staff.id" :value="staff.id.toString()">
                                    {{ staff.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showAssignModal = false">
                        Cancel
                    </Button>
                    <Button type="button" @click="assignVisit" :disabled="assignForm.processing">
                        <Loader2 v-if="assignForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Assign & Process
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
