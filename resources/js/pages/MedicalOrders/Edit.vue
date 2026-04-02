<script setup lang="ts">
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { FormControl, FormField, FormItem } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, update } from '@/routes/medical-orders';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import {
    Activity,
    ArrowLeft,
    ChevronDown,
    ChevronRight,
    FlaskConical,
    Package,
    Pill,
    Plus,
    Scan,
    Search,
    Syringe,
    Trash2,
    UserCheck,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface LabPanelItem {
    id: number;
    item_name: string;
    quantity_required: number;
    notes: string | null;
}

interface LabPanel {
    id: number;
    name: string;
    description: string;
    price: number;
    items: LabPanelItem[];
}

interface RXMedicine {
    id: number;
    item_name: string;
    description: string | null;
    dose_unit: string | null;
    quantity: number;
    category: string | null;
    unit_price: number;
    selling_price: number;
}

interface OrderItem {
    id?: number;
    item_type: string;
    item_name: string;
    details?: string;
    dosage?: string;
    frequency?: string;
    route?: string;
    quantity_required: number;
    status?: string;
    notes?: string;
    inventory_id?: number;
    unit_price?: number;
    selling_price?: number;
}

interface Props {
    medicalOrder: {
        id: number;
        patient_id: number | null;
        staff_id: number | null;
        order_details: string;
        status: string;
        priority: string;
        notes?: string;
        ordered_at: string;
        completed_at?: string;
        order_items: OrderItem[];
    };
    patients: Array<{
        id: number;
        name: string;
    }>;
    staff: Array<{
        id: number;
        name: string;
    }>;
    labPanels: LabPanel[];
    rxMedicines: RXMedicine[];
    inventoryItems: Array<{
        id: number;
        item_name: string;
        type_of_supply: string;
        type_label: string;
        unit: string;
        quantity: number;
        unit_price: number;
        selling_price: number;
    }>;
    medicalServices: Array<{
        id: number;
        name: string;
        description: string | null;
        type: string;
        price: number;
    }>;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Orders',
        href: index().url,
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const form = useForm<{
    patient_id: number | null;
    staff_id: number | null;
    order_details: string;
    priority: string;
    status: string;
    notes: string;
    ordered_at: string;
    completed_at: string;
    order_items: OrderItem[];
}>({
    patient_id: props.medicalOrder.patient_id,
    staff_id: props.medicalOrder.staff_id,
    order_details: props.medicalOrder.order_details,
    priority: props.medicalOrder.priority,
    status: props.medicalOrder.status,
    notes: props.medicalOrder.notes || '',
    ordered_at: props.medicalOrder.ordered_at,
    completed_at: props.medicalOrder.completed_at || '',
    order_items: props.medicalOrder.order_items.map((item) => ({ ...item })),
});

const patientValue = computed({
    get: () => form.patient_id?.toString() || 'null',
    set: (value) => {
        form.patient_id = value === 'null' ? null : Number(value);
    },
});

const staffValue = computed({
    get: () => form.staff_id?.toString() || 'null',
    set: (value) => {
        form.staff_id = value === 'null' ? null : Number(value);
    },
});

const patientOptions = computed(() => {
    const base = [{ value: 'null', label: 'None' }];
    if (!props.patients) return base;
    return [
        ...base,
        ...props.patients.map((p) => ({
            value: p.id.toString(),
            label: p.name || 'Unknown Patient',
        })),
    ];
});

const staffOptions = computed(() => {
    const base = [{ value: 'null', label: 'None' }];
    if (!props.staff) return base;
    return [
        ...base,
        ...props.staff.map((s) => ({ value: s.id.toString(), label: s.name || 'Unknown Staff' })),
    ];
});

const submitForm = () => {
    console.log('Submitting form data:', form.data());
    form.put(update(props.medicalOrder.id).url);
};

const priorities = [
    { value: 'routine', label: 'Routine' },
    { value: 'urgent', label: 'Urgent' },
    { value: 'stat', label: 'STAT' },
];

const statuses = [
    { value: 'pending', label: 'Pending' },
    { value: 'processing', label: 'Processing' },
    { value: 'processed', label: 'Processed' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancel', label: 'Cancel' },
    { value: 'rejected', label: 'Rejected' },
];

// Lab selection state
const showLabDialog = ref(false);
const selectedLabItems = ref<number[]>([]);
const labItemQuantities = ref<Record<number, number>>({});
const labItemIncludePackage = ref<Record<number, boolean>>({});
const activeLabPanel = ref<string>(props.labPanels[0]?.id.toString() || '');
const labSearchQuery = ref('');

const toggleLabItem = (itemId: number, checked: boolean) => {
    if (checked) {
        if (!selectedLabItems.value.includes(itemId)) {
            selectedLabItems.value.push(itemId);
            // Initialize quantity to 1 when selecting
            if (!labItemQuantities.value[itemId]) {
                labItemQuantities.value[itemId] = 1;
            }
            // Initialize include package to false
            if (labItemIncludePackage.value[itemId] === undefined) {
                labItemIncludePackage.value[itemId] = false;
            }
        }
    } else {
        selectedLabItems.value = selectedLabItems.value.filter(
            (id) => id !== itemId,
        );
        // Clean up quantity and package toggle when deselecting
        delete labItemQuantities.value[itemId];
        delete labItemIncludePackage.value[itemId];
    }
};

const addSelectedLabItems = () => {
    const allPanelItems = props.labPanels.flatMap((panel) => panel.items);

    selectedLabItems.value.forEach((itemId) => {
        const panelItem = allPanelItems.find((item) => item.id === itemId);
        if (panelItem) {
            // Find the inventory item to get its price
            const inventoryItem = props.inventoryItems.find(
                (inv) => inv.id === panelItem.id,
            );

            const includePackage = labItemIncludePackage.value[itemId] || false;
            const qty = labItemQuantities.value[itemId] || 1;

            form.order_items.push({
                item_type: 'lab',
                item_name: panelItem.item_name,
                details: `${panelItem.notes || ''}${includePackage ? ' (Package Included)' : ''}`,
                quantity_required: qty,
                status: 'pending',
                notes: includePackage ? 'Include Package - Not counted in billing' : '',
                inventory_id: panelItem.id,
                unit_price: includePackage ? 0 : (inventoryItem?.unit_price || 0),
                selling_price: includePackage ? 0 : (inventoryItem?.selling_price || 0),
            });
        }
    });

    selectedLabItems.value = [];
    labItemQuantities.value = {};
    labItemIncludePackage.value = {};
    showLabDialog.value = false;
};

const selectedCount = computed(() => selectedLabItems.value.length);

// Filtered lab items
const filteredLabItems = computed(() => {
    const activePanel = props.labPanels.find(
        (p) => p.id.toString() === activeLabPanel.value,
    );
    if (!activePanel) return [];

    return activePanel.items.filter(
        (item) =>
            item.item_name
                .toLowerCase()
                .includes(labSearchQuery.value.toLowerCase()) ||
            (item.notes &&
                item.notes
                    .toLowerCase()
                    .includes(labSearchQuery.value.toLowerCase())),
    );
});

// RX Medicine selection state
const showRxDialog = ref(false);
const selectedRxItems = ref<number[]>([]);
const rxItemQuantities = ref<Record<number, number>>({});
const rxItemIncludePackage = ref<Record<number, boolean>>({});
const rxSearchQuery = ref('');

const rxCategories = computed(() => {
    const categories = new Set<string>();
    props.rxMedicines.forEach((med) => {
        if (med.category) categories.add(med.category);
    });
    return Array.from(categories).sort();
});

const activeRxCategory = ref<string>('All');

const toggleRxItem = (itemId: number, checked: boolean) => {
    if (checked) {
        if (!selectedRxItems.value.includes(itemId)) {
            selectedRxItems.value.push(itemId);
            // Initialize quantity to 1 when selecting
            if (!rxItemQuantities.value[itemId]) {
                rxItemQuantities.value[itemId] = 1;
            }
            // Initialize include package to false
            if (rxItemIncludePackage.value[itemId] === undefined) {
                rxItemIncludePackage.value[itemId] = false;
            }
        }
    } else {
        selectedRxItems.value = selectedRxItems.value.filter(
            (id) => id !== itemId,
        );
        // Clean up quantity and package toggle when deselecting
        delete rxItemQuantities.value[itemId];
        delete rxItemIncludePackage.value[itemId];
    }
};

const addSelectedRxItems = () => {
    selectedRxItems.value.forEach((itemId) => {
        const medicine = props.rxMedicines.find((item) => item.id === itemId);
        if (medicine) {
            const includePackage = rxItemIncludePackage.value[itemId] || false;
            const qty = rxItemQuantities.value[itemId] || 1;

            form.order_items.push({
                item_type: 'rx_medicine',
                item_name: medicine.item_name,
                details: `${medicine.description || ''}${includePackage ? ' (Package Included)' : ''}`,
                dosage: '',
                frequency: '',
                route: '',
                quantity_required: qty,
                status: 'pending',
                notes: includePackage ? 'Include Package - Not counted in billing' : '',
                inventory_id: medicine.id,
                unit_price: includePackage ? 0 : medicine.unit_price,
                selling_price: includePackage ? 0 : medicine.selling_price,
            });
        }
    });

    selectedRxItems.value = [];
    rxItemQuantities.value = {};
    rxItemIncludePackage.value = {};
    showRxDialog.value = false;
};

const selectedRxCount = computed(() => selectedRxItems.value.length);

// Filtered RX medicines
const filteredRxMedicines = computed(() => {
    const baseMedicines =
        activeRxCategory.value === 'All' || !activeRxCategory.value
            ? props.rxMedicines
            : props.rxMedicines.filter(
                  (med) => med.category === activeRxCategory.value,
              );

    return baseMedicines.filter(
        (medicine) =>
            medicine.item_name
                .toLowerCase()
                .includes(rxSearchQuery.value.toLowerCase()) ||
            (medicine.description &&
                medicine.description
                    .toLowerCase()
                    .includes(rxSearchQuery.value.toLowerCase())) ||
            (medicine.category &&
                medicine.category
                    .toLowerCase()
                    .includes(rxSearchQuery.value.toLowerCase())),
    );
});

// Medical Services selection state
const showMedicalServiceDialog = ref(false);
const selectedMedicalServiceItems = ref<number[]>([]);
const medicalServiceSearchQuery = ref('');
const activeMedicalServiceType = ref<string>('All');

const medicalServiceTypes = computed(() => {
    const types = new Set<string>();
    props.medicalServices.forEach((service) => {
        types.add(service.type);
    });
    return Array.from(types).sort();
});

const toggleMedicalServiceItem = (itemId: number, checked: boolean) => {
    if (checked) {
        if (!selectedMedicalServiceItems.value.includes(itemId)) {
            selectedMedicalServiceItems.value.push(itemId);
        }
    } else {
        selectedMedicalServiceItems.value =
            selectedMedicalServiceItems.value.filter((id) => id !== itemId);
    }
};

const addSelectedMedicalServiceItems = () => {
    selectedMedicalServiceItems.value.forEach((itemId) => {
        const service = props.medicalServices.find(
            (item) => item.id === itemId,
        );
        if (service) {
            form.order_items.push({
                item_type: service.type,
                item_name: service.name,
                details: service.description || '',
                quantity_required: 1,
                status: 'pending',
                notes: '',
                unit_price: service.price,
                selling_price: service.price,
            });
        }
    });

    selectedMedicalServiceItems.value = [];
    showMedicalServiceDialog.value = false;
};

const selectedMedicalServiceCount = computed(
    () => selectedMedicalServiceItems.value.length,
);

// Filtered medical services
const filteredMedicalServices = computed(() => {
    const baseServices =
        activeMedicalServiceType.value === 'All' ||
        !activeMedicalServiceType.value
            ? props.medicalServices
            : props.medicalServices.filter(
                  (service) => service.type === activeMedicalServiceType.value,
              );

    return baseServices.filter(
        (service) =>
            service.name
                .toLowerCase()
                .includes(medicalServiceSearchQuery.value.toLowerCase()) ||
            (service.description &&
                service.description
                    .toLowerCase()
                    .includes(medicalServiceSearchQuery.value.toLowerCase())),
    );
});

// Order summary
const orderSummary = computed(() => {
    const summary = {
        total: form.order_items.length,
        lab: 0,
        rx_medicine: 0,
        procedure: 0,
        imaging: 0,
        supply: 0,
    };

    form.order_items.forEach((item) => {
        if (summary[item.item_type as keyof typeof summary] !== undefined) {
            summary[item.item_type as keyof typeof summary]++;
        }
    });

    return summary;
});

// Order total price calculation
const orderTotalPrice = computed(() => {
    return form.order_items.reduce((total, item) => {
        let itemPrice = 0;

        if (item.selling_price) {
            // All items use selling price multiplied by quantity
            itemPrice = item.selling_price * item.quantity_required;
        }

        return total + itemPrice;
    }, 0);
});

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const getLabItemPrice = (inventoryId: number) => {
    const inventoryItem = props.inventoryItems.find(
        (item) => item.id === inventoryId,
    );
    return inventoryItem?.selling_price || 0;
};

const getItemPrice = (item: OrderItem) => {
    if (item.selling_price) {
        return item.selling_price * item.quantity_required;
    }
    return 0;
};

// Item expansion state - all collapsed by default
const expandedItems = ref<Set<number>>(new Set());

const addSupplyItem = () => {
    form.order_items.push({
        item_type: 'supply',
        item_name: '',
        inventory_id: undefined,
        quantity_required: 1,
        status: 'pending',
        notes: '',
    });
};

const removeOrderItem = (index: number) => {
    form.order_items.splice(index, 1);
};

const duplicateOrderItem = (index: number) => {
    const item = form.order_items[index];
    const duplicatedItem = { ...item };
    // Remove id if it exists (for existing items being duplicated)
    if ('id' in duplicatedItem) {
        delete (duplicatedItem as any).id;
    }
    form.order_items.splice(index + 1, 0, duplicatedItem);
};

const clearAllItems = () => {
    if (confirm('Are you sure you want to remove all order items?')) {
        form.order_items = [];
    }
};

const toggleItemExpansion = (index: number) => {
    if (expandedItems.value.has(index)) {
        expandedItems.value.delete(index);
    } else {
        expandedItems.value.add(index);
    }
};

const selectInventoryItem = (item: OrderItem, inventoryId: number) => {
    const inventory = props.inventoryItems.find((i) => i.id === inventoryId);
    if (inventory) {
        item.inventory_id = inventoryId;
        item.item_name = inventory.item_name;
        item.unit_price = inventory.unit_price;
        item.selling_price = inventory.selling_price;
    }
};

const getItemTypeIcon = (type: string) => {
    const icons: Record<string, any> = {
        lab: FlaskConical,
        rx_medicine: Pill,
        procedure: Syringe,
        imaging: Scan,
        consultation: UserCheck,
        therapy: Activity,
        supply: Package,
    };
    return icons[type] || Package;
};

const groupedOrderItems = computed(() => {
    const groups: Record<
        string,
        { item: OrderItem; index: number; panelName?: string }[]
    > = {};

    form.order_items.forEach((item, index) => {
        let groupKey = item.item_type;
        let panelName: string | undefined;

        // For lab items, group by panel name instead of just "lab"
        if (item.item_type === 'lab' && item.inventory_id) {
            // Find which panel contains this lab item
            const panel = props.labPanels.find((p) =>
                p.items.some((panelItem) => panelItem.id === item.inventory_id),
            );
            if (panel) {
                groupKey = `lab-${panel.id}`;
                panelName = panel.name;
            }
        }

        if (!groups[groupKey]) {
            groups[groupKey] = [];
        }
        groups[groupKey].push({ item, index, panelName });
    });

    return groups;
});

const getItemTypeDisplayName = (type: string, panelName?: string) => {
    if (panelName) {
        return panelName;
    }

    const names: Record<string, string> = {
        lab: 'Lab Tests',
        rx_medicine: 'RX Medicines',
        procedure: 'Procedures',
        imaging: 'Imaging',
        supply: 'Supplies',
    };
    return names[type] || type;
};
</script>

<template>
    <Head title="Edit Medical Order" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            v-if="hasPermission('edit_medical_orders')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="index().url">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Medical Order</h1>
                    <p class="text-muted-foreground">
                        Update medical order and its items
                    </p>
                </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Order Information</CardTitle>
                        <CardDescription
                            >Update basic details about this medical
                            order</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <FormField name="patient_id">
                                <FormItem>
                                    <Label>Patient</Label>
                                    <SearchableSelect
                                        v-model="patientValue"
                                        :options="patientOptions"
                                        placeholder="Select a patient (optional)"
                                    />
                                    <div
                                        v-if="form.errors.patient_id"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.patient_id }}
                                    </div>
                                </FormItem>
                            </FormField>

                            <FormField name="staff_id">
                                <FormItem>
                                    <Label>Ordering Staff</Label>
                                    <SearchableSelect
                                        v-model="staffValue"
                                        :options="staffOptions"
                                        placeholder="Select a staff member (optional)"
                                    />
                                    <div
                                        v-if="form.errors.staff_id"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.staff_id }}
                                    </div>
                                </FormItem>
                            </FormField>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <FormField name="priority">
                                <FormItem>
                                    <Label>Priority *</Label>
                                    <FormControl>
                                        <Select v-model="form.priority">
                                            <SelectTrigger>
                                                <SelectValue
                                                    placeholder="Select priority"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="priority in priorities"
                                                    :key="priority.value"
                                                    :value="priority.value"
                                                >
                                                    {{ priority.label }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
                                    <div
                                        v-if="form.errors.priority"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.priority }}
                                    </div>
                                </FormItem>
                            </FormField>

                            <FormField name="status">
                                <FormItem>
                                    <Label>Status *</Label>
                                    <FormControl>
                                        <Select v-model="form.status">
                                            <SelectTrigger>
                                                <SelectValue
                                                    placeholder="Select status"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="status in statuses"
                                                    :key="status.value"
                                                    :value="status.value"
                                                >
                                                    {{ status.label }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
                                    <div
                                        v-if="form.errors.status"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.status }}
                                    </div>
                                </FormItem>
                            </FormField>
                        </div>

                        <FormField name="order_details">
                            <FormItem>
                                <Label>Order Details *</Label>
                                <FormControl>
                                    <Textarea
                                        v-model="form.order_details"
                                        placeholder="Enter order details"
                                        rows="3"
                                    />
                                </FormControl>
                                <div
                                    v-if="form.errors.order_details"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.order_details }}
                                </div>
                            </FormItem>
                        </FormField>

                        <div class="grid grid-cols-3 gap-4">
                            <FormField name="ordered_at">
                                <FormItem>
                                    <Label>Order Date *</Label>
                                    <FormControl>
                                        <Input
                                            v-model="form.ordered_at"
                                            type="date"
                                        />
                                    </FormControl>
                                    <div
                                        v-if="form.errors.ordered_at"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.ordered_at }}
                                    </div>
                                </FormItem>
                            </FormField>

                            <FormField name="completed_at">
                                <FormItem>
                                    <Label>Completed Date (Optional)</Label>
                                    <FormControl>
                                        <Input
                                            v-model="form.completed_at"
                                            type="date"
                                        />
                                    </FormControl>
                                    <div
                                        v-if="form.errors.completed_at"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.completed_at }}
                                    </div>
                                </FormItem>
                            </FormField>

                            <FormField name="notes">
                                <FormItem>
                                    <Label>Notes (Optional)</Label>
                                    <FormControl>
                                        <Textarea
                                            v-model="form.notes"
                                            placeholder="Additional notes"
                                            rows="1"
                                        />
                                    </FormControl>
                                    <div
                                        v-if="form.errors.notes"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.notes }}
                                    </div>
                                </FormItem>
                            </FormField>
                        </div>
                    </CardContent>
                </Card>

                <!-- Order Summary -->
                <Card
                    v-if="orderSummary.total > 0"
                    class="border-green-200 bg-green-50/50 dark:border-green-800 dark:bg-green-950/20"
                >
                    <CardContent class="pt-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Package
                                    class="size-5 text-green-600 dark:text-green-400"
                                />
                                <span
                                    class="font-medium text-green-800 dark:text-green-200"
                                    >Order Summary</span
                                >
                            </div>
                            <div class="text-right">
                                <div
                                    class="text-2xl font-bold text-green-800 dark:text-green-200"
                                >
                                    {{ orderSummary.total }}
                                </div>
                                <div
                                    class="text-sm text-green-600 dark:text-green-400"
                                >
                                    {{ formatPrice(orderTotalPrice) }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                                <div
                                    v-if="orderSummary.lab > 0"
                                    class="flex flex-col items-center justify-center p-4 rounded-lg border bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950/50 dark:to-blue-900/50 border-blue-200 dark:border-blue-800"
                                >
                                    <FlaskConical class="size-6 text-blue-600 dark:text-blue-400 mb-2" />
                                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ orderSummary.lab }}</div>
                                    <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">Lab Tests</div>
                                </div>
                                <div
                                    v-if="orderSummary.rx_medicine > 0"
                                    class="flex flex-col items-center justify-center p-4 rounded-lg border bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-950/50 dark:to-purple-900/50 border-purple-200 dark:border-purple-800"
                                >
                                    <Pill class="size-6 text-purple-600 dark:text-purple-400 mb-2" />
                                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ orderSummary.rx_medicine }}</div>
                                    <div class="text-xs text-purple-600 dark:text-purple-400 font-medium">RX Medicines</div>
                                </div>
                                <div
                                    v-if="orderSummary.procedure > 0"
                                    class="flex flex-col items-center justify-center p-4 rounded-lg border bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-950/50 dark:to-orange-900/50 border-orange-200 dark:border-orange-800"
                                >
                                    <Syringe class="size-6 text-orange-600 dark:text-orange-400 mb-2" />
                                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-300">{{ orderSummary.procedure }}</div>
                                    <div class="text-xs text-orange-600 dark:text-orange-400 font-medium">Procedures</div>
                                </div>
                                <div
                                    v-if="orderSummary.imaging > 0"
                                    class="flex flex-col items-center justify-center p-4 rounded-lg border bg-gradient-to-br from-red-50 to-red-100 dark:from-red-950/50 dark:to-red-900/50 border-red-200 dark:border-red-800"
                                >
                                    <Scan class="size-6 text-red-600 dark:text-red-400 mb-2" />
                                    <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ orderSummary.imaging }}</div>
                                    <div class="text-xs text-red-600 dark:text-red-400 font-medium">Imaging</div>
                                </div>
                                <div
                                    v-if="orderSummary.supply > 0"
                                    class="flex flex-col items-center justify-center p-4 rounded-lg border bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-950/50 dark:to-gray-900/50 border-gray-200 dark:border-gray-800"
                                >
                                    <Package class="size-6 text-gray-600 dark:text-gray-400 mb-2" />
                                    <div class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ orderSummary.supply }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">Supplies</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Order Items *</CardTitle>
                                <CardDescription
                                    >Update lab tests, procedures, imaging, and
                                    supplies</CardDescription
                                >
                            </div>
                            <div
                                v-if="form.order_items.length > 0"
                                class="flex gap-2"
                            >
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="clearAllItems"
                                >
                                    <Trash2 class="size-4" />
                                    Clear All
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex flex-wrap gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="showLabDialog = true"
                            >
                                <FlaskConical class="size-4" />
                                Add Lab Tests
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="showRxDialog = true"
                            >
                                <Pill class="size-4" />
                                Add RX Medicines
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="showMedicalServiceDialog = true"
                            >
                                <Activity class="size-4" />
                                Add Procedures/Imaging
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="addSupplyItem"
                            >
                                <Package class="size-4" />
                                Add Supply
                            </Button>
                        </div>

                        <div
                            v-if="form.errors.order_items"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.order_items }}
                        </div>

                        <div
                            v-if="form.order_items.length === 0"
                            class="rounded-md border border-dashed p-8 text-center text-muted-foreground"
                        >
                            No items added yet. Click the buttons above to add
                            items to this order.
                        </div>

                        <!-- Lab Selection Dialog -->
                        <Card v-if="showLabDialog" class="border-primary">
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <CardTitle>Select Lab Tests</CardTitle>
                                        <CardDescription
                                            >Choose tests from available lab
                                            panels</CardDescription
                                        >
                                    </div>
                                    <div class="flex gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            @click="showLabDialog = false"
                                        >
                                            Cancel
                                        </Button>
                                        <Button
                                            type="button"
                                            size="sm"
                                            @click="addSelectedLabItems"
                                            :disabled="selectedCount === 0"
                                        >
                                            Add
                                            {{
                                                selectedCount > 0
                                                    ? `(${selectedCount})`
                                                    : ''
                                            }}
                                            Selected
                                        </Button>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div class="mb-4">
                                    <div class="relative">
                                        <Search
                                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 transform text-muted-foreground"
                                        />
                                        <Input
                                            v-model="labSearchQuery"
                                            placeholder="Search lab tests..."
                                            class="pl-10"
                                        />
                                    </div>
                                </div>
                                <Tabs v-model="activeLabPanel">
                                    <TabsList
                                        class="grid w-full"
                                        :style="`grid-template-columns: repeat(${labPanels.length}, 1fr)`"
                                    >
                                        <TabsTrigger
                                            v-for="panel in labPanels"
                                            :key="panel.id"
                                            :value="panel.id.toString()"
                                        >
                                            {{ panel.name }}
                                        </TabsTrigger>
                                    </TabsList>
                                    <TabsContent
                                        v-for="panel in labPanels"
                                        :key="panel.id"
                                        :value="panel.id.toString()"
                                        class="space-y-4"
                                    >
                                        <div
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{ panel.description }}
                                        </div>
                                        <div
                                            class="max-h-96 space-y-2 overflow-y-auto"
                                        >
                                            <div
                                                v-for="item in filteredLabItems"
                                                :key="item.id"
                                                class="flex items-start space-x-3 rounded-md border p-3 hover:bg-accent"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :id="`lab-item-${item.id}`"
                                                    :checked="
                                                        selectedLabItems.includes(
                                                            item.id,
                                                        )
                                                    "
                                                    @change="
                                                        toggleLabItem(
                                                            item.id,
                                                            (
                                                                $event.target as HTMLInputElement
                                                            ).checked,
                                                        )
                                                    "
                                                    class="mt-1 h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                                <div class="flex-1 space-y-2">
                                                    <label
                                                        :for="`lab-item-${item.id}`"
                                                        class="cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                    >
                                                        {{ item.item_name }}
                                                        <span
                                                            v-if="item.notes"
                                                            class="ml-2 text-muted-foreground"
                                                            >({{
                                                                item.notes
                                                            }})</span
                                                        >
                                                    </label>

                                                    <div v-if="selectedLabItems.includes(item.id)" class="flex items-center gap-4">
                                                        <div class="flex items-center gap-2">
                                                            <Label :for="`lab-qty-${item.id}`" class="text-xs">QTY:</Label>
                                                            <Input
                                                                :id="`lab-qty-${item.id}`"
                                                                v-model.number="labItemQuantities[item.id]"
                                                                type="number"
                                                                min="1"
                                                                class="h-8 w-20"
                                                            />
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <Switch
                                                                :id="`lab-package-${item.id}`"
                                                                v-model:checked="labItemIncludePackage[item.id]"
                                                            />
                                                            <Label :for="`lab-package-${item.id}`" class="text-xs cursor-pointer">
                                                                Include Package
                                                            </Label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right text-xs">
                                                    <div
                                                        class="font-medium text-green-600 dark:text-green-400"
                                                    >
                                                        {{
                                                            formatPrice(
                                                                getLabItemPrice(
                                                                    item.id,
                                                                ),
                                                            )
                                                        }}
                                                    </div>
                                                    <span
                                                        class="text-muted-foreground"
                                                        >Qty:
                                                        {{
                                                            item.quantity_required
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    filteredLabItems.length ===
                                                    0
                                                "
                                                class="py-8 text-center text-muted-foreground"
                                            >
                                                No lab tests found matching "{{
                                                    labSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                </Tabs>
                            </CardContent>
                        </Card>

                        <!-- RX Medicine Selection Dialog -->
                        <Card v-if="showRxDialog" class="border-primary">
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <CardTitle
                                            >Select RX Medicines</CardTitle
                                        >
                                        <CardDescription
                                            >Choose medicines from
                                            inventory</CardDescription
                                        >
                                    </div>
                                    <div class="flex gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            @click="showRxDialog = false"
                                        >
                                            Cancel
                                        </Button>
                                        <Button
                                            type="button"
                                            size="sm"
                                            @click="addSelectedRxItems"
                                            :disabled="selectedRxCount === 0"
                                        >
                                            Add
                                            {{
                                                selectedRxCount > 0
                                                    ? `(${selectedRxCount})`
                                                    : ''
                                            }}
                                            Selected
                                        </Button>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div class="mb-4">
                                    <div class="relative">
                                        <Search
                                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 transform text-muted-foreground"
                                        />
                                        <Input
                                            v-model="rxSearchQuery"
                                            placeholder="Search medicines..."
                                            class="pl-10"
                                        />
                                    </div>
                                </div>
                                <Tabs v-model="activeRxCategory">
                                    <TabsList
                                        class="grid w-full"
                                        :style="`grid-template-columns: repeat(${rxCategories.length || 1}, 1fr)`"
                                    >
                                        <TabsTrigger value="All"
                                            >All Medicines</TabsTrigger
                                        >
                                        <TabsTrigger
                                            v-for="category in rxCategories"
                                            :key="category"
                                            :value="category"
                                        >
                                            {{ category }}
                                        </TabsTrigger>
                                    </TabsList>
                                    <TabsContent value="All" class="space-y-4">
                                        <div
                                            class="max-h-96 space-y-2 overflow-y-auto"
                                        >
                                            <div
                                                v-for="medicine in filteredRxMedicines"
                                                :key="medicine.id"
                                                class="flex items-start space-x-3 rounded-md border p-3 hover:bg-accent"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :id="`rx-item-${medicine.id}`"
                                                    :checked="
                                                        selectedRxItems.includes(
                                                            medicine.id,
                                                        )
                                                    "
                                                    @change="
                                                        toggleRxItem(
                                                            medicine.id,
                                                            (
                                                                $event.target as HTMLInputElement
                                                            ).checked,
                                                        )
                                                    "
                                                    class="mt-1 h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                                <div class="flex-1 space-y-2">
                                                    <label
                                                        :for="`rx-item-${medicine.id}`"
                                                        class="cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                    >
                                                        {{ medicine.item_name }}
                                                        <span
                                                            v-if="
                                                                medicine.description
                                                            "
                                                            class="mt-1 ml-2 block text-xs text-muted-foreground"
                                                            >{{
                                                                medicine.description
                                                            }}</span
                                                        >
                                                        <span
                                                            v-if="
                                                                medicine.dose_unit
                                                            "
                                                            class="ml-2 text-xs text-muted-foreground"
                                                            >({{
                                                                medicine.dose_unit
                                                            }})</span
                                                        >
                                                    </label>

                                                    <div v-if="selectedRxItems.includes(medicine.id)" class="flex items-center gap-4">
                                                        <div class="flex items-center gap-2">
                                                            <Label :for="`rx-qty-${medicine.id}`" class="text-xs">QTY:</Label>
                                                            <Input
                                                                :id="`rx-qty-${medicine.id}`"
                                                                v-model.number="rxItemQuantities[medicine.id]"
                                                                type="number"
                                                                min="1"
                                                                class="h-8 w-20"
                                                            />
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <Switch
                                                                :id="`rx-package-${medicine.id}`"
                                                                v-model:checked="rxItemIncludePackage[medicine.id]"
                                                            />
                                                            <Label :for="`rx-package-${medicine.id}`" class="text-xs cursor-pointer">
                                                                Include Package
                                                            </Label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right text-xs">
                                                    <div
                                                        class="text-muted-foreground"
                                                    >
                                                        Stock:
                                                        {{ medicine.quantity }}
                                                    </div>
                                                    <div
                                                        class="font-medium text-green-600 dark:text-green-400"
                                                    >
                                                        {{
                                                            formatPrice(
                                                                medicine.selling_price,
                                                            )
                                                        }}
                                                    </div>
                                                    <div
                                                        v-if="medicine.category"
                                                        class="text-muted-foreground"
                                                    >
                                                        {{ medicine.category }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    filteredRxMedicines.length ===
                                                    0
                                                "
                                                class="py-8 text-center text-muted-foreground"
                                            >
                                                No medicines found matching "{{
                                                    rxSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                    <TabsContent
                                        v-for="category in rxCategories"
                                        :key="category"
                                        :value="category"
                                        class="space-y-4"
                                    >
                                        <div
                                            class="max-h-96 space-y-2 overflow-y-auto"
                                        >
                                            <div
                                                v-for="medicine in filteredRxMedicines"
                                                :key="medicine.id"
                                                class="flex items-center space-x-2 rounded-md border p-3 hover:bg-accent"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :id="`rx-item-cat-${medicine.id}`"
                                                    :checked="
                                                        selectedRxItems.includes(
                                                            medicine.id,
                                                        )
                                                    "
                                                    @change="
                                                        toggleRxItem(
                                                            medicine.id,
                                                            (
                                                                $event.target as HTMLInputElement
                                                            ).checked,
                                                        )
                                                    "
                                                    class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                                <label
                                                    :for="`rx-item-cat-${medicine.id}`"
                                                    class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                >
                                                    {{ medicine.item_name }}
                                                    <span
                                                        v-if="
                                                            medicine.description
                                                        "
                                                        class="mt-1 ml-2 block text-xs text-muted-foreground"
                                                        >{{
                                                            medicine.description
                                                        }}</span
                                                    >
                                                    <span
                                                        v-if="
                                                            medicine.dose_unit
                                                        "
                                                        class="ml-2 text-xs text-muted-foreground"
                                                        >({{
                                                            medicine.dose_unit
                                                        }})</span
                                                    >
                                                </label>
                                                <div
                                                    class="text-right text-xs text-muted-foreground"
                                                >
                                                    <div
                                                        class="text-muted-foreground"
                                                    >
                                                        Stock:
                                                        {{ medicine.quantity }}
                                                    </div>
                                                    <div
                                                        class="font-medium text-green-600 dark:text-green-400"
                                                    >
                                                        {{
                                                            formatPrice(
                                                                medicine.selling_price,
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    filteredRxMedicines.length ===
                                                    0
                                                "
                                                class="py-8 text-center text-muted-foreground"
                                            >
                                                No medicines found matching "{{
                                                    rxSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                </Tabs>
                            </CardContent>
                        </Card>

                        <!-- Medical Services Selection Dialog -->
                        <Card v-if="showMedicalServiceDialog" class="border-primary">
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <CardTitle
                                            >Select Procedures/Imaging</CardTitle
                                        >
                                        <CardDescription
                                            >Choose medical services from
                                            catalog</CardDescription
                                        >
                                    </div>
                                    <div class="flex gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            @click="showMedicalServiceDialog = false"
                                        >
                                            Cancel
                                        </Button>
                                        <Button
                                            type="button"
                                            size="sm"
                                            @click="addSelectedMedicalServiceItems"
                                            :disabled="selectedMedicalServiceCount === 0"
                                        >
                                            Add
                                            {{
                                                selectedMedicalServiceCount > 0
                                                    ? `(${selectedMedicalServiceCount})`
                                                    : ''
                                            }}
                                            Selected
                                        </Button>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div class="mb-4">
                                    <div class="relative">
                                        <Search
                                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 transform text-muted-foreground"
                                        />
                                        <Input
                                            v-model="medicalServiceSearchQuery"
                                            placeholder="Search procedures/imaging..."
                                            class="pl-10"
                                        />
                                    </div>
                                </div>
                                <Tabs v-model="activeMedicalServiceType">
                                    <TabsList
                                        class="grid w-full"
                                        :style="`grid-template-columns: repeat(${medicalServiceTypes.length || 1}, 1fr)`"
                                    >
                                        <TabsTrigger value="All"
                                            >All Services</TabsTrigger
                                        >
                                        <TabsTrigger
                                            v-for="type in medicalServiceTypes"
                                            :key="type"
                                            :value="type"
                                        >
                                            {{ type }}
                                        </TabsTrigger>
                                    </TabsList>
                                    <TabsContent value="All" class="space-y-4">
                                        <div
                                            class="max-h-96 space-y-2 overflow-y-auto"
                                        >
                                            <div
                                                v-for="service in filteredMedicalServices"
                                                :key="service.id"
                                                class="flex items-center space-x-2 rounded-md border p-3 hover:bg-accent"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :id="`medical-service-item-${service.id}`"
                                                    :checked="
                                                        selectedMedicalServiceItems.includes(
                                                            service.id,
                                                        )
                                                    "
                                                    @change="
                                                        toggleMedicalServiceItem(
                                                            service.id,
                                                            (
                                                                $event.target as HTMLInputElement
                                                            ).checked,
                                                        )
                                                    "
                                                    class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                                <label
                                                    :for="`medical-service-item-${service.id}`"
                                                    class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                >
                                                    {{ service.name }}
                                                    <span
                                                        v-if="
                                                            service.description
                                                        "
                                                        class="mt-1 ml-2 block text-xs text-muted-foreground"
                                                        >{{
                                                            service.description
                                                        }}</span
                                                    >
                                                </label>
                                                <div class="text-right text-xs">
                                                    <div
                                                        class="font-medium text-green-600 dark:text-green-400"
                                                    >
                                                        {{
                                                            formatPrice(
                                                                service.price,
                                                            )
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-muted-foreground capitalize"
                                                    >
                                                        {{ service.type }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    filteredMedicalServices.length ===
                                                    0
                                                "
                                                class="py-8 text-center text-muted-foreground"
                                            >
                                                No services found matching "{{
                                                    medicalServiceSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                    <TabsContent
                                        v-for="type in medicalServiceTypes"
                                        :key="type"
                                        :value="type"
                                        class="space-y-4"
                                    >
                                        <div
                                            class="max-h-96 space-y-2 overflow-y-auto"
                                        >
                                            <div
                                                v-for="service in filteredMedicalServices"
                                                :key="service.id"
                                                class="flex items-center space-x-2 rounded-md border p-3 hover:bg-accent"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :id="`medical-service-item-cat-${service.id}`"
                                                    :checked="
                                                        selectedMedicalServiceItems.includes(
                                                            service.id,
                                                        )
                                                    "
                                                    @change="
                                                        toggleMedicalServiceItem(
                                                            service.id,
                                                            (
                                                                $event.target as HTMLInputElement
                                                            ).checked,
                                                        )
                                                    "
                                                    class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                                <label
                                                    :for="`medical-service-item-cat-${service.id}`"
                                                    class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                >
                                                    {{ service.name }}
                                                    <span
                                                        v-if="
                                                            service.description
                                                        "
                                                        class="mt-1 ml-2 block text-xs text-muted-foreground"
                                                        >{{
                                                            service.description
                                                        }}</span
                                                    >
                                                </label>
                                                <div class="text-right text-xs">
                                                    <div
                                                        class="font-medium text-green-600 dark:text-green-400"
                                                    >
                                                        {{
                                                            formatPrice(
                                                                service.price,
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    filteredMedicalServices.length ===
                                                    0
                                                "
                                                class="py-8 text-center text-muted-foreground"
                                            >
                                                No services found matching "{{
                                                    medicalServiceSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                </Tabs>
                            </CardContent>
                        </Card>

                        <div
                            v-if="
                                form.order_items.length === 0 &&
                                !showLabDialog &&
                                !showRxDialog &&
                                !showMedicalServiceDialog
                            "
                            class="rounded-lg border-2 border-dashed py-12 text-center text-muted-foreground"
                        >
                            <p class="mb-2">No items added yet</p>
                            <p class="text-sm">
                                Click the buttons above to add lab tests,
                                procedures, imaging, or supplies
                            </p>
                        </div>

                        <!-- Grouped Order Items Display -->
                        <div v-else class="space-y-6">
                            <div
                                v-for="[type, items] in Object.entries(
                                    groupedOrderItems,
                                )"
                                :key="type"
                                class="space-y-3"
                            >
                                <!-- Group Header -->
                                <div
                                    class="flex items-center gap-3 border-b pb-2"
                                >
                                    <component
                                        :is="
                                            getItemTypeIcon(
                                                items[0]?.panelName
                                                    ? 'lab'
                                                    : type,
                                            )
                                        "
                                        class="size-5 text-primary"
                                    />
                                    <h3 class="text-lg font-semibold">
                                        {{
                                            getItemTypeDisplayName(
                                                type,
                                                items[0]?.panelName,
                                            )
                                        }}
                                    </h3>
                                    <Badge
                                        variant="secondary"
                                        class="ml-auto"
                                        >{{ items.length }}</Badge
                                    >
                                </div>

                                <!-- Items in this group -->
                                <div class="ml-8 space-y-2">
                                    <div
                                        v-for="itemData in items"
                                        :key="itemData.index"
                                        class="overflow-hidden rounded-lg border"
                                    >
                                        <!-- Item Header -->
                                        <div
                                            class="flex cursor-pointer items-center justify-between bg-muted/30 p-3 hover:bg-muted/50"
                                            @click="
                                                toggleItemExpansion(
                                                    itemData.index,
                                                )
                                            "
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    class="h-6 w-6 p-0"
                                                    @click.stop="
                                                        toggleItemExpansion(
                                                            itemData.index,
                                                        )
                                                    "
                                                >
                                                    <component
                                                        :is="
                                                            expandedItems.has(
                                                                itemData.index,
                                                            )
                                                                ? ChevronDown
                                                                : ChevronRight
                                                        "
                                                        class="size-4"
                                                    />
                                                </Button>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-medium"
                                                        >{{
                                                            itemData.item
                                                                .item_name ||
                                                            'Unnamed item'
                                                        }}</span
                                                    >
                                                    <span
                                                        v-if="
                                                            itemData.item
                                                                .details
                                                        "
                                                        class="max-w-xs truncate text-xs text-muted-foreground"
                                                        >{{
                                                            itemData.item
                                                                .details
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <div class="text-right">
                                                    <div
                                                        class="text-sm font-medium"
                                                    >
                                                        {{
                                                            formatPrice(
                                                                getItemPrice(
                                                                    itemData.item,
                                                                ),
                                                            )
                                                        }}
                                                    </div>
                                                    <div
                                                        v-if="
                                                            itemData.item
                                                                .quantity_required >
                                                            1
                                                        "
                                                        class="text-xs text-muted-foreground"
                                                    >
                                                        {{
                                                            itemData.item
                                                                .quantity_required
                                                        }}
                                                        ×
                                                        {{
                                                            formatPrice(
                                                                itemData.item
                                                                    .selling_price ||
                                                                    0,
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                                <div class="flex gap-1">
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        @click.stop="
                                                            duplicateOrderItem(
                                                                itemData.index,
                                                            )
                                                        "
                                                        title="Duplicate item"
                                                    >
                                                        <Plus
                                                            class="size-4 text-muted-foreground"
                                                        />
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        @click.stop="
                                                            removeOrderItem(
                                                                itemData.index,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="size-4 text-destructive"
                                                        />
                                                    </Button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Item Content (Collapsible) -->
                                        <div
                                            v-if="
                                                expandedItems.has(
                                                    itemData.index,
                                                )
                                            "
                                            class="space-y-3 border-t p-3"
                                        >
                                            <!-- Lab Test Fields -->
                                            <div
                                                v-if="
                                                    itemData.item.item_type ===
                                                    'lab'
                                                "
                                                class="flex items-end gap-3"
                                            >
                                                <div class="flex-1">
                                                    <Label class="text-xs"
                                                        >Test Name</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .item_name
                                                        "
                                                        placeholder="Test name"
                                                        readonly
                                                        class="h-8 bg-muted"
                                                    />
                                                </div>
                                                <div class="flex-1">
                                                    <Label class="text-xs"
                                                        >Details</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .details
                                                        "
                                                        placeholder="Test parameters, instructions"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="w-20">
                                                    <Label class="text-xs"
                                                        >Qty</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .quantity_required
                                                        "
                                                        type="number"
                                                        min="1"
                                                        placeholder="1"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="w-32">
                                                    <Label class="text-xs"
                                                        >Notes</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item.notes
                                                        "
                                                        placeholder="Additional notes"
                                                        class="h-8"
                                                    />
                                                </div>
                                            </div>

                                            <!-- RX Medicine Fields -->
                                            <div
                                                v-if="
                                                    itemData.item.item_type ===
                                                    'rx_medicine'
                                                "
                                                class="space-y-2"
                                            >
                                                <div
                                                    class="flex items-end gap-3"
                                                >
                                                    <div class="flex-1">
                                                        <Label class="text-xs"
                                                            >Medicine
                                                            Name</Label
                                                        >
                                                        <Input
                                                            v-model="
                                                                itemData.item
                                                                    .item_name
                                                            "
                                                            placeholder="Medicine name"
                                                            readonly
                                                            class="h-8 bg-muted"
                                                        />
                                                    </div>
                                                    <div class="w-24">
                                                        <Label class="text-xs"
                                                            >Dosage</Label
                                                        >
                                                        <Input
                                                            v-model="
                                                                itemData.item
                                                                    .dosage
                                                            "
                                                            placeholder="500mg"
                                                            class="h-8"
                                                        />
                                                    </div>
                                                    <div class="w-32">
                                                        <Label class="text-xs"
                                                            >Frequency</Label
                                                        >
                                                        <Input
                                                            v-model="
                                                                itemData.item
                                                                    .frequency
                                                            "
                                                            placeholder="Twice daily"
                                                            class="h-8"
                                                        />
                                                    </div>
                                                    <div class="w-32">
                                                        <Label class="text-xs"
                                                            >Route</Label
                                                        >
                                                        <Select
                                                            v-model="
                                                                itemData.item
                                                                    .route
                                                            "
                                                        >
                                                            <SelectTrigger
                                                                class="h-8"
                                                            >
                                                                <SelectValue
                                                                    placeholder="Route"
                                                                />
                                                            </SelectTrigger>
                                                            <SelectContent>
                                                                <SelectItem
                                                                    value="oral"
                                                                    >Oral</SelectItem
                                                                >
                                                                <SelectItem
                                                                    value="iv"
                                                                    >IV</SelectItem
                                                                >
                                                                <SelectItem
                                                                    value="im"
                                                                    >IM</SelectItem
                                                                >
                                                                <SelectItem
                                                                    value="subcutaneous"
                                                                    >Subcutaneous</SelectItem
                                                                >
                                                                <SelectItem
                                                                    value="topical"
                                                                    >Topical</SelectItem
                                                                >
                                                                <SelectItem
                                                                    value="inhalation"
                                                                    >Inhalation</SelectItem
                                                                >
                                                                <SelectItem
                                                                    value="rectal"
                                                                    >Rectal</SelectItem
                                                                >
                                                            </SelectContent>
                                                        </Select>
                                                    </div>
                                                    <div class="w-20">
                                                        <Label class="text-xs"
                                                            >Qty</Label
                                                        >
                                                        <Input
                                                            v-model="
                                                                itemData.item
                                                                    .quantity_required
                                                            "
                                                            type="number"
                                                            min="1"
                                                            placeholder="1"
                                                            class="h-8"
                                                        />
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex items-end gap-3"
                                                >
                                                    <div class="flex-1">
                                                        <Label class="text-xs"
                                                            >Instructions</Label
                                                        >
                                                        <Input
                                                            v-model="
                                                                itemData.item
                                                                    .details
                                                            "
                                                            placeholder="Special instructions"
                                                            class="h-8"
                                                        />
                                                    </div>
                                                    <div class="w-32">
                                                        <Label class="text-xs"
                                                            >Notes</Label
                                                        >
                                                        <Input
                                                            v-model="
                                                                itemData.item
                                                                    .notes
                                                            "
                                                            placeholder="Additional notes"
                                                            class="h-8"
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Procedure Fields -->
                                            <div
                                                v-if="
                                                    itemData.item.item_type ===
                                                    'procedure'
                                                "
                                                class="flex items-end gap-3"
                                            >
                                                <div class="flex-1">
                                                    <Label class="text-xs"
                                                        >Procedure Name *</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .item_name
                                                        "
                                                        placeholder="Enter procedure name"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="flex-1">
                                                    <Label class="text-xs"
                                                        >Details</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .details
                                                        "
                                                        placeholder="Procedure details, requirements"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="w-20">
                                                    <Label class="text-xs"
                                                        >Qty</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .quantity_required
                                                        "
                                                        type="number"
                                                        min="1"
                                                        placeholder="1"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="w-32">
                                                    <Label class="text-xs"
                                                        >Notes</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item.notes
                                                        "
                                                        placeholder="Additional notes"
                                                        class="h-8"
                                                    />
                                                </div>
                                            </div>

                                            <!-- Imaging Fields -->
                                            <div
                                                v-if="
                                                    itemData.item.item_type ===
                                                    'imaging'
                                                "
                                                class="flex items-end gap-3"
                                            >
                                                <div class="flex-1">
                                                    <Label class="text-xs"
                                                        >Imaging Type *</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .item_name
                                                        "
                                                        placeholder="e.g., Chest X-Ray, CT Scan, MRI"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="flex-1">
                                                    <Label class="text-xs"
                                                        >Details</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .details
                                                        "
                                                        placeholder="Body part, with/without contrast, views"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="w-20">
                                                    <Label class="text-xs"
                                                        >Qty</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .quantity_required
                                                        "
                                                        type="number"
                                                        min="1"
                                                        placeholder="1"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="w-32">
                                                    <Label class="text-xs"
                                                        >Notes</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item.notes
                                                        "
                                                        placeholder="Additional notes"
                                                        class="h-8"
                                                    />
                                                </div>
                                            </div>

                                            <!-- Supply Fields -->
                                            <div
                                                v-if="
                                                    itemData.item.item_type ===
                                                    'supply'
                                                "
                                                class="flex items-end gap-3"
                                            >
                                                <div class="flex-1">
                                                    <Label class="text-xs"
                                                        >Supply Item</Label
                                                    >
                                                    <Select
                                                        @update:modelValue="
                                                            (val) =>
                                                                selectInventoryItem(
                                                                    itemData.item,
                                                                    Number(val),
                                                                )
                                                        "
                                                    >
                                                        <SelectTrigger
                                                            class="h-8"
                                                        >
                                                            <SelectValue
                                                                placeholder="Select from inventory"
                                                            />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem
                                                                v-for="invItem in inventoryItems"
                                                                :key="
                                                                    invItem.id
                                                                "
                                                                :value="
                                                                    invItem.id
                                                                "
                                                            >
                                                                {{
                                                                    invItem.item_name
                                                                }}
                                                                ({{
                                                                    invItem.type_label
                                                                }})
                                                            </SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                                <div class="w-20">
                                                    <Label class="text-xs"
                                                        >Qty</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item
                                                                .quantity_required
                                                        "
                                                        type="number"
                                                        min="1"
                                                        placeholder="1"
                                                        class="h-8"
                                                    />
                                                </div>
                                                <div class="w-32">
                                                    <Label class="text-xs"
                                                        >Notes</Label
                                                    >
                                                    <Input
                                                        v-model="
                                                            itemData.item.notes
                                                        "
                                                        placeholder="Additional notes"
                                                        class="h-8"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{
                            form.processing
                                ? 'Updating...'
                                : 'Update Medical Order'
                        }}
                    </Button>
                    <Button variant="outline" as-child>
                        <a :href="index().url">Cancel</a>
                    </Button>
                </div>
            </form>
        </div>
        <div v-else class="flex h-full flex-1 items-center justify-center">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">
                    Access Denied
                </h2>
                <p class="text-muted-foreground">
                    You do not have permission to edit medical orders.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
