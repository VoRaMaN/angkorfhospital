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
import { Plus, Trash2, Edit } from 'lucide-vue-next'

interface InventoryItem {
    id: number
    item_name: string
    unit_price: number
    category: string
    type_of_supply: string
}

interface PatchRow {
    id: number
    name: string
    total_unit_price: number
    items_count: number
}

interface PatchEdit {
    id: number
    name: string
    inventory_ids: number[]
}

interface Props {
    patches: PatchRow[]
    inventories: InventoryItem[]
    patch?: PatchEdit
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Settings', href: '/settings' },
    { title: 'Patches', href: '/settings/patches' },
]

const patchForm = useForm({
    name: props.patch?.name || '',
    inventory_ids: props.patch?.inventory_ids || [] as number[],
})

const editingPatchId = ref<number | null>(props.patch?.id || null)

const selectedTotal = computed(() => {
    return props.inventories
        .filter((i) => patchForm.inventory_ids.includes(i.id))
        .reduce((acc, cur) => acc + Number(cur.unit_price || 0), 0)
})

const toggle = (list: number[], id: number) => {
    if (list.includes(id)) {
        return list.filter((v) => v !== id)
    }
    return [...list, id]
}

const submitPatch = () => {
    if (editingPatchId.value) {
        patchForm.put(`/settings/patches/${editingPatchId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                patchForm.reset()
                editingPatchId.value = null
            },
        })
    } else {
        patchForm.post('/settings/patches', {
            preserveScroll: true,
            onSuccess: () => patchForm.reset(),
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
}

const deletePatch = (patchId: number) => {
    if (confirm('Are you sure you want to delete this patch?')) {
        router.delete(`/settings/patches/${patchId}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Patches" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Patches</h1>
                    <p class="text-slate-600 dark:text-slate-300">
                        Create reusable sets of inventory items for medical orders
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="space-y-4">
                    <div class="rounded-lg border bg-card p-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold">Existing patches</h2>
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
                                        <TableHead class="text-right">Total price</TableHead>
                                        <TableHead class="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="patch in props.patches" :key="patch.id">
                                        <TableCell class="font-medium">{{ patch.name }}</TableCell>
                                        <TableCell class="text-right">{{ patch.items_count }}</TableCell>
                                        <TableCell class="text-right font-semibold">
                                            {{ patch.total_unit_price.toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                        </TableCell>
                                        <TableCell class="text-right">
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
                                    <TableRow v-if="props.patches.length === 0">
                                        <TableCell colspan="4" class="text-center text-slate-600 dark:text-slate-300">
                                            No patches yet
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <div class="rounded-lg border bg-card p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold">
                                {{ editingPatchId ? 'Edit patch' : 'Create patch' }}
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
                                <label class="text-sm font-medium">Patch Name</label>
                                <Input v-model="patchForm.name" placeholder="e.g. Diabetes starter" />
                                <div v-if="patchForm.errors.name" class="text-sm text-red-500">
                                    {{ patchForm.errors.name }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-sm font-semibold">Select inventory items ({{ props.inventories.length }} available)</div>
                            <div class="max-h-96 overflow-y-auto rounded border p-4 space-y-3">
                                <div v-if="props.inventories.length === 0" class="text-center text-slate-500 py-8">
                                    No inventory items available. Add items in the Inventory section first.
                                </div>
                                <label
                                    v-for="item in props.inventories"
                                    :key="item.id"
                                    class="flex items-center justify-between gap-3 p-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded cursor-pointer"
                                >
                                    <div class="flex items-center gap-3 flex-1">
                                        <input
                                            type="checkbox"
                                            class="size-4"
                                            :checked="patchForm.inventory_ids.includes(item.id)"
                                            @change="patchForm.inventory_ids = toggle(patchForm.inventory_ids, item.id)"
                                        />
                                        <div class="flex-1">
                                            <div class="font-medium">{{ item.item_name }}</div>
                                            <div class="text-xs text-slate-600 dark:text-slate-400">
                                                {{ item.type_of_supply }} • {{ item.category }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">
                                        {{ Number(item.unit_price).toLocaleString(undefined, { style: 'currency', currency: 'USD' }) }}
                                    </span>
                                </label>
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
                                :disabled="patchForm.processing || patchForm.inventory_ids.length === 0"
                                @click="submitPatch"
                            >
                                <Plus class="mr-2 size-4" />
                                {{ editingPatchId ? 'Update patch' : 'Save patch' }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
