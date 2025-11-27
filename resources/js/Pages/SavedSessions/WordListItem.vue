<template>
  <div class="flex items-center p-4 bg-gray-800/70 border-b border-gray-700 last:border-b-0 rounded-xl shadow-lg transition-colors duration-200">
    
    <div 
      class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-indigo-400 cursor-grab active:cursor-grabbing transition-colors mr-4"
      title="Drag to reorder"
    >
      <Bars3Icon class="w-5 h-5" />
    </div>

    <div class="flex-1 min-w-0">
      <h4 class="text-lg font-semibold text-white truncate">{{ word.word }}</h4>
      <p class="text-sm text-gray-400 truncate">{{ word.definition }}</p>
    </div>

    <div class="flex items-center space-x-2 ml-4">
      <span class="px-3 py-1 text-xs font-medium rounded-full bg-indigo-900/50 text-indigo-300">
        {{ word.cefr_level }}
      </span>
      <button 
        @click="$emit('remove')"
        class="w-8 h-8 flex items-center justify-center rounded-full text-red-400 hover:bg-red-900/30 transition-colors"
        title="Remove word from session"
      >
        <MinusCircleIcon class="w-5 h-5" />
      </button>
    </div>

  </div>
</template>

<script setup>
import { Bars3Icon, MinusCircleIcon } from '@heroicons/vue/24/outline'; // Assumed icons

defineProps({
  word: {
    type: Object,
    required: true
  }
});

defineEmits(['remove']);
</script>