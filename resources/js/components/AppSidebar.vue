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
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    Building,
    Calendar,
    ChevronRight,
    ClipboardCheckIcon,
    ClipboardList,
    DollarSign,
    FileText,
    Heart,
    LayoutGrid,
    Pill,
    ScrollText,
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
    cultureMedium,
    index as inventoryIndex,
    labInventory,
    plasticWare,
    rxMedicine,
} from '@/routes/inventory';
import { index as labPanelIndex } from '@/routes/lab-panels';
import { index as medicalOrdersIndex } from '@/routes/medical-orders';
import { index as medicalRecordsIndex } from '@/routes/medical-records';
import { index as medicalServicesIndex } from '@/routes/medical-services';
import { index as patientFilesIndex } from '@/routes/patient-files';
import { index as patientsIndex } from '@/routes/patients';
import { index as medicineGroupsIndex } from '@/routes/medicine-groups';
import { index as rolesIndex } from '@/routes/settings/roles';
import { index as staffIndex } from '@/routes/staff';
import { index as staffFilesIndex } from '@/routes/staff-files';
import { index as visitsIndex } from '@/routes/visits';
import { index as medicineReportIndex } from '@/routes/medicine-report';
import { index as billingReportIndex } from '@/routes/billing-report';
import { index as activityLogIndex } from '@/routes/activity-log';

import { useAuth } from '@/composables/useAuth';
import { router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref } from 'vue';

import doctor from '@/routes/doctors';

// For now, we'll show all menu items. In a real app, you'd check user roles/permissions
const page = usePage();

const { hasPermission, hasAnyPermission, isAdmin } = useAuth();

const userRole = computed(() => page.props.auth.user?.roles?.[0]);

const coreNavItems = computed(() => [
    { title: 'Dashboard', href: dashboard().url, icon: LayoutGrid },
    {
        title: 'Patients',
        href: patientsIndex().url,
        icon: Heart,
        permissions: 'view_patients',
    },
    {
        title: 'Appointments',
        href: index().url,
        icon: Calendar,
        permissions: 'view_appointments',
    },
    {
        title: 'Visits',
        href: visitsIndex().url,
        icon: Calendar,
        permissions: 'view_visits',
    },
    {
        title: 'My Visits',
        href: doctor.myVisits().url,
        icon: Calendar,
        permissions: 'view_visits',
    },
    {
        title: 'My Visits to Process',
        href: doctor.myToBeProcessVisits().url,
        icon: Users2Icon,
        permissions: 'process_medical_orders',
    },
]);

// NOTE: Per-item visibility is now handled using `permissions` on each nav item
// and the `isAllowed` filter further below — no separate core filter is needed here.

const patientCareNavItems = computed(() => [
    {
        title: 'Medical Services',
        href: medicalServicesIndex().url,
        icon: ClipboardList,
        permissions: 'view_medical_services',
    },
    {
        title: 'Medical Records',
        href: medicalRecordsIndex().url,
        icon: Users2Icon,
        permissions: 'view_medical_records',
    },
    {
        title: 'Medical Orders',
        href: medicalOrdersIndex().url,
        icon: ClipboardList,
        permissions: 'view_medical_orders',
    },
]);

const documentNavItems = computed(() => [
    {
        title: 'Patient Files',
        href: patientFilesIndex().url,
        icon: ClipboardList,
        permissions: 'view_files',
    },
    {
        title: 'Staff Files',
        href: staffFilesIndex().url,
        icon: ClipboardList,
        permissions: 'view_files',
    },
]);

const medicalResourcesNavItems = computed(() => [
    {
        title: 'Lab Panels',
        href: labPanelIndex().url,
        icon: Heart,
        permissions: 'view_lab_packages',
    },
    {
        title: 'Special Items',
        href: medicineGroupsIndex().url,
        icon: Pill,
        permissions: 'view_medications',
    },
    {
        title: 'Packages',
        href: '/settings/patches',
        icon: LayoutGrid,
        permissions: 'view_lab_packages',
    },
    {
        title: 'All Inventory',
        href: inventoryIndex().url,
        icon: Warehouse,
        permissions: 'view_inventory',
    },
    {
        title: 'RX Medicine',
        href: rxMedicine().url,
        icon: Pill,
        permissions: 'view_medications',
    },
]);

const labInventoryNavItems = computed(() => [
    {
        title: 'All Lab Items',
        href: labInventory().url,
        permissions: 'view_inventory',
    },
    {
        title: 'Plastic Ware',
        href: plasticWare().url,
        permissions: 'view_inventory',
    },
    {
        title: 'Culture Medium',
        href: cultureMedium().url,
        permissions: 'view_inventory',
    },
]);

const financialNavItems = computed(() => [
    {
        title: 'Billings',
        href: billingsIndex().url,
        icon: DollarSign,
        permissions: 'view_billing',
    },
]);

const reportsNavItems = computed(() => [
    {
        title: 'Medicine Report',
        href: medicineReportIndex().url,
        icon: FileText,
        permissions: 'view_medications',
    },
    {
        title: 'Billing Report',
        href: billingReportIndex().url,
        icon: DollarSign,
        permissions: 'view_billings',
    },
]);

