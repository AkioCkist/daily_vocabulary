<template>
  <div class="p-8 border-t border-gray-800 bg-gray-900/70">
    <transition name="fade" mode="out-in">
      
      <!-- Action Buttons (Definition Visible) -->
      <div v-if="isDefinitionVisible" :key="'answered'" class="flex justify-center space-x-4">
        <button
          @click.stop="$emit('answer', false)"
          class="flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-red-500/30 hover:shadow-xl hover:scale-105 active:scale-95 min-w-[200px]"
        >
          <ArrowUturnLeftIcon class="w-5 h-5" />
          Needs Review
        </button>
        <button
          @click.stop="$emit('answer', true)"
          class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-green-500/30 hover:shadow-xl hover:scale-105 active:scale-95 min-w-[200px]"
        >
          <CheckIcon class="w-5 h-5" />
          Got It Right
        </button>
      </div>
      
      <!-- Unanswered State -->
      <div v-else :key="'unanswered'" class="text-center">
        <p class="text-gray-400 mb-4 text-sm font-medium">
          Click the card above to reveal the definition
        </p>
        <button
          @click.stop="$emit('answer', false)"
          class="bg-gray-700 hover:bg-gray-600 text-gray-300 font-semibold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 min-w-[200px]"
        >
          I Don't Remember
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ArrowUturnLeftIcon } from '@heroicons/vue/24/outline';

const CheckIcon = {
  template: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
};

defineProps({
  isDefinitionVisible: {
    type: Boolean,
    default: false
  }
});

defineEmits(['answer']);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
