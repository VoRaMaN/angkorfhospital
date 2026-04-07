<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    modelValue: string;
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

// Parse DD/MM/YYYY into parts
const parseParts = (val: string) => {
    const parts = val.split('/');
    return {
        day: parts[0] || '',
        month: parts[1] || '',
        year: parts[2] || '',
    };
};

const initial = parseParts(props.modelValue);
const day = ref(initial.day);
const month = ref(initial.month);
const year = ref(initial.year);

const dayRef = ref<HTMLInputElement | null>(null);
const monthRef = ref<HTMLInputElement | null>(null);
const yearRef = ref<HTMLInputElement | null>(null);

// Sync from parent when modelValue changes externally
watch(() => props.modelValue, (newVal) => {
    const parts = parseParts(newVal);
    if (parts.day !== day.value || parts.month !== month.value || parts.year !== year.value) {
        day.value = parts.day;
        month.value = parts.month;
        year.value = parts.year;
    }
});

const emitCombined = () => {
    const d = day.value.padStart(2, '0');
    const m = month.value.padStart(2, '0');
    const y = year.value;
    if (day.value && month.value && year.value) {
        emit('update:modelValue', `${d}/${m}/${y}`);
    } else {
        emit('update:modelValue', `${day.value}/${month.value}/${year.value}`);
    }
};

const inputClass = cn(
    'flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-center text-base shadow-xs transition-[color,box-shadow] outline-none md:text-sm',
    'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
    'dark:bg-input/30',
    'border-input',
);

const handleDayInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 2) value = value.slice(0, 2);
    day.value = value;
    emitCombined();
    if (value.length === 2) {
        nextTick(() => monthRef.value?.focus());
    }
};

const handleMonthInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 2) value = value.slice(0, 2);
    month.value = value;
    emitCombined();
    if (value.length === 2) {
        nextTick(() => yearRef.value?.focus());
    }
};

const handleYearInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 4) value = value.slice(0, 4);
    year.value = value;
    emitCombined();
};

const handleMonthKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Backspace' && !month.value) {
        dayRef.value?.focus();
    }
};

const handleYearKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Backspace' && !year.value) {
        monthRef.value?.focus();
    }
};
</script>

<template>
    <div class="grid grid-cols-3 gap-2">
        <input
            ref="dayRef"
            type="text"
            inputmode="numeric"
            maxlength="2"
            placeholder="DD"
            :value="day"
            :class="inputClass"
            @input="handleDayInput"
        />
        <input
            ref="monthRef"
            type="text"
            inputmode="numeric"
            maxlength="2"
            placeholder="MM"
            :value="month"
            :class="inputClass"
            @input="handleMonthInput"
            @keydown="handleMonthKeydown"
        />
        <input
            ref="yearRef"
            type="text"
            inputmode="numeric"
            maxlength="4"
            placeholder="YYYY"
            :value="year"
            :class="inputClass"
            @input="handleYearInput"
            @keydown="handleYearKeydown"
        />
    </div>
    <div v-if="error" class="mt-1 text-sm text-destructive">{{ error }}</div>
</template>
