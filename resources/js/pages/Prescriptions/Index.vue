<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus, Search, Eye, Edit, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    prescriptions: {
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
    }[];
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Prescriptions',
        href: '/prescriptions',
    },
];
</script>

<template>
    <Head title="Prescriptions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Prescriptions</h1>
                    <p class="text-muted-foreground">Manage patient prescriptions</p>
                </div>
                <div class="ml-auto">
                    <Button as-child>
                        <Link href="/prescriptions/create">
                            <Plus class="size-4" />
                            Add Prescription
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search prescriptions..."
                        class="pl-9"
                    />
                </div>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Patient</TableHead>
                            <TableHead>Doctor</TableHead>
                            <TableHead>Medication</TableHead>
                            <TableHead>Dosage</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Prescribed Date</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="prescription in props.prescriptions" :key="prescription.id">
                            <TableCell class="font-medium">{{ prescription.patient_name }}</TableCell>
                            <TableCell>{{ prescription.doctor_name }}</TableCell>
                            <TableCell>{{ prescription.medication_name }}</TableCell>
                            <TableCell>{{ prescription.dosage }}</TableCell>
                            <TableCell>
                                <Badge :variant="prescription.status === 'active' ? 'default' : 'secondary'">
                                    {{ prescription.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ new Date(prescription.prescribed_date).toLocaleDateString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="`/prescriptions/${prescription.id}`">
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="`/prescriptions/${prescription.id}/edit`">
                                            <Edit class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm">
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.prescriptions.length === 0">
                            <TableCell colspan="7" class="text-center text-muted-foreground">
                                No prescriptions found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
