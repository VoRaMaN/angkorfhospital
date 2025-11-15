<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import { computed } from 'vue';

interface Permission {
    id: number;
    name: string;
    group: string;
}

const props = defineProps<{
    action: string;
    method?: 'post' | 'put';
    initialName?: string;
    initialDescription?: string;
    initialPermissions?: number[];
    available_permissions?: Permission[];
    cancelUrl?: string;
    submitText?: string;
}>();

const emit = defineEmits<{
    (e: 'success'): void;
}>();

const form = useForm({
    name: props.initialName ?? '',
    description: props.initialDescription ?? '',
    permissions: props.initialPermissions ?? [],
});

const submit = () => {
    if ((props.method ?? 'post') === 'post') {
        form.post(props.action, { onSuccess: () => emit('success') });
    } else {
        form.put(props.action, { onSuccess: () => emit('success') });
    }
};

const availablePermissions = props.available_permissions ?? [];

const availablePermissionsGrouped = computed(() => {
    const groups: Record<string, Permission[]> = {};
    (availablePermissions || []).forEach((perm: Permission) => {
        const group = perm.group || 'other';
        if (!groups[group]) groups[group] = [];
        groups[group].push(perm);
    });

    return Object.keys(groups).map((g) => ({
        group: g,
        permissions: groups[g],
    }));
});
</script>

<template>
    <form
        @submit.prevent="submit"
        class="space-y-6 rounded-lg border bg-card p-6"
    >
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
            <div
                v-if="form.errors.description"
                class="text-sm text-destructive"
            >
                {{ form.errors.description }}
            </div>
        </div>

        <div class="flex gap-4">
            <Button type="submit" :disabled="form.processing">
                <Save class="size-4" />
                {{
                    props.submitText ??
                    (props.method === 'put' ? 'Update Role' : 'Create Role')
                }}
            </Button>
            <Button type="button" variant="outline" as-child>
                <a :href="props.cancelUrl ?? '/settings/roles'">Cancel</a>
            </Button>
        </div>

        <div class="border-t pt-6">
            <h3 class="mb-4 text-lg font-medium">Permissions</h3>
            <div class="space-y-4">
                <div
                    v-for="group in availablePermissionsGrouped"
                    :key="group.group"
                    class="space-y-2"
                >
                    <h4
                        class="text-sm font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        {{ group.group }}
                    </h4>
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        <label
                            v-for="p in group.permissions"
                            :key="p.id"
                            class="flex items-center gap-2"
                        >
                            <input
                                type="checkbox"
                                :value="p.id"
                                v-model="form.permissions"
                            />
                            <span class="text-sm">{{ p.name }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
