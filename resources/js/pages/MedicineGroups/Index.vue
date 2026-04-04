<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { index, create, edit, destroy } from '@/actions/App/Http/Controllers/MedicineGroupController';

interface MedicineGroupItem {
    medicine_name: string;
    quantity: number;
    dosage: string | null;
    frequency: string | null;
}

interface MedicineGroup {
    id: number;
    name: string;
    description: string | null;
    custom_price: number | null;
    total_price: number;
    is_active: boolean;
    item_count: number;
    items: MedicineGroupItem[];
}

defineProps<{
    medicineGroups: MedicineGroup[];
}>();

const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this special item group?')) {
        router.delete(destroy.url(id));
    }
};
</script>

<template>
    <AppLayout title="Special Items">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Special Items
                    </h2>
                    <a
                        :href="create.url()"
                        class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        Add New Group
                    </a>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Group Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Description
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Items & Details
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Price
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                <tr v-for="group in medicineGroups" :key="group.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ group.name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ group.description || '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div class="space-y-1">
                                            <div v-for="(item, index) in group.items" :key="index" class="text-xs">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ item.medicine_name }}</span>
                                                <span class="text-gray-500 dark:text-gray-400">
                                                    - Qty: {{ item.quantity }}
                                                    <template v-if="item.dosage">, {{ item.dosage }}</template>
                                                    <template v-if="item.frequency">, {{ item.frequency }}</template>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div v-if="group.custom_price" class="text-green-600 dark:text-green-400">
                                            ${{ group.custom_price.toFixed(2) }} (Custom)
                                        </div>
                                        <div v-else>
                                            ${{ group.total_price.toFixed(2) }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span
                                            :class="
                                                group.is_active
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                            "
                                            class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                        >
                                            {{ group.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <a
                                            :href="edit.url(group.id)"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            Edit
                                        </a>
                                        <button
                                            type="button"
                                            @click="handleDelete(group.id)"
                                            class="ml-4 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div
                            v-if="medicineGroups.length === 0"
                            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
                        >
                            No special items found. Create one to get started.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
