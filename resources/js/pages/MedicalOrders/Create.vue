<script setup lang="ts">
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
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store } from '@/routes/medical-orders';
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

interface Props {
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
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Orders',
        href: index().url,
    },
    {
        title: 'Create',
        href: '#',
    },
];

interface OrderItem {
    item_type: string;
    item_name: string;
    details?: string;
    dosage?: string;
    frequency?: string;
    route?: string;
    quantity_required: number;
    notes?: string;
    inventory_id?: number;
    unit_price?: number;
    selling_price?: number;
}

const form = useForm<{
    patient_id: number | null;
    staff_id: number | null;
    order_details: string;
    priority: string;
    notes: string;
    ordered_at: string;
    order_items: OrderItem[];
}>({
    patient_id: null,
    staff_id: null,
    order_details: '',
    priority: 'routine',
    notes: '',
    ordered_at: new Date().toISOString().split('T')[0],
    order_items: [] as OrderItem[],
});

const submitForm = () => {
    console.log('Submitting form data:', form.data());
    form.post(store().url);
};

const priorities = [
    { value: 'routine', label: 'Routine' },
    { value: 'urgent', label: 'Urgent' },
    { value: 'stat', label: 'STAT' },
];

// Lab selection state
const showLabDialog = ref(false);
const selectedLabItems = ref<number[]>([]);
const activeLabPanel = ref<string>(props.labPanels[0]?.id.toString() || '');
const labSearchQuery = ref('');

const toggleLabItem = (itemId: number, checked: boolean) => {
    if (checked) {
        if (!selectedLabItems.value.includes(itemId)) {
            selectedLabItems.value.push(itemId);
        }
    } else {
        selectedLabItems.value = selectedLabItems.value.filter(
            (id) => id !== itemId,
        );
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

            form.order_items.push({
                item_type: 'lab',
                item_name: panelItem.item_name,
                details: panelItem.notes || '',
                quantity_required: panelItem.quantity_required,
                notes: '',
                inventory_id: panelItem.id,
                unit_price: inventoryItem?.unit_price || 0,
                selling_price: inventoryItem?.selling_price || 0,
            });
        }
    });

    selectedLabItems.value = [];
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
const rxSearchQuery = ref('');

const rxCategories = computed(() => {
    const categories = new Set<string>();
    props.rxMedicines.forEach((med) => {
        if (med.category) categories.add(med.category);
    });
    return Array.from(categories).sort();
});

const activeRxCategory = ref<string>('All');

const rxMedicinesByCategory = computed(() => {
    if (!activeRxCategory.value || activeRxCategory.value === 'All') {
        return props.rxMedicines;
    }
    return props.rxMedicines.filter(
        (med) => med.category === activeRxCategory.value,
    );
});

const toggleRxItem = (itemId: number, checked: boolean) => {
    if (checked) {
        if (!selectedRxItems.value.includes(itemId)) {
            selectedRxItems.value.push(itemId);
        }
    } else {
        selectedRxItems.value = selectedRxItems.value.filter(
            (id) => id !== itemId,
        );
    }
};

