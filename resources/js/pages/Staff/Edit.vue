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
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import StaffFilesTab from './StaffFilesTab.vue';
import { useAuth } from '@/composables/useAuth';

interface Props {
    staff: {
        id: number;
        user_id: number;
        first_name: string;
        last_name: string;
        name: string;
        email: string;
        role_id: number;
        department_id: number;
        contact_number: string;
        staffFiles?: Array<any>;
    };
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
    first_name: props.staff.first_name,
    last_name: props.staff.last_name,
    name: props.staff.name,
    email: props.staff.email,
    role_id: props.staff.role_id?.toString() || '',
    department_id: props.staff.department_id?.toString() || '',
    contact_number: props.staff.contact_number || '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Staff',
        href: '/staff',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const { hasPermission } = useAuth();

const submit = () => {
    form.put(`/staff/${props.staff.id}`, {
        onSuccess: () => {
            // Success handled by Inertia
        },
    });
};
</script>

<template>
    <Head title="Edit Staff" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('edit_staff')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/staff">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Staff</h1>
                    <p class="text-muted-foreground">
                        Update staff information
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <Tabs default-value="information" class="w-full">
                    <TabsList class="grid w-full grid-cols-2">
                        <TabsTrigger value="information"
                            >Staff Information</TabsTrigger
                        >
                        <TabsTrigger value="files">Files</TabsTrigger>
                    </TabsList>

                    <TabsContent value="information" class="mt-6">
                        <form
                            @submit.prevent="submit"
                            class="space-y-6 rounded-lg border bg-card p-6"
                        >
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="first_name">First Name</Label>
                                    <Input
                                        id="first_name"
                                        v-model="form.first_name"
                                        placeholder="Enter first name"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.first_name"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.first_name }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="last_name">Last Name</Label>
                                    <Input
                                        id="last_name"
                                        v-model="form.last_name"
                                        placeholder="Enter last name"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.last_name"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.last_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="name">Full Name</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        placeholder="Enter full name"
                                        readonly
                                    />
                                    <div
                                        v-if="form.errors.name"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="Enter email address"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.email"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="role_id">Role</Label>
                                    <Select v-model="form.role_id">
                                        <SelectTrigger>
                                            <SelectValue
                                                placeholder="Select role"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="role in props.roles"
                                                :key="role.id"
                                                :value="role.id.toString()"
                                            >
                                                {{ role.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <div
                                        v-if="form.errors.role_id"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.role_id }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="department_id"
                                        >Department</Label
                                    >
                                    <Select v-model="form.department_id">
                                        <SelectTrigger>
                                            <SelectValue
                                                placeholder="Select department"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="department in props.departments"
                                                :key="department.id"
                                                :value="
                                                    department.id.toString()
                                                "
                                            >
                                                {{ department.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <div
                                        v-if="form.errors.department_id"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.department_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="contact_number">Phone</Label>
                                <Input
                                    id="contact_number"
                                    v-model="form.contact_number"
                                    placeholder="Enter phone number"
                                />
                                <div
                                    v-if="form.errors.contact_number"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.contact_number }}
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    <Save class="size-4" />
                                    {{
                                        form.processing
                                            ? 'Updating...'
                                            : 'Update Staff'
                                    }}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    as-child
                                >
                                    <a href="/staff">Cancel</a>
                                </Button>
                            </div>
                        </form>
                    </TabsContent>

                    <TabsContent value="files" class="mt-6">
                        <StaffFilesTab :staff="props.staff" />
                    </TabsContent>
                </Tabs>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to edit staff.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
