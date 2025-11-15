<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import PatientFilesTab from '@/pages/Patients/PatientFilesTab.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    patient: {
        id: number;
        user?: { name: string; email: string };
        first_name: string;
        last_name: string;
        date_of_birth: string;
        gender: string;
        address: string;
        phone_number: string;
        email?: string;
        insurance_info: string;
        patientFiles?: Array<any>;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Patients',
        href: '/patients',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const form = useForm({
    create_user_account: false,
    name: '',
    email: '',
    first_name: props.patient.first_name,
    last_name: props.patient.last_name,
    date_of_birth: props.patient.date_of_birth,
    gender: props.patient.gender,
    address: props.patient.address,
    phone_number: props.patient.phone_number,
    patient_email: props.patient.email || '',
    insurance_info: props.patient.insurance_info,
});
</script>

<template>
    <Head title="Edit Patient" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
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
                    <h1 class="text-2xl font-bold">Edit Patient</h1>
                    <p class="text-muted-foreground">
                        Update patient information
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <Tabs default-value="information" class="w-full">
                    <TabsList class="grid w-full grid-cols-2">
                        <TabsTrigger value="information"
                            >Patient Information</TabsTrigger
                        >
                        <TabsTrigger value="files">Files</TabsTrigger>
                    </TabsList>

                    <TabsContent value="information" class="mt-6">
                        <form
                            @submit.prevent="
                                form.put(`/patients/${props.patient.id}`)
                            "
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
                                    <Label for="date_of_birth"
                                        >Date of Birth</Label
                                    >
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
                                    <label class="text-sm font-medium"
                                        >Gender</label
                                    >
                                    <Select v-model="form.gender">
                                        <SelectTrigger>
                                            <SelectValue
                                                placeholder="Select gender"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="male"
                                                >Male</SelectItem
                                            >
                                            <SelectItem value="female"
                                                >Female</SelectItem
                                            >
                                            <SelectItem value="other"
                                                >Other</SelectItem
                                            >
                                        </SelectContent>
                                    </Select>
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
                                <Label for="patient_email">Email</Label>
                                <Input
                                    id="patient_email"
                                    v-model="form.patient_email"
                                    type="email"
                                    placeholder="Enter email address"
                                />
                                <div
                                    v-if="form.errors.patient_email"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.patient_email }}
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

                            <div
                                class="flex items-center space-x-2"
                                v-if="!props.patient.user"
                            >
                                <Checkbox
                                    id="create_user_account"
                                    v-model:checked="form.create_user_account"
                                />
                                <label
                                    for="create_user_account"
                                    class="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    Create user account for self-service
                                </label>
                            </div>

                            <template
                                v-if="
                                    form.create_user_account &&
                                    !props.patient.user
                                "
                            >
                                <div class="border-t pt-6">
                                    <h3 class="mb-4 text-lg font-medium">
                                        User Account Information
                                    </h3>

                                    <div class="space-y-2">
                                        <Label for="user_name">Full Name</Label>
                                        <Input
                                            id="user_name"
                                            v-model="form.name"
                                            placeholder="Enter full name for user account"
                                        />
                                        <div
                                            v-if="form.errors.name"
                                            class="text-sm text-destructive"
                                        >
                                            {{ form.errors.name }}
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="user_email">Email</Label>
                                        <Input
                                            id="user_email"
                                            v-model="form.email"
                                            type="email"
                                            placeholder="Enter email for user account"
                                        />
                                        <div
                                            v-if="form.errors.email"
                                            class="text-sm text-destructive"
                                        >
                                            {{ form.errors.email }}
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="flex gap-4">
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    Update Patient
                                </Button>
                                <Button variant="outline" as-child>
                                    <a href="/patients">Cancel</a>
                                </Button>
                            </div>
                        </form>
                    </TabsContent>

                    <TabsContent value="files" class="mt-6">
                        <PatientFilesTab :patient="props.patient" />
                    </TabsContent>
                </Tabs>
            </div>
        </div>
    </AppLayout>
</template>
