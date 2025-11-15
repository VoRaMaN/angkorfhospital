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
import { Download, Eye, Plus } from 'lucide-vue-next';

interface Props {
    patient: {
        id: number;
        patient_files?: Array<{
            id: number;
            type: string;
            file: {
                id: number;
                name: string;
                size: number;
                mime_type: string;
            };
        }>;
    };
}

const props = defineProps<Props>();

const form = useForm({
    file: null as File | null,
    patient_id: props.patient.id.toString(),
    type: 'medical_record',
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
</script>

<template>
    <div class="space-y-6">
        <!-- Upload Form -->
        <div class="rounded-lg border bg-card p-6">
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

                <Button type="submit" :disabled="form.processing">
                    <Plus class="mr-2 size-4" />
                    Upload File
                </Button>
            </form>
        </div>

        <!-- Files List -->
        <div class="rounded-lg border bg-card p-6">
            <h3 class="mb-4 text-lg font-medium">Patient Files</h3>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>File Name</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Size</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="item in props.patient.patient_files"
                            :key="item.id"
                        >
                            <TableCell>{{ item.file.name }}</TableCell>
                            <TableCell>{{ item.type }}</TableCell>
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
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-if="
                                !props.patient.patient_files ||
                                props.patient.patient_files.length === 0
                            "
                        >
                            <TableCell
                                colspan="4"
                                class="text-center text-muted-foreground"
                            >
                                No files uploaded yet
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </div>
</template>
