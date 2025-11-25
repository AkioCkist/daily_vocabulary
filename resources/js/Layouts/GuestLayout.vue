<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome - DailyVocab" />
    
    <div class="min-h-screen relative overflow-hidden bg-black flex items-center justify-center p-4">
        
        <video
            autoplay
            loop
            muted
            playsinline
            src="/videos/background_2.mp4"
            class="absolute inset-0 w-full h-full object-cover z-0 opacity-40"
        ></video>
        
        <div class="absolute inset-0 bg-black/30 z-10"></div>
        
        <Link 
            :href="route('home')"
            class="absolute top-4 right-4 z-50 text-white/70 hover:text-white transition-colors p-2 rounded-full backdrop-blur-sm bg-black/20"
            aria-label="Close"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </Link>
        
        <div class="relative z-20 w-full max-w-5xl grid lg:grid-cols-2 gap-12 items-center">
            
            <div class="hidden lg:block space-y-6 p-6">
                <Link href="/" class="inline-block transition-transform">
                    <ApplicationLogo class="h-8 w-auto fill-current text-white/90" />
                </Link>
                
                <h1 class="text-5xl font-bold tracking-tight leading-snug text-white">
                    Master words <br />
                    with daily practice.
                </h1>
                <p class="text-lg text-gray-400">
                    Stop forgetting what you learn. Built for developers.
                </p>

                <div class="text-xs text-gray-600 pt-8">
                     © {{ new Date().getFullYear() }} DailyVocab, Inc. All Rights Reserved.
                </div>
            </div>

            <div class="flex justify-center lg:justify-end">
                <slot />
            </div>
        </div>
    </div>
</template>