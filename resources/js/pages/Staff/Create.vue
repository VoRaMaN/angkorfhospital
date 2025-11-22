<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';
import { index, store } from '@/routes/staff';

interface Props {
    roles: {
        id: number;
        name: string;
    }[];
    departments: {
        id: number;
        name: string;
    }[];
}

const props = defineProps<Props>();

const form = useForm({
    first_name: 'Test',
    last_name: 'Test',
    email: 'test1234@example.com',
    password: '1234567890',
    password_confirmation: '1234567890',
    role_id: '',
    department_id: '',
    hire_date: '',
    contact_number: '1234567890',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Staff',
        href: index().url,
    },
    {
        title: 'Create',
        href: '#',
    },
];

const { hasPermission } = useAuth();

const submit = () => {
    form.post(store().url, {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>

    <Head title="Create Staff" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('create_staff')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="index().url">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Create Staff</h1>
                    <p class="text-muted-foreground">Add a new staff member</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="submit" class="space-y-6 rounded-lg border bg-card p-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="first_name">First Name</Label>
                            <Input id="first_name" v-model="form.first_name" placeholder="Enter first name" required />
                            <div v-if="form.errors.first_name" class="text-sm text-destructive">
                                {{ form.errors.first_name }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="last_name">Last Name</Label>
                            <Input id="last_name" v-model="form.last_name" placeholder="Enter last name" required />
                            <div v-if="form.errors.last_name" class="text-sm text-destructive">
                                {{ form.errors.last_name }}
                            </div>
                        </div>
                    </div>

                    <div class="md:grid-cols grid gap-4">
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input id="email" v-model="form.email" placeholder="Enter email" required />
                            <div v-if="form.errors.email" class="text-sm text-destructive">
                                {{ form.errors.email }}
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="password">Password</Label>
                            <Input id="password" v-model="form.password" type="password" placeholder="Enter password"
                                required />
                            <div v-if="form.errors.password" class="text-sm text-destructive">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="password_confirmation">Confirm Password</Label>
                            <Input id="password_confirmation" v-model="form.password_confirmation" type="password"
                                placeholder="Confirm password" required />
                            <div v-if="form.errors.password_confirmation" class="text-sm text-destructive">
                                {{ form.errors.password_confirmation }}
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="role_id">Role</Label>
                            <Select v-model="form.role_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select role" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="role in props.roles" :key="role.id" :value="role.id.toString()">
                                        {{ role.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.role_id" class="text-sm text-destructive">
                                {{ form.errors.role_id }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="department_id">Department</Label>
                            <Select v-model="form.department_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select department" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="department in props.departments" :key="department.id"
                                        :value="department.id.toString()">
                                        {{ department.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.department_id" class="text-sm text-destructive">
                                {{ form.errors.department_id }}
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="hire_date">Hire Date</Label>
                            <Input id="hire_date" v-model="form.hire_date" type="date" required />
                            <div v-if="form.errors.hire_date" class="text-sm text-destructive">
                                {{ form.errors.hire_date }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="contact_number">contact_number</Label>
                            <Input id="contact_number" v-model="form.contact_number" placeholder="Enter Phone number" />
                            <div v-if="form.errors.contact_number" class="text-sm text-destructive">
                                {{ form.errors.contact_number }}
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="size-4" />
                            {{
                                form.processing ? 'Creating...' : 'Create Staff'
                            }}
                        </Button>
                        <Button type="button" variant="outline" as-child>
                            <a :href="index().url">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to create staff.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
