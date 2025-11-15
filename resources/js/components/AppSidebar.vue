<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Building,
    Calendar,
    ClipboardCheckIcon,
    ClipboardList,
    DollarSign,
    Heart,
    LayoutGrid,
    Pill,
    Shield,
    UserCheck,
    Users2Icon,
    Warehouse,
} from 'lucide-vue-next';

import { dashboard } from '@/routes';
import { index } from '@/routes/appointments';
import { index as billingsIndex } from '@/routes/billings';
import { index as departmentsIndex } from '@/routes/departments';
import {
    index as inventoryIndex,
    labInventory,
    rxMedicine,
} from '@/routes/inventory';
import { index as labPanelIndex } from '@/routes/lab-panels';
import { index as medicalOrdersIndex } from '@/routes/medical-orders';
import { index as medicalRecordsIndex } from '@/routes/medical-records';
import { index as patientFilesIndex } from '@/routes/patient-files';
import { index as patientsIndex } from '@/routes/patients';
import { index as rolesIndex } from '@/routes/settings/roles';
import { index as staffIndex } from '@/routes/staff';
import { index as staffFilesIndex } from '@/routes/staff-files';
import { index as visitsIndex } from '@/routes/visits';

import { computed } from 'vue';
import { useAuth } from '@/composables/useAuth';

import doctor from '@/routes/doctors';

// For now, we'll show all menu items. In a real app, you'd check user roles/permissions
const page = usePage();

const { hasPermission, hasAnyPermission, isAdmin } = useAuth();

const coreNavItems = computed(() => [
    { title: 'Dashboard', href: dashboard().url, icon: LayoutGrid },
    { title: 'Appointments', href: index().url, icon: Calendar, permissions: 'view_appointments' },
    { title: 'Lab Panels', href: labPanelIndex().url, icon: Heart, permissions: 'view_lab_packages' },
    { title: 'Visits', href: visitsIndex().url, icon: Calendar, permissions: 'view_visits' },
]);

// NOTE: Per-item visibility is now handled using `permissions` on each nav item
// and the `isAllowed` filter further below — no separate core filter is needed here.

const patientCareNavItems = computed(() => [
    { title: 'Patients', href: patientsIndex().url, icon: Heart, permissions: 'view_patients' },
    { title: 'Medical Records', href: medicalRecordsIndex().url, icon: Users2Icon, permissions: 'view_medical_records' },
    { title: 'Medical Orders', href: medicalOrdersIndex().url, icon: ClipboardList, permissions: 'view_medical_orders' },
]);

const documentNavItems = computed(() => [
    {
        title: 'Patient Files',
        href: patientFilesIndex().url,
        icon: ClipboardList,
        permissions: 'view_files',
    },
    { title: 'Staff Files', href: staffFilesIndex().url, icon: ClipboardList, permissions: 'view_files' },
]);

const medicalResourcesNavItems = computed(() => [
    { title: 'All Inventory', href: inventoryIndex().url, icon: Warehouse, permissions: 'view_inventory' },
    { title: 'RX Medicine', href: rxMedicine().url, icon: Pill, permissions: 'view_medications' },
    { title: 'Lab Inventory', href: labInventory().url, icon: Warehouse, permissions: 'view_inventory' },
]);

const financialNavItems = computed(() => [
    { title: 'Billings', href: billingsIndex().url, icon: DollarSign, permissions: 'view_billing' },
]);

const doctorNavItems = computed(() => [
    {
        title: 'My Appointments',
        href: doctor.myAppointments().url,
        icon: ClipboardCheckIcon,
        permissions: 'view_appointments',
    },
    { title: 'My Patients', href: doctor.myPatients().url, icon: Users2Icon, permissions: 'view_patients' },
    { title: 'My Visits', href: doctor.myVisits().url, icon: Calendar, permissions: 'view_visits' },
]);

const managementNavItems = computed(() => [
    { title: 'Departments', href: departmentsIndex().url, icon: Building, permissions: 'view_departments' },
    { title: 'Staff', href: staffIndex().url, icon: UserCheck, permissions: 'view_staff' },
    { title: 'Roles', href: rolesIndex().url, icon: Shield, permissions: 'view_roles' },
]);

// Generic helper - tests the item's permissions and allows admins to see everything
const isAllowed = (required?: string | string[]) => {
    if (!required) return true;
    if (Array.isArray(required)) return hasAnyPermission(required) || isAdmin.value;
    return hasPermission(required) || isAdmin.value;
};

const filteredCoreNavItems = computed(() => coreNavItems.value.filter((item) => isAllowed(item.permissions)));
const filteredPatientCareNavItems = computed(() => patientCareNavItems.value.filter((item) => isAllowed(item.permissions)));
const filteredDocumentNavItems = computed(() => documentNavItems.value.filter((item) => isAllowed(item.permissions)));
const filteredMedicalResourcesNavItems = computed(() => medicalResourcesNavItems.value.filter((item) => isAllowed(item.permissions)));
const filteredFinancialNavItems = computed(() => financialNavItems.value.filter((item) => isAllowed(item.permissions)));
const filteredDoctorNavItems = computed(() => doctorNavItems.value.filter((item) => isAllowed(item.permissions)));
const filteredManagementNavItems = computed(() => managementNavItems.value.filter((item) => isAllowed(item.permissions)));

// Filtered helpers — simplify v-if checks in the template
// group visibility computed values are now derived from filtered arrays
const showDocuments = computed(() => filteredDocumentNavItems.value.length > 0);
const showPatientCare = computed(() => filteredPatientCareNavItems.value.length > 0);
const showMedicalResources = computed(() => filteredMedicalResourcesNavItems.value.length > 0);
const showFinancial = computed(() => filteredFinancialNavItems.value.length > 0);
const showDoctorsSection = computed(() => filteredDoctorNavItems.value.length > 0);
const showManagement = computed(() => filteredManagementNavItems.value.length > 0);

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Core Operations -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Core Operations</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredCoreNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Documents -->
            <SidebarGroup class="px-2 py-0" v-if="showDocuments">
                <SidebarGroupLabel>Documents</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredDocumentNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Patient Care -->
            <SidebarGroup class="px-2 py-0" v-if="showPatientCare">
                <SidebarGroupLabel>Patient Care</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredPatientCareNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Medical Resources -->
            <SidebarGroup class="px-2 py-0" v-if="showMedicalResources">
                <SidebarGroupLabel>Medical Resources</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredMedicalResourcesNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Financial -->
            <SidebarGroup class="px-2 py-0" v-if="showFinancial">
                <SidebarGroupLabel>Financial</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredFinancialNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Doctors Section -->
            <SidebarGroup class="px-2 py-0" v-if="showDoctorsSection">
                <SidebarGroupLabel>Doctors</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredDoctorNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Management Section -->
            <SidebarGroup class="px-2 py-0" v-if="showManagement">
                <SidebarGroupLabel>Management</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredManagementNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
