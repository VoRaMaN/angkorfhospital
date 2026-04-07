<script setup lang="ts">
import { Button } from '@/components/ui/button';
import DateOfBirthInput from '@/components/DateOfBirthInput.vue';
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
import { watch } from 'vue';

const props = defineProps<{
    doctors: Array<{ id: number; name: string }>;
}>();

const cambodiaProvinces = [
    'Banteay Meanchey',
    'Battambang',
    'Kampong Cham',
    'Kampong Chhnang',
    'Kampong Speu',
    'Kampong Thom',
    'Kampot',
    'Kandal',
    'Kep',
    'Koh Kong',
    'Kratié',
    'Mondulkiri',
    'Oddar Meanchey',
    'Pailin',
    'Phnom Penh',
    'Preah Sihanouk',
    'Preah Vihear',
    'Pursat',
    'Prey Veng',
    'Ratanakiri',
    'Siem Reap',
    'Stung Treng',
    'Svay Rieng',
    'Takéo',
    'Tbong Khmum',
];

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

// Auto-populate religion based on nationality
watch(() => form.nationality, (newNationality) => {
    const nationalityReligionMap: Record<string, string> = {
        'Cambodian': 'Buddhism',
        'Thai': 'Buddhism',
        'Laotian': 'Buddhism',
        'Myanmar': 'Buddhism',
        'American': 'Christianity',
        'British': 'Christianity',
        'Canadian': 'Christianity',
        'Australian': 'Christianity',
        'German': 'Christianity',
        'French': 'Christianity',
        'Italian': 'Christianity',
        'Spanish': 'Christianity',
        'Portuguese': 'Christianity',
        'Brazilian': 'Christianity',
        'Mexican': 'Christianity',
        'Filipino': 'Christianity',
        'South Korean': 'Christianity',
        'Indonesian': 'Islam',
        'Malaysian': 'Islam',
        'Pakistani': 'Islam',
        'Bangladeshi': 'Islam',
        'Turkish': 'Islam',
        'Egyptian': 'Islam',
        'Saudi Arabian': 'Islam',
        'Emirati': 'Islam',
        'Moroccan': 'Islam',
        'Iranian': 'Islam',
        'Iraqi': 'Islam',
        'Jordanian': 'Islam',
        'Lebanese': 'Islam',
        'Syrian': 'Islam',
        'Indian': 'Hinduism',
        'Nepali': 'Hinduism',
        'Chinese': 'Buddhism',
        'Japanese': 'Buddhism',
        'Vietnamese': 'Buddhism',
        'Singaporean': 'Buddhism',
    };

    if (newNationality && nationalityReligionMap[newNationality]) {
        form.religion = nationalityReligionMap[newNationality];
    }
});

