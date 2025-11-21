<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden min-h-[480px] flex flex-col">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 text-white p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
            {{ word.cefr_level }}
          </span>
          <span class="text-sm opacity-90">{{ word.topic }}</span>
        </div>
        <slot name="actions"></slot>
      </div>
    </div>

    <!-- Content -->
    <div 
      class="flex-1 flex items-center justify-center p-8 min-h-[320px] cursor-pointer hover:bg-gradient-to-br hover:from-gray-50 hover:to-blue-50 dark:hover:from-gray-700/30 dark:hover:to-indigo-900/20 transition-all duration-300 relative"
      @click="!showDefinition && $emit('toggle')"
    >
      <div class="text-center w-full">
        <transition name="fade-scale" mode="out-in">
          <!-- Front: Word -->
          <div v-if="!showDefinition" :key="'word'" class="space-y-4 py-4">
            <div class="flex items-center justify-center gap-2">
              <h2 class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 leading-tight pb-2">
                {{ word.word }}
              </h2>
              <SpeakerButton :text="word.word" />
            </div>
            <div v-if="word.pronunciation" class="text-xl text-gray-600 dark:text-gray-400 font-light">
              {{ word.pronunciation }}
            </div>
          </div>
          <!-- Back: Definition -->
          <div v-else :key="'definition'" class="space-y-6 py-4">
            <h3 class="text-3xl font-semibold text-gray-900 dark:text-white leading-relaxed pb-2">
              {{ word.definition }}
            </h3>
            <div v-if="word.example" class="max-w-lg mx-auto p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl">
              <p class="text-gray-700 dark:text-gray-300 italic">
                "{{ word.example }}"
              </p>
            </div>
            <div class="flex justify-center mt-6">
              <button
                @click.stop="$emit('toggle')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow transition-all duration-200"
              >
                <ArrowUturnLeftIcon class="w-5 h-5" />
                Back to Word
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>

    <!-- Actions -->
    <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 border-t border-gray-200 dark:border-gray-600">
      <transition name="fade" mode="out-in">
        <!-- After flip: Answer buttons -->
        <div v-if="showDefinition" :key="'answered'" class="flex gap-3 justify-center">
          <button
            @click.stop="$emit('answer', false)"
            class="group flex-1 max-w-xs bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            I Don't Remember
          </button>
          <button
            @click.stop="$emit('answer', true)"
            class="group flex-1 max-w-xs bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Got It Right
          </button>
        </div>
        
        <!-- Before flip: Skip button -->
        <div v-else :key="'unanswered'" class="text-center">
          <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
            Click the card above to reveal the definition
          </p>
          <button
            @click.stop="$emit('answer', false)"
            class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
          >
            I Don't Remember
          </button>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
defineProps({
  word: {
    type: Object,
    required: true
  },
  showDefinition: {
    type: Boolean,
    default: false
  }
});

defineEmits(['toggle', 'answer']);
import SpeakerButton from '@/Components/Flashcard/SpeakerButton.vue';
import { ArrowUturnLeftIcon } from '@heroicons/vue/24/outline';
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.fade-scale-enter-active, .fade-scale-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-scale-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(8px);
}
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-8px);
}
</style>
