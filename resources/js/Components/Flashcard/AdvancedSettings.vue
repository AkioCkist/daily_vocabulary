<template>
  <div class="space-y-6">
    <!-- CEFR Level Selection -->
    <div class="space-y-3">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        CEFR Level
      </label>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="(label, level) in cefrLevels"
          :key="level"
          @click="toggleLevel(level)"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
            selectedLevels.includes(level)
              ? 'bg-indigo-500 text-white shadow-md scale-105'
              : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
          ]"
        >
          {{ level }}
        </button>
      </div>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        {{ selectedLevels.length > 0 ? selectedLevels.join(', ') : 'All levels' }}
      </p>
    </div>

    <!-- Word Count Slider -->
    <div class="space-y-3">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Number of Words: <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ wordCount }}</span>
      </label>
      <input
        type="range"
        :value="wordCount"
        @input="$emit('update:wordCount', parseInt($event.target.value))"
        min="5"
        max="50"
        step="5"
        class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-500"
      >
      <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
        <span>5</span>
        <span>25</span>
        <span>50</span>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  cefrLevels: {
    type: Object,
    required: true
  },
  selectedLevels: {
    type: Array,
    default: () => []
  },
  wordCount: {
    type: Number,
    default: 10
  }
});

const emit = defineEmits(['update:selectedLevels', 'update:wordCount']);

function toggleLevel(level) {
  const currentLevels = [...props.selectedLevels];
  const index = currentLevels.indexOf(level);
  
  if (index > -1) {
    currentLevels.splice(index, 1);
  } else {
    currentLevels.push(level);
  }
  
  emit('update:selectedLevels', currentLevels);
}
</script>
