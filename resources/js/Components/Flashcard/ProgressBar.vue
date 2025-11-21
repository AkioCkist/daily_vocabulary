<template>
  <div class="mb-8">
    <div class="flex items-center justify-between mb-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
          {{ modeLabel }} Flashcard Practice
        </h1>
        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            {{ correctCount }} correct
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-red-500"></span>
            {{ incorrectCount }} incorrect
          </span>
        </div>
      </div>
      <div class="text-right">
        <div class="text-sm text-gray-600 dark:text-gray-400">Progress</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">
          {{ currentIndex + 1 }} / {{ total }}
        </div>
      </div>
    </div>
    
    <div class="relative w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
      <div 
        class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 rounded-full transition-all duration-500 ease-out"
        :style="{ width: `${progress}%` }"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  currentIndex: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    required: true
  },
  correctCount: {
    type: Number,
    default: 0
  },
  incorrectCount: {
    type: Number,
    default: 0
  },
  flashcardType: {
    type: String,
    default: 'standard'
  }
});

const progress = computed(() => {
  return ((props.currentIndex + 1) / props.total) * 100;
});

const modeLabel = computed(() => {
  const labels = {
    standard: 'Standard',
    fill_blank: 'Fill-in-the-Blank',
    mixed: 'Mixed'
  };
  return labels[props.flashcardType] || 'Standard';
});
</script>
