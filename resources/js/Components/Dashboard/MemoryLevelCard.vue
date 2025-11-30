<template>
  <div
    class="rounded-2xl bg-black/80 border border-gray-800 p-6 flex flex-col justify-between shadow-lg hover:border-purple-500/50 transition-colors cursor-pointer"
    @click="$emit('view-details')"
  >
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-white">Memory Level</h3>
      <div class="flex items-center space-x-1 p-1 bg-black/60 rounded-md">
        <button
          v-for="day in dayOptions"
          :key="day"
          @click.stop="$emit('update:dayRange', day)"
          :class="[
            'px-2 py-1 text-xs font-semibold rounded-md transition-all',
            dayRange === day
              ? 'bg-purple-700 text-white shadow-sm'
              : 'text-gray-300 hover:text-white hover:bg-gray-700/50'
          ]"
        >
          {{ day }}D
        </button>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-y-4 gap-x-6">
      <div class="flex flex-col">
        <p class="text-xl font-bold text-white">
          <span v-if="isLoading" class="text-gray-500">...</span>
          <span v-else>{{ stats?.total_study_sessions || '0' }}</span>
        </p>
        <p class="text-xs font-medium text-gray-400">Times Studied</p>
      </div>

      <div class="flex flex-col">
        <p class="text-xl font-bold text-white">
          <span v-if="isLoading" class="text-gray-500">...</span>
          <span v-else>{{ stats?.total_words_learned || '0' }}</span>
        </p>
        <p class="text-xs font-medium text-gray-400">Words Learned</p>
      </div>

      <div class="flex flex-col">
        <p class="text-xl font-bold">
          <span v-if="isLoading" class="text-gray-500">...</span>
          <template v-else>
            <span class="text-green-400">{{ stats?.correct_answers || '0' }}</span>
            <span class="text-gray-500">/</span>
            <span class="text-red-400">{{ stats?.incorrect_answers || '0' }}</span>
          </template>
        </p>
        <p class="text-xs font-medium text-gray-400">
          Correct / Wrong
          <span v-if="!isLoading && accuracyPercentage !== null" class="text-indigo-400">
            ({{ accuracyPercentage }}%)
          </span>
        </p>
      </div>

      <div class="flex flex-col">
        <p class="text-xl font-bold text-yellow-400">
          <span v-if="isLoading" class="text-gray-500">...</span>
          <span v-else>{{ stats?.streak_days || '0' }}</span>
        </p>
        <p class="text-xs font-medium text-gray-400">Day Streak</p>
      </div>
    </div>

    <p class="mt-4 text-xs text-purple-400/80 hover:text-purple-300 transition-colors text-right">
      Click to view detailed report &rarr;
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  dayRange: {
    type: Number,
    default: 7
  },
  stats: {
    type: Object,
    default: null
  },
  isLoading: {
    type: Boolean,
    default: false
  }
});

const dayOptions = [1, 7, 30];

const accuracyPercentage = computed(() => {
  if (!props.stats) return null;
  const correct = props.stats.correct_answers || 0;
  const incorrect = props.stats.incorrect_answers || 0;
  const total = correct + incorrect;
  if (total === 0) return 0;
  return Math.round((correct / total) * 100);
});

defineEmits(['view-details', 'update:dayRange']);
</script>