const doctorNavItems = computed(() => [
    {
        title: 'My Appointments',
        href: doctor.myAppointments().url,
        icon: ClipboardCheckIcon,
        permissions: 'view_appointments',
    },
    {
        title: 'My Patients',
        href: doctor.myPatients().url,
        icon: Users2Icon,
        permissions: 'view_patients',
    },
]);

const managementNavItems = computed(() => [
    {
        title: 'Departments',
        href: departmentsIndex().url,
        icon: Building,
        permissions: 'view_departments',
    },
    {
        title: 'Staff',
        href: staffIndex().url,
        icon: UserCheck,
        permissions: 'view_staff',
    },
    {
        title: 'Roles',
        href: rolesIndex().url,
        icon: Shield,
        permissions: 'view_roles',
    },
    {
        title: 'Activity Log',
        href: activityLogIndex().url,
        icon: ScrollText,
        permissions: 'view_activity_logs',
    },
]);

// Generic helper - tests the item's permissions and allows admins to see everything
const isAllowed = (required?: string | string[]) => {
    if (!required) return true;
    if (Array.isArray(required))
        return hasAnyPermission(required) || isAdmin.value;
    return hasPermission(required) || isAdmin.value;
};

const sidebarContentRef = ref<any>();

onMounted(() => {
    router.on('success', () => {
        nextTick(() => {
            if (sidebarContentRef.value && sidebarContentRef.value.$el) {
                const activeItem = sidebarContentRef.value.$el.querySelector(
                    '[data-active="true"]',
                ) as HTMLElement;
                if (activeItem) {
                    activeItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                    });
                }
            }
        });
    });
});

const filteredCoreNavItems = computed(() =>
    coreNavItems.value.filter((item) => isAllowed(item.permissions)),
);
const filteredPatientCareNavItems = computed(() =>
    patientCareNavItems.value.filter((item) => isAllowed(item.permissions)),
);
const filteredDocumentNavItems = computed(() =>
    documentNavItems.value.filter((item) => isAllowed(item.permissions)),
);
const filteredMedicalResourcesNavItems = computed(() =>
    medicalResourcesNavItems.value.filter((item) =>
        isAllowed(item.permissions),
    ),
);
const filteredFinancialNavItems = computed(() =>
    financialNavItems.value.filter((item) => isAllowed(item.permissions)),
);
const filteredReportsNavItems = computed(() =>
    reportsNavItems.value.filter((item) => isAllowed(item.permissions)),
);
const filteredDoctorNavItems = computed(() =>
    doctorNavItems.value.filter((item) => isAllowed(item.permissions)),
);
const filteredManagementNavItems = computed(() =>
    managementNavItems.value.filter((item) => isAllowed(item.permissions)),
);

// Filtered helpers — simplify v-if checks in the template
// group visibility computed values are now derived from filtered arrays
const showDocuments = computed(() => filteredDocumentNavItems.value.length > 0);
const showPatientCare = computed(
    () => filteredPatientCareNavItems.value.length > 0,
);
const showMedicalResources = computed(
    () => filteredMedicalResourcesNavItems.value.length > 0,
);
const showFinancial = computed(
    () => filteredFinancialNavItems.value.length > 0,
);
const showReports = computed(
    () => filteredReportsNavItems.value.length > 0,
);

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

        <SidebarContent ref="sidebarContentRef"
            class="scrollbar-thin scrollbar-thumb-slate-400 scrollbar-track-transparent scrollbar-thumb-rounded hover:scrollbar-thumb-slate-500 dark:scrollbar-thumb-slate-600 dark:hover:scrollbar-thumb-slate-500">
            <!-- Core Operations -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Core Operations</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredCoreNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href" :preserve-scroll="true">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Doctors Section -->
            <SidebarGroup class="px-2 py-0" v-if="userRole === 'doctor'">
                <SidebarGroupLabel>Doctors</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredDoctorNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href" :preserve-scroll="true">
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
                            <Link :href="item.href" :preserve-scroll="true">
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
                            <Link :href="item.href" :preserve-scroll="true">
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
                            <Link :href="item.href" :preserve-scroll="true">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>

                    <!-- Lab Inventory Submenu -->
                    <Collapsible as-child default-open class="group/collapsible">
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton :tooltip="'Lab Inventory'">
                                    <Warehouse />
                                    <span>Lab Inventory</span>
                                    <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem v-for="subItem in labInventoryNavItems" :key="subItem.title">
                                        <SidebarMenuSubButton as-child :is-active="urlIsActive(subItem.href, page.url)">
                                            <Link :href="subItem.href" :preserve-scroll="true">
                                                <span>{{ subItem.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Financial -->
            <SidebarGroup class="px-2 py-0" v-if="showFinancial">
                <SidebarGroupLabel>Financial</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredFinancialNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href" :preserve-scroll="true">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Reports -->
            <SidebarGroup class="px-2 py-0" v-if="showReports">
                <SidebarGroupLabel>Reports</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredReportsNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href" :preserve-scroll="true">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Management Section -->
            <SidebarGroup class="px-2 py-0" v-if="userRole === 'admin'">
                <SidebarGroupLabel>Management</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in filteredManagementNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                            <Link :href="item.href" :preserve-scroll="true">
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
