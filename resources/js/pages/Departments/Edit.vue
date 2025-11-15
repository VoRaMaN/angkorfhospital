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
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    department: {
        id: number;
        name: string;
        description: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const form = useForm({
    name: props.department.name,
    description: props.department.description,
});
</script>

<template>
    <Head title="Edit Department" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/departments">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Department</h1>
                    <p class="text-muted-foreground">
                        Update department information
                    </p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form
                    :action="`/departments/${props.department.id}`"
                    method="POST"
                    class="space-y-6"
                >
                    <input type="hidden" name="_method" value="PUT" />

                    <FormField v-slot="{ componentField }" name="name">
                        <FormItem>
                            <FormLabel>Department Name</FormLabel>
                            <FormControl>
                                <Input
                                    v-bind="componentField"
                                    placeholder="Enter department name"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="description">
                        <FormItem>
                            <FormLabel>Description</FormLabel>
                            <FormControl>
                                <Textarea
                                    v-bind="componentField"
                                    placeholder="Enter department description"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Update Department
                        </Button>
                        <Button variant="outline" as-child>
                            <a href="/departments">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
