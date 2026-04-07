<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    day: string | number;
    month: string | number;
    year: string | number;
    errors?: {
        day?: string;
        month?: string;
        year?: string;
    };
}>();

const emit = defineEmits<{
    (e: 'update:day', value: string): void;
    (e: 'update:month', value: string): void;
    (e: 'update:year', value: string): void;
}>();

const dayRef = ref<HTMLInputElement | null>(null);
const monthRef = ref<HTMLInputElement | null>(null);
const yearRef = ref<HTMLInputElement | null>(null);

const inputClass = (hasError: boolean) =>
    cn(
        'flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-center text-base shadow-xs transition-[color,box-shadow] outline-none md:text-sm',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        'dark:bg-input/30',
        hasError
            ? 'border-destructive ring-destructive/20 dark:ring-destructive/40'
            : 'border-input',
    );

const handleDayInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 2) value = value.slice(0, 2);
    emit('update:day', value);
    if (value.length === 2) {
        nextTick(() => monthRef.value?.focus());
    }
};

const handleMonthInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 2) value = value.slice(0, 2);
    emit('update:month', value);
    if (value.length === 2) {
        nextTick(() => yearRef.value?.focus());
    }
};

const handleYearInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 4) value = value.slice(0, 4);
    emit('update:year', value);
};

const handleDayKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Backspace' && !props.day) {
        // Already empty, do nothing (first field)
    }
};

const handleMonthKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Backspace' && !props.month) {
        dayRef.value?.focus();
    }
};

const handleYearKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Backspace' && !props.year) {
        monthRef.value?.focus();
    }
};
</script>

<template>
    <div class="grid grid-cols-3 gap-2">
        <div>
            <input
                ref="dayRef"
                type="text"
                inputmode="numeric"
                maxlength="2"
                placeholder="DD"
                :value="day"
                :class="inputClass(!!errors?.day)"
                @input="handleDayInput"
                @keydown="handleDayKeydown"
            />
            <div v-if="errors?.day" class="mt-1 text-sm text-destructive">{{ errors.day }}</div>
        </div>
        <div>
            <input
                ref="monthRef"
                type="text"
                inputmode="numeric"
                maxlength="2"
                placeholder="MM"
                :value="month"
                :class="inputClass(!!errors?.month)"
                @input="handleMonthInput"
                @keydown="handleMonthKeydown"
            />
            <div v-if="errors?.month" class="mt-1 text-sm text-destructive">{{ errors.month }}</div>
        </div>
        <div>
            <input
                ref="yearRef"
                type="text"
                inputmode="numeric"
                maxlength="4"
                placeholder="YYYY"
                :value="year"
                :class="inputClass(!!errors?.year)"
                @input="handleYearInput"
                @keydown="handleYearKeydown"
            />
            <div v-if="errors?.year" class="mt-1 text-sm text-destructive">{{ errors.year }}</div>
        </div>
    </div>
</template>
