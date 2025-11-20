<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
      <div :class="[
        'w-12 h-12 rounded-lg flex items-center justify-center',
        gradientClass
      ]">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath" />
        </svg>
      </div>
      <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
        {{ label }}
      </span>
    </div>
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
      {{ value }}
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400">
      {{ description }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['learning', 'accuracy', 'streak', 'mastered'].includes(value)
  },
  value: {
    type: [String, Number],
    required: true
  },
  label: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  }
});

const iconPath = computed(() => {
  const paths = {
    learning: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    accuracy: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    streak: 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z',
    mastered: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'
  };
  return paths[props.type];
});

const gradientClass = computed(() => {
  const gradients = {
    learning: 'bg-gradient-to-br from-blue-500 to-blue-600',
    accuracy: 'bg-gradient-to-br from-green-500 to-green-600',
    streak: 'bg-gradient-to-br from-orange-500 to-orange-600',
    mastered: 'bg-gradient-to-br from-purple-500 to-purple-600'
  };
  return gradients[props.type];
});
</script>
