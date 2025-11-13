<template>
  <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-8 border border-indigo-100 ring-1 ring-gray-900/5 hover:border-indigo-300 transition-all duration-300 transform hover:-translate-y-1 animate-scale-in dark:bg-gray-800/80 dark:border-indigo-900/50 dark:ring-white/10">
    <!-- Word Header -->
    <div class="mb-6">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
          <span class="text-2xl">💬</span>
        </div>
        <div class="flex-1">
          <h2 class="text-4xl font-black text-gray-900 tracking-tight dark:text-white">{{ word.word }}</h2>
        </div>
      </div>
      <span class="inline-block bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-sm font-semibold dark:bg-indigo-900/30 dark:text-indigo-300">
        {{ word.part_of_speech }}
      </span>
    </div>

    <!-- Definition -->
    <div class="mb-6">
      <div class="flex items-start gap-2 mb-2">
        <span class="text-indigo-500 font-bold text-lg dark:text-indigo-400">📖</span>
        <p class="text-xl text-gray-800 leading-relaxed font-medium dark:text-gray-200">{{ word.definition }}</p>
      </div>
    </div>

    <!-- Example -->
    <div v-if="word.example" class="bg-gradient-to-r from-indigo-50 to-purple-50 p-5 rounded-2xl border-l-4 border-indigo-500 shadow-sm dark:from-indigo-950/50 dark:to-purple-950/50 dark:border-indigo-400">
      <div class="flex items-start gap-2">
        <span class="text-indigo-600 font-bold dark:text-indigo-400">💡</span>
        <div class="flex-1">
          <p class="text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1 dark:text-indigo-400">Example</p>
          <p class="text-gray-700 italic leading-relaxed dark:text-gray-300">"{{ word.example }}"</p>
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
