<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { ChevronsUpDown, Check } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Option {
    value: string;
    label: string;
}

interface Props {
    options: Option[];
    modelValue: string;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    class?: string;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Select...',
    searchPlaceholder: 'Search...',
    emptyText: 'No results found.',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const open = ref(false);

const selectedOption = computed(() => {
    return props.options.find(option => option.value === props.modelValue);
});

const selectedLabel = computed(() => {
    return selectedOption.value?.label || props.placeholder;
});

const handleSelect = (value: string) => {
    emit('update:modelValue', value);
    open.value = false;
};
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                role="combobox"
                :aria-expanded="open"
                :class="cn('w-full justify-between', props.class)"
                :disabled="disabled"
            >
                {{ selectedLabel }}
                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-full p-0" :class="cn('w-[--radix-popover-trigger-width]')">
            <Command>
                <CommandInput :placeholder="searchPlaceholder" />
                <CommandList>
                    <CommandEmpty>{{ emptyText }}</CommandEmpty>
                    <CommandGroup>
                        <CommandItem
                            v-for="option in options"
                            :key="option.value"
                            :value="option.label"
                            @select="handleSelect(option.value)"
                        >
                            <Check
                                :class="cn(
                                    'mr-2 h-4 w-4',
                                    selectedOption?.value === option.value ? 'opacity-100' : 'opacity-0'
                                )"
                            />
                            {{ option.label }}
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>