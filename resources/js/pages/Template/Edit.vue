<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { FormControl, FormItem, FormLabel, FormMessage, FormField } from '@/components/ui/form';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    title: string;
    indexRoute: string;
    item: Record<string, any>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: props.indexRoute,
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const form = useForm({
    // Add your form fields here based on the entity
    name: props.item.name || '',
    description: props.item.description || '',
});
</script>

<template>
    <Head :title="`Edit ${title.slice(0, -1)}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="indexRoute">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit {{ title.slice(0, -1) }}</h1>
                    <p class="text-muted-foreground">Update {{ title.slice(0, -1).toLowerCase() }} information</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="form.put(indexRoute.replace(':id', item.id))" class="space-y-6">
                    <FormField v-slot="{ componentField }" name="name">
                        <FormItem>
                            <FormLabel>Name</FormLabel>
                            <FormControl>
                                <Input v-bind="componentField" placeholder="Enter name" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="description">
                        <FormItem>
                            <FormLabel>Description</FormLabel>
                            <FormControl>
                                <Textarea v-bind="componentField" placeholder="Enter description" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Update {{ title.slice(0, -1) }}
                        </Button>
                        <Button variant="outline" as-child>
                            <a :href="indexRoute">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
