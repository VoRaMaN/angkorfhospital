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
    store as inventoryStore,
} from '@/routes/inventory';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';

interface Props {
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
        title: 'Create',
        href: '#',
    },
];

const form = useForm({
    item_name: '',
    description: '',
    quantity: 0,
    unit: 'pieces',
    minimum_stock: 10,
    type_of_supply: '',
    supplier: '',
    cost_per_unit: 0,
    expiry_date: '',
});
</script>

<template>
    <Head title="Create Inventory Item" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="hasPermission('create_inventories')"
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
                    <h1 class="text-2xl font-bold">Create Inventory Item</h1>
                    <p class="text-muted-foreground">
                        Add a new item to inventory
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form
                    @submit.prevent="form.post(inventoryStore().url)"
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
                                        >Pieces (ដុំ)</SelectItem
                                    >
                                    <SelectItem value="boxes">Boxes (ប្រអប់)</SelectItem>
                                    <SelectItem value="bottles"
                                        >Bottles (ដប)</SelectItem
                                    >
                                    <SelectItem value="packs">Packs (កញ្ចប់)</SelectItem>
                                    <SelectItem value="kg"
                                        >Kilograms (គីឡូក្រាម)</SelectItem
                                    >
                                    <SelectItem value="liters"
                                        >Liters (លីត្រ)</SelectItem
                                    >
                                    <SelectItem value="ompul"
                                        >Ompul (អំពូល)</SelectItem
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
                            <label class="text-sm font-medium"
                                >Cost per Unit ($)</label
                            >
                            <Input
                                v-model="form.cost_per_unit"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                            <p
                                v-if="form.errors.cost_per_unit"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.cost_per_unit }}
                            </p>
                        </div>

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
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Create Item
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
                    You don't have permission to create inventory items.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
