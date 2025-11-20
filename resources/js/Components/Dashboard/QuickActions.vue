<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-200">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
        Learning Center
      </h2>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        Start your practice session
      </p>
    </div>

    <!-- Actions -->
    <div class="p-6 space-y-3">
      <!-- Primary Action: Quick Start -->
      <button 
        @click="$emit('startQuick')"
        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Quick Start (10 words)
      </button>

      <!-- Advanced Start -->
      <button 
        @click="$emit('openAdvanced')"
        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110 4m0-4v2m0-6V4" />
        </svg>
        Advanced Start
      </button>

      <!-- Review Action (if has words due) -->
      <button 
        v-if="wordsDue > 0"
        @click="$emit('startReview')"
        class="w-full bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Review {{ wordsDue }} Words
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  wordsDue: {
    type: Number,
    default: 0
  }
});

defineEmits(['startQuick', 'startReview', 'openAdvanced']);
</script>
