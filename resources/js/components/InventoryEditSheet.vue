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
import { Separator } from '@/components/ui/separator';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { update as inventoryUpdate } from '@/routes/inventory';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

export interface InventoryItem {
    id: number;
    item_name: string;
    description: string | null;
    category: string | null;
    barcode: string | null;
    type_of_supply: string;
    quantity: number;
    unit: string;
    dose_unit: string | null;
    total_per_box: number | null;
    minimum_stock: number;
    unit_price: number | null;
    selling_price: number | null;
    supplier: string | null;
    location: string | null;
    expiry_date: string | null;
    alert_days: number | null;
    notes: string | null;
    status: string;
    original_quantity: number | null;
}

interface Props {
    item: InventoryItem | null;
    open: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const isOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});

const form = useForm({
    item_name: '',
    description: '',
    category: '',
    barcode: '',
    type_of_supply: '',
    quantity: 0,
    unit: '',
    dose_unit: '',
    total_per_box: null as number | null,
    minimum_stock: 0,
    unit_price: null as number | null,
    selling_price: null as number | null,
    supplier: '',
    location: '',
    expiry_date: '',
    alert_days: null as number | null,
    notes: '',
});

watch(
    () => props.item,
    (item) => {
        if (item) {
            form.item_name = item.item_name ?? '';
            form.description = item.description ?? '';
            form.category = item.category ?? '';
            form.barcode = item.barcode ?? '';
            form.type_of_supply = item.type_of_supply ?? '';
            form.quantity = item.quantity ?? 0;
            form.unit = item.unit ?? '';
            form.dose_unit = item.dose_unit ?? '';
            form.total_per_box = item.total_per_box;
            form.minimum_stock = item.minimum_stock ?? 0;
            form.unit_price = item.unit_price;
            form.selling_price = item.selling_price;
            form.supplier = item.supplier ?? '';
            form.location = item.location ?? '';
            form.expiry_date = item.expiry_date ? item.expiry_date.split('T')[0] : '';
            form.alert_days = item.alert_days;
            form.notes = item.notes ?? '';
        }
    },
    { immediate: true },
);

const submitForm = () => {
    if (!props.item) return;

    form.put(inventoryUpdate(props.item.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            isOpen.value = false;
        },
    });
};

