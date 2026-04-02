<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { store } from '@/actions/App/Http/Controllers/MedicineGroupController';
import { ref } from 'vue';

interface RxMedicine {
    id: number;
    name: string;
    unit_price: number;
    selling_price: number;
    stock_quantity: number;
}

interface MedicineItem {
    inventory_id: number | null;
    quantity: number;
    dosage: string;
    frequency: string;
}

defineProps<{
    rxMedicines: RxMedicine[];
}>();

const formData = ref({
    name: '',
    description: '',
    custom_price: null as number | null,
    is_active: true,
    items: [] as MedicineItem[],
});

const processing = ref(false);
const errors = ref<Record<string, string>>({});

const addItem = () => {
    formData.value.items.push({
        inventory_id: null,
        quantity: 1,
        dosage: '',
        frequency: '',
    });
};

const removeItem = (index: number) => {
    formData.value.items.splice(index, 1);
};

const getMedicineName = (inventoryId: number | null, rxMedicines: RxMedicine[]) => {
    if (!inventoryId) return 'Select medicine';
    const medicine = rxMedicines.find((m) => m.id === inventoryId);
    return medicine ? medicine.name : 'Unknown';
};

const handleSubmit = () => {
    processing.value = true;
    errors.value = {};

    router.post(store.url(), formData.value, {
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
    <AppLayout title="Create Medicine Group">
        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Create Medicine Group
                    </h2>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <form @submit.prevent="handleSubmit">
                            <div class="space-y-6">
                                <!-- Name -->
                                <div>
                                    <label
                                        for="name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Group Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        v-model="formData.name"
                                        type="text"
                                        name="name"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                    />
                                    <p v-if="errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ errors.name }}
                                    </p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label
                                        for="description"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="formData.description"
                                        name="description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                    ></textarea>
                                    <p v-if="errors.description" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ errors.description }}
                                    </p>
                                </div>

                                <!-- Custom Price -->
                                <div>
                                    <label
                                        for="custom_price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Custom Price (Optional)
                                    </label>
                                    <input
                                        id="custom_price"
                                        v-model.number="formData.custom_price"
                                        type="number"
                                        name="custom_price"
                                        step="0.01"
                                        min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                        placeholder="Leave empty to calculate from items"
                                    />
                                    <p v-if="errors.custom_price" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ errors.custom_price }}
                                    </p>
                                </div>

                                <!-- Is Active -->
                                <div class="flex items-center">
                                    <input
                                        id="is_active"
                                        v-model="formData.is_active"
                                        type="checkbox"
                                        name="is_active"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900"
                                    />
                                    <label
                                        for="is_active"
                                        class="ml-2 block text-sm text-gray-900 dark:text-gray-300"
                                    >
                                        Active
                                    </label>
                                </div>

                                <!-- Medicines -->
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Medicines <span class="text-red-500">*</span>
                                        </label>
                                        <button
                                            type="button"
                                            @click="addItem"
                                            class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
                                        >
                                            Add Medicine
                                        </button>
                                    </div>

                                    <p v-if="errors.items" class="mb-2 text-sm text-red-600 dark:text-red-400">
                                        {{ errors.items }}
                                    </p>

                                    <div v-if="formData.items.length === 0" class="rounded-md bg-yellow-50 p-4 dark:bg-yellow-900/20">
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                            Please add at least one medicine to this group.
                                        </p>
                                    </div>

                                    <div class="space-y-4">
                                        <div
                                            v-for="(item, index) in formData.items"
                                            :key="index"
                                            class="rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                                        >
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <!-- Medicine Select -->
                                                <div class="md:col-span-2">
                                                    <label
                                                        :for="`medicine-${index}`"
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                                    >
                                                        Medicine <span class="text-red-500">*</span>
                                                    </label>
                                                    <select
                                                        :id="`medicine-${index}`"
                                                        v-model.number="item.inventory_id"
                                                        :name="`items[${index}][inventory_id]`"
                                                        required
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                                    >
                                                        <option :value="null">Select medicine</option>
                                                        <option
                                                            v-for="medicine in rxMedicines"
                                                            :key="medicine.id"
                                                            :value="medicine.id"
                                                        >
                                                            {{ medicine.name }} - ${{ medicine.selling_price }} (Stock: {{ medicine.stock_quantity }})
                                                        </option>
                                                    </select>
                                                    <p v-if="errors[`items.${index}.inventory_id`]" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                        {{ errors[`items.${index}.inventory_id`] }}
                                                    </p>
                                                </div>

                                                <!-- Quantity -->
                                                <div>
                                                    <label
                                                        :for="`quantity-${index}`"
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                                    >
                                                        Quantity <span class="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        :id="`quantity-${index}`"
                                                        v-model.number="item.quantity"
                                                        type="number"
                                                        :name="`items[${index}][quantity]`"
                                                        min="1"
                                                        required
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                                    />
                                                    <p v-if="errors[`items.${index}.quantity`]" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                        {{ errors[`items.${index}.quantity`] }}
                                                    </p>
                                                </div>

                                                <!-- Dosage -->
                                                <div>
                                                    <label
                                                        :for="`dosage-${index}`"
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                                    >
                                                        Dosage
                                                    </label>
                                                    <input
                                                        :id="`dosage-${index}`"
                                                        v-model="item.dosage"
                                                        type="text"
                                                        :name="`items[${index}][dosage]`"
                                                        placeholder="e.g., 500mg"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                                    />
                                                </div>

                                                <!-- Frequency -->
                                                <div class="md:col-span-2">
                                                    <label
                                                        :for="`frequency-${index}`"
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                                    >
                                                        Frequency
                                                    </label>
                                                    <input
                                                        :id="`frequency-${index}`"
                                                        v-model="item.frequency"
                                                        type="text"
                                                        :name="`items[${index}][frequency]`"
                                                        placeholder="e.g., 3 times daily"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                                    />
                                                </div>
                                            </div>

                                            <!-- Remove Button -->
                                            <div class="mt-3 text-right">
                                                <button
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                >
                                                    Remove Medicine
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-end gap-3">
                                    <a
                                        href="/medicine-groups"
                                        class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-600"
                                    >
                                        Cancel
                                    </a>
                                    <button
                                        type="submit"
                                        :disabled="processing"
                                        class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50"
                                    >
                                        {{ processing ? 'Creating...' : 'Create Group' }}
                                    </button>
                                </div>
                            </div>
                        </Form>
                    </div>
                </div>
            </div>
        </div>f
    </AppLayout>
</template>
