<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps<{
    doctors: Array<{ id: number; name: string }>;
}>();

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
    name: '',
    surname: '',
    khmer_china_name: '',
    khmer_china_surname: '',
    date_of_birth_day: '',
    date_of_birth_month: '',
    date_of_birth_year: '',
    gender: '',
    id_card_or_passport: '',
    marital_status: '',
    nationality: '',
    religion: '',
    race: '',

    // Address
    address: '',
    building_village: '',
    moo: '',
    soi: '',
    road: '',
    sub_district: '',
    district: '',
    province: '',
    zip_code: '',

    // Contact
    home_phone: '',
    mobile_phone: '',
    email: '',
    occupation: '',
    company_name: '',
    company_phone: '',

    // Emergency Contact
    emergency_contact_name: '',
    emergency_contact_relationship: '',
    emergency_contact_description_other: '',
    emergency_contact_address_same_as_patient: false,
    emergency_contact_address: '',
    emergency_contact_road: '',
    emergency_contact_sub_district: '',
    emergency_contact_district: '',
    emergency_contact_province: '',
    emergency_contact_zip_code: '',
    emergency_contact_home_phone: '',
    emergency_contact_mobile_phone: '',
    emergency_contact_email: '',

    // Payment
    payment_method: '',
    contract_name: '',
    insurance_name: '',
    staff_id: '',
    patient_type: '',
});

// Watch for address changes to copy to emergency contact if needed (optional feature)
// For now, we'll just keep it simple.