const formatExpiryStatus = (item: InventoryItem) => {
    if (!item.expiry_date) return null;
    const expiry = new Date(item.expiry_date);
    const now = new Date();
    if (expiry < now) return 'Expired';
    const diff = Math.ceil((expiry.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
    if (diff <= (item.alert_days ?? 30)) return `Expiring in ${diff} days`;
    return null;
};
</script>

<template>
    <Sheet v-model:open="isOpen">
        <SheetContent side="right" class="w-full overflow-y-auto sm:max-w-lg">
            <SheetHeader>
                <SheetTitle>Edit Details</SheetTitle>
                <SheetDescription v-if="item">
                    {{ item.item_name }}
                </SheetDescription>
            </SheetHeader>

            <form v-if="item" @submit.prevent="submitForm" class="flex flex-col gap-6 px-1 pb-4">
                <!-- Status Overview -->
                <div class="flex flex-wrap gap-2">
                    <Badge :variant="item.status === 'In Stock' ? 'default' : 'destructive'">
                        {{ item.status }}
                    </Badge>
                    <Badge v-if="formatExpiryStatus(item)" variant="outline" class="border-amber-500 text-amber-600">
                        {{ formatExpiryStatus(item) }}
                    </Badge>
                </div>

                <!-- Pricing Section -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-muted-foreground uppercase tracking-wide">Pricing</h3>
                    <Separator />
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Unit Price ($)</label>
                            <Input v-model="form.unit_price" type="number" step="0.01" min="0" placeholder="0.00" />
                            <p v-if="form.errors.unit_price" class="text-sm text-red-600">{{ form.errors.unit_price }}</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Selling Price ($)</label>
                            <Input v-model="form.selling_price" type="number" step="0.01" min="0" placeholder="0.00" />
                            <p v-if="form.errors.selling_price" class="text-sm text-red-600">{{ form.errors.selling_price }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stock Section -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-muted-foreground uppercase tracking-wide">Stock</h3>
                    <Separator />
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Quantity</label>
                            <Input v-model="form.quantity" type="number" min="0" />
                            <p v-if="form.errors.quantity" class="text-sm text-red-600">{{ form.errors.quantity }}</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Min. Stock</label>
                            <Input v-model="form.minimum_stock" type="number" min="0" />
                            <p v-if="form.errors.minimum_stock" class="text-sm text-red-600">{{ form.errors.minimum_stock }}</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Unit</label>
                            <Select v-model="form.unit">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select unit" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="pieces">Pieces</SelectItem>
                                    <SelectItem value="boxes">Boxes</SelectItem>
                                    <SelectItem value="bottles">Bottles</SelectItem>
                                    <SelectItem value="packs">Packs</SelectItem>
                                    <SelectItem value="kg">Kilograms</SelectItem>
                                    <SelectItem value="liters">Liters</SelectItem>
                                    <SelectItem value="tablets">Tablets</SelectItem>
                                    <SelectItem value="capsules">Capsules</SelectItem>
                                    <SelectItem value="vials">Vials</SelectItem>
                                    <SelectItem value="tubes">Tubes</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Total per Box</label>
                            <Input v-model="form.total_per_box" type="number" min="0" placeholder="—" />
                            <p v-if="form.errors.total_per_box" class="text-sm text-red-600">{{ form.errors.total_per_box }}</p>
                        </div>
                    </div>
                </div>

                <!-- Expiration Section -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-muted-foreground uppercase tracking-wide">Expiration</h3>
                    <Separator />
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Expiry Date</label>
                            <Input v-model="form.expiry_date" type="date" />
                            <p v-if="form.errors.expiry_date" class="text-sm text-red-600">{{ form.errors.expiry_date }}</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Alert Days</label>
                            <Input v-model="form.alert_days" type="number" min="0" placeholder="30" />
                            <p v-if="form.errors.alert_days" class="text-sm text-red-600">{{ form.errors.alert_days }}</p>
                        </div>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-muted-foreground uppercase tracking-wide">Details</h3>
                    <Separator />
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Item Name</label>
                            <Input v-model="form.item_name" placeholder="Item name" />
                            <p v-if="form.errors.item_name" class="text-sm text-red-600">{{ form.errors.item_name }}</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Category</label>
                            <Input v-model="form.category" placeholder="Category" />
                            <p v-if="form.errors.category" class="text-sm text-red-600">{{ form.errors.category }}</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Barcode</label>
                            <Input v-model="form.barcode" placeholder="Barcode" />
                            <p v-if="form.errors.barcode" class="text-sm text-red-600">{{ form.errors.barcode }}</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Dose Unit</label>
                            <Input v-model="form.dose_unit" placeholder="e.g. mg, ml" />
                            <p v-if="form.errors.dose_unit" class="text-sm text-red-600">{{ form.errors.dose_unit }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Supplier</label>
                        <Input v-model="form.supplier" placeholder="Supplier name" />
                        <p v-if="form.errors.supplier" class="text-sm text-red-600">{{ form.errors.supplier }}</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Location</label>
                        <Input v-model="form.location" placeholder="Storage location" />
                        <p v-if="form.errors.location" class="text-sm text-red-600">{{ form.errors.location }}</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Notes</label>
                        <Textarea v-model="form.notes" placeholder="Additional notes" rows="3" />
                        <p v-if="form.errors.notes" class="text-sm text-red-600">{{ form.errors.notes }}</p>
                    </div>
                </div>

                <SheetFooter class="gap-2">
                    <SheetClose as-child>
                        <Button variant="outline" type="button">Cancel</Button>
                    </SheetClose>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </SheetFooter>
            </form>
        </SheetContent>
    </Sheet>
</template>
