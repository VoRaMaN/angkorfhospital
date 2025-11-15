<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        :class="className"
        v-bind="$attrs"
    >
        <path
            fill="currentColor"
            d="M20 5 C10 5 5 10 5 20 C5 30 10 35 20 35 L30 35 C35 35 40 30 40 20 C40 10 35 5 25 5 L20 5 Z M20 10 L25 10 C30 10 35 15 35 20 C35 25 30 30 25 30 L20 30 C15 30 10 25 10 20 C10 15 15 10 20 10 Z"
        />
    </svg>
</template>
