<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import { onMounted, onUnmounted } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// ── Dynamic blob animation ─────────────────────────────────────
let blobTimers: ReturnType<typeof setTimeout>[] = [];

function rnd(min: number, max: number) {
    return Math.random() * (max - min) + min;
}

function randomRadius() {
    const v = () => Math.round(rnd(28, 72));
    return `${v()}% ${v()}% ${v()}% ${v()}% / ${v()}% ${v()}% ${v()}% ${v()}%`;
}

function morphBlob(el: HTMLElement, depth = 0) {
    const x   = rnd(-18, 55);
    const y   = rnd(-18, 55);
    const sc  = rnd(0.72, 1.40);
    const dur = rnd(3.5, 7);           // seconds for this move
    const pause = rnd(300, 1200);      // brief pause at destination (ms)

    el.style.transition = `transform ${dur}s cubic-bezier(0.37,0,0.63,1), border-radius ${dur * 1.2}s ease-in-out`;
    el.style.transform  = `translate(${x}vw, ${y}vh) scale(${sc})`;
    el.style.borderRadius = randomRadius();

    // schedule next move after animation finishes + optional pause
    const timer = setTimeout(() => morphBlob(el, depth + 1), dur * 1000 + pause);
    blobTimers.push(timer);
}

onMounted(() => {
    if (typeof document === 'undefined') return;

    const blobs = document.querySelectorAll<HTMLElement>('.modern-blobs span');
    blobs.forEach((el, i) => {
        // stagger starts so they don't all jump together
        const delay = i * 600;
        const timer = setTimeout(() => morphBlob(el), delay);
        blobTimers.push(timer);
    });
});

onUnmounted(() => {
    blobTimers.forEach(clearTimeout);
    blobTimers = [];
});
</script>

<template>
    <AppShell variant="sidebar">
        <!-- Animated blob background — shown via CSS only in .modern theme -->
        <div class="modern-blobs" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