// Auto-populate gender based on title
watch(() => form.title, (newTitle) => {
    if (newTitle === 'Mr.') {
        form.gender = 'Male';
    } else if (newTitle === 'Mrs.' || newTitle === 'Ms.') {
        form.gender = 'Female';
    }
});const copyAddressToEmergency = () => {
    form.emergency_contact_address = form.address;
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

            <div class="w-full">
                <form
                    @submit.prevent="form.post('/patients')"
                    class="space-y-6"
                >
                    <!-- Two Column Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column: Personal Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium">Personal Information</h3>
                            <div class="rounded-md border bg-blue-50 p-6 space-y-4">
                                <div>
                                    <label class="text-sm font-medium">Title</label>
                                    <Select v-model="form.title">
                                        <SelectTrigger class="mt-1.5">
                                            <SelectValue placeholder="Select title" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Mr.">Mr.</SelectItem>
                                            <SelectItem value="Mrs.">Mrs.</SelectItem>
                                            <SelectItem value="Ms.">Ms.</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Name <span class="text-destructive">*</span></label>
                                    <Input v-model="form.name" placeholder="Name" class="mt-1.5" />
                                    <div v-if="form.errors.name" class="text-sm text-destructive mt-1">{{ form.errors.name }}</div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Surname <span class="text-destructive">*</span></label>
                                    <Input v-model="form.surname" placeholder="Surname" class="mt-1.5" />
                                    <div v-if="form.errors.surname" class="text-sm text-destructive mt-1">{{ form.errors.surname }}</div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Khmer/China Name</label>
                                    <Input v-model="form.khmer_china_name" placeholder="Khmer/China Name" class="mt-1.5" />
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Khmer/China Surname</label>
                                    <Input v-model="form.khmer_china_surname" placeholder="Khmer/China Surname" class="mt-1.5" />
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Date of Birth <span class="text-destructive">*</span></label>
                                    <div class="mt-1.5">
                                        <DateOfBirthInput
                                            v-model:day="form.date_of_birth_day"
                                            v-model:month="form.date_of_birth_month"
                                            v-model:year="form.date_of_birth_year"
                                            :errors="{
                                                day: form.errors.date_of_birth_day,
                                                month: form.errors.date_of_birth_month,
                                                year: form.errors.date_of_birth_year,
                                            }"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Gender</label>
                                    <Select v-model="form.gender">
                                        <SelectTrigger class="mt-1.5">
                                            <SelectValue placeholder="Select gender" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Male">Male</SelectItem>
                                            <SelectItem value="Female">Female</SelectItem>
                                            <SelectItem value="Other">Other</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">ID Card/Passport</label>
                                    <Input v-model="form.id_card_or_passport" placeholder="ID Card/Passport" class="mt-1.5" />
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Marital Status</label>
                                    <Select v-model="form.marital_status">
                                        <SelectTrigger class="mt-1.5">
                                            <SelectValue placeholder="Select marital status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Single">Single</SelectItem>
                                            <SelectItem value="Married">Married</SelectItem>
                                            <SelectItem value="Divorced">Divorced</SelectItem>
                                            <SelectItem value="Widowed">Widowed</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Nationality <span class="text-destructive">*</span></label>
                                    <Select v-model="form.nationality">
                                        <SelectTrigger class="mt-1.5">
                                            <SelectValue placeholder="Select nationality" />
                                        </SelectTrigger>
                                        <SelectContent>
                                                    <SelectItem value="Cambodian">Cambodian</SelectItem>
                                                    <SelectItem value="Thai">Thai</SelectItem>
                                                    <SelectItem value="Vietnamese">Vietnamese</SelectItem>
                                                    <SelectItem value="Laotian">Laotian</SelectItem>
                                                    <SelectItem value="Myanmar">Myanmar</SelectItem>
                                                    <SelectItem value="Chinese">Chinese</SelectItem>
                                                    <SelectItem value="Japanese">Japanese</SelectItem>
                                                    <SelectItem value="South Korean">South Korean</SelectItem>
                                                    <SelectItem value="Filipino">Filipino</SelectItem>
                                                    <SelectItem value="Indonesian">Indonesian</SelectItem>
                                                    <SelectItem value="Malaysian">Malaysian</SelectItem>
                                                    <SelectItem value="Singaporean">Singaporean</SelectItem>
                                                    <SelectItem value="Indian">Indian</SelectItem>
                                                    <SelectItem value="Pakistani">Pakistani</SelectItem>
                                                    <SelectItem value="Bangladeshi">Bangladeshi</SelectItem>
                                                    <SelectItem value="Nepali">Nepali</SelectItem>
                                                    <SelectItem value="Sri Lankan">Sri Lankan</SelectItem>
                                                    <SelectItem value="American">American</SelectItem>
                                                    <SelectItem value="Canadian">Canadian</SelectItem>
                                                    <SelectItem value="Mexican">Mexican</SelectItem>
                                                    <SelectItem value="Brazilian">Brazilian</SelectItem>
                                                    <SelectItem value="British">British</SelectItem>
                                                    <SelectItem value="French">French</SelectItem>
                                                    <SelectItem value="German">German</SelectItem>
                                                    <SelectItem value="Italian">Italian</SelectItem>
                                                    <SelectItem value="Spanish">Spanish</SelectItem>
                                                    <SelectItem value="Portuguese">Portuguese</SelectItem>
                                                    <SelectItem value="Dutch">Dutch</SelectItem>
                                                    <SelectItem value="Belgian">Belgian</SelectItem>
                                                    <SelectItem value="Swiss">Swiss</SelectItem>
                                                    <SelectItem value="Austrian">Austrian</SelectItem>
                                                    <SelectItem value="Swedish">Swedish</SelectItem>
                                                    <SelectItem value="Norwegian">Norwegian</SelectItem>
                                                    <SelectItem value="Danish">Danish</SelectItem>
                                                    <SelectItem value="Finnish">Finnish</SelectItem>
                                                    <SelectItem value="Polish">Polish</SelectItem>
                                                    <SelectItem value="Russian">Russian</SelectItem>
                                                    <SelectItem value="Ukrainian">Ukrainian</SelectItem>
                                                    <SelectItem value="Turkish">Turkish</SelectItem>
                                                    <SelectItem value="Greek">Greek</SelectItem>
                                                    <SelectItem value="Australian">Australian</SelectItem>
                                                    <SelectItem value="New Zealander">New Zealander</SelectItem>
                                                    <SelectItem value="Saudi Arabian">Saudi Arabian</SelectItem>
                                                    <SelectItem value="Emirati">Emirati</SelectItem>
                                                    <SelectItem value="Qatari">Qatari</SelectItem>
                                                    <SelectItem value="Kuwaiti">Kuwaiti</SelectItem>
                                                    <SelectItem value="Egyptian">Egyptian</SelectItem>
                                                    <SelectItem value="Moroccan">Moroccan</SelectItem>
                                                    <SelectItem value="Iranian">Iranian</SelectItem>
                                                    <SelectItem value="Iraqi">Iraqi</SelectItem>
                                                    <SelectItem value="Jordanian">Jordanian</SelectItem>
                                                    <SelectItem value="Lebanese">Lebanese</SelectItem>
                                                    <SelectItem value="Syrian">Syrian</SelectItem>
                                                    <SelectItem value="Israeli">Israeli</SelectItem>
                                                    <SelectItem value="South African">South African</SelectItem>
                                                    <SelectItem value="Nigerian">Nigerian</SelectItem>
                                                    <SelectItem value="Kenyan">Kenyan</SelectItem>
                                            <SelectItem value="Other">Other</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <div v-if="form.errors.nationality" class="text-sm text-destructive mt-1">{{ form.errors.nationality }}</div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Religion</label>
                                    <Select v-model="form.religion">
                                        <SelectTrigger class="mt-1.5">
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
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Race</label>
                                    <Select v-model="form.race">
                                        <SelectTrigger class="mt-1.5">
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
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Address and Contact Information -->
                        <div class="space-y-4">
                            <!-- Address -->
                            <div>
                                <h3 class="text-lg font-medium">Address</h3>
                                <div class="rounded-md border bg-green-50 p-6 space-y-4 mt-2">
                                    <div>
                                        <label class="text-sm font-medium">Address</label>
                                        <Input v-model="form.address" placeholder="Address" class="mt-1.5" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Province / State / Region</label>
                                        <Select v-model="form.province">
                                            <SelectTrigger class="mt-1.5">
                                                <SelectValue placeholder="Select province or enter custom" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="custom">Other (Type below)</SelectItem>
                                                <SelectItem v-for="province in cambodiaProvinces" :key="province" :value="province">
                                                    {{ province }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <Input
                                            v-if="form.province === 'custom'"
                                            v-model="form.province"
                                            placeholder="Enter province/state/region"
                                            class="mt-2"
                                        />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Building/Village</label>
                                        <Input v-model="form.building_village" placeholder="Building/Village" class="mt-1.5" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Sub-district</label>
                                        <Input v-model="form.sub_district" placeholder="Sub-district" class="mt-1.5" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">District</label>
                                        <Input v-model="form.district" placeholder="District" class="mt-1.5" />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Zip Code</label>
                                        <Input v-model="form.zip_code" placeholder="Zip Code" class="mt-1.5" />
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <h3 class="text-lg font-medium">Contact Information</h3>
                                <div class="rounded-md border bg-purple-50 p-6 space-y-4 mt-2">
                                    <div>
                                        <label class="text-sm font-medium">Home Phone</label>
                                        <Input v-model="form.home_phone" placeholder="Home Phone" class="mt-1.5" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Mobile Phone</label>
                                        <Input v-model="form.mobile_phone" placeholder="Mobile Phone" class="mt-1.5" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Email</label>
                                        <Input v-model="form.email" type="email" placeholder="Email Address" class="mt-1.5" />
                                        <div v-if="form.errors.email" class="text-sm text-destructive mt-1">{{ form.errors.email }}</div>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Occupation</label>
                                        <Input v-model="form.occupation" placeholder="Occupation" class="mt-1.5" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Company Name</label>
                                        <Input v-model="form.company_name" placeholder="Company Name" class="mt-1.5" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Company Phone</label>
                                        <Input v-model="form.company_phone" placeholder="Company Phone" class="mt-1.5" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Referring Doctor</label>
                                        <Select v-model="form.staff_id">
                                            <SelectTrigger class="mt-1.5">
                                                <SelectValue placeholder="Select doctor" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="doctor in props.doctors" :key="doctor.id" :value="doctor.id.toString()">
                                                    {{ doctor.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                            </div>
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
                                        <td class="p-4 font-medium">Contact Address</td>
                                        <td class="p-4">
                                            <Input v-model="form.emergency_contact_address" placeholder="Contact Address" />
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