const copyAddressToEmergency = () => {
    form.emergency_contact_address = form.address;
    form.emergency_contact_road = form.road;
    form.emergency_contact_sub_district = form.sub_district;
    form.emergency_contact_district = form.district;
    form.emergency_contact_province = form.province;
    form.emergency_contact_zip_code = form.zip_code;
};
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
                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Personal Information</h3>
                        <div class="rounded-md border">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium w-1/3">Title</td>
                                        <td class="p-4">
                                            <Select v-model="form.title">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select title" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Mr.">Mr.</SelectItem>
                                                    <SelectItem value="Mrs.">Mrs.</SelectItem>
                                                    <SelectItem value="Ms.">Ms.</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Name <span class="text-destructive">*</span></td>
                                        <td class="p-4">
                                            <Input v-model="form.name" placeholder="Name" />
                                            <div v-if="form.errors.name" class="text-sm text-destructive mt-1">{{ form.errors.name }}</div>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Surname <span class="text-destructive">*</span></td>
                                        <td class="p-4">
                                            <Input v-model="form.surname" placeholder="Surname" />
                                            <div v-if="form.errors.surname" class="text-sm text-destructive mt-1">{{ form.errors.surname }}</div>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Khmer/China Name</td>
                                        <td class="p-4">
                                            <Input v-model="form.khmer_china_name" placeholder="Khmer/China Name" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Khmer/China Surname</td>
                                        <td class="p-4">
                                            <Input v-model="form.khmer_china_surname" placeholder="Khmer/China Surname" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Date of Birth <span class="text-destructive">*</span></td>
                                        <td class="p-4">
                                            <div class="grid grid-cols-3 gap-4">
                                                <div>
                                                    <Input v-model="form.date_of_birth_day" type="number" placeholder="DD" />
                                                    <div v-if="form.errors.date_of_birth_day" class="text-sm text-destructive mt-1">{{ form.errors.date_of_birth_day }}</div>
                                                </div>
                                                <div>
                                                    <Input v-model="form.date_of_birth_month" type="number" placeholder="MM" />
                                                    <div v-if="form.errors.date_of_birth_month" class="text-sm text-destructive mt-1">{{ form.errors.date_of_birth_month }}</div>
                                                </div>
                                                <div>
                                                    <Input v-model="form.date_of_birth_year" type="number" placeholder="YYYY" />
                                                    <div v-if="form.errors.date_of_birth_year" class="text-sm text-destructive mt-1">{{ form.errors.date_of_birth_year }}</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Gender</td>
                                        <td class="p-4">
                                            <Select v-model="form.gender">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select gender" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Male">Male</SelectItem>
                                                    <SelectItem value="Female">Female</SelectItem>
                                                    <SelectItem value="Other">Other</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">ID Card/Passport</td>
                                        <td class="p-4">
                                            <Input v-model="form.id_card_or_passport" placeholder="ID Card/Passport" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Marital Status</td>
                                        <td class="p-4">
                                            <Select v-model="form.marital_status">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select marital status" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Single">Single</SelectItem>
                                                    <SelectItem value="Married">Married</SelectItem>
                                                    <SelectItem value="Divorced">Divorced</SelectItem>
                                                    <SelectItem value="Widowed">Widowed</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Nationality <span class="text-destructive">*</span></td>
                                        <td class="p-4">
                                            <Select v-model="form.nationality">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select nationality" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Thai">Thai</SelectItem>
                                                    <SelectItem value="Cambodian">Cambodian</SelectItem>
                                                    <SelectItem value="Vietnamese">Vietnamese</SelectItem>
                                                    <SelectItem value="Laotian">Laotian</SelectItem>
                                                    <SelectItem value="Myanma">Myanma</SelectItem>
                                                    <SelectItem value="Chinese">Chinese</SelectItem>
                                                    <SelectItem value="Other">Other</SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <div v-if="form.errors.nationality" class="text-sm text-destructive mt-1">{{ form.errors.nationality }}</div>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Religion</td>
                                        <td class="p-4">
                                            <Select v-model="form.religion">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select religion" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Buddhism">Buddhism</SelectItem>
                                                    <SelectItem value="Christianity">Christianity</SelectItem>
                                                    <SelectItem value="Islam">Islam</SelectItem>
                                                    <SelectItem value="Hinduism">Hinduism</SelectItem>
                                                    <SelectItem value="Other">Other</SelectItem>
                                                    <SelectItem value="None">None</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-4 font-medium">Race</td>
                                        <td class="p-4">
                                            <Select v-model="form.race">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select race" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Asian">Asian</SelectItem>
                                                    <SelectItem value="Caucasian">Caucasian</SelectItem>
                                                    <SelectItem value="African">African</SelectItem>
                                                    <SelectItem value="Hispanic">Hispanic</SelectItem>
                                                    <SelectItem value="Other">Other</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-4 border-t pt-4">
                        <h3 class="text-lg font-medium">Address</h3>
                        <div class="rounded-md border">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium w-1/3">Address</td>
                                        <td class="p-4">
                                            <Input v-model="form.address" placeholder="Address" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Building/Village</td>
                                        <td class="p-4">
                                            <Input v-model="form.building_village" placeholder="Building/Village" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Moo</td>
                                        <td class="p-4">
                                            <Input v-model="form.moo" placeholder="Moo" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Soi</td>
                                        <td class="p-4">
                                            <Input v-model="form.soi" placeholder="Soi" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Road</td>
                                        <td class="p-4">
                                            <Input v-model="form.road" placeholder="Road" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Sub-district</td>
                                        <td class="p-4">
                                            <Input v-model="form.sub_district" placeholder="Sub-district" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">District</td>
                                        <td class="p-4">
                                            <Input v-model="form.district" placeholder="District" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Province</td>
                                        <td class="p-4">
                                            <Input v-model="form.province" placeholder="Province" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-4 font-medium">Zip Code</td>
                                        <td class="p-4">
                                            <Input v-model="form.zip_code" placeholder="Zip Code" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-4 border-t pt-4">
                        <h3 class="text-lg font-medium">Contact Information</h3>
                        <div class="rounded-md border">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium w-1/3">Home Phone</td>
                                        <td class="p-4">
                                            <Input v-model="form.home_phone" placeholder="Home Phone" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Mobile Phone</td>
                                        <td class="p-4">
                                            <Input v-model="form.mobile_phone" placeholder="Mobile Phone" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Email</td>
                                        <td class="p-4">
                                            <Input v-model="form.email" type="email" placeholder="Email Address" />
                                            <div v-if="form.errors.email" class="text-sm text-destructive mt-1">{{ form.errors.email }}</div>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Occupation</td>
                                        <td class="p-4">
                                            <Input v-model="form.occupation" placeholder="Occupation" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Company Name</td>
                                        <td class="p-4">
                                            <Input v-model="form.company_name" placeholder="Company Name" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-4 font-medium">Company Phone</td>
                                        <td class="p-4">
                                            <Input v-model="form.company_phone" placeholder="Company Phone" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="space-y-4 border-t pt-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">Emergency Contact</h3>
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="emergency_contact_address_same_as_patient"
                                    v-model:checked="form.emergency_contact_address_same_as_patient"
                                    @update:checked="copyAddressToEmergency"
                                />
                                <label
                                    for="emergency_contact_address_same_as_patient"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    Same as patient's address
                                </label>
                            </div>
                        </div>
                        <div class="rounded-md border">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium w-1/3">Name</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_name" placeholder="Contact Name" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Relationship</td>
                                        <td class="p-4">
                                            <Select v-model="form.emergency_contact_relationship">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select relationship" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Spouse">Spouse</SelectItem>
                                                    <SelectItem value="Parent">Parent</SelectItem>
                                                    <SelectItem value="Sibling">Sibling</SelectItem>
                                                    <SelectItem value="Friend">Friend</SelectItem>
                                                    <SelectItem value="Other">Other</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Other Relationship</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_description_other" placeholder="Please specify" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Address</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_address" placeholder="Contact Address" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Road</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_road" placeholder="Road" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Sub-district</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_sub_district" placeholder="Sub-district" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">District</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_district" placeholder="District" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Province</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_province" placeholder="Province" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Zip Code</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_zip_code" placeholder="Zip Code" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Home Phone</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_home_phone" placeholder="Home Phone" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Mobile Phone</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_mobile_phone" placeholder="Mobile Phone" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-4 font-medium">Email</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_email" placeholder="Email" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="space-y-4 border-t pt-4">
                        <h3 class="text-lg font-medium">Payment Information</h3>
                        <div class="rounded-md border">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium w-1/3">Payment Method</td>
                                        <td class="p-4">
                                            <Select v-model="form.payment_method">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select method" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Corporate Contract">Corporate Contract</SelectItem>
                                                    <SelectItem value="Self-Pay">Self-Pay</SelectItem>
                                                    <SelectItem value="Insurance">Insurance</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Contract Name</td>
                                        <td class="p-4">
                                            <Input v-model="form.contract_name" placeholder="Contract Name" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Insurance Name</td>
                                        <td class="p-4">
                                            <Input v-model="form.insurance_name" placeholder="Insurance Name" />
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-4 font-medium">Referring Doctor</td>
                                        <td class="p-4">
                                            <Select v-model="form.staff_id">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select doctor" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="doctor in props.doctors" :key="doctor.id" :value="doctor.id.toString()">
                                                        {{ doctor.name }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-4 font-medium">Patient Type</td>
                                        <td class="p-4">
                                            <Select v-model="form.patient_type">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Patient">Patient</SelectItem>
                                                    <SelectItem value="Customer">Customer</SelectItem>
                                                    <SelectItem value="Dependent">Dependent</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
