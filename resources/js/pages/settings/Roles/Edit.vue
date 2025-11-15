<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

interface Props {
    role: {
        id: number;
        name: string;
        description: string;
        permissions: number[];
    };
}

const props = defineProps<Props>();

const form = useForm({
    name: props.role.name,
    description: props.role.description,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
    {
        title: 'Roles',
        href: '/settings/roles',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

const submit = () => {
    form.put(`/settings/roles/${props.role.id}`, {
        onSuccess: () => {
            // Success handled by Inertia
        },
    });
};
</script>

<template>
    <Head title="Edit Role" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a href="/settings/roles">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Role</h1>
                    <p class="text-muted-foreground">Update role information and permissions</p>
                </div>
            </div>

            <div class="max-w-4xl">
                <form @submit.prevent="submit" class="space-y-6 rounded-lg border bg-card p-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="name">Role Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Enter role name"
                                required
                            />
                            <div v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            placeholder="Describe the role..."
                            rows="3"
                        />
                        <div v-if="form.errors.description" class="text-sm text-destructive">
                            {{ form.errors.description }}
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="size-4" />
                            {{ form.processing ? 'Updating...' : 'Update Role' }}
                        </Button>
                        <Button type="button" variant="outline" as-child>
                            <a href="/settings/roles">Cancel</a>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
