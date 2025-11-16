<template>
  <div class="bg-white/80 dark:bg-gray-800/80 rounded-3xl shadow-depth-4 p-8 border border-white/30 dark:border-white/10 hover-lift animate-scale-in relative overflow-hidden group">
    <!-- Background decoration -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/3 via-purple-500/3 to-pink-500/3 dark:from-indigo-400/3 dark:via-purple-400/3 dark:to-pink-400/3 opacity-20 group-hover:opacity-30 transition-opacity duration-500"></div>
    
    <!-- Word Header -->
    <div class="mb-8 relative z-10">
      <div class="flex items-center gap-4 mb-4">
        <div class="relative">
          <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl blur-md opacity-30 animate-pulse-glow"></div>
          <div class="relative w-14 h-14 bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-depth-3 border border-white/20">
            <span class="text-3xl drop-shadow-sm">💬</span>
          </div>
        </div>
        <div class="flex-1">
          <h2 class="text-5xl font-black text-gray-900 tracking-tight dark:text-white drop-shadow-sm bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-100 dark:to-white bg-clip-text">{{ word.word }}</h2>
        </div>
      </div>
      <div class="inline-flex items-center">
        <span class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-depth-2 border border-white/20">
          {{ word.part_of_speech }}
        </span>
      </div>
    </div>

    <!-- Definition -->
    <div class="mb-8 relative z-10">
      <div class="flex items-start gap-3 p-6 bg-white/25 dark:bg-white/5 rounded-2xl border border-white/30 dark:border-white/10 shadow-depth-1">
        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
          <span class="text-2xl">📖</span>
        </div>
        <p class="text-xl text-gray-800 leading-relaxed font-medium dark:text-gray-200 pt-1">{{ word.definition }}</p>
      </div>
    </div>

    <!-- Example -->
    <div v-if="word.example" class="relative z-10">
      <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-950/50 dark:via-purple-950/50 dark:to-pink-950/50 p-6 rounded-2xl shadow-depth-2 border border-indigo-200/50 dark:border-indigo-700/50 relative overflow-hidden">
        <!-- Decorative border -->
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-indigo-500 via-purple-500 to-pink-500 rounded-full"></div>
        
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
            <span class="text-2xl">💡</span>
          </div>
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
              <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider px-2 py-1 bg-indigo-100 dark:bg-indigo-900/30 rounded-md">Example</p>
            </div>
            <p class="text-gray-700 dark:text-gray-300 italic leading-relaxed font-medium text-lg">"{{ word.example }}"</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  word: {
    type: Object,
    required: true
  }
});
</script>
