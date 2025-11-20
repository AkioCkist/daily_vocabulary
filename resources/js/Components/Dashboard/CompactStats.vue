<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-200">
    <!-- Header - Always Visible -->
    <button 
      @click="isExpanded = !isExpanded"
      class="w-full px-6 py-4 flex items-center justify-between cursor-pointer transition-all"
    >
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-6">
          <div class="text-center">
            <div class="text-3xl font-bold text-gray-900 dark:text-white">
              {{ stats.words_learning }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Learning</div>
          </div>
          
          <div class="text-center">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400">
              {{ stats.accuracy_rate }}%
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Accuracy</div>
          </div>
          
          <div class="text-center">
            <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">
              {{ stats.learning_streak }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Day Streak</div>
          </div>
          
          <div class="text-center">
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
              {{ stats.words_mastered }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Mastered</div>
          </div>
        </div>
      </div>

      <!-- Expand Icon -->
      <svg 
        :class="['w-5 h-5 text-gray-400 transition-transform', isExpanded ? 'rotate-180' : '']"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Expanded Details -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      leave-active-class="transition-all duration-200 ease-in"
      enter-from-class="opacity-0 max-h-0"
      enter-to-class="opacity-100 max-h-96"
      leave-from-class="opacity-100 max-h-96"
      leave-to-class="opacity-0 max-h-0"
    >
      <div 
        v-show="isExpanded"
        class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-100 dark:bg-gray-900 overflow-hidden"
      >
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
          <div class="text-gray-500 dark:text-gray-400 mb-1">Total Attempts</div>
          <div class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ stats.total_attempts }}
          </div>
        </div>
        
        <div>
          <div class="text-gray-500 dark:text-gray-400 mb-1">Correct Answers</div>
          <div class="text-lg font-semibold text-green-600 dark:text-green-400">
            {{ stats.correct_answers }}
          </div>
        </div>
        
        <div>
          <div class="text-gray-500 dark:text-gray-400 mb-1">Words Due</div>
          <div class="text-lg font-semibold text-orange-600 dark:text-orange-400">
            {{ stats.words_due_for_review }}
          </div>
        </div>
        
        <div>
          <div class="text-gray-500 dark:text-gray-400 mb-1">This Week</div>
          <div class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">
            {{ stats.words_learned_this_week || 0 }}
          </div>
        </div>
      </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  stats: {
    type: Object,
    required: true
  }
});

const isExpanded = ref(false);
</script>
