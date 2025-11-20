<template>
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl mb-4 shadow-sm">
      <span class="text-3xl">📊</span>
    </div>
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
      {{ title }}
    </h1>
    <p class="text-gray-600 dark:text-gray-400">
      {{ subtitle }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
});

const title = computed(() => {
  return props.user 
    ? `Welcome back, ${props.user.name}!` 
    : 'Vocabulary Dashboard';
});

const subtitle = computed(() => {
  return props.user 
    ? 'Track your progress and continue learning' 
    : 'Discover, learn, and master new words';
});
</script>
