<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Plus, Trash2, Edit, Search, ChevronDown, ChevronUp } from 'lucide-vue-next'

interface InventoryItem {
    id: number
    item_name: string
    unit_price: number
    category: string
    type_of_supply: string
    quantity?: number
}

interface PatchRow {
    id: number
    name: string
    custom_price: number | null
    total_unit_price: number
    items_count: number
    items: InventoryItem[]
}

interface InventoryItemWithQty {
    id: number
    quantity: number
}

interface PatchEdit {
    id: number
    name: string
    custom_price: number | null
    inventory_items: InventoryItemWithQty[]
}

interface Props {
    patches: PatchRow[]
    inventories: InventoryItem[]
    patch?: PatchEdit
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Settings', href: '/settings' },
    { title: 'Packages', href: '/settings/patches' },
]

const patchForm = useForm({
    name: props.patch?.name || '',
    custom_price: props.patch?.custom_price || null as number | null,
    inventory_items: props.patch?.inventory_items || [] as InventoryItemWithQty[],
})

const editingPatchId = ref<number | null>(props.patch?.id || null)
const searchQuery = ref('')
const expandedPatchIds = ref<Set<number>>(new Set())

const togglePatchExpand = (patchId: number) => {
    if (expandedPatchIds.value.has(patchId)) {
        expandedPatchIds.value.delete(patchId)
    } else {
        expandedPatchIds.value.add(patchId)
    }
}

const isPatchExpanded = (patchId: number) => expandedPatchIds.value.has(patchId)

const filteredInventories = computed(() => {
    if (!searchQuery.value) return props.inventories
    const query = searchQuery.value.toLowerCase()
    return props.inventories.filter(item =>
        item.item_name.toLowerCase().includes(query) ||
        item.category.toLowerCase().includes(query) ||
        item.type_of_supply.toLowerCase().includes(query)
    )
})

const selectedTotal = computed(() => {
    if (patchForm.custom_price) {
        return Number(patchForm.custom_price)
    }
    return patchForm.inventory_items.reduce((acc, item) => {
        const inventory = props.inventories.find(i => i.id === item.id)
        if (inventory) {
            return acc + (Number(inventory.unit_price || 0) * item.quantity)
        }
        return acc
    }, 0)
})

const isItemSelected = (itemId: number) => {
    return patchForm.inventory_items.some(item => item.id === itemId)
}

const getItemQuantity = (itemId: number) => {
    const item = patchForm.inventory_items.find(item => item.id === itemId)
    return item?.quantity || 1
}

const toggleItem = (itemId: number) => {
    if (isItemSelected(itemId)) {
        patchForm.inventory_items = patchForm.inventory_items.filter(item => item.id !== itemId)
    } else {
        patchForm.inventory_items.push({ id: itemId, quantity: 1 })
    }
}

const updateQuantity = (itemId: number, quantity: number) => {
    const item = patchForm.inventory_items.find(item => item.id === itemId)
    if (item) {
        item.quantity = Math.max(1, quantity)
    }
}

