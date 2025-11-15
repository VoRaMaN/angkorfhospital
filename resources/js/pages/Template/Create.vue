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
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: props.indexRoute,
    },
    {
        title: 'Create',
        href: '#',
    },
];

const form = useForm({
    // Add your form fields here based on the entity
    name: '',
    description: '',
});
</script>

<template>
    <Head :title="`Create ${title.slice(0, -1)}`" />

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
                    <h1 class="text-2xl font-bold">Create {{ title.slice(0, -1) }}</h1>
                    <p class="text-muted-foreground">Add a new {{ title.slice(0, -1).toLowerCase() }}</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="form.post(indexRoute)" class="space-y-6">
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
                            Create {{ title.slice(0, -1) }}
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
