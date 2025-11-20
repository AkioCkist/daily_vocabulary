<template>
  <div class="text-center max-w-3xl mx-auto mb-12">
    <!-- Main Headline -->
    <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-4">
      Master English Vocabulary
      <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-600">
        One Word at a Time
      </span>
    </h1>
    
    <!-- Subtitle -->
    <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">
      Learn, practice, and retain new words with spaced repetition and smart flashcards
    </p>

    <!-- Quick Stats (for authenticated users) -->
    <div v-if="user && stats" class="flex items-center justify-center gap-8 text-sm">
      <div class="flex items-center gap-2">
        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
        <span class="text-gray-600 dark:text-gray-400">
          <strong class="text-gray-900 dark:text-white">{{ stats.words_learning }}</strong> learning
        </span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
        <span class="text-gray-600 dark:text-gray-400">
          <strong class="text-gray-900 dark:text-white">{{ stats.words_mastered }}</strong> mastered
        </span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
        <span class="text-gray-600 dark:text-gray-400">
          <strong class="text-gray-900 dark:text-white">{{ stats.learning_streak }}</strong> day streak
        </span>
      </div>
    </div>

    <!-- CTA Button (for guests) -->
    <div v-else class="flex items-center justify-center gap-4">
      <a 
        href="/register" 
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
      >
        Start Learning Free
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
      </a>
      <a 
        href="/login" 
        class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-lg font-semibold border border-gray-300 dark:border-gray-600 transition-colors"
      >
        Sign In
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps({
  user: {
    type: Object,
    default: null
  },
  stats: {
    type: Object,
    default: null
  }
});
</script>
