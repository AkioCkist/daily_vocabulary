<template>
  <div class="space-y-6">
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

    <!-- Difficulty Filter -->
    <div class="space-y-3">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Difficulty Level
      </label>
      <select
        :value="difficultyFilter"
        @change="$emit('update:difficultyFilter', $event.target.value)"
        class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
      >
        <option value="all">All Difficulties</option>
        <option value="easy">Easy (Low difficulty score)</option>
        <option value="medium">Medium</option>
        <option value="hard">Hard (High difficulty score)</option>
      </select>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        Based on your past performance with each word
      </p>
    </div>

    <!-- Mastery Filter -->
    <div class="space-y-3">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Mastery Status
      </label>
      <select
        :value="masteryFilter"
        @change="$emit('update:masteryFilter', $event.target.value)"
        class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
      >
        <option value="all">All Words</option>
        <option value="mastered">Mastered Only</option>
        <option value="not_mastered">Not Mastered</option>
      </select>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        Filter by whether you've mastered the words
      </p>
    </div>

    <!-- Time-based Filter -->
    <div class="space-y-3">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Study Recency
      </label>
      <select
        :value="timeFilter"
        @change="$emit('update:timeFilter', $event.target.value)"
        class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
      >
        <option value="all">All Words</option>
        <option value="recent">Recently Studied (Last 7 days)</option>
        <option value="not_recent">Not Recently Studied</option>
      </select>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        Filter by when you last studied the words
      </p>
    </div>

    <!-- Sorting Options -->
    <div class="space-y-3">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Sort Order
      </label>
      <select
        :value="sortBy"
        @change="$emit('update:sortBy', $event.target.value)"
        class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
      >
        <option value="random">Random</option>
        <option value="alphabetical">Alphabetical (A-Z)</option>
        <option value="difficulty">By Difficulty (Hardest First)</option>
        <option value="frequency">By Frequency</option>
      </select>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        Choose how words should be ordered
      </p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  wordCount: {
    type: Number,
    default: 10
  },
  difficultyFilter: {
    type: String,
    default: 'all'
  },
  masteryFilter: {
    type: String,
    default: 'all'
  },
  timeFilter: {
    type: String,
    default: 'all'
  },
  sortBy: {
    type: String,
    default: 'random'
  }
});

const emit = defineEmits([
  'update:wordCount',
  'update:difficultyFilter',
  'update:masteryFilter',
  'update:timeFilter',
  'update:sortBy'
]);
</script>
