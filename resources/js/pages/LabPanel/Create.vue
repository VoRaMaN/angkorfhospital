<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Trash2 } from 'lucide-vue-next';
import { index as labPanelIndex, store as labPanelStore } from '@/routes/lab-panels';

interface Props {
    labSupplies: Array<{
        id: number;
        item_name: string;
        unit: string;
        quantity: number;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lab Panels',
        href: labPanelIndex().url,
    },
    {
        title: 'Create',
        href: '#',
    },
];

const form = useForm({
    name: '',
    description: '',
    price: 0,
    is_active: '1',
    inventory_items: [] as Array<{
        inventory_id: number;
        quantity_required: number;
        notes: string;
    }>,
});

const addInventoryItem = () => {
    form.inventory_items.push({
        inventory_id: 0,
        quantity_required: 1,
        notes: '',
    });
};

const removeInventoryItem = (index: number) => {
    form.inventory_items.splice(index, 1);
};

const getAvailableSupplies = () => {
    const selectedIds = form.inventory_items.map(item => item.inventory_id);
    return props.labSupplies.filter(supply => !selectedIds.includes(supply.id));
};
</script>

<template>

    <Head title="Create Lab Panel" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="labPanelIndex().url">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Create Lab Panel</h1>
                    <p class="text-muted-foreground">Create a new laboratory test panel</p>
                </div>
            </div>

            <div class="max-w-4xl">
                <form @submit.prevent="form.post(labPanelStore().url)" class="space-y-6">
                    <!-- Basic Information -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Panel Information</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Panel Name</label>
                                    <Input v-model="form.name" placeholder="Enter panel name" />
                                    <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Price ($)</label>
                                    <Input v-model="form.price" type="number" step="0.01" min="0" />
                                    <p v-if="form.errors.price" class="text-sm text-red-600">{{ form.errors.price }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Description</label>
                                <Textarea v-model="form.description" placeholder="Enter panel description" />
                                <p v-if="form.errors.description" class="text-sm text-red-600">{{
                                    form.errors.description }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Status</label>
                                <Select v-model="form.is_active">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">Active</SelectItem>
                                        <SelectItem value="0">Inactive</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Inventory Items -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Inventory Items</CardTitle>
                                <Button type="button" variant="outline" @click="addInventoryItem">
                                    <Plus class="size-4 mr-2" />
                                    Add Item
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div v-if="form.inventory_items.length === 0"
                                class="text-center py-8 text-muted-foreground">
                                No inventory items added yet. Click "Add Item" to get started.
                            </div>

                            <div v-else class="space-y-4">
                                <div v-for="(item, index) in form.inventory_items" :key="index"
                                    class="border rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-4">
                                        <h4 class="font-medium">Item {{ index + 1 }}</h4>
                                        <Button type="button" variant="outline" size="sm"
                                            @click="removeInventoryItem(index)">
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-3">
                                        <div class="space-y-2">
                                            <label class="text-sm font-medium">Inventory Item</label>
                                            <Select v-model="item.inventory_id">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select item" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="supply in getAvailableSupplies()"
                                                        :key="supply.id" :value="supply.id">
                                                        {{ supply.item_name }} ({{ supply.quantity }} {{ supply.unit }}
                                                        available)
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-sm font-medium">Quantity Required</label>
                                            <Input v-model="item.quantity_required" type="number" min="1" />
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-sm font-medium">Notes</label>
                                            <Input v-model="item.notes" placeholder="Optional notes" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p v-if="form.errors['inventory_items']" class="text-sm text-red-600 mt-2">{{
                                form.errors['inventory_items'] }}</p>
                        </CardContent>
                    </Card>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Create Panel
                        </Button>
                        <Button variant="outline" as-child>
                            <a :href="labPanelIndex().url">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>