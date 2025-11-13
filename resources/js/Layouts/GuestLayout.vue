<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-purple-50 px-4 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950"
    >
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <Link href="/" class="inline-block transition-transform hover:scale-105">
                    <ApplicationLogo class="h-16 w-16 fill-current text-indigo-600 dark:text-indigo-400" />
                </Link>
                <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Daily Vocabulary</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Expand your vocabulary, one word at a time</p>
            </div>

            <div
                class="overflow-hidden rounded-2xl bg-white/80 backdrop-blur-sm shadow-xl ring-1 ring-gray-900/5 dark:bg-gray-800/80 dark:ring-white/10"
            >
                <div class="px-8 py-10">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
