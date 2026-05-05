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
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as inventoryIndex,
    update as inventoryUpdate,
} from '@/routes/inventory';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';

interface Props {
    item: {
        id: number;
        item_name: string;
        description: string;
        category: string;
        barcode: string;
        quantity: number;
        unit: string;
        dose_unit: string;
        total_per_box: number;
        minimum_stock: number;
        type_of_supply: string;
        unit_price: number;
        selling_price: number;
        supplier: string;
        location: string;
        expiry_date: string;
        alert_days: number;
        notes: string;
    };
    typesOfSupply: Record<string, string>;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: inventoryIndex().url,
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const form = useForm({
    item_name: props.item.item_name,
    description: props.item.description,
    category: props.item.category ?? '',
    barcode: props.item.barcode ?? '',
    quantity: props.item.quantity,
    unit: props.item.unit,
    dose_unit: props.item.dose_unit ?? '',
    total_per_box: props.item.total_per_box,
    minimum_stock: props.item.minimum_stock,
    type_of_supply: props.item.type_of_supply,
    unit_price: props.item.unit_price,
    selling_price: props.item.selling_price,
    supplier: props.item.supplier,
    location: props.item.location ?? '',
    expiry_date: props.item.expiry_date ? props.item.expiry_date.split('T')[0] : '',
    alert_days: props.item.alert_days,
    notes: props.item.notes ?? '',
});
</script>

<template>
    <Head title="Edit Inventory Item" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('edit_inventories')"
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="inventoryIndex().url">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Inventory Item</h1>
                    <p class="text-muted-foreground">Update item information</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form
                    @submit.prevent="
                        form.put(inventoryUpdate(props.item.id).url)
                    "
                    class="space-y-6"
                >
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Item Name</label>
                        <Input
                            v-model="form.item_name"
                            placeholder="Enter item name"
                        />
                        <p
                            v-if="form.errors.item_name"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.item_name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Description</label>
                        <Textarea
                            v-model="form.description"
                            placeholder="Enter item description"
                        />
                        <p
                            v-if="form.errors.description"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Quantity</label>
                            <Input
                                v-model="form.quantity"
                                type="number"
                                min="0"
                            />
                            <p
                                v-if="form.errors.quantity"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.quantity }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Unit</label>
                            <Select v-model="form.unit">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select unit" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="pieces"
                                        >Pieces</SelectItem
                                    >
                                    <SelectItem value="boxes">Boxes</SelectItem>
                                    <SelectItem value="bottles"
                                        >Bottles</SelectItem
                                    >
                                    <SelectItem value="packs">Packs</SelectItem>
                                    <SelectItem value="kg"
                                        >Kilograms</SelectItem
                                    >
                                    <SelectItem value="liters"
                                        >Liters</SelectItem
                                    >
                                    <SelectItem value="ompul"
                                        >Ompul</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Min Stock</label>
                            <Input
                                v-model="form.minimum_stock"
                                type="number"
                                min="0"
                            />
                            <p
                                v-if="form.errors.minimum_stock"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.minimum_stock }}
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium"
                                >Type of Supply</label
                            >
                            <Select v-model="form.type_of_supply">
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Select type of supply"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="(
                                            label, value
                                        ) in props.typesOfSupply"
                                        :key="value"
                                        :value="value"
                                    >
                                        {{ label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p
                                v-if="form.errors.type_of_supply"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.type_of_supply }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Supplier</label>
                            <Input
                                v-model="form.supplier"
                                placeholder="Enter supplier name"
                            />
                            <p
                                v-if="form.errors.supplier"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.supplier }}
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Category</label>
                            <Input
                                v-model="form.category"
                                placeholder="e.g. Plastic Ware, Culture Medium"
                            />
                            <p
                                v-if="form.errors.category"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.category }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Barcode</label>
                            <Input
                                v-model="form.barcode"
                                placeholder="Enter barcode"
                            />
                            <p
                                v-if="form.errors.barcode"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.barcode }}
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Dose Unit</label>
                            <Input
                                v-model="form.dose_unit"
                                placeholder="e.g. mg, ml"
                            />
                            <p
                                v-if="form.errors.dose_unit"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.dose_unit }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Total per Box</label>
                            <Input
                                v-model="form.total_per_box"
                                type="number"
                                min="0"
                                placeholder="—"
                            />
                            <p
                                v-if="form.errors.total_per_box"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.total_per_box }}
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium"
                                >Unit Price ($)</label
                            >
                            <Input
                                v-model="form.unit_price"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                            <p
                                v-if="form.errors.unit_price"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.unit_price }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium"
                                >Selling Price ($)</label
                            >
                            <Input
                                v-model="form.selling_price"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                            <p
                                v-if="form.errors.selling_price"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.selling_price }}
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium"
                                >Expiry Date</label
                            >
                            <Input v-model="form.expiry_date" type="date" />
                            <p
                                v-if="form.errors.expiry_date"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.expiry_date }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Alert Days</label>
                            <Input
                                v-model="form.alert_days"
                                type="number"
                                min="0"
                                placeholder="30"
                            />
                            <p
                                v-if="form.errors.alert_days"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.alert_days }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Location</label>
                        <Input
                            v-model="form.location"
                            placeholder="Storage location"
                        />
                        <p
                            v-if="form.errors.location"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.location }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Notes</label>
                        <Textarea
                            v-model="form.notes"
                            placeholder="Additional notes"
                            rows="3"
                        />
                        <p
                            v-if="form.errors.notes"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Update Item
                        </Button>
                        <Button variant="outline" as-child>
                            <a :href="inventoryIndex().url">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-destructive">Access Denied</h2>
                <p class="text-muted-foreground">
                    You don't have permission to edit inventory items.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
