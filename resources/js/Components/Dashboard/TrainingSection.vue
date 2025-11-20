<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
      Training Mode
    </h2>
    
    <div class="space-y-3">
      <!-- Quick Flashcards -->
      <button 
        @click="$emit('startQuick')"
        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-4 px-6 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-center gap-3"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <span>Quick Flashcards</span>
      </button>

      <!-- Advanced Options -->
      <button 
        @click="$emit('openAdvanced')"
        class="w-full bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-3 px-6 rounded-lg hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-200 flex items-center justify-center gap-3"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110 4m0-4v2m0-6V4" />
        </svg>
        <span>Advanced Options</span>
      </button>
    </div>
  </div>
</template>

<script setup>
defineEmits(['startQuick', 'openAdvanced']);
</script>
