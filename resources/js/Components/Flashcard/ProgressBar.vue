<template>
  <div class="mb-8 p-6 bg-gray-800/60 backdrop-blur-sm rounded-xl border border-gray-700 shadow-xl">
    <div class="flex items-center justify-between mb-4">
      
      <div>
        <h1 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-400 mb-1">
          {{ modeLabel }} Practice
        </h1>
        
        <div class="flex items-center gap-4 text-sm font-medium">
          <span class="flex items-center gap-2 text-green-400">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ correctCount }} Correct
          </span>
          <span class="flex items-center gap-2 text-red-400">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            {{ incorrectCount }} Incorrect
          </span>
        </div>
      </div>
      
      <div class="text-right flex-shrink-0">
        <div class="text-sm text-gray-400 font-semibold uppercase">Flashcard</div>
        <div class="text-4xl font-extrabold text-white leading-none">
          {{ currentIndex + 1 }}
          <span class="text-xl font-normal text-gray-400">/ {{ total }}</span>
        </div>
      </div>
    </div>
    
    <div class="relative w-full bg-gray-700/70 rounded-full h-2.5 overflow-hidden shadow-inner mt-2">
      <div 
        class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 rounded-full transition-all duration-500 ease-out shadow-lg shadow-indigo-500/50"
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