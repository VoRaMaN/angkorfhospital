<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { formatDate } from '@/lib/utils';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, processAndBill, processWithUpdate, show, update } from '@/routes/medical-orders';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import {
    Activity,
    AlertCircle,
    ArrowLeft,
    CheckCircle,
    ChevronDown,
    ChevronRight,
    Clock,
    FlaskConical,
    Package,
    Pill,
    Plus,
    Scan,
    Search,
    Send,
    Syringe,
    Trash2,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const page = usePage<{ flash: { success?: string; error?: string } }>();

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

interface PatchItem {
    id: number;
    item_name: string;
    unit_price: number;
    selling_price: number;
    type_of_supply: string;
    category: string;
}

interface PatchRow {
    id: number;
    name: string;
    total_unit_price: number;
    items: PatchItem[];
}

interface MedicineGroupItem {
    id: number;
    item_name: string;
    dosage: string | null;
    frequency: string | null;
    quantity: number;
    unit_price: number;
    selling_price: number;
}

interface MedicineGroup {
    id: number;
    name: string;
    description: string | null;
    custom_price: number | null;
    total_price: number;
    items: MedicineGroupItem[];
}

interface SpecialItem {
    id: number;
    name: string;
    description: string | null;
    unit_price: number;
    items: SpecialItemSubItem[];
}

interface SpecialItemSubItem {
    id: number;
    item_name: string;
    quantity: number;
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
    is_package_included?: boolean;
}

interface Props {
    medicalOrder: {
        id: number;
        patient_id: number | null;
        patient_name: string;
        staff_id: number | null;
        staff_name: string;
        order_details: string;
        status: string;
        status_label: string;
        priority: string;
        priority_label: string;
        notes?: string;
        ordered_at: string;
        completed_at?: string;
        created_at: string;
        updated_at: string;
        order_items: OrderItem[];
    };
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
    medicalServices?: Array<{
        id: number;
        name: string;
        description: string | null;
        type: string;
        price: number;
    }>;
    patches: PatchRow[];
    medicineGroups: MedicineGroup[];
    specialItems: SpecialItem[];
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Orders',
        href: index().url,
    },
    {
        title: 'Process Order',
        href: '#',
    },
];

const storageKey = `medical-order-draft-${props.medicalOrder.id}`;

const loadDraft = (): { order_details: string; notes: string; order_items: OrderItem[] } | null => {
    try {
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            return JSON.parse(saved);
        }
    } catch {
        // ignore
    }
    return null;
};

const draft = loadDraft();

const form = useForm<{
    order_details: string;
    notes: string;
    order_items: OrderItem[];
}>({
    order_details: draft?.order_details ?? props.medicalOrder.order_details,
    notes: draft?.notes ?? props.medicalOrder.notes ?? '',
    order_items: draft?.order_items
        ?? (props.medicalOrder.order_items
            ? props.medicalOrder.order_items.map((item) => ({ ...item }))
            : []),
});

// Auto-save draft to localStorage when form changes
watch(
    () => ({ order_details: form.order_details, notes: form.notes, order_items: form.order_items }),
    (val) => {
        try {
            localStorage.setItem(storageKey, JSON.stringify(val));
        } catch {
            // ignore quota errors
        }
    },
    { deep: true },
);

const clearDraft = () => {
    localStorage.removeItem(storageKey);
};

// Patch selection state
const selectedPatchId = ref<string>('');
const selectedPatch = computed(() =>
    props.patches.find((p) => p.id.toString() === selectedPatchId.value),
);

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
            if (!labItemQuantities.value[itemId]) {
                labItemQuantities.value[itemId] = 1;
            }
            if (labItemIncludePackage.value[itemId] === undefined) {
                labItemIncludePackage.value[itemId] = false;
            }
        }
    } else {
        selectedLabItems.value = selectedLabItems.value.filter(
            (id) => id !== itemId,
        );
        delete labItemQuantities.value[itemId];
        delete labItemIncludePackage.value[itemId];
    }
};

const addSelectedLabItems = () => {
    const allPanelItems = props.labPanels.flatMap((panel) => panel.items);

    selectedLabItems.value.forEach((itemId) => {
        const panelItem = allPanelItems.find((item) => item.id === itemId);
        if (panelItem) {
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
                is_package_included: includePackage,
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
const showConfirmDialog = ref(false);
const isProcessing = ref(false);
const isSendingToAccount = ref(false);
const submitError = ref<string | null>(null);
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
            if (!rxItemQuantities.value[itemId]) {
                rxItemQuantities.value[itemId] = 1;
            }
            if (rxItemIncludePackage.value[itemId] === undefined) {
                rxItemIncludePackage.value[itemId] = false;
            }
        }
    } else {
        selectedRxItems.value = selectedRxItems.value.filter(
            (id) => id !== itemId,
        );
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
                is_package_included: includePackage,
            });
        }
    });

    selectedRxItems.value = [];
    rxItemQuantities.value = {};
    rxItemIncludePackage.value = {};
    showRxDialog.value = false;
};

const selectedRxCount = computed(() => selectedRxItems.value.length);

// Special Items selection state
const showMedicineGroupDialog = ref(false);
const selectedMedicineGroupIds = ref<number[]>([]);
const medicineGroupIncludePackage = ref(false);
const medicineGroupSearchQuery = ref('');

const toggleMedicineGroup = (groupId: number, checked: boolean) => {
    if (checked) {
        if (!selectedMedicineGroupIds.value.includes(groupId)) {
            selectedMedicineGroupIds.value.push(groupId);
        }
    } else {
        selectedMedicineGroupIds.value = selectedMedicineGroupIds.value.filter((id) => id !== groupId);
    }
};

const addSelectedMedicineGroups = (includePackage: boolean) => {
    selectedMedicineGroupIds.value.forEach((groupId) => {
        const group = props.medicineGroups.find((g) => g.id === groupId);
        if (!group) return;
        if (group.items.length === 0) {
            form.order_items.push({
                item_type: 'special_item',
                item_name: group.name,
                details: group.description || '',
                quantity_required: 1,
                status: 'pending',
                notes: `Special Items: ${group.name}${includePackage ? ' - Include Package - Not counted in billing' : ''}`,
                unit_price: includePackage ? 0 : (group.custom_price || group.total_price || 0),
                selling_price: includePackage ? 0 : (group.custom_price || group.total_price || 0),
                is_package_included: includePackage,
            });
        } else {
            group.items.forEach((item) => {
                form.order_items.push({
                    item_type: 'rx_medicine',
                    item_name: item.item_name,
                    details: `From Group: ${group.name}${includePackage ? ' (Package Included)' : ''}`,
                    dosage: item.dosage || '',
                    frequency: item.frequency || '',
                    route: '',
                    quantity_required: item.quantity,
                    status: 'pending',
                    notes: `Special Items: ${group.name}${includePackage ? ' - Include Package - Not counted in billing' : ''}`,
                    inventory_id: item.id,
                    unit_price: includePackage ? 0 : item.unit_price,
                    selling_price: includePackage ? 0 : item.selling_price,
                    is_package_included: includePackage,
                });
            });
        }
    });
};

