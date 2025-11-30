<template>
  <div class="rounded-2xl bg-indigo-900/90 p-6 text-white relative overflow-hidden">
    <video
      autoplay
      loop
      muted
      playsinline
      class="absolute inset-0 w-full h-full object-cover z-0"
      src="/videos/study_feature_background.webm"
    ></video>
    <div class="absolute inset-0 w-full h-full bg-black/40 z-0 pointer-events-none"></div>

    <h3 class="text-lg font-bold mb-4 relative z-10">Study Feature</h3>
    <button
      @click="$emit('open-flashcard-modal')"
      class="w-full justify-center flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-medium rounded-lg transition-all shadow-lg shadow-white/10 relative z-10"
    >
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="font-semibold text-base">Start Study</span>
    </button>
  </div>
</template>

<script setup>
defineEmits(['open-flashcard-modal']);
</script>