const submitPatch = () => {
    if (editingPatchId.value) {
        patchForm.put(`/settings/patches/${editingPatchId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                patchForm.reset()
                editingPatchId.value = null
                searchQuery.value = ''
            },
        })
    } else {
        patchForm.post('/settings/patches', {
            preserveScroll: true,
            onSuccess: () => {
                patchForm.reset()
                searchQuery.value = ''
            },
        })
    }
}

const editPatch = (patch: PatchRow) => {
    router.get(`/settings/patches/${patch.id}/edit`, {}, {
        preserveScroll: true,
    })
}

const cancelEdit = () => {
    patchForm.reset()
    editingPatchId.value = null
    searchQuery.value = ''
}

const deletePatch = (patchId: number) => {
    if (confirm('Are you sure you want to delete this package?')) {
        router.delete(`/settings/patches/${patchId}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Packages" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Packages</h1>
                    <p class="text-slate-600 dark:text-slate-300">
                        Create reusable sets of inventory items for medical orders
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="space-y-4">
                    <div class="rounded-lg border bg-card p-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold">Existing packages</h2>
                            <span class="text-sm text-slate-600 dark:text-slate-300">
                                Total: {{ props.patches.length }}
                            </span>
                        </div>
                        <div class="mt-3 rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead class="text-right">Items</TableHead>
                                        <TableHead class="text-right">Custom Price</TableHead>
                                        <TableHead class="text-right">Total price</TableHead>
                                        <TableHead class="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <template v-for="patch in props.patches" :key="patch.id">
                                        <TableRow class="cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-900" @click="togglePatchExpand(patch.id)">
                                            <TableCell class="font-medium">
                                                <div class="flex items-center gap-2">
                                                    <ChevronDown v-if="!isPatchExpanded(patch.id)" class="size-4 text-slate-500" />
                                                    <ChevronUp v-else class="size-4 text-slate-500" />
                                                    {{ patch.name }}
                                                </div>
                                            </TableCell>
                                            <TableCell class="text-right">{{ patch.items_count }}</TableCell>
                                            <TableCell class="text-right">
                                                <span v-if="patch.custom_price" class="font-semibold text-blue-600">
                                                    {{ Number(patch.custom_price).toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                                </span>
                                                <span v-else class="text-slate-400">-</span>
                                            </TableCell>
                                            <TableCell class="text-right font-semibold">
                                                {{ patch.total_unit_price.toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                            </TableCell>
                                            <TableCell class="text-right" @click.stop>
                                                <div class="flex items-center justify-end gap-2">
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="editPatch(patch)"
                                                    >
                                                        <Edit class="size-4" />
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="deletePatch(patch.id)"
                                                    >
                                                        <Trash2 class="size-4 text-red-500" />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                        <TableRow v-if="isPatchExpanded(patch.id)" class="bg-slate-50 dark:bg-slate-900">
                                            <TableCell colspan="5" class="py-0">
                                                <div class="px-4 py-3 space-y-2">
                                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                                        Package Items:
                                                    </div>
                                                    <div class="space-y-1">
                                                        <div
                                                            v-for="item in patch.items"
                                                            :key="item.id"
                                                            class="flex items-center justify-between py-2 px-3 bg-white dark:bg-slate-800 rounded border"
                                                        >
                                                            <div class="flex-1">
                                                                <div class="font-medium text-sm">{{ item.item_name }}</div>
                                                                <div class="text-xs text-slate-600 dark:text-slate-400">
                                                                    {{ item.type_of_supply }} • {{ item.category }}
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-4">
                                                                <div class="text-sm text-slate-600 dark:text-slate-400">
                                                                    Qty: <span class="font-semibold">{{ item.quantity }}</span>
                                                                </div>
                                                                <div class="text-sm text-slate-600 dark:text-slate-400">
                                                                    Unit: {{ Number(item.unit_price).toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                                                </div>
                                                                <div class="font-semibold text-slate-700 dark:text-slate-300 w-24 text-right">
                                                                    {{ (Number(item.unit_price) * (item.quantity || 1)).toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                    <TableRow v-if="props.patches.length === 0">
                                        <TableCell colspan="5" class="text-center text-slate-600 dark:text-slate-300">
                                            No packages yet
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <div class="rounded-lg border bg-card p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold">
                                {{ editingPatchId ? 'Edit package' : 'Create package' }}
                            </h2>
                            <div class="text-sm text-slate-600 dark:text-slate-300">
                                Selected total:
                                <span class="font-semibold">
                                    {{ selectedTotal.toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                </span>
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Package Name</label>
                                <Input v-model="patchForm.name" placeholder="e.g. Diabetes starter" />
                                <div v-if="patchForm.errors.name" class="text-sm text-red-500">
                                    {{ patchForm.errors.name }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Custom Price (Optional)</label>
                                <Input
                                    v-model.number="patchForm.custom_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="Leave empty to auto-calculate"
                                />
                                <div v-if="patchForm.errors.custom_price" class="text-sm text-red-500">
                                    {{ patchForm.errors.custom_price }}
                                </div>
                                <p class="text-xs text-slate-500">
                                    If set, this price will override the calculated total
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold">
                                    Select inventory items ({{ filteredInventories.length }} available)
                                </div>
                                <div class="relative w-64">
                                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                                    <Input
                                        v-model="searchQuery"
                                        placeholder="Search items..."
                                        class="pl-9"
                                    />
                                </div>
                            </div>
                            <div class="max-h-96 overflow-y-auto rounded border p-4 space-y-3">
                                <div v-if="filteredInventories.length === 0" class="text-center text-slate-500 py-8">
                                    No inventory items found
                                </div>
                                <label
                                    v-for="item in filteredInventories"
                                    :key="item.id"
                                    class="flex items-center justify-between gap-3 p-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded cursor-pointer"
                                >
                                    <div class="flex items-center gap-3 flex-1">
                                        <input
                                            type="checkbox"
                                            class="size-4"
                                            :checked="isItemSelected(item.id)"
                                            @change="toggleItem(item.id)"
                                        />
                                        <div class="flex-1">
                                            <div class="font-medium">{{ item.item_name }}</div>
                                            <div class="text-xs text-slate-600 dark:text-slate-400">
                                                {{ item.type_of_supply }} • {{ item.category }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div v-if="isItemSelected(item.id)" class="flex items-center gap-2">
                                            <label class="text-xs text-slate-600">QTY:</label>
                                            <Input
                                                type="number"
                                                min="1"
                                                :value="getItemQuantity(item.id)"
                                                @input="(e) => updateQuantity(item.id, parseInt((e.target as HTMLInputElement).value))"
                                                class="w-20 h-8"
                                                @click.stop
                                            />
                                        </div>
                                        <span class="font-semibold text-slate-700 dark:text-slate-300 w-20 text-right">
                                            {{ Number(item.unit_price).toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div v-if="patchForm.inventory_items.length > 0" class="space-y-2">
                            <div class="text-sm font-semibold">
                                Selected Items Preview ({{ patchForm.inventory_items.length }})
                            </div>
                            <div class="rounded border bg-slate-50 dark:bg-slate-900 p-4 space-y-2">
                                <div
                                    v-for="selectedItem in patchForm.inventory_items"
                                    :key="selectedItem.id"
                                    class="flex items-center justify-between py-2 border-b last:border-b-0"
                                >
                                    <div class="flex-1">
                                        <div class="font-medium">
                                            {{ props.inventories.find(i => i.id === selectedItem.id)?.item_name }}
                                        </div>
                                        <div class="text-xs text-slate-600 dark:text-slate-400">
                                            {{ props.inventories.find(i => i.id === selectedItem.id)?.type_of_supply }} •
                                            {{ props.inventories.find(i => i.id === selectedItem.id)?.category }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            Qty: <span class="font-semibold">{{ selectedItem.quantity }}</span>
                                        </div>
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            Unit: {{ Number(props.inventories.find(i => i.id === selectedItem.id)?.unit_price || 0).toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                        </div>
                                        <div class="font-semibold text-slate-700 dark:text-slate-300 w-24 text-right">
                                            {{ (Number(props.inventories.find(i => i.id === selectedItem.id)?.unit_price || 0) * selectedItem.quantity).toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-4 pt-2 border-t-2 border-slate-300 dark:border-slate-700">
                                    <div class="text-sm font-semibold">
                                        {{ patchForm.custom_price ? 'Custom Price:' : 'Calculated Total:' }}
                                    </div>
                                    <div class="text-lg font-bold text-slate-900 dark:text-slate-100 w-24 text-right">
                                        {{ selectedTotal.toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button
                                v-if="editingPatchId"
                                variant="outline"
                                @click="cancelEdit"
                            >
                                Cancel
                            </Button>
                            <Button
                                :disabled="patchForm.processing || patchForm.inventory_items.length === 0"
                                @click="submitPatch"
                            >
                                <Plus class="mr-2 size-4" />
                                {{ editingPatchId ? 'Update package' : 'Save package' }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
