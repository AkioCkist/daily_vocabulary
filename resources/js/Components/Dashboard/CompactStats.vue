<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
    <!-- Header - Always Visible -->
    <button 
      @click="isExpanded = !isExpanded"
      class="w-full px-6 py-5 flex items-center justify-between cursor-pointer transition-all group"
    >
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-8">
          <!-- Learning Card -->
          <div class="text-center relative px-4 py-3 rounded-xl transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-md hover:scale-105 cursor-pointer">
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-0.5">
              {{ stats.words_learning }}
            </div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Learning</div>
          </div>
          
          <!-- Divider -->
          <div class="h-12 w-px bg-gray-200 dark:bg-gray-700"></div>
          
          <!-- Accuracy Card -->
          <div class="text-center relative px-4 py-3 rounded-xl transition-all duration-200 hover:bg-green-50 dark:hover:bg-green-900/30 hover:shadow-md hover:scale-105 cursor-pointer">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-0.5">
              {{ stats.accuracy_rate }}%
            </div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Accuracy</div>
          </div>
          
          <!-- Divider -->
          <div class="h-12 w-px bg-gray-200 dark:bg-gray-700"></div>
          
          <!-- Streak Card -->
          <div class="text-center relative px-4 py-3 rounded-xl transition-all duration-200 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:shadow-md hover:scale-105 cursor-pointer">
            <div class="flex items-center gap-2">
              <div>
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-0.5">
                  {{ stats.learning_streak }}
                </div>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Day Streak</div>
              </div>
              <svg class="w-5 h-5 text-orange-500 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
          
          <!-- Divider -->
          <div class="h-12 w-px bg-gray-200 dark:bg-gray-700"></div>
          
          <!-- Mastered Card -->
          <div class="text-center relative px-4 py-3 rounded-xl transition-all duration-200 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:shadow-md hover:scale-105 cursor-pointer">
            <div class="flex items-center gap-2">
              <div>
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-0.5">
                  {{ stats.words_mastered }}
                </div>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Mastered</div>
              </div>
              <svg class="w-5 h-5 text-purple-500 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Expand Icon with subtle animation -->
      <div class="flex items-center gap-3">
        <span class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">
        </span>
        <svg 
          :class="['w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform duration-300', isExpanded ? 'rotate-180' : '']"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
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
        class="border-t border-gray-200 dark:border-gray-700 overflow-hidden"
      >
        <div class="px-6 py-5 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Total Attempts -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-500 hover:shadow-lg transition-all duration-200 hover:scale-105 cursor-pointer">
              <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Total Attempts</div>
              <div class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ stats.total_attempts }}
              </div>
            </div>
            
            <!-- Correct Answers -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-green-200 dark:border-green-900/50 hover:border-green-400 dark:hover:border-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 hover:shadow-lg transition-all duration-200 hover:scale-105 cursor-pointer">
              <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Correct Answers</div>
              <div class="text-2xl font-bold text-green-600 dark:text-green-400 flex items-center gap-2">
                {{ stats.correct_answers }}
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            
            <!-- Words Due -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-orange-200 dark:border-orange-900/50 hover:border-orange-400 dark:hover:border-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:shadow-lg transition-all duration-200 hover:scale-105 cursor-pointer">
              <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Words Due</div>
              <div class="text-2xl font-bold text-orange-600 dark:text-orange-400 flex items-center gap-2">
                {{ stats.words_due_for_review }}
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            
            <!-- This Week -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-indigo-200 dark:border-indigo-900/50 hover:border-indigo-400 dark:hover:border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:shadow-lg transition-all duration-200 hover:scale-105 cursor-pointer">
              <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">This Week</div>
              <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                {{ stats.words_learned_this_week || 0 }}
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                </svg>
              </div>
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