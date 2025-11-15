<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { FormControl, FormItem, FormLabel, FormMessage, FormField } from '@/components/ui/form';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
    {
        title: 'Create',
        href: '#',
    },
];

const form = useForm({
    name: '',
    description: '',
});
</script>

<template>
    <Head title="Create Department" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/departments">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Create Department</h1>
                    <p class="text-muted-foreground">Add a new hospital department</p>
                </div>
            </div>

            <div class="max-w-2xl">
                <form @submit.prevent="form.post('/departments')" class="space-y-6">
                    <FormField v-slot="{ componentField }" name="name">
                        <FormItem>
                            <FormLabel>Department Name</FormLabel>
                            <FormControl>
                                <Input v-bind="componentField" placeholder="Enter department name" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="description">
                        <FormItem>
                            <FormLabel>Description</FormLabel>
                            <FormControl>
                                <Textarea v-bind="componentField" placeholder="Enter department description" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            Create Department
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
