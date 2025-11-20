<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-200">
    <!-- Header -->
    <button 
      @click="isExpanded = !isExpanded"
      class="w-full px-6 py-4 flex items-center justify-between cursor-pointer transition-all"
    >
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
        Recent Practice Words
        <span v-if="activities && activities.length > 0" class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
          ({{ activities.length }})
        </span>
      </h2>
      <svg 
        :class="['w-5 h-5 text-gray-400 transition-transform', isExpanded ? 'rotate-180' : '']"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    
    <!-- Content -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      leave-active-class="transition-all duration-200 ease-in"
      enter-from-class="opacity-0 max-h-0"
      enter-to-class="opacity-100 max-h-[600px]"
      leave-from-class="opacity-100 max-h-[600px]"
      leave-to-class="opacity-0 max-h-0"
    >
      <div 
        v-show="isExpanded"
        class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-100 dark:bg-gray-900 overflow-hidden"
      >
      <div v-if="activities && activities.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
        <div 
          v-for="(activity, index) in activities" 
          :key="`${activity.word}-${index}`"
          class="flex items-center gap-2 p-2.5 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600"
        >
          <!-- Status Indicator -->
          <div 
            :class="[
              'w-2 h-2 rounded-full flex-shrink-0',
              activity.is_correct ? 'bg-green-500' : 'bg-red-500'
            ]"
          />
          
          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="font-medium text-gray-900 dark:text-white truncate text-sm">
              {{ activity.word }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              {{ activity.created_at }}
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
        No recent activity
      </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  activities: {
    type: Array,
    default: () => []
  }
});

const isExpanded = ref(false);
</script>
