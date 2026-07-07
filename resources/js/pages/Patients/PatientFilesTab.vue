<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { show, store } from '@/routes/patient-files';
import { useForm } from '@inertiajs/vue3';
import { Download, Eye, Plus, Trash2 } from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';
import { computed } from 'vue';
import { formatDateTime } from '@/lib/utils';

interface Props {
    patient: {
        id: number;
        patient_files?: Array<{
            id: number;
            type: string;
            medical_order_id?: number | null;
            created_at: string;
            file: {
                id: number;
                name: string;
                size: number;
                mime_type: string;
            };
        }>;
        medical_orders?: Array<{
            id: number;
            ordered_at?: string;
            orderItems?: Array<{ item_type: string }>;
        }>;
    };
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const form = useForm({
    file: null as File | null,
    patient_id: props.patient.id.toString(),
    type: 'medical_record',
    medical_order_id: '',
});

const labOrders = computed(() => {
    return (props.patient.medical_orders || [])
        .filter((order) => (order.orderItems || []).some((item) => item.item_type === 'lab'))
        .sort((a, b) => (b.ordered_at || '').localeCompare(a.ordered_at || ''));
});

// Section order and labels for the grouped files list. Lab files come first
// so lab results generated from the LabPanel workflow have their own section.
const FILE_TYPE_SECTIONS: Array<{ type: string; label: string }> = [
    { type: 'lab_result', label: 'Lab Files' },
    { type: 'medical_record', label: 'Medical Records' },
    { type: 'insurance', label: 'Insurance Documents' },
    { type: 'identification', label: 'Identification' },
    { type: 'consent_form', label: 'Consent Forms' },
    { type: 'discharge_summary', label: 'Discharge Summaries' },
];

const fileGroups = computed(() => {
    const files = props.patient.patient_files || [];
    const groups = FILE_TYPE_SECTIONS.map(({ type, label }) => ({
        type,
        label,
        files: files.filter((f) => f.type === type),
    })).filter((g) => g.files.length > 0);

    const knownTypes = new Set(FILE_TYPE_SECTIONS.map((s) => s.type));
    const other = files.filter((f) => !knownTypes.has(f.type));
    if (other.length > 0) {
        groups.push({ type: 'other', label: 'Other Documents', files: other });
    }

    return groups;
});

const submitForm = () => {
    if (!form.file) {
        form.setError('file', 'Please select a file');
        return;
    }
    if (!form.type) {
        form.setError('type', 'Please select a type');
        return;
    }
    form.post(store().url, { forceFormData: true });
};

import { router } from '@inertiajs/vue3';

function deleteFile(id: number) {
    if (confirm('Are you sure you want to delete this file?')) {
        router.delete(`/patient-files/${id}`);
    }
}
</script>

<template>
    <div class="space-y-6">
        <!-- Upload Form -->
        <div v-if="hasPermission('create_files')" class="rounded-lg border bg-card p-6">
            <h3 class="mb-4 text-lg font-medium">Upload New File</h3>
            <form @submit.prevent="submitForm" class="space-y-4">
                <FormField v-slot="{}" name="file">
                    <FormItem>
                        <FormLabel>File</FormLabel>
                        <FormControl>
                            <Input
                                type="file"
                                @change="form.file = $event.target.files[0]"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{}" name="type">
                    <FormItem>
                        <FormLabel>Type</FormLabel>
                        <Select v-model="form.type">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Select file type"
                                    />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="medical_record"
                                    >Medical Record</SelectItem
                                >
                                <SelectItem value="lab_result"
                                    >Lab Result</SelectItem
                                >
                                <SelectItem value="insurance"
                                    >Insurance Document</SelectItem
                                >
                                <SelectItem value="identification"
                                    >Identification</SelectItem
                                >
                                <SelectItem value="consent_form"
                                    >Consent Form</SelectItem
                                >
                                <SelectItem value="discharge_summary"
                                    >Discharge Summary</SelectItem
                                >
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{}" name="medical_order_id">
                    <FormItem>
                        <FormLabel>Lab Order</FormLabel>
                        <Select v-model="form.medical_order_id">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Link to lab order (optional)"
                                    />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="">None</SelectItem>
                                <SelectItem
                                    v-for="order in labOrders"
                                    :key="order.id"
                                    :value="order.id.toString()"
                                >
                                    Order #{{ order.id }}
                                    <span class="text-muted-foreground">
                                        {{ order.ordered_at?.slice(0, 10) }}
                                    </span>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <Button type="submit" :disabled="form.processing">
                    <Plus class="mr-2 size-4" />
                    Upload File
                </Button>
            </form>
        </div>

        <div v-else-if="!hasPermission('create_files')" class="rounded-lg border bg-card p-6">
            <div class="text-center">
                <h3 class="text-lg font-medium text-muted-foreground">Upload Access Denied</h3>
                <p class="text-sm text-muted-foreground">
                    You don't have permission to upload files.
                </p>
            </div>
        </div>

        <!-- Files List (grouped by type, lab files first) -->
        <div v-if="hasPermission('view_files')" class="rounded-lg border bg-card p-6">
            <h3 class="mb-4 text-lg font-medium">Patient Files</h3>

            <div v-if="fileGroups.length === 0" class="rounded-md border p-6 text-center text-muted-foreground">
                No files uploaded yet
            </div>

            <div v-for="group in fileGroups" :key="group.type" class="mb-6 last:mb-0">
                <h4 class="mb-2 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                    {{ group.label }}
                    <span class="ml-1 font-normal">({{ group.files.length }})</span>
                </h4>
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>File Name</TableHead>
                                <TableHead>Uploaded</TableHead>
                                <TableHead>Lab Order</TableHead>
                                <TableHead>Size</TableHead>
                                <TableHead>Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="item in group.files"
                                :key="item.id"
                            >
                                <TableCell>{{ item.file.name }}</TableCell>
                                <TableCell>{{ formatDateTime(item.created_at) }}</TableCell>
                                <TableCell>
                                    <template v-if="item.medical_order_id">
                                        <a
                                            :href="`/medical-orders/${item.medical_order_id}`"
                                            class="text-blue-600 hover:underline"
                                        >
                                            Order #{{ item.medical_order_id }}
                                        </a>
                                    </template>
                                    <template v-else>
                                        —
                                    </template>
                                </TableCell>
                                <TableCell>{{ item.file.size }}</TableCell>
                                <TableCell>
                                    <div class="flex gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            as-child
                                        >
                                            <a
                                                :href="`${show(item.id).url}?inline=1`"
                                                target="_blank"
                                            >
                                                <Eye class="size-4" />
                                            </a>
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            as-child
                                        >
                                            <a
                                                :href="show(item.id).url"
                                                target="_blank"
                                            >
                                                <Download class="size-4" />
                                            </a>
                                        </Button>
                                        <Button
                                            v-if="hasPermission('delete_patient_files')"
                                            variant="destructive"
                                            size="sm"
                                            @click.prevent="deleteFile(item.id)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>

        <div v-else-if="!hasPermission('view_files')" class="rounded-lg border bg-card p-6">
            <div class="text-center">
                <h3 class="text-lg font-medium text-muted-foreground">Files Access Denied</h3>
                <p class="text-sm text-muted-foreground">
                    You don't have permission to view patient files.
                </p>
            </div>
        </div>
    </div>
</template>
