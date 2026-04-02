<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { update } from '@/actions/App/Http/Controllers/SpecialItemController';
import { ref } from 'vue';

interface InventoryItem {
    id: number;
    name: string;
    type: string;
    unit: string;
}

interface SpecialItemSubItem {
    id?: number;
    inventory_id: number;
    inventory_name?: string;
    quantity: number;
}

interface SpecialItem {
    id: number;
    name: string;
    description: string | null;
    unit_price: number;
    is_active: boolean;
    items: SpecialItemSubItem[];
}

const props = defineProps<{
    specialItem: SpecialItem;
    inventoryItems: InventoryItem[];
}>();

const formData = ref({
    name: props.specialItem.name,
    description: props.specialItem.description || '',
    unit_price: props.specialItem.unit_price,
    is_active: props.specialItem.is_active,
    items: props.specialItem.items.map(item => ({
        inventory_id: item.inventory_id,
        quantity: item.quantity,
    })),
});

const processing = ref(false);
const errors = ref<Record<string, string>>({});

const addItem = () => {
    formData.value.items.push({
        inventory_id: null as any,
        quantity: 1,
    });
};

const removeItem = (index: number) => {
    formData.value.items.splice(index, 1);
};

const handleSubmit = () => {
    processing.value = true;
    errors.value = {};

    router.patch(update.url(props.specialItem.id), formData.value, {
        onError: (pageErrors) => {
            errors.value = pageErrors;
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <AppLayout title="Edit Special Item">
        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Edit Special Item
                    </h2>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <form @submit.prevent="handleSubmit">
                            <div class="space-y-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Item Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        v-model="formData.name"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                    />
                                    <p v-if="errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ errors.name }}
                                    </p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="formData.description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                    ></textarea>
                                </div>

                                <!-- Price -->
                                <div>
                                    <label for="unit_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Price <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="unit_price"
                                        v-model.number="formData.unit_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                    />
                                </div>

                                <!-- Is Active -->
                                <div class="flex items-center">
                                    <input
                                        id="is_active"
                                        v-model="formData.is_active"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                        Active
                                    </label>
                                </div>

                                <!-- Items -->
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Inventory Items (Optional)
                                        </label>
                                        <button
                                            type="button"
                                            @click="addItem"
                                            class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
                                        >
                                            Add Item
                                        </button>
                                    </div>

                                    <div class="space-y-4">
                                        <div
                                            v-for="(item, index) in formData.items"
                                            :key="index"
                                            class="rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                                        >
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Inventory Item
                                                    </label>
                                                    <select
                                                        v-model="item.inventory_id"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                                    >
                                                        <option :value="null">Select item</option>
                                                        <option
                                                            v-for="inventoryItem in inventoryItems"
                                                            :key="inventoryItem.id"
                                                            :value="inventoryItem.id"
                                                        >
                                                            {{ inventoryItem.name }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Quantity
                                                    </label>
                                                    <input
                                                        v-model.number="item.quantity"
                                                        type="number"
                                                        min="1"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                                    />
                                                </div>
                                            </div>

                                            <div class="mt-3 flex justify-end">
                                                <button
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="text-sm text-red-600 hover:text-red-900"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="flex justify-end gap-3">
                                    <a
                                        href="/special-items"
                                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                    >
                                        Cancel
                                    </a>
                                    <button
                                        type="submit"
                                        :disabled="processing"
                                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
                                    >
                                        {{ processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
