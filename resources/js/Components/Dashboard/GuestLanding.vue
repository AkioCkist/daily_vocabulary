<template>
  <div v-if="!user" class="min-h-[80vh] flex flex-col justify-center relative overflow-hidden">
    <video
      autoplay
      loop
      muted
      playsinline
      class="fixed inset-0 w-full h-full object-cover z-0"
      src="/videos/background.mp4"
    ></video>
    <div class="fixed inset-0 w-full h-full bg-black/28 z-10 pointer-events-none"></div>

    <div class="grid lg:grid-cols-2 gap-12 items-center relative z-10">
      <div class="space-y-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black/70 border border-indigo-800 text-indigo-200 text-xs font-semibold tracking-wide uppercase">
          <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
          #newwaytolearn
        </div>

        <h1 class="text-5xl lg:text-7xl font-bold tracking-tight leading-[1.1] text-white drop-shadow-lg">
          Master words <br />
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-400">
            Built for Developers.
          </span>
        </h1>

        <p class="text-xl text-gray-200 max-w-lg leading-relaxed drop-shadow">
          Stop forgetting what you learn. A spaced-repetition system designed with the precision of a code editor.
        </p>

        <GuestContent @word-selected="$emit('word-selected')" />
      </div>

      <div class="relative hidden lg:block">
        <div class="aspect-square rounded-3xl bg-black/70 border border-gray-700 flex items-center justify-center shadow-2xl">
          <span class="text-gray-300 font-mono text-sm">[ Insert 3D Abstract Hero Image Here ]</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import GuestContent from '@/Components/Dashboard/GuestContent.vue';

defineProps({
  user: {
    type: Object,
    default: null
  }
});

defineEmits(['word-selected']);
</script>
