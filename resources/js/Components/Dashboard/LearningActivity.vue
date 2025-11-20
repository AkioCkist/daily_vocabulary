<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-gray-900 dark:text-white">
        Learning Activity
      </h2>
      
      <!-- Period Selector -->
      <div class="flex gap-2">
        <button 
          v-for="period in periods" 
          :key="period"
          @click="$emit('changePeriod', period)"
          :class="[
            'px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200',
            currentPeriod === period 
              ? 'bg-indigo-500 text-white' 
              : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
          ]"
        >
          {{ capitalize(period) }}
        </button>
      </div>
    </div>
    
    <!-- Heatmap Component -->
    <LearningHeatmap 
      :data="heatmapData" 
      :period="currentPeriod"
    />
  </div>
</template>

<script setup>
import LearningHeatmap from '@/Components/LearningHeatmap.vue';

defineProps({
  heatmapData: {
    type: Object,
    required: true
  },
  currentPeriod: {
    type: String,
    default: 'monthly'
  }
});

defineEmits(['changePeriod']);

const periods = ['weekly', 'monthly', 'yearly'];

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);
</script>
