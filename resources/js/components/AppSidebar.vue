<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavUser from '@/components/NavUser.vue';
import AppLogo from '@/components/AppLogo.vue';
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
import { Link, usePage } from '@inertiajs/vue3';
import { type NavItem } from '@/types';
import { urlIsActive } from '@/lib/utils';
import {
    LayoutGrid,
    Calendar,
    Heart,
    Building,
    Pill,
    ClipboardList,
    Warehouse,
    DollarSign,
    UserCheck,
    Shield,
    Users2Icon,
    ClipboardCheckIcon
} from 'lucide-vue-next';

import { dashboard } from '@/routes';
import { index } from '@/routes/appointments';
import { index as patientsIndex } from '@/routes/patients';
import { index as departmentsIndex } from '@/routes/departments';
import { index as medicalRecordsIndex } from '@/routes/medical-records';
import { index as medicalOrdersIndex } from '@/routes/medical-orders';
// import { index as prescriptionsIndex } from '@/routes/prescriptions';
import { index as visitsIndex } from '@/routes/visits';
import { index as inventoryIndex } from '@/routes/inventory';
import { rxMedicine } from '@/routes/inventory';
import { labInventory } from '@/routes/inventory';
import { index as billingsIndex } from '@/routes/billings';
import { index as staffIndex } from '@/routes/staff';
import { index as rolesIndex } from '@/routes/settings/roles';
import { index as labPanelIndex } from '@/routes/lab-panels';
import { computed } from 'vue';

import doctor from "@/routes/doctors";

// For now, we'll show all menu items. In a real app, you'd check user roles/permissions
const page = usePage();

const coreNavItems = computed(() => [
    { title: 'Dashboard', href: dashboard().url, icon: LayoutGrid, },
    { title: 'Appointments', href: index().url, icon: Calendar, },
    { title: 'Lab Panels', href: labPanelIndex().url, icon: Heart, }, {
        title: 'Visits', href: visitsIndex().url, icon: Calendar,
    },
]);

const patientCareNavItems = computed(() => [
    { title: 'Patients', href: patientsIndex().url, icon: Heart, },
    { title: 'Medical Records', href: medicalRecordsIndex().url, icon: Users2Icon, },
    { title: 'Medical Orders', href: medicalOrdersIndex().url, icon: ClipboardList, },
]);

const medicalResourcesNavItems = computed(() => [
    { title: 'All Inventory', href: inventoryIndex().url, icon: Warehouse, },
    { title: 'RX Medicine', href: rxMedicine().url, icon: Pill, },
    { title: 'Lab Inventory', href: labInventory().url, icon: Warehouse, }
]);

const financialNavItems = computed(() => [
    { title: 'Billings', href: billingsIndex().url, icon: DollarSign, },
]);

const doctorNavItems = computed(() => [
    { title: 'My Appointments', href: doctor.myAppointments().url, icon: ClipboardCheckIcon, },
    { title: 'My Patients', href: doctor.myPatients().url, icon: Users2Icon, },
    { title: 'My Visits', href: doctor.myVisits().url, icon: Calendar, },
]);

const managementNavItems = computed(() => [
    { title: 'Departments', href: departmentsIndex().url, icon: Building, },
    { title: 'Staff', href: staffIndex().url, icon: UserCheck, },
    { title: 'Roles', href: rolesIndex().url, icon: Shield, },
]);

const footerNavItems: NavItem[] = [

];
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
                    <SidebarMenuItem v-for="item in coreNavItems" :key="item.title">
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
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Patient Care</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in patientCareNavItems" :key="item.title">
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
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Medical Resources</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in medicalResourcesNavItems" :key="item.title">
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
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Financial</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in financialNavItems" :key="item.title">
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
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Doctors</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in doctorNavItems" :key="item.title">
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
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Management</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in managementNavItems" :key="item.title">
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
