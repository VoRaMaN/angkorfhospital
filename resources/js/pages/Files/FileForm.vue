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
import SearchableSelect from '@/components/SearchableSelect.vue';
import {
    store as patientStore,
    update as patientUpdate,
} from '@/routes/patient-files';
import {
    store as staffStore,
    update as staffUpdate,
} from '@/routes/staff-files';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    mode: 'create' | 'edit';
    indexRoute: string;
    item?: Record<string, any>;
    patients?: Array<Record<string, any>>;
    staff?: Array<Record<string, any>>;
    typeOptions?: Record<string, string>;
    fileExists?: boolean;
    currentStaff?: Record<string, any>;
}

const props = defineProps<Props>();

const entities = props.patients || props.staff || [];
const entityKey = props.patients ? 'patient_id' : 'staff_id';
const entityLabel = props.patients ? 'Patient' : 'Staff';

const form = useForm({
    file: null as File | null,
    [entityKey]:
        props.item?.[entityKey]?.toString() ||
        (props.currentStaff ? props.currentStaff.id.toString() : ''),
    type: props.item?.type || '',
});

const entityOptions = computed(() => entities.map(e => ({ value: e.id.toString(), label: e.name })));

const entityValue = computed({
    get: () => form[entityKey] || '',
    set: (value) => { form[entityKey] = value; }
});

const isPatient = !!props.patients;

const fileDownloadUrl = computed(() => {
    if (!props.item?.id) return '';
    const base = isPatient ? 'patient-files' : 'staff-files';
    return `/${base}/${props.item.id}/download`;
});

const filePreviewUrl = computed(
    () => `${fileDownloadUrl.value}?inline=1&t=${new Date().getTime()}`,
);

const submitForm = () => {
    if (props.mode === 'create') {
        const storeRoute = isPatient ? patientStore() : staffStore();
        form.post(storeRoute.url, { forceFormData: true });
    } else {
        const updateRoute = isPatient
            ? patientUpdate(props.item!.id)
            : staffUpdate(props.item!.id);
        form.put(updateRoute.url, { forceFormData: true });
    }
};
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div v-if="mode === 'edit' && item?.file" class="space-y-4">
            <h3 class="text-lg font-medium">Current File</h3>
            <div class="rounded-lg border p-4">
                <div class="flex items-center gap-4">
                    <div
                        v-if="
                            item.file.mime_type.startsWith('image/') &&
                            fileExists
                        "
                        class="flex-shrink-0"
                    >
                        <img
                            :src="filePreviewUrl"
                            :alt="item.file.name"
                            class="h-16 w-16 rounded object-cover"
                        />
                    </div>
                    <div
                        v-else-if="
                            item.file.mime_type.startsWith('image/') &&
                            !fileExists
                        "
                        class="flex-shrink-0"
                    >
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded bg-gray-200 text-xs text-gray-500"
                        >
                            No Preview
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">{{ item.file.name }}</p>
                        <p class="text-sm text-muted-foreground">
                            {{ item.file.size }} bytes •
                            {{ item.file.mime_type }}
                        </p>
                        <a
                            v-if="fileExists"
                            :href="fileDownloadUrl"
                            target="_blank"
                            class="text-sm text-blue-600 hover:underline"
                        >
                            Download current file
                        </a>
                        <span v-else class="text-sm text-gray-500">
                            File not found
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <FormField v-slot="{}" name="file">
            <FormItem>
                <FormLabel
                    >File{{
                        mode === 'edit' ? ' (leave empty to keep current)' : ''
                    }}</FormLabel
                >
                <FormControl>
                    <Input
                        type="file"
                        @change="form.file = $event.target.files[0]"
                    />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-if="props.patients" v-slot="{}" :name="entityKey">
            <FormItem>
                <FormLabel>{{ entityLabel }}</FormLabel>
                <SearchableSelect
                    v-model="entityValue"
                    :options="entityOptions"
                    :placeholder="`Select a ${entityLabel.toLowerCase()}`"
                />
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{}" name="type">
            <FormItem>
                <FormLabel>Type</FormLabel>
                <Select v-model="form.type">
                    <FormControl>
                        <SelectTrigger>
                            <SelectValue placeholder="Select file type" />
                        </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                        <SelectItem
                            v-for="[value, label] in Object.entries(
                                typeOptions || {},
                            )"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <FormMessage />
            </FormItem>
        </FormField>

        <div class="flex gap-4">
            <Button type="submit" :disabled="form.processing">
                {{ mode === 'create' ? 'Upload File' : 'Update File' }}
            </Button>
            <Button variant="outline" as-child>
                <a :href="indexRoute">Cancel</a>
            </Button>
        </div>
    </form>
</template>