const selectedMedicineGroupCount = computed(() => selectedMedicineGroupIds.value.length);

// Special Items selection state
const selectedSpecialItemIds = ref<number[]>([]);

const toggleSpecialItem = (itemId: number, checked: boolean) => {
    if (checked) {
        if (!selectedSpecialItemIds.value.includes(itemId)) {
            selectedSpecialItemIds.value.push(itemId);
        }
    } else {
        selectedSpecialItemIds.value = selectedSpecialItemIds.value.filter((id) => id !== itemId);
    }
};

const addSelectedSpecialItems = (includePackage: boolean) => {
    selectedSpecialItemIds.value.forEach((itemId) => {
        const item = props.specialItems.find((i) => i.id === itemId);
        if (!item) return;
        const includesNote = item.items.length > 0 ? ' (includes: ' + item.items.map(i => i.item_name).join(', ') + ')' : '';
        form.order_items.push({
            item_type: 'special_item',
            item_name: item.name,
            details: item.description || '',
            quantity_required: 1,
            status: 'pending',
            notes: `Special Item${includesNote}${includePackage ? ' - Include Package - Not counted in billing' : ''}`,
            unit_price: includePackage ? 0 : item.unit_price,
            selling_price: includePackage ? 0 : item.unit_price,
            is_package_included: includePackage,
        });
    });
};

// Adds whichever of groups / individual special items are currently selected
// (previously only one or the other could be added per click, silently
// dropping the other selection) and resets the shared dialog state once.
const addSelectedGroupsAndSpecialItems = () => {
    const includePackage = medicineGroupIncludePackage.value;
    if (selectedMedicineGroupIds.value.length > 0) {
        addSelectedMedicineGroups(includePackage);
    }
    if (selectedSpecialItemIds.value.length > 0) {
        addSelectedSpecialItems(includePackage);
    }
    selectedMedicineGroupIds.value = [];
    selectedSpecialItemIds.value = [];
    medicineGroupIncludePackage.value = false;
    showMedicineGroupDialog.value = false;
};

const selectedSpecialItemCount = computed(() => selectedSpecialItemIds.value.length);

// Filtered medicine groups and special items
const filteredMedicineGroups = computed(() =>
    props.medicineGroups.filter((group) =>
        group.name.toLowerCase().includes(medicineGroupSearchQuery.value.toLowerCase()) ||
        (group.description && group.description.toLowerCase().includes(medicineGroupSearchQuery.value.toLowerCase())),
    ),
);

