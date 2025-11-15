<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    doctor: {
        id: number;
        user_id: number;
        staff_id: number;
        first_name: string;
        last_name: string;
        name: string;
        email: string;
        specialization: string;
        department_id: number;
        license_number: string;
    };
    availableStaff: Array<{
        id: number;
        user: { name: string; email: string };
        first_name: string;
        last_name: string;
        contact_number: string;
    }>;
    departments: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Doctors',
        href: '/doctors',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const form = useForm({
    user_id: props.doctor.user_id.toString(),
    staff_id: props.doctor.staff_id.toString(),
    specialization: props.doctor.specialization,
    department_id: props.doctor.department_id.toString(),
    license_number: props.doctor.license_number,
});
</script>

<template>
    <Head title="Edit Doctor" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/doctors">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Doctor</h1>
                    <p class="text-muted-foreground">Update doctor information</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="form.put(`/doctors/${props.doctor.id}`)" class="space-y-6 rounded-lg border bg-card p-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label>Name</Label>
                            <Input
                                :value="props.doctor.name"
                                readonly
                            />
                        </div>

                        <div class="space-y-2">
                            <Label>Email</Label>
                            <Input
                                :value="props.doctor.email"
                                readonly
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="specialization">Specialization</Label>
                            <Input
                                id="specialization"
                                v-model="form.specialization"
                                placeholder="e.g., Cardiology, Pediatrics"
                                required
                            />
                            <div v-if="form.errors.specialization" class="text-sm text-destructive">
                                {{ form.errors.specialization }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="department_id">Department</Label>
                            <Select v-model="form.department_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select department" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="department in props.departments" :key="department.id" :value="department.id">
                                        {{ department.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.department_id" class="text-sm text-destructive">
                                {{ form.errors.department_id }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="license_number">License Number</Label>
                        <Input
                            id="license_number"
                            v-model="form.license_number"
                            placeholder="Enter license number"
                            required
                        />
                        <div v-if="form.errors.license_number" class="text-sm text-destructive">
                            {{ form.errors.license_number }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Update Doctor
                        </Button>
                        <Button variant="outline" as-child>
                            <a href="/doctors">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
