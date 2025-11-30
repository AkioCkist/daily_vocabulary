<template>
  <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 shadow-lg">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="p-2 bg-black/60 rounded-md">
          <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-white">Learning Activity</h3>
      </div>

      <div class="flex p-1 bg-black/60 rounded-lg">
        <button
          v-for="period in periods"
          :key="period"
          @click="$emit('update:period', period)"
          :class="[
            'px-4 py-1.5 text-xs font-semibold rounded-md transition-all',
            activePeriod === period ? 'bg-indigo-700 text-white shadow-sm' : 'text-gray-300 hover:text-white'
          ]"
        >
          {{ capitalize(period) }}
        </button>
      </div>
    </div>

    <div class="w-full overflow-x-auto">
      <GitHubHeatmap :data="data" :period="activePeriod" />
    </div>
  </div>
</template>

<script setup>
import GitHubHeatmap from '@/Components/Dashboard/GitHubHeatmap.vue';

defineProps({
  data: {
    type: Object,
    default: null
  },
  activePeriod: {
    type: String,
    default: 'yearly'
  }
});

const periods = ['weekly', 'monthly', 'yearly'];

const capitalize = (str) => {
  return str.charAt(0).toUpperCase() + str.slice(1);
};

defineEmits(['update:period']);
</script>