const filteredSpecialItems = computed(() =>
    props.specialItems.filter((item) =>
        item.name.toLowerCase().includes(medicineGroupSearchQuery.value.toLowerCase()) ||
        (item.description && item.description.toLowerCase().includes(medicineGroupSearchQuery.value.toLowerCase())),
    ),
);

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
    if (props.medicalServices) {
        props.medicalServices.forEach((service) => {
            types.add(service.type);
        });
    }
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
    if (!props.medicalServices) return;

    selectedMedicalServiceItems.value.forEach((itemId) => {
        const service = props.medicalServices!.find(
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
    if (!props.medicalServices) return [];

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

// Map inventory type_of_supply to valid medical order item_type
const mapSupplyTypeToItemType = (supplyType: string): string => {
    const mapping: Record<string, string> = {
        'lab_supply': 'lab',
        'medication': 'rx_medicine',
        'rx_medicine': 'rx_medicine',
        'equipment': 'supply',
        'office_supply': 'supply',
        'medical_supply': 'supply',
        'supply': 'supply',
        'lab': 'lab',
        'procedure': 'procedure',
        'imaging': 'imaging',
        'consultation': 'consultation',
        'therapy': 'therapy',
    };
    return mapping[supplyType.toLowerCase()] || 'supply';
};

const applyPatch = () => {
    const patch = selectedPatch.value;
    if (!patch) {
        return;
    }

    patch.items.forEach((item) => {
        form.order_items.push({
            inventory_id: item.id,
            item_type: mapSupplyTypeToItemType(item.type_of_supply),
            item_name: item.item_name,
            quantity_required: 1,
            status: 'pending',
            notes: `Patch: ${patch.name}`,
            unit_price: item.unit_price,
            selling_price: item.selling_price,
        });
    });

    selectedPatchId.value = '';
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
        consultation: CheckCircle,
        therapy: Clock,
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

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'completed':
            return CheckCircle;
        case 'processing':
            return Clock;
        case 'pending':
            return AlertCircle;
        default:
            return AlertCircle;
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed':  return 'text-emerald-600';
        case 'processing': return 'text-sky-600';
        case 'pending':    return 'text-amber-600';
        case 'rejected':   return 'text-rose-600';
        default:           return 'text-slate-500';
    }
};

const sendToAccount = () => {
    isSendingToAccount.value = true;
    submitError.value = null;

    router.visit(
        update(props.medicalOrder.id).url,
        {
            method: 'put',
            data: {
                order_details: form.order_details,
                notes: form.notes,
                order_items: form.order_items.map((item) => ({
                    id: item.id,
                    item_type: item.item_type,
                    item_name: item.item_name,
                    details: item.details,
                    dosage: item.dosage,
                    frequency: item.frequency,
                    route: item.route,
                    quantity_required: item.quantity_required,
                    status: item.status,
                    notes: item.notes,
                    inventory_id: item.inventory_id,
                    unit_price: item.unit_price,
                    selling_price: item.selling_price,
                })),
            },
            preserveState: false,
            preserveScroll: false,
            onSuccess: () => {
                clearDraft();
                router.patch(processAndBill(props.medicalOrder.id).url);
            },
            onError: (errors) => {
                submitError.value = Object.values(errors).flat().join(', ');
            },
            onFinish: () => {
                isSendingToAccount.value = false;
                showConfirmDialog.value = false;
            },
        },
    );
};

const submitForm = () => {
    isProcessing.value = true;
    submitError.value = null;

    router.visit(
        processWithUpdate(props.medicalOrder.id).url,
        {
            method: 'patch',
            data: {
                order_details: form.order_details,
                notes: form.notes,
                order_items: form.order_items.map(item => ({
                    id: item.id,
                    item_type: item.item_type,
                    item_name: item.item_name,
                    details: item.details,
                    dosage: item.dosage,
                    frequency: item.frequency,
                    route: item.route,
                    quantity_required: item.quantity_required,
                    status: item.status,
                    notes: item.notes,
                    inventory_id: item.inventory_id,
                    unit_price: item.unit_price,
                    selling_price: item.selling_price,
                })),
            },
            preserveState: false,
            preserveScroll: false,
            onSuccess: () => {
                clearDraft();
            },
            onError: (errors) => {
                submitError.value = Object.values(errors).flat().join(', ');
            },
            onFinish: () => {
                isProcessing.value = false;
                showConfirmDialog.value = false;
            },
        }
    );
};
</script>

<template>

    <Head title="Process Medical Order" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('process_medical_orders')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="show(props.medicalOrder.id).url">
                        <ArrowLeft class="size-4" />
                        Back to Order
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Process Medical Order</h1>
                    <p class="text-muted-foreground">
                        Add and manage items for this medical order
                    </p>
                </div>
            </div>

            <!-- Order Information Card -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        Order #{{ medicalOrder.id }}
                        <Badge :class="medicalOrder.status === 'completed'
                                ? 'bg-green-100 text-green-800'
                                : medicalOrder.status === 'processing'
                                    ? 'bg-blue-100 text-blue-800'
                                    : 'bg-yellow-100 text-yellow-800'
                            ">
                            {{ medicalOrder.status_label }}
                        </Badge>
                    </CardTitle>
                    <CardDescription>Order details and patient information</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Patient</Label>
                            <p class="text-sm font-medium">
                                {{ medicalOrder.patient_name || 'Unknown Patient' }}
                            </p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Doctor</Label>
                            <p class="text-sm font-medium">
                                {{ medicalOrder.staff_name || 'Unassigned' }}
                            </p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Priority</Label>
                            <p class="text-sm font-medium">
                                {{ medicalOrder.priority_label }}
                            </p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Ordered Date</Label>
                            <p class="text-sm font-medium">
                                {{
                                    formatDate(medicalOrder.ordered_at)
                                }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <Label class="text-sm font-medium text-muted-foreground">Order Details</Label>
                        <Textarea v-model="form.order_details" placeholder="Enter order details..."
                            class="min-h-[80px]" />
                    </div>
                    <div>
                        <Label class="text-sm font-medium text-muted-foreground">Notes</Label>
                        <Textarea v-model="form.notes" placeholder="Enter additional notes..." class="min-h-[60px]" />
                    </div>
                </CardContent>
            </Card>

            <!-- Order Summary -->
            <Card v-if="orderSummary.total > 0"
                class="border-green-200 bg-green-50/50 dark:border-green-800 dark:bg-green-950/20">
                <CardContent class="pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <Package class="size-5 text-green-600 dark:text-green-400" />
                            <span class="font-medium text-green-800 dark:text-green-200">Order Summary</span>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-green-800 dark:text-green-200">
                                {{ orderSummary.total }}
                            </div>
                            <div class="text-sm text-green-600 dark:text-green-400">
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

            <form @submit.prevent="submitForm" class="space-y-6">

                <!-- Error Messages -->
                <div v-if="page.props.flash?.error || submitError" class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/50">
                    <div class="flex items-center gap-2">
                        <AlertCircle class="size-5 text-red-600 dark:text-red-400 shrink-0" />
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                            {{ page.props.flash?.error || submitError }}
                        </p>
                    </div>
                </div>
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Apply Patch</CardTitle>
                                <CardDescription>
                                    Quickly add predefined item sets to this order
                                </CardDescription>
                            </div>
                            <div class="text-sm text-muted-foreground">
                                {{ selectedPatch?.total_unit_price?.toLocaleString(undefined, { style: 'currency', currency: 'USD' }) || '--' }}
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-3 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label class="text-sm font-medium">Select patch</Label>
                                <Select v-model="selectedPatchId">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Choose a patch" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="patch in props.patches"
                                            :key="patch.id"
                                            :value="patch.id.toString()"
                                        >
                                            {{ patch.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label class="text-sm font-medium">Items</Label>
                                <div
                                    class="rounded-md border p-3 max-h-40 overflow-y-auto text-sm text-muted-foreground"
                                >
                                    <div v-if="!selectedPatch">
                                        Select a patch to preview items.
                                    </div>
                                    <div v-else class="space-y-1">
                                        <div
                                            v-for="item in selectedPatch.items"
                                            :key="item.id"
                                            class="flex justify-between"
                                        >
                                            <span>{{ item.item_name }} <span class="text-xs text-muted-foreground">({{ item.type_of_supply }})</span></span>
                                            <span>
                                                {{ Number(item.unit_price).toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <Button
                                type="button"
                                :disabled="!selectedPatch"
                                @click="applyPatch"
                            >
                                <Plus class="size-4 mr-2" />
                                Add patch items
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Order Items</CardTitle>
                                <CardDescription>Add and manage lab tests, procedures,
                                    imaging, and supplies
                                </CardDescription>
                            </div>
                            <div v-if="form.order_items.length > 0" class="flex gap-2">
                                <Button type="button" variant="outline" size="sm" @click="clearAllItems">
                                    <Trash2 class="size-4" />
                                    Clear All
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex flex-wrap gap-2">
                            <Button type="button" variant="outline" size="sm" @click="showLabDialog = true">
                                <FlaskConical class="size-4" />
                                Add Lab Tests
                            </Button>
                            <Button type="button" variant="outline" size="sm" @click="showRxDialog = true">
                                <Pill class="size-4" />
                                Add RX Medicines
                            </Button>
                            <Button type="button" variant="outline" size="sm" @click="showMedicineGroupDialog = true; medicineGroupSearchQuery = ''">
                                <Package class="size-4" />
                                Add Special Items
                            </Button>
                            <Button type="button" variant="outline" size="sm" @click="showMedicalServiceDialog = true">
                                <Activity class="size-4" />
                                Add Procedures/Imaging
                            </Button>
                            <Button type="button" variant="outline" size="sm" @click="addSupplyItem">
                                <Package class="size-4" />
                                Add Supply
                            </Button>
                        </div>

                        <div v-if="form.errors.order_items" class="text-sm text-destructive">
                            {{ form.errors.order_items }}
                        </div>

                        <div v-if="form.order_items.length === 0"
                            class="rounded-md border border-dashed p-8 text-center text-muted-foreground">
                            No items added yet. Click the buttons above to add
                            items to this order.
                        </div>

                        <!-- Lab Selection Dialog -->
                        <Card v-if="showLabDialog" class="border-primary">
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <CardTitle>Select Lab Tests</CardTitle>
                                        <CardDescription>Choose tests from available lab
                                            panels</CardDescription>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button type="button" variant="outline" size="sm"
                                            @click="showLabDialog = false">
                                            Cancel
                                        </Button>
                                        <Button type="button" size="sm" @click="addSelectedLabItems"
                                            :disabled="selectedCount === 0">
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
                                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 transform text-muted-foreground" />
                                        <Input v-model="labSearchQuery" placeholder="Search lab tests..."
                                            class="pl-10" />
                                    </div>
                                </div>
                                <Tabs v-model="activeLabPanel">
                                    <TabsList class="grid w-full"
                                        :style="`grid-template-columns: repeat(${labPanels.length}, 1fr)`">
                                        <TabsTrigger v-for="panel in labPanels" :key="panel.id"
                                            :value="panel.id.toString()">
                                            {{ panel.name }}
                                        </TabsTrigger>
                                    </TabsList>
                                    <TabsContent v-for="panel in labPanels" :key="panel.id" :value="panel.id.toString()"
                                        class="space-y-4">
                                        <div class="text-sm text-muted-foreground">
                                            {{ panel.description }}
                                        </div>
                                        <div class="max-h-96 space-y-2 overflow-y-auto">
                                            <div v-for="item in filteredLabItems" :key="item.id"
                                                class="rounded-md border p-3 hover:bg-accent">
                                                <div class="flex items-start space-x-2">
                                                    <input type="checkbox" :id="`lab-item-${item.id}`" :checked="selectedLabItems.includes(
                                                        item.id,
                                                    )
                                                        " @change="
                                                            toggleLabItem(
                                                                item.id,
                                                                (
                                                                    $event.target as HTMLInputElement
                                                                ).checked,
                                                            )
                                                            "
                                                        class="mt-0.5 h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                                                    <label :for="`lab-item-${item.id}`"
                                                        class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                                        {{ item.item_name }}
                                                        <span v-if="item.notes" class="ml-2 text-muted-foreground">({{
                                                            item.notes
                                                        }})</span>
                                                    </label>
                                                    <div class="text-right text-xs">
                                                        <div class="font-medium text-green-600 dark:text-green-400">
                                                            {{
                                                                formatPrice(
                                                                    getLabItemPrice(
                                                                        item.id,
                                                                    ),
                                                                )
                                                            }}
                                                        </div>
                                                        <span class="text-muted-foreground">Qty:
                                                            {{
                                                                item.quantity_required
                                                            }}</span>
                                                    </div>
                                                </div>
                                                <div v-if="selectedLabItems.includes(item.id)" class="mt-3 ml-6 flex items-center gap-4 border-t pt-3">
                                                    <div class="flex items-center space-x-2">
                                                        <input
                                                            type="checkbox"
                                                            :id="`lab-package-${item.id}`"
                                                            v-model="labItemIncludePackage[item.id]"
                                                            class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                        />
                                                        <label :for="`lab-package-${item.id}`" class="text-xs font-medium cursor-pointer">
                                                            Include Package
                                                        </label>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <label :for="`lab-qty-${item.id}`" class="text-xs font-medium whitespace-nowrap">
                                                            QTY:
                                                        </label>
                                                        <Input
                                                            :id="`lab-qty-${item.id}`"
                                                            type="number"
                                                            v-model.number="labItemQuantities[item.id]"
                                                            min="1"
                                                            class="h-8 w-20 text-xs"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="
                                                filteredLabItems.length ===
                                                0
                                            " class="py-8 text-center text-muted-foreground">
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
                                        <CardTitle>Select RX Medicines</CardTitle>
                                        <CardDescription>Choose medicines from
                                            inventory</CardDescription>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button type="button" variant="outline" size="sm" @click="showRxDialog = false">
                                            Cancel
                                        </Button>
                                        <Button type="button" size="sm" @click="addSelectedRxItems"
                                            :disabled="selectedRxCount === 0">
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
                                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 transform text-muted-foreground" />
                                        <Input v-model="rxSearchQuery" placeholder="Search medicines..."
                                            class="pl-10" />
                                    </div>
                                </div>
                                <Tabs v-model="activeRxCategory">
                                    <TabsList class="grid w-full"
                                        :style="`grid-template-columns: repeat(${rxCategories.length || 1}, 1fr)`">
                                        <TabsTrigger value="All">All Medicines</TabsTrigger>
                                        <TabsTrigger v-for="category in rxCategories" :key="category" :value="category">
                                            {{ category }}
                                        </TabsTrigger>
                                    </TabsList>
                                    <TabsContent value="All" class="space-y-4">
                                        <div class="max-h-96 space-y-2 overflow-y-auto">
                                            <div v-for="medicine in filteredRxMedicines" :key="medicine.id"
                                                class="rounded-md border p-3 hover:bg-accent">
                                                <div class="flex items-start space-x-2">
                                                    <input type="checkbox" :id="`rx-item-${medicine.id}`" :checked="selectedRxItems.includes(
                                                        medicine.id,
                                                    )
                                                        " @change="
                                                            toggleRxItem(
                                                                medicine.id,
                                                                (
                                                                    $event.target as HTMLInputElement
                                                                ).checked,
                                                            )
                                                            "
                                                        class="mt-0.5 h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                                                    <label :for="`rx-item-${medicine.id}`"
                                                        class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                                        {{ medicine.item_name }}
                                                        <span v-if="
                                                            medicine.description
                                                        " class="mt-1 ml-2 block text-xs text-muted-foreground">{{
                                                                medicine.description
                                                            }}</span>
                                                        <span v-if="
                                                            medicine.dose_unit
                                                        " class="ml-2 text-xs text-muted-foreground">({{
                                                                medicine.dose_unit
                                                            }})</span>
                                                    </label>
                                                    <div class="text-right text-xs">
                                                        <div class="text-muted-foreground">
                                                            Stock:
                                                            {{ medicine.quantity }}
                                                        </div>
                                                        <div class="font-medium text-green-600 dark:text-green-400">
                                                            {{
                                                                formatPrice(
                                                                    medicine.selling_price,
                                                                )
                                                            }}
                                                        </div>
                                                        <div v-if="medicine.category" class="text-muted-foreground">
                                                            {{ medicine.category }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="selectedRxItems.includes(medicine.id)" class="mt-3 ml-6 flex items-center gap-4 border-t pt-3">
                                                    <div class="flex items-center space-x-2">
                                                        <input
                                                            type="checkbox"
                                                            :id="`rx-package-${medicine.id}`"
                                                            v-model="rxItemIncludePackage[medicine.id]"
                                                            class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                        />
                                                        <label :for="`rx-package-${medicine.id}`" class="text-xs font-medium cursor-pointer">
                                                            Include Package
                                                        </label>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <label :for="`rx-qty-${medicine.id}`" class="text-xs font-medium whitespace-nowrap">
                                                            QTY:
                                                        </label>
                                                        <Input
                                                            :id="`rx-qty-${medicine.id}`"
                                                            type="number"
                                                            v-model.number="rxItemQuantities[medicine.id]"
                                                            min="1"
                                                            class="h-8 w-20 text-xs"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="
                                                filteredRxMedicines.length ===
                                                0
                                            " class="py-8 text-center text-muted-foreground">
                                                No medicines found matching "{{
                                                    rxSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                    <TabsContent v-for="category in rxCategories" :key="category" :value="category"
                                        class="space-y-4">
                                        <div class="max-h-96 space-y-2 overflow-y-auto">
                                            <div v-for="medicine in filteredRxMedicines" :key="medicine.id"
                                                class="rounded-md border p-3 hover:bg-accent">
                                                <div class="flex items-start space-x-2">
                                                    <input type="checkbox" :id="`rx-item-cat-${medicine.id}`" :checked="selectedRxItems.includes(
                                                        medicine.id,
                                                    )
                                                        " @change="
                                                            toggleRxItem(
                                                                medicine.id,
                                                                (
                                                                    $event.target as HTMLInputElement
                                                                ).checked,
                                                            )
                                                            "
                                                        class="mt-0.5 h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                                                    <label :for="`rx-item-cat-${medicine.id}`"
                                                        class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                                        {{ medicine.item_name }}
                                                        <span v-if="
                                                            medicine.description
                                                        " class="mt-1 ml-2 block text-xs text-muted-foreground">{{
                                                                medicine.description
                                                            }}</span>
                                                        <span v-if="
                                                            medicine.dose_unit
                                                        " class="ml-2 text-xs text-muted-foreground">({{
                                                                medicine.dose_unit
                                                            }})</span>
                                                    </label>
                                                    <div class="text-right text-xs text-muted-foreground">
                                                        <div class="text-muted-foreground">
                                                            Stock:
                                                            {{ medicine.quantity }}
                                                        </div>
                                                        <div class="font-medium text-green-600 dark:text-green-400">
                                                            {{
                                                                formatPrice(
                                                                    medicine.selling_price,
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="selectedRxItems.includes(medicine.id)" class="mt-3 ml-6 flex items-center gap-4 border-t pt-3">
                                                    <div class="flex items-center space-x-2">
                                                        <input
                                                            type="checkbox"
                                                            :id="`rx-package-cat-${medicine.id}`"
                                                            v-model="rxItemIncludePackage[medicine.id]"
                                                            class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                        />
                                                        <label :for="`rx-package-cat-${medicine.id}`" class="text-xs font-medium cursor-pointer">
                                                            Include Package
                                                        </label>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <label :for="`rx-qty-cat-${medicine.id}`" class="text-xs font-medium whitespace-nowrap">
                                                            QTY:
                                                        </label>
                                                        <Input
                                                            :id="`rx-qty-cat-${medicine.id}`"
                                                            type="number"
                                                            v-model.number="rxItemQuantities[medicine.id]"
                                                            min="1"
                                                            class="h-8 w-20 text-xs"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="
                                                filteredRxMedicines.length ===
                                                0
                                            " class="py-8 text-center text-muted-foreground">
                                                No medicines found matching "{{
                                                    rxSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                </Tabs>
                            </CardContent>
                        </Card>

                        <!-- Special Items Selection Dialog -->
                        <Card v-if="showMedicineGroupDialog" class="border-primary">
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <CardTitle>Select Special Items</CardTitle>
                                        <CardDescription>Choose a pre-configured special items group</CardDescription>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button type="button" variant="outline" size="sm" @click="showMedicineGroupDialog = false; selectedSpecialItemIds = []; medicineGroupSearchQuery = ''">
                                            Cancel
                                        </Button>
                                        <Button type="button" size="sm" @click="addSelectedGroupsAndSpecialItems()" :disabled="selectedMedicineGroupCount === 0 && selectedSpecialItemCount === 0">
                                            Add {{ (selectedMedicineGroupCount + selectedSpecialItemCount) > 0 ? `(${selectedMedicineGroupCount + selectedSpecialItemCount}) ` : '' }}Selected
                                        </Button>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-4">
                                    <!-- Search -->
                                    <div class="mb-2">
                                        <div class="relative">
                                            <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 transform text-muted-foreground" />
                                            <Input v-model="medicineGroupSearchQuery" placeholder="Search special items..." class="pl-10" />
                                        </div>
                                    </div>

                                    <!-- Include Package Toggle -->
                                    <div class="flex items-center space-x-2 p-3 rounded-md bg-muted/50">
                                        <input
                                            type="checkbox"
                                            id="medicine-group-include-package"
                                            v-model="medicineGroupIncludePackage"
                                            class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                        />
                                        <label for="medicine-group-include-package" class="text-sm font-medium cursor-pointer">
                                            Include Package (Price will be $0 - Not counted in billing)
                                        </label>
                                    </div>

                                    <div v-if="filteredMedicineGroups.length > 0">
                                        <div class="mb-3 text-sm font-semibold text-muted-foreground uppercase tracking-wide">Medicine Groups</div>
                                    </div>
                                    <div v-for="group in filteredMedicineGroups" :key="group.id"
                                        class="rounded-md border p-4 hover:bg-accent cursor-pointer"
                                        :class="{ 'border-primary bg-primary/5': selectedMedicineGroupIds.includes(group.id) }"
                                        @click="toggleMedicineGroup(group.id, !selectedMedicineGroupIds.includes(group.id)); selectedSpecialItemIds = selectedSpecialItemIds.filter(() => true)">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <input
                                                        type="checkbox"
                                                        :id="`group-${group.id}`"
                                                        :checked="selectedMedicineGroupIds.includes(group.id)"
                                                        @change="toggleMedicineGroup(group.id, ($event.target as HTMLInputElement).checked)"
                                                        class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                    />
                                                    <label :for="`group-${group.id}`" class="font-medium cursor-pointer">
                                                        {{ group.name }}
                                                    </label>
                                                </div>
                                                <p v-if="group.description" class="mt-1 text-sm text-muted-foreground">
                                                    {{ group.description }}
                                                </p>
                                                <div class="mt-3 space-y-2" v-if="group.items.length > 0">
                                                    <p class="text-sm font-medium">Includes:</p>
                                                    <div class="ml-4 space-y-1">
                                                        <div v-for="item in group.items" :key="item.id" class="text-sm text-muted-foreground">
                                                            • {{ item.item_name }}
                                                            <span v-if="item.dosage"> - {{ item.dosage }}</span>
                                                            <span v-if="item.frequency"> ({{ item.frequency }})</span>
                                                            <span class="ml-2">x{{ item.quantity }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-else class="mt-2">
                                                    <p class="text-sm italic text-muted-foreground">Group name only (no medicines)</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-semibold" :class="medicineGroupIncludePackage ? 'text-muted-foreground line-through' : 'text-green-600 dark:text-green-400'">
                                                    {{ formatPrice(group.custom_price || group.total_price) }}
                                                </div>
                                                <div v-if="medicineGroupIncludePackage" class="text-xs font-semibold text-green-600 dark:text-green-400">
                                                    $0.00 (Package)
                                                </div>
                                                <div v-else-if="group.custom_price" class="text-xs text-muted-foreground">
                                                    Package Price
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="filteredMedicineGroups.length === 0 && filteredSpecialItems.length === 0" class="py-8 text-center text-muted-foreground">
                                        No special items available
                                    </div>

                                    <!-- Special Items (single items with custom pricing) -->
                                    <div v-if="filteredSpecialItems.length > 0">
                                        <div class="mb-3 text-sm font-semibold text-muted-foreground uppercase tracking-wide">Individual Special Items</div>
                                        <div class="space-y-3">
                                            <div v-for="item in filteredSpecialItems" :key="`si-${item.id}`"
                                                class="rounded-md border p-4 hover:bg-accent cursor-pointer"
                                                :class="{ 'border-primary bg-primary/5': selectedSpecialItemIds.includes(item.id) }"
                                                @click="toggleSpecialItem(item.id, !selectedSpecialItemIds.includes(item.id))">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2">
                                                            <input
                                                                type="checkbox"
                                                                :id="`special-item-${item.id}`"
                                                                :checked="selectedSpecialItemIds.includes(item.id)"
                                                                @change="toggleSpecialItem(item.id, ($event.target as HTMLInputElement).checked)"
                                                                class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                            />
                                                            <label :for="`special-item-${item.id}`" class="font-medium cursor-pointer">
                                                                {{ item.name }}
                                                            </label>
                                                        </div>
                                                        <p v-if="item.description" class="mt-1 text-sm text-muted-foreground">
                                                            {{ item.description }}
                                                        </p>
                                                        <div v-if="item.items.length > 0" class="mt-3 space-y-2">
                                                            <p class="text-sm font-medium">Includes:</p>
                                                            <div class="ml-4 space-y-1">
                                                                <div v-for="subItem in item.items" :key="subItem.id" class="text-sm text-muted-foreground">
                                                                    • {{ subItem.item_name }} x{{ subItem.quantity }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="font-semibold text-green-600 dark:text-green-400">
                                                            {{ formatPrice(item.unit_price) }}
                                                        </div>
                                                        <div class="text-xs text-muted-foreground">
                                                            Custom Price
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Medical Services Selection Dialog -->
                        <Card v-if="showMedicalServiceDialog" class="border-primary">
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <CardTitle>Select Medical Services</CardTitle>
                                        <CardDescription>Choose procedures and imaging
                                            services</CardDescription>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button type="button" variant="outline" size="sm" @click="
                                            showMedicalServiceDialog = false
                                            ">
                                            Cancel
                                        </Button>
                                        <Button type="button" size="sm" @click="
                                            addSelectedMedicalServiceItems
                                        " :disabled="selectedMedicalServiceCount ===
                                                0
                                                ">
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
                                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 transform text-muted-foreground" />
                                        <Input v-model="medicalServiceSearchQuery" placeholder="Search services..."
                                            class="pl-10" />
                                    </div>
                                </div>
                                <Tabs v-model="activeMedicalServiceType">
                                    <TabsList class="grid w-full"
                                        :style="`grid-template-columns: repeat(${medicalServiceTypes.length || 1}, 1fr)`">
                                        <TabsTrigger value="All">All Services</TabsTrigger>
                                        <TabsTrigger v-for="type in medicalServiceTypes" :key="type" :value="type">
                                            {{ type }}
                                        </TabsTrigger>
                                    </TabsList>
                                    <TabsContent value="All" class="space-y-4">
                                        <div class="max-h-96 space-y-2 overflow-y-auto">
                                            <div v-for="service in filteredMedicalServices" :key="service.id"
                                                class="flex items-center space-x-2 rounded-md border p-3 hover:bg-accent">
                                                <input type="checkbox" :id="`service-item-${service.id}`" :checked="selectedMedicalServiceItems.includes(
                                                    service.id,
                                                )
                                                    " @change="
                                                        toggleMedicalServiceItem(
                                                            service.id,
                                                            (
                                                                $event.target as HTMLInputElement
                                                            ).checked,
                                                        )
                                                        "
                                                    class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                                                <label :for="`service-item-${service.id}`"
                                                    class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                                    {{ service.name }}
                                                    <span v-if="
                                                        service.description
                                                    " class="mt-1 ml-2 block text-xs text-muted-foreground">{{
                                                            service.description
                                                        }}</span>
                                                </label>
                                                <div class="text-right text-xs">
                                                    <div class="font-medium text-green-600 dark:text-green-400">
                                                        {{
                                                            formatPrice(
                                                                service.price,
                                                            )
                                                        }}
                                                    </div>
                                                    <div class="text-muted-foreground capitalize">
                                                        {{ service.type }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="
                                                filteredMedicalServices.length ===
                                                0
                                            " class="py-8 text-center text-muted-foreground">
                                                No services found matching "{{
                                                    medicalServiceSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                    <TabsContent v-for="type in medicalServiceTypes" :key="type" :value="type"
                                        class="space-y-4">
                                        <div class="max-h-96 space-y-2 overflow-y-auto">
                                            <div v-for="service in filteredMedicalServices" :key="service.id"
                                                class="flex items-center space-x-2 rounded-md border p-3 hover:bg-accent">
                                                <input type="checkbox" :id="`service-item-type-${service.id}`" :checked="selectedMedicalServiceItems.includes(
                                                    service.id,
                                                )
                                                    " @change="
                                                        toggleMedicalServiceItem(
                                                            service.id,
                                                            (
                                                                $event.target as HTMLInputElement
                                                            ).checked,
                                                        )
                                                        "
                                                    class="h-4 w-4 rounded border border-input bg-background text-primary ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                                                <label :for="`service-item-type-${service.id}`"
                                                    class="flex-1 cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                                    {{ service.name }}
                                                    <span v-if="
                                                        service.description
                                                    " class="mt-1 ml-2 block text-xs text-muted-foreground">{{
                                                            service.description
                                                        }}</span>
                                                </label>
                                                <div class="text-right text-xs">
                                                    <div class="font-medium text-green-600 dark:text-green-400">
                                                        {{
                                                            formatPrice(
                                                                service.price,
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="
                                                filteredMedicalServices.length ===
                                                0
                                            " class="py-8 text-center text-muted-foreground">
                                                No services found matching "{{
                                                    medicalServiceSearchQuery
                                                }}"
                                            </div>
                                        </div>
                                    </TabsContent>
                                </Tabs>
                            </CardContent>
                        </Card>

                        <div v-if="
                            form.order_items.length === 0 &&
                            !showLabDialog &&
                            !showRxDialog &&
                            !showMedicalServiceDialog
                        " class="rounded-lg border-2 border-dashed py-12 text-center text-muted-foreground">
                            <p class="mb-2">No items added yet</p>
                            <p class="text-sm">
                                Click the buttons above to add lab tests,
                                procedures, imaging, or supplies
                            </p>
                        </div>

                        <!-- Grouped Order Items Display -->
                        <div v-else class="space-y-6">
                            <div v-for="[type, items] in Object.entries(
                                groupedOrderItems,
                            )" :key="type" class="space-y-3">
                                <!-- Group Header -->
                                <div class="flex items-center gap-3 border-b pb-2">
                                    <component :is="getItemTypeIcon(
                                        items[0]?.panelName
                                            ? 'lab'
                                            : type,
                                    )
                                        " class="size-5 text-primary" />
                                    <h3 class="text-lg font-semibold">
                                        {{
                                            getItemTypeDisplayName(
                                                type,
                                                items[0]?.panelName,
                                            )
                                        }}
                                    </h3>
                                    <Badge variant="secondary" class="ml-auto">{{ items.length }}</Badge>
                                </div>

                                <!-- Items in this group -->
                                <div class="ml-8 space-y-2">
                                    <div v-for="itemData in items" :key="itemData.index"
                                        class="overflow-hidden rounded-lg border">
                                        <!-- Item Header -->
                                        <div class="flex cursor-pointer items-center justify-between bg-muted/30 p-3 hover:bg-muted/50"
                                            @click="
                                                toggleItemExpansion(
                                                    itemData.index,
                                                )
                                                ">
                                            <div class="flex items-center gap-3">
                                                <Button type="button" variant="ghost" size="sm" class="h-6 w-6 p-0"
                                                    @click.stop="
                                                        toggleItemExpansion(
                                                            itemData.index,
                                                        )
                                                        ">
                                                    <component :is="expandedItems.has(
                                                        itemData.index,
                                                    )
                                                            ? ChevronDown
                                                            : ChevronRight
                                                        " class="size-4" />
                                                </Button>
                                                <component :is="getStatusIcon(
                                                    itemData.item
                                                        .status ||
                                                    'pending',
                                                )
                                                    "
                                                    :class="`size-4 ${getStatusColor(itemData.item.status || 'pending')}`" />
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium">{{
                                                        itemData.item
                                                            .item_name ||
                                                        'Unnamed item'
                                                    }}</span>
                                                    <span v-if="
                                                        itemData.item
                                                            .details
                                                    " class="max-w-xs truncate text-xs text-muted-foreground">{{
                                                            itemData.item
                                                                .details
                                                        }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="text-right">
                                                    <div class="text-sm font-medium">
                                                        {{
                                                            formatPrice(
                                                                getItemPrice(
                                                                    itemData.item,
                                                                ),
                                                            )
                                                        }}
                                                    </div>
                                                    <div v-if="
                                                        itemData.item
                                                            .quantity_required >
                                                        1
                                                    " class="text-xs text-muted-foreground">
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
                                                    <Button type="button" variant="ghost" size="sm" @click.stop="
                                                        duplicateOrderItem(
                                                            itemData.index,
                                                        )
                                                        " title="Duplicate item">
                                                        <Plus class="size-4 text-muted-foreground" />
                                                    </Button>
                                                    <Button type="button" variant="ghost" size="sm" @click.stop="
                                                        removeOrderItem(
                                                            itemData.index,
                                                        )
                                                        ">
                                                        <Trash2 class="size-4 text-destructive" />
                                                    </Button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Item Content (Collapsible) -->
                                        <div v-if="
                                            expandedItems.has(
                                                itemData.index,
                                            )
                                        " class="space-y-3 border-t p-3">
                                            <!-- Lab Test Fields -->
                                            <div v-if="
                                                itemData.item.item_type ===
                                                'lab'
                                            " class="flex items-end gap-3">
                                                <div class="flex-1">
                                                    <Label class="text-xs">Test Name</Label>
                                                    <Input v-model="itemData.item
                                                            .item_name
                                                        " placeholder="Test name" readonly class="h-8 bg-muted" />
                                                </div>
                                                <div class="flex-1">
                                                    <Label class="text-xs">Details</Label>
                                                    <Input v-model="itemData.item
                                                            .details
                                                        " placeholder="Test parameters, instructions" class="h-8" />
                                                </div>
                                                <div class="w-20">
                                                    <Label class="text-xs">Qty</Label>
                                                    <Input v-model="itemData.item
                                                            .quantity_required
                                                        " type="number" min="1" placeholder="1" class="h-8" />
                                                </div>
                                                <div class="w-32">
                                                    <Label class="text-xs">Notes</Label>
                                                    <Input v-model="itemData.item.notes
                                                        " placeholder="Additional notes" class="h-8" />
                                                </div>
                                            </div>

                                            <!-- RX Medicine Fields -->
                                            <div v-if="
                                                itemData.item.item_type ===
                                                'rx_medicine'
                                            " class="space-y-2">
                                                <div class="flex items-end gap-3">
                                                    <div class="flex-1">
                                                        <Label class="text-xs">Medicine
                                                            Name</Label>
                                                        <Input v-model="itemData.item
                                                                .item_name
                                                            " placeholder="Medicine name" readonly
                                                            class="h-8 bg-muted" />
                                                    </div>
                                                    <div class="w-24">
                                                        <Label class="text-xs">Dosage</Label>
                                                        <Input v-model="itemData.item
                                                                .dosage
                                                            " placeholder="500mg" class="h-8" />
                                                    </div>
                                                    <div class="w-32">
                                                        <Label class="text-xs">Frequency</Label>
                                                        <Input v-model="itemData.item
                                                                .frequency
                                                            " placeholder="Twice daily" class="h-8" />
                                                    </div>
                                                    <div class="w-32">
                                                        <Label class="text-xs">Route</Label>
                                                        <Select v-model="itemData.item
                                                                .route
                                                            ">
                                                            <SelectTrigger class="h-8">
                                                                <SelectValue placeholder="Route" />
                                                            </SelectTrigger>
                                                            <SelectContent>
                                                                <SelectItem value="oral">Oral</SelectItem>
                                                                <SelectItem value="iv">IV</SelectItem>
                                                                <SelectItem value="im">IM</SelectItem>
                                                                <SelectItem value="subcutaneous">Subcutaneous
                                                                </SelectItem>
                                                                <SelectItem value="topical">Topical</SelectItem>
                                                                <SelectItem value="inhalation">Inhalation</SelectItem>
                                                                <SelectItem value="rectal">Rectal</SelectItem>
                                                            </SelectContent>
                                                        </Select>
                                                    </div>
                                                    <div class="w-20">
                                                        <Label class="text-xs">Qty</Label>
                                                        <Input v-model="itemData.item
                                                                .quantity_required
                                                            " type="number" min="1" placeholder="1" class="h-8" />
                                                    </div>
                                                </div>
                                                <div class="flex items-end gap-3">
                                                    <div class="flex-1">
                                                        <Label class="text-xs">Instructions</Label>
                                                        <Input v-model="itemData.item
                                                                .details
                                                            " placeholder="Special instructions" class="h-8" />
                                                    </div>
                                                    <div class="w-32">
                                                        <Label class="text-xs">Notes</Label>
                                                        <Input v-model="itemData.item
                                                                .notes
                                                            " placeholder="Additional notes" class="h-8" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Procedure Fields -->
                                            <div v-if="
                                                itemData.item.item_type ===
                                                'procedure'
                                            " class="flex items-end gap-3">
                                                <div class="flex-1">
                                                    <Label class="text-xs">Procedure Name *</Label>
                                                    <Input v-model="itemData.item
                                                            .item_name
                                                        " placeholder="Enter procedure name" class="h-8" />
                                                </div>
                                                <div class="flex-1">
                                                    <Label class="text-xs">Details</Label>
                                                    <Input v-model="itemData.item
                                                            .details
                                                        " placeholder="Procedure details, requirements" class="h-8" />
                                                </div>
                                                <div class="w-20">
                                                    <Label class="text-xs">Qty</Label>
                                                    <Input v-model="itemData.item
                                                            .quantity_required
                                                        " type="number" min="1" placeholder="1" class="h-8" />
                                                </div>
                                                <div class="w-32">
                                                    <Label class="text-xs">Notes</Label>
                                                    <Input v-model="itemData.item.notes
                                                        " placeholder="Additional notes" class="h-8" />
                                                </div>
                                            </div>

                                            <!-- Imaging Fields -->
                                            <div v-if="
                                                itemData.item.item_type ===
                                                'imaging'
                                            " class="flex items-end gap-3">
                                                <div class="flex-1">
                                                    <Label class="text-xs">Imaging Type *</Label>
                                                    <Input v-model="itemData.item
                                                            .item_name
                                                        " placeholder="e.g., Chest X-Ray, CT Scan, MRI" class="h-8" />
                                                </div>
                                                <div class="flex-1">
                                                    <Label class="text-xs">Details</Label>
                                                    <Input v-model="itemData.item
                                                            .details
                                                        " placeholder="Body part, with/without contrast, views"
                                                        class="h-8" />
                                                </div>
                                                <div class="w-20">
                                                    <Label class="text-xs">Qty</Label>
                                                    <Input v-model="itemData.item
                                                            .quantity_required
                                                        " type="number" min="1" placeholder="1" class="h-8" />
                                                </div>
                                                <div class="w-32">
                                                    <Label class="text-xs">Notes</Label>
                                                    <Input v-model="itemData.item.notes
                                                        " placeholder="Additional notes" class="h-8" />
                                                </div>
                                            </div>

                                            <!-- Supply Fields -->
                                            <div v-if="
                                                itemData.item.item_type ===
                                                'supply'
                                            " class="flex items-end gap-3">
                                                <div class="flex-1">
                                                    <Label class="text-xs">Supply Item</Label>
                                                    <Select @update:modelValue="
                                                        (val) =>
                                                            selectInventoryItem(
                                                                itemData.item,
                                                                Number(val),
                                                            )
                                                    ">
                                                        <SelectTrigger class="h-8">
                                                            <SelectValue placeholder="Select from inventory" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem v-for="invItem in inventoryItems" :key="invItem.id
                                                                " :value="invItem.id
                                                                    ">
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
                                                    <Label class="text-xs">Qty</Label>
                                                    <Input v-model="itemData.item
                                                            .quantity_required
                                                        " type="number" min="1" placeholder="1" class="h-8" />
                                                </div>
                                                <div class="w-32">
                                                    <Label class="text-xs">Notes</Label>
                                                    <Input v-model="itemData.item.notes
                                                        " placeholder="Additional notes" class="h-8" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div v-if="form.order_items.length === 0"
                    class="rounded-md border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200">
                    <div class="flex items-center gap-2">
                        <AlertCircle class="size-5" />
                        <strong>No items added</strong>
                    </div>
                    <p class="mt-1 text-sm">
                        Please add at least one item to the medical order before
                        processing.
                    </p>
                </div>

                <div class="flex gap-4">
                    <Button type="button" @click="showConfirmDialog = true" :disabled="isProcessing || isSendingToAccount || form.order_items.length === 0">
                        {{ isProcessing ? 'Processing...' : 'Confirm Process' }}
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        class="border-teal-600 text-teal-700 hover:bg-teal-50 dark:border-teal-500 dark:text-teal-400 dark:hover:bg-teal-950/20"
                        :disabled="isSendingToAccount || isProcessing || form.order_items.length === 0"
                        @click="sendToAccount"
                    >
                        <Send class="mr-2 size-4" />
                        {{ isSendingToAccount ? 'Sending...' : 'Send to Account' }}
                    </Button>
                    <Button variant="outline" as-child>
                        <a :href="show(props.medicalOrder.id).url">Cancel</a>
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
                    You do not have permission to process medical orders.
                </p>
            </div>
        </div>

        <Dialog v-model:open="showConfirmDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirm Process</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to process this medical order?
                        This action cannot be undone.
                        <br /><br />
                        <strong>Items to be processed:
                            {{ form.order_items.length }}</strong>
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showConfirmDialog = false">Cancel</Button>
                    <Button @click="submitForm" :disabled="isProcessing || form.order_items.length === 0
                        ">
                        {{ isProcessing ? 'Processing...' : 'Confirm' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
