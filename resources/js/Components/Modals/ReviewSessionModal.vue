<template>
  <transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div 
          class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity"
          @click="$emit('close')"
        ></div>

        <div class="inline-block align-bottom bg-[#0B0C10]/90 backdrop-blur-lg rounded-2xl text-left overflow-hidden shadow-2xl shadow-indigo-900/40 ring-1 ring-indigo-900/50 transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full text-white">
          
          <div class="px-6 py-5 border-b border-gray-800">
            <h3 class="text-2xl font-bold leading-6 text-white">
              Review Saved Session
            </h3>
            <p class="mt-1 text-sm text-gray-400">
              Session: <span class="font-medium text-indigo-400">{{ session?.name }}</span>
            </p>
          </div>
          
          <div class="px-6 py-6 space-y-6">
            <p class="text-lg text-gray-300">
              This session contains <span class="font-bold text-indigo-400">{{ session?.flashcard_count }}</span> words.
            </p>

            <div>
              <div class="flex justify-between items-center mb-3">
                <label for="word-count" class="block text-sm font-medium text-gray-300">Number of Words to Review</label>
                <span class="text-lg font-bold text-indigo-400">{{ wordCount }}</span>
              </div>
              <input
                id="word-count"
                type="range"
                v-model.number="wordCount"
                min="1"
                :max="session?.flashcard_count || 1"
                class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-indigo-500 hover:accent-indigo-400 transition-colors"
              />
              <div class="flex justify-between mt-1 text-xs text-gray-500">
                <span>Min: 1</span>
                <span>Max: {{ session?.flashcard_count }}</span>
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl border border-gray-700 hover:border-gray-600 transition-colors">
                <div>
                  <label for="shuffle-toggle" class="block text-sm font-medium text-gray-300 cursor-pointer">Shuffle Word Order</label>
                  <p class="text-xs text-gray-500 mt-1">Randomize the order of words during review</p>
                </div>
                <button
                  id="shuffle-toggle"
                  type="button"
                  @click="shuffleWords = !shuffleWords"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                  :class="shuffleWords ? 'bg-indigo-600' : 'bg-gray-700'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200"
                    :class="shuffleWords ? 'translate-x-6' : 'translate-x-1'"
                  ></span>
                </button>
              </div>
            </div>
            
          </div>

          <div class="px-6 py-4 bg-gray-900/50 flex justify-end gap-3 border-t border-gray-800">
            <button
              type="button"
              @click="$emit('close')"
              class="inline-flex justify-center py-2.5 px-4 border border-gray-700 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 transition-colors"
            >
              Cancel
            </button>
            
            <button
              type="button"
              :disabled="!isValid"
              @click="startReview"
              class="inline-flex justify-center py-2.5 px-4 border border-transparent text-sm font-bold rounded-xl shadow-lg transition-all duration-200 hover:scale-[1.02] active:scale-95
                     bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-indigo-600/30 disabled:opacity-50 disabled:shadow-none"
            >
              <PlayIcon class="w-5 h-5 mr-2" />
              Start Review ({{ wordCount }})
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { PlayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  session: {
    type: Object,
    default: () => null
  }
});

const emit = defineEmits(['close', 'start-review']);

const wordCount = ref(1);
const shuffleWords = ref(false);

watch(() => props.session, (newSession) => {
    if (newSession) {
        // Reset word count to 1 when a new session is selected
        wordCount.value = 1;
    }
}, { immediate: true });

const isValid = computed(() => {
    return props.session && wordCount.value >= 1 && wordCount.value <= props.session.flashcard_count;
});

const startReview = () => {
    if (!isValid.value) return;

    // Emit the settings needed to start the flashcard practice
    emit('start-review', {
        mode: 'saved_session',
        flashcard_type: 'standard', // Defaulting to standard as requested to remove selection
        word_count: wordCount.value,
        shuffle: shuffleWords.value,
    });
};
</script>


<style scoped>
/* Scoped styles for transitions (optional) */
</style>