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
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { watch } from 'vue';

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
    title: '',
    first_name: '',
    last_name: '',
    native_name: '',
    native_surname: '',
    date_of_birth: '',
    identification_number: '',
    marital_status: 'Single',
    nationality: 'Thai',
    religion: '',
    race: '',
    gender: '',
    address: '',
    address_building_village: '',
    address_moo: '',
    address_soi: '',
    address_road: '',
    address_sub_district: '',
    address_district: '',
    address_province: '',
    address_zip_code: '',
    phone_number: '',
    home_phone_number: '',
    email: '',
    occupation: '',
    company_name: '',
    company_phone_number: '',
    emergency_contact_name: '',
    emergency_contact_relationship: 'Spouse',
    emergency_contact_description: '',
    emergency_contact_same_address: false,
    emergency_contact_address: '',
    emergency_contact_road: '',
    emergency_contact_sub_district: '',
    emergency_contact_district: '',
    emergency_contact_province: '',
    emergency_contact_zip_code: '',
    emergency_contact_home_phone: '',
    emergency_contact_mobile_phone: '',
    emergency_contact_email: '',
    payment_method: 'Cash',
    contract_name: '',
    insurance_name: '',
    insurance_info: '',
    agent_name: '',
    patient_type: 'Patient',
});

watch(
    () => form.emergency_contact_same_address,
    (val) => {
        if (val) {
            form.emergency_contact_address = form.address;
            form.emergency_contact_road = form.address_road;
            form.emergency_contact_sub_district = form.address_sub_district;
            form.emergency_contact_district = form.address_district;
            form.emergency_contact_province = form.address_province;
            form.emergency_contact_zip_code = form.address_zip_code;
        } else {
            // Optional: clear fields if unchecked? Maybe not.
        }
    },
);
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
                    <!-- Personal Info -->
                    <div class="grid gap-4 md:grid-cols-12">
                        <div class="col-span-2 space-y-2">
                            <Label for="title">Title</Label>
                            <Select v-model="form.title">
                                <SelectTrigger>
                                    <SelectValue placeholder="Title" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Mr.">Mr.</SelectItem>
                                    <SelectItem value="Mrs.">Mrs.</SelectItem>
                                    <SelectItem value="Ms.">Ms.</SelectItem>
                                    <SelectItem value="Dr.">Dr.</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="col-span-5 space-y-2">
                            <Label for="first_name">Name</Label>
                            <Input id="first_name" v-model="form.first_name" />
                            <div v-if="form.errors.first_name" class="text-sm text-destructive">{{ form.errors.first_name }}</div>
                        </div>
                        <div class="col-span-5 space-y-2">
                            <Label for="last_name">Surname</Label>
                            <Input id="last_name" v-model="form.last_name" />
                            <div v-if="form.errors.last_name" class="text-sm text-destructive">{{ form.errors.last_name }}</div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="native_name">Thai/China Name</Label>
                            <Input id="native_name" v-model="form.native_name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="native_surname">Surname</Label>
                            <Input id="native_surname" v-model="form.native_surname" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="date_of_birth">Date of Birth</Label>
                            <Input id="date_of_birth" v-model="form.date_of_birth" type="date" />
                            <div v-if="form.errors.date_of_birth" class="text-sm text-destructive">{{ form.errors.date_of_birth }}</div>
                        </div>
                        <div class="space-y-2">
                            <Label for="identification_number">ID Card No./Passport No.</Label>
                            <Input id="identification_number" v-model="form.identification_number" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="space-y-2">
                            <Label for="marital_status">Marital</Label>
                            <Select v-model="form.marital_status">
                                <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Single">Single</SelectItem>
                                    <SelectItem value="Married">Married</SelectItem>
                                    <SelectItem value="Divorced">Divorced</SelectItem>
                                    <SelectItem value="Widowed">Widowed</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-2">
                            <Label for="nationality">Nationality</Label>
                            <Select v-model="form.nationality">
                                <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Thai">Thai</SelectItem>
                                    <SelectItem value="Chinese">Chinese</SelectItem>
                                    <SelectItem value="Other">Other</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-2">
                            <Label for="religion">Religion</Label>
                            <Select v-model="form.religion">
                                <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Buddhism">Buddhism</SelectItem>
                                    <SelectItem value="Christianity">Christianity</SelectItem>
                                    <SelectItem value="Islam">Islam</SelectItem>
                                    <SelectItem value="Other">Other</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-2">
                            <Label for="race">Race</Label>
                            <Input id="race" v-model="form.race" />
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-2">
                        <Label for="address">Address</Label>
                        <div class="grid gap-4 md:grid-cols-12">
                            <div class="col-span-4">
                                <Input v-model="form.address" placeholder="House No." />
                            </div>
                            <div class="col-span-4">
                                <Input v-model="form.address_building_village" placeholder="Building, Village" />
                            </div>
                            <div class="col-span-4">
                                <Input v-model="form.address_moo" placeholder="Moo" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-12 mt-2">
                            <div class="col-span-4">
                                <Input v-model="form.address_soi" placeholder="SOI" />
                            </div>
                            <div class="col-span-4">
                                <Input v-model="form.address_road" placeholder="Road" />
                            </div>
                            <div class="col-span-4">
                                <Input v-model="form.address_sub_district" placeholder="Sub-District" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-12 mt-2">
                            <div class="col-span-4">
                                <Input v-model="form.address_district" placeholder="District" />
                            </div>
                            <div class="col-span-4">
                                <Input v-model="form.address_province" placeholder="Province" />
                            </div>
                            <div class="col-span-4">
                                <Input v-model="form.address_zip_code" placeholder="ZipCode" />
                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="home_phone_number">Home Phone No</Label>
                            <Input id="home_phone_number" v-model="form.home_phone_number" />
                        </div>
                        <div class="space-y-2">
                            <Label for="phone_number">Mobile Phone No</Label>
                            <Input id="phone_number" v-model="form.phone_number" />
                            <div v-if="form.errors.phone_number" class="text-sm text-destructive">{{ form.errors.phone_number }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label for="email">E-Mail</Label>
                        <Input id="email" v-model="form.email" type="email" />
                        <div v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</div>
                    </div>

                    <!-- Employment -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="occupation">Occupation</Label>
                            <Input id="occupation" v-model="form.occupation" />
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="company_name">Company Name</Label>
                            <Input id="company_name" v-model="form.company_name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="company_phone_number">Company Phone No</Label>
                            <Input id="company_phone_number" v-model="form.company_phone_number" />
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="space-y-4 border-t pt-4">
                        <h3 class="font-semibold">Emergency Personal Contact:</h3>
                        <div class="space-y-2">
                            <Input v-model="form.emergency_contact_name" placeholder="Name" />
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Relationship</Label>
                                <Select v-model="form.emergency_contact_relationship">
                                    <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Spouse">Spouse</SelectItem>
                                        <SelectItem value="Parent">Parent</SelectItem>
                                        <SelectItem value="Sibling">Sibling</SelectItem>
                                        <SelectItem value="Friend">Friend</SelectItem>
                                        <SelectItem value="Other">Other</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Description Other</Label>
                                <Input v-model="form.emergency_contact_description" />
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox id="same_address" :checked="form.emergency_contact_same_address" @update:checked="form.emergency_contact_same_address = $event" />
                            <Label for="same_address">Address as same as Patient</Label>
                        </div>
                        
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>EP-Address</Label>
                                <Input v-model="form.emergency_contact_address" />
                            </div>
                            <div class="space-y-2">
                                <Label>EP-Road</Label>
                                <Input v-model="form.emergency_contact_road" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>EP-Sub-District</Label>
                                <Input v-model="form.emergency_contact_sub_district" />
                            </div>
                            <div class="space-y-2">
                                <Label>EP-District</Label>
                                <Input v-model="form.emergency_contact_district" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>EP-Province</Label>
                                <Input v-model="form.emergency_contact_province" />
                            </div>
                            <div class="space-y-2">
                                <Label>EP-ZipCode</Label>
                                <Input v-model="form.emergency_contact_zip_code" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label>EP-Home Phone No</Label>
                                <Input v-model="form.emergency_contact_home_phone" />
                            </div>
                            <div class="space-y-2">
                                <Label>EP-Mobile Phone No</Label>
                                <Input v-model="form.emergency_contact_mobile_phone" />
                            </div>
                            <div class="space-y-2">
                                <Label>EP-E-Mail</Label>
                                <Input v-model="form.emergency_contact_email" />
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Insurance -->
                    <div class="space-y-4 border-t pt-4">
                        <div class="space-y-2">
                            <Label>Payment Method</Label>
                            <Select v-model="form.payment_method">
                                <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Cash">Cash</SelectItem>
                                    <SelectItem value="Credit Card">Credit Card</SelectItem>
                                    <SelectItem value="Insurance">Insurance</SelectItem>
                                    <SelectItem value="Corporate Contract">Corporate Contract</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Contract Name</Label>
                                <Input v-model="form.contract_name" />
                            </div>
                            <div class="space-y-2">
                                <Label>Insurance Name</Label>
                                <Input v-model="form.insurance_name" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Agent Name</Label>
                                <Input v-model="form.agent_name" />
                            </div>
                            <div class="space-y-2">
                                <Label>Patient Type</Label>
                                <Select v-model="form.patient_type">
                                    <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Patient">Patient</SelectItem>
                                        <SelectItem value="VIP">VIP</SelectItem>
                                        <SelectItem value="Staff">Staff</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
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