const addSelectedRxItems = () => {
    selectedRxItems.value.forEach((itemId) => {
        const medicine = props.rxMedicines.find((item) => item.id === itemId);
        if (medicine) {
            form.order_items.push({
                item_type: 'rx_medicine',
                item_name: medicine.item_name,
                details: medicine.description || '',
                dosage: '',
                frequency: '',
                route: '',
                quantity_required: 1,
                notes: '',
                inventory_id: medicine.id,
                unit_price: medicine.unit_price,
                selling_price: medicine.selling_price,
            });
        }
    });

    selectedRxItems.value = [];
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

// Item expansion state - all collapsed by default
const expandedItems = ref<Set<number>>(new Set());

const addProcedureItem = () => {
    form.order_items.push({
        item_type: 'procedure',
        item_name: '',
        details: '',
        quantity_required: 1,
        notes: '',
    });
};

const addImagingItem = () => {
    form.order_items.push({
        item_type: 'imaging',
        item_name: '',
        details: '',
        quantity_required: 1,
        notes: '',
    });
};

const addSupplyItem = () => {
    form.order_items.push({
        item_type: 'supply',
        item_name: '',
        inventory_id: undefined,
        quantity_required: 1,
        notes: '',
    });
};

const removeOrderItem = (index: number) => {
    form.order_items.splice(index, 1);
};

const duplicateOrderItem = (index: number) => {
    const item = form.order_items[index];
    const duplicatedItem = { ...item };
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

const getItemTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        lab: 'Lab Test',
        rx_medicine: 'RX Medicine',
        procedure: 'Procedure',
        imaging: 'Imaging',
        consultation: 'Consultation',
        therapy: 'Therapy',
        supply: 'Supply',
    };
    return labels[type] || type;
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

const getItemPrice = (item: OrderItem) => {
    if (item.selling_price) {
        return item.selling_price * item.quantity_required;
    }
    return 0;
};

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
</script>

<template>
    <Head title="Create Medical Order" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
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
                    <h1 class="text-2xl font-bold">Create Medical Order</h1>
                    <p class="text-muted-foreground">
                        Create a comprehensive medical order with multiple items
                    </p>
                </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Order Information</CardTitle>
                        <CardDescription
                            >Basic details about this medical
                            order</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <FormField name="patient_id">
                                <FormItem>
                                    <Label>Patient</Label>
                                    <FormControl>
                                        <Select v-model="form.patient_id">
                                            <SelectTrigger>
                                                <SelectValue
                                                    placeholder="Select a patient (optional)"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem :value="null"
                                                    >None</SelectItem
                                                >
                                                <SelectItem
                                                    v-for="patient in patients"
                                                    :key="patient.id"
                                                    :value="patient.id"
                                                >
                                                    {{ patient.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
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
                                    <FormControl>
                                        <Select v-model="form.staff_id">
                                            <SelectTrigger>
                                                <SelectValue
                                                    placeholder="Select staff member (optional)"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem :value="null"
                                                    >None</SelectItem
                                                >
                                                <SelectItem
                                                    v-for="staff in staff"
                                                    :key="staff.id"
                                                    :value="staff.id"
                                                >
                                                    {{ staff.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
                                    <div
                                        v-if="form.errors.staff_id"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.staff_id }}
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
                        <div class="mt-3 grid grid-cols-5 gap-4 text-sm">
                            <div
                                v-if="orderSummary.lab > 0"
                                class="flex items-center gap-1"
                            >
                                <FlaskConical class="size-4 text-blue-600" />
                                <span>{{ orderSummary.lab }} Lab</span>
                            </div>
                            <div
                                v-if="orderSummary.rx_medicine > 0"
                                class="flex items-center gap-1"
                            >
                                <Pill class="size-4 text-purple-600" />
                                <span>{{ orderSummary.rx_medicine }} RX</span>
                            </div>
                            <div
                                v-if="orderSummary.procedure > 0"
                                class="flex items-center gap-1"
                            >
                                <Syringe class="size-4 text-orange-600" />
                                <span>{{ orderSummary.procedure }} Proc</span>
                            </div>
                            <div
                                v-if="orderSummary.imaging > 0"
                                class="flex items-center gap-1"
                            >
                                <Scan class="size-4 text-red-600" />
                                <span>{{ orderSummary.imaging }} Imaging</span>
                            </div>
                            <div
                                v-if="orderSummary.supply > 0"
                                class="flex items-center gap-1"
                            >
                                <Package class="size-4 text-gray-600" />
                                <span>{{ orderSummary.supply }} Supply</span>
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
                                    >Add lab tests, procedures, imaging, and
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
                                @click="addProcedureItem"
                            >
                                <Syringe class="size-4" />
                                Add Procedure
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="addImagingItem"
                            >
                                <Scan class="size-4" />
                                Add Imaging
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
                                            class="flex items-center justify-between text-sm text-muted-foreground"
                                        >
                                            <span>{{ panel.description }}</span>
                                            <span
                                                class="text-xs text-muted-foreground"
                                                >Panel contains
                                                {{ panel.items.length }}
                                                tests</span
                                            >
                                        </div>
                                        <div
                                            class="max-h-96 space-y-2 overflow-y-auto"
                                        >
                                            <div
                                                v-for="item in filteredLabItems"
                                                :key="item.id"
                                                class="flex items-center space-x-2 rounded-md border p-3 hover:bg-accent"
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
                                                    class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                                <label
                                                    :for="`lab-item-${item.id}`"
                                                    class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
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
                                                class="flex items-center space-x-2 rounded-md border p-3 hover:bg-accent"
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
                                                    class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                                <label
                                                    :for="`rx-item-${medicine.id}`"
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
                                                    Stock:
                                                    {{ medicine.quantity }}
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

                        <div
                            v-if="
                                form.order_items.length === 0 &&
                                !showLabDialog &&
                                !showRxDialog
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
                                                                v-for="invItem in props.inventoryItems"
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
                                ? 'Creating...'
                                : 'Create Medical Order'
                        }}
                    </Button>
                    <Button variant="outline" as-child>
                        <a :href="index().url">Cancel</a>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
