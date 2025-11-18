<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, show } from '@/routes/patients';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

interface Props {
    patients: Array<{
        id: number;
        user: { name: string; email: string };
        date_of_birth: string;
        gender: string;
        phone_number: string;
    }>;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Patients',
        href: '#',
    },
];
</script>

<template>

    <Head title="Patients" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('view_patients')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Patients</h1>
                    <p class="text-muted-foreground">Manage patient records</p>
                </div>
                <Button as-child v-if="hasPermission('create_patients')">
                    <Link :href="create().url">
                    <Plus class="size-4" />
                    Add Patient
                    </Link>
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Date of Birth</TableHead>
                            <TableHead>Gender</TableHead>
                            <TableHead>Phone</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="patient in props.patients" :key="patient.id">
                            <TableCell>{{ patient.user.name }}</TableCell>
                            <TableCell>{{ patient.user.email }}</TableCell>
                            <TableCell>{{
                                new Date(
                                    patient.date_of_birth,
                                ).toLocaleDateString()
                            }}</TableCell>
                            <TableCell>{{ patient.gender }}</TableCell>
                            <TableCell>{{ patient.phone_number }}</TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child v-if="hasPermission('view_patients')">
                                        <Link :href="show(patient.id).url">View</Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child v-if="hasPermission('edit_patients')">
                                        <Link :href="edit(patient.id).url">Edit</Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.patients.length === 0">
                            <TableCell colspan="6" class="text-center text-muted-foreground">
                                No patients found
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
                    You do not have permission to view patients.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
