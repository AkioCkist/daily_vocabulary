<template>
  <div 
    v-if="wordsCount > 0"
    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700"
  >
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
      Review Due
    </h2>
    
    <div class="text-center">
      <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-full flex items-center justify-center">
        <span class="text-4xl font-bold text-orange-600 dark:text-orange-400">
          {{ wordsCount }}
        </span>
      </div>
      
      <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
        Words need review
      </div>
      
      <button 
        @click="$emit('start')"
        class="w-full bg-gradient-to-r from-orange-500 to-red-600 text-white font-semibold py-3 px-6 rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
      >
        Start Review
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  wordsCount: {
    type: Number,
    required: true
  }
});

defineEmits(['start']);
</script>
