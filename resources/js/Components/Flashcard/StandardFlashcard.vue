<template>
  <div class="bg-gray-900 rounded-3xl shadow-2xl shadow-indigo-900/40 ring-1 ring-gray-700 overflow-hidden min-h-[480px] flex flex-col text-white">
    
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-purple-700 p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="px-3 py-1 bg-white/30 backdrop-blur-sm rounded-full text-sm font-bold text-gray-900">
            {{ word.cefr_level }}
          </span>
          <span class="text-sm font-medium text-indigo-100/70">{{ word.topic }}</span>
        </div>
        <slot name="actions"></slot>
      </div>
    </div>

    <div 
      class="flex-1 flex items-center justify-center p-10 min-h-[320px] cursor-pointer bg-gray-900 hover:bg-gray-800 transition-colors duration-300 relative"
      @click="!showDefinition && $emit('toggle')"
    >
      <div class="text-center w-full">
        <transition name="fade-scale" mode="out-in">
          
          <div v-if="!showDefinition" :key="'word'" class="space-y-4">
            <h2 class="text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-400 leading-tight">
              {{ word.word }}
            </h2>
            <div class="text-xl font-medium text-indigo-200/80">
              <span v-if="word.part_of_speech" class="italic">{{ word.part_of_speech }}</span>
            </div>
          </div>
          
          <div v-else :key="'definition'" class="space-y-6">
            <div class="flex items-center justify-center gap-3">
              <h2 class="text-5xl font-extrabold text-white leading-snug">
                {{ word.word }}
              </h2>
              <SpeakerButton :text="word.word" />
            </div>
            
            <p class="text-2xl text-gray-300 font-light leading-relaxed max-w-xl mx-auto">
              {{ word.definition }}
            </p>

            <div v-if="word.example" class="mt-4 max-w-xl mx-auto p-4 rounded-xl bg-gray-800 border border-gray-700 shadow-inner">
              <p class="text-sm text-indigo-400 italic mb-2 font-semibold">Example:</p>
              <p class="text-lg text-gray-400 mt-1">
                {{ word.example }}
              </p>
            </div>
          </div>
        </transition>
      </div>
    </div>

    <div class="p-8 border-t border-gray-800 bg-gray-900/70">
      <transition name="fade" mode="out-in">
        
        <div v-if="showDefinition" :key="'answered'" class="flex justify-center space-x-4">
          <button
            @click.stop="$emit('answer', false)"
            class="flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-red-500/30 hover:shadow-xl hover:scale-105 active:scale-95 min-w-[200px]"
          >
            <ArrowUturnLeftIcon class="w-5 h-5"/>
            Needs Review
          </button>
          <button
            @click.stop="$emit('answer', true)"
            class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-green-500/30 hover:shadow-xl hover:scale-105 active:scale-95 min-w-[200px]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Got It Right
          </button>
        </div>
        
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
  </div>
</template>

<script setup>
// Logic remains unchanged
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
/* Transitions remain unchanged */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.fade-scale-enter-active, .fade-scale-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-scale-enter-from, .fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>