<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FormControl, FormItem, FormLabel, FormMessage, FormField } from '@/components/ui/form';
import { useForm } from '@inertiajs/vue3';

interface Props {
    mode: 'create' | 'edit';
    indexRoute: string;
    item?: Record<string, any>;
    patients?: Array<Record<string, any>>;
    staff?: Array<Record<string, any>>;
    typeOptions?: Record<string, string>;
}

const props = defineProps<Props>();

const entities = props.patients || props.staff || [];
const entityKey = props.patients ? 'patient_id' : 'staff_id';
const entityLabel = props.patients ? 'Patient' : 'Staff';

const form = useForm({
    file: null as File | null,
    [entityKey]: props.item?.[entityKey]?.toString() || '',
    type: props.item?.type || '',
});

const submitForm = () => {
    if (props.mode === 'create') {
        form.post(props.indexRoute, { forceFormData: true });
    } else {
        form.post(props.indexRoute.replace(':id', props.item!.id), { forceFormData: true, method: 'put' });
    }
};
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <FormField v-slot="{ componentField }" name="file">
            <FormItem>
                <FormLabel>File{{ mode === 'edit' ? ' (leave empty to keep current)' : '' }}</FormLabel>
                <FormControl>
                    <Input type="file" @input="form.file = $event.target.files[0]" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" :name="entityKey">
            <FormItem>
                <FormLabel>{{ entityLabel }}</FormLabel>
                <Select v-model="form[entityKey]">
                    <FormControl>
                        <SelectTrigger>
                            <SelectValue :placeholder="`Select a ${entityLabel.toLowerCase()}`" />
                        </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                        <SelectItem v-for="entity in entities" :key="entity.id" :value="entity.id.toString()">
                            {{ entity.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="type">
            <FormItem>
                <FormLabel>Type</FormLabel>
                <Select v-model="form.type">
                    <FormControl>
                        <SelectTrigger>
                            <SelectValue placeholder="Select file type" />
                        </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                        <SelectItem v-for="[value, label] in Object.entries(typeOptions || {})" :key="value" :value="value">
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