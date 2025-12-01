<template>
  <div class="p-8 border-t border-gray-800 bg-gray-900/70">
    <div class="flex justify-center space-x-4">
      
      <div v-if="!answered" class="flex justify-center space-x-4">
        <button
          @click.stop="$emit('skip')"
          class="bg-gray-700 hover:bg-gray-600 text-gray-300 font-semibold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          Skip
        </button>
        
        <button
          @click.stop="$emit('hint')"
          :disabled="maxHintsReached"
          :class="[
            'font-bold py-3 px-6 rounded-xl transition-all duration-150 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2',
            maxHintsReached 
              ? 'bg-gray-600 text-gray-400' 
              : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-500/30'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          {{ maxHintsReached ? 'No More Hints' : 'Hint' }}
        </button>
        
        <button
          @click.stop="$emit('submit')"
          :disabled="!userAnswer.trim()"
          class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-green-500/30 hover:shadow-xl hover:scale-105 active:scale-95 disabled:opacity-50 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Submit
        </button>
      </div>
      
      <div v-else class="flex justify-center">
        <button
          @click.stop="$emit('next')"
          class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-12 rounded-xl transition-all duration-300 shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:scale-105 active:scale-95 flex items-center gap-2"
        >
          Next Word
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14"/></svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  answered: {
    type: Boolean,
    default: false
  },
  userAnswer: {
    type: String,
    default: ''
  },
  maxHintsReached: {
    type: Boolean,
    default: false
  }
});

defineEmits(['submit', 'hint', 'skip', 'next']);
</script>
