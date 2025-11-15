<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Patients</h1>
                    <p class="text-muted-foreground">Manage patient records</p>
                </div>
                <Button as-child>
                    <Link href="/patients/create">
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
                            <TableCell>{{ new Date(patient.date_of_birth).toLocaleDateString() }}</TableCell>
                            <TableCell>{{ patient.gender }}</TableCell>
                            <TableCell>{{ patient.phone_number }}</TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/patients/${patient.id}`">View</Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/patients/${patient.id}/edit`">Edit</Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>