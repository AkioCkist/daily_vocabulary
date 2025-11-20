<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-200">
    <!-- Header -->
    <button 
      @click="isExpanded = !isExpanded"
      class="w-full px-6 py-4 flex items-center justify-between cursor-pointer transition-all"
    >
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
        Topics
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
      enter-to-class="opacity-100 max-h-[500px]"
      leave-from-class="opacity-100 max-h-[500px]"
      leave-to-class="opacity-0 max-h-0"
    >
      <div 
        v-show="isExpanded"
        class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 space-y-2 bg-gray-100 dark:bg-gray-900 overflow-hidden"
      >
      <!-- Topic List -->
      <div 
        v-for="topic in displayTopics" 
        :key="topic.id"
        class="flex items-center justify-between p-3 rounded-lg bg-white dark:bg-gray-700 transition-all cursor-pointer border border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-600"
        @click="$emit('select', topic)"
      >
        <div>
          <div class="font-medium text-gray-900 dark:text-white text-sm">
            {{ topic.name }}
          </div>
          <div class="text-xs text-gray-500 dark:text-gray-400">
            {{ topic.words_count }} words
          </div>
        </div>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>

      <!-- Create Topic Button -->
      <button 
        @click="$emit('manage')"
        class="w-full py-2.5 px-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors text-sm font-medium"
      >
        + Create Custom Topic
      </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  topics: {
    type: Object,
    required: true
  },
  limit: {
    type: Number,
    default: 5
  }
});

defineEmits(['select', 'manage']);

const isExpanded = ref(false);

const displayTopics = computed(() => {
  return props.topics?.system?.slice(0, props.limit) || [];
});
</script>
