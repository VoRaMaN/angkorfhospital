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
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Patients',
        href: '/patients',
    },
    {
        title: 'Create',
        href: '#',
    },
];

const { hasPermission } = useAuth();

const form = useForm({
    first_name: '',
    last_name: '',
    date_of_birth: '',
    gender: '',
    address: '',
    phone_number: '',
    email: '',
    insurance_info: '',
});
</script>

<template>
    <Head title="Create Patient" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('create_patients')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/patients">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Create Patient</h1>
                    <p class="text-muted-foreground">
                        Add a new patient record
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form
                    @submit.prevent="form.post('/patients')"
                    class="space-y-6"
                >
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="first_name">First Name</Label>
                            <Input
                                id="first_name"
                                v-model="form.first_name"
                                placeholder="Enter first name"
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
                            <Label for="date_of_birth">Date of Birth</Label>
                            <Input
                                id="date_of_birth"
                                v-model="form.date_of_birth"
                                type="date"
                            />
                            <div
                                v-if="form.errors.date_of_birth"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.date_of_birth }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="gender">Gender</Label>
                            <Select v-model="form.gender">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select gender" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="male">Male</SelectItem>
                                    <SelectItem value="female"
                                        >Female</SelectItem
                                    >
                                    <SelectItem value="other">Other</SelectItem>
                                </SelectContent>
                            </Select>
                            <div
                                v-if="form.errors.gender"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.gender }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="phone_number">Phone Number</Label>
                        <Input
                            id="phone_number"
                            v-model="form.phone_number"
                            placeholder="Enter phone number"
                        />
                        <div
                            v-if="form.errors.phone_number"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.phone_number }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="Enter email address"
                        />
                        <div
                            v-if="form.errors.email"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="address">Address</Label>
                        <Input
                            id="address"
                            v-model="form.address"
                            placeholder="Enter address"
                        />
                        <div
                            v-if="form.errors.address"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.address }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="insurance_info"
                            >Insurance Information</Label
                        >
                        <Input
                            id="insurance_info"
                            v-model="form.insurance_info"
                            placeholder="Enter insurance info"
                        />
                        <div
                            v-if="form.errors.insurance_info"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.insurance_info }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Create Patient
                        </Button>
                        <Button variant="outline" as-child>
                            <a href="/patients">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
        <div v-else class="flex h-full flex-1 items-center justify-center">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">
                    Access Denied
                </h2>
                <p class="text-muted-foreground">
                    You do not have permission to create patients.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
