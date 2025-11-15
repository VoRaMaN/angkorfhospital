<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { login, register } from '@/routes';
import { Head } from '@inertiajs/vue3';

defineProps<{
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Welcome" />

    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10"
    >
        <div class="w-full max-w-md">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <div
                        class="mb-1 flex h-12 w-12 items-center justify-center rounded-md"
                    >
                        <AppLogoIcon
                            class="size-12 fill-current text-[var(--foreground)] dark:text-white"
                        />
                    </div>
                    <div class="space-y-2 text-center">
                        <h1 class="text-2xl font-bold">Welcome to CynoSys</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            Healthcare Management System
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <Button as-child class="w-full">
                        <TextLink :href="login()">Log in</TextLink>
                    </Button>

                    <Button
                        v-if="canRegister"
                        variant="outline"
                        as-child
                        class="w-full"
                    >
                        <TextLink :href="register()">Create account</TextLink>
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
