<template>
  <div class="learning-heatmap">
    <!-- Summary Stats -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-6 text-sm text-gray-600 dark:text-gray-400">
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 bg-green-500 rounded-full"></div>
          <span>{{ data.summary.total_active_days }} active days</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
          <span>{{ data.summary.current_streak }} day streak</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
          <span>{{ data.summary.longest_streak }} best streak</span>
        </div>
      </div>
      
      <!-- Legend -->
      <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
        <span>Less</span>
        <div class="flex gap-1">
          <div
            v-for="level in 5"
            :key="level"
            :class="getHeatmapColor(level - 1)"
            class="w-3 h-3 rounded-sm"
          ></div>
        </div>
        <span>More</span>
      </div>
    </div>

    <!-- Heatmap Grid -->
    <div class="heatmap-container">
      <!-- Month Labels -->
      <div class="month-labels">
        <div
          v-for="month in monthLabels"
          :key="month.name"
          :style="{ gridColumn: `${month.start} / ${month.end}` }"
          class="text-xs text-gray-500 dark:text-gray-400 font-medium"
        >
          {{ month.name }}
        </div>
      </div>

      <!-- Day Labels -->
      <div class="day-labels">
        <div class="text-xs text-gray-500 dark:text-gray-400">Mon</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">Wed</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">Fri</div>
      </div>

      <!-- Heatmap Cells -->
      <div class="heatmap-grid">
        <div
          v-for="(day, index) in displayData"
          :key="day.date"
          :class="[
            'heatmap-cell',
            getHeatmapColor(day.level),
            'group relative'
          ]"
          :style="getGridPosition(index)"
          @mouseenter="showTooltip($event, day)"
          @mouseleave="hideTooltip"
        >
          <!-- Tooltip -->
          <div
            v-if="tooltip.show && tooltip.day?.date === day.date"
            class="tooltip"
            :style="tooltip.style"
          >
            <div class="bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg p-3 shadow-lg border border-gray-600">
              <div class="font-medium">{{ formatDate(day.date) }}</div>
              <div class="mt-1 space-y-1">
                <div>{{ day.attempts }} attempts</div>
                <div v-if="day.attempts > 0">{{ day.accuracy }}% accuracy</div>
                <div v-else class="text-gray-400">No activity</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Week View (for weekly period) -->
    <div v-if="period === 'weekly'" class="mt-8">
      <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">This Week</h4>
      <div class="grid grid-cols-7 gap-2">
        <div
          v-for="day in weekData"
          :key="day.date"
          class="text-center"
        >
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
            {{ formatDayOfWeek(day.date) }}
          </div>
          <div
            :class="[
              'w-12 h-12 rounded-lg flex items-center justify-center text-sm font-medium',
              getHeatmapColor(day.level),
              day.level > 0 ? 'text-white' : 'text-gray-500 dark:text-gray-400'
            ]"
          >
            {{ day.attempts }}
          </div>
          <div class="text-xs text-gray-400 mt-1">
            {{ day.accuracy > 0 ? day.accuracy + '%' : '' }}
          </div>
        </div>
      </div>
    </div>

    <!-- Monthly Summary (for monthly/yearly period) -->
    <div v-if="period !== 'weekly'" class="mt-8">
      <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Monthly Summary</h4>
      <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div
          v-for="month in monthlySummary"
          :key="month.month"
          class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3"
        >
          <div class="text-sm font-medium text-gray-900 dark:text-white">
            {{ month.name }}
          </div>
          <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
            {{ month.totalAttempts }}
          </div>
          <div class="text-xs text-gray-500 dark:text-gray-400">
            {{ month.avgAccuracy }}% avg accuracy
          </div>
          <div class="text-xs text-gray-500 dark:text-gray-400">
            {{ month.activeDays }} active days
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, reactive } from 'vue';

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  period: {
    type: String,
    default: 'monthly',
    validator: (value) => ['weekly', 'monthly', 'yearly'].includes(value)
  }
});

// Reactive state
const tooltip = reactive({
  show: false,
  day: null,
  style: {}
});

// Computed properties
const displayData = computed(() => {
  switch (props.period) {
    case 'weekly':
      return props.data.data.slice(-7);
    case 'monthly':
      return props.data.data.slice(-30);
    case 'yearly':
    default:
      return props.data.data;
  }
});

const weekData = computed(() => {
  if (props.period !== 'weekly') return [];
  return props.data.data.slice(-7);
});

const monthLabels = computed(() => {
  if (props.period === 'weekly') return [];
  
  const months = [];
  const data = displayData.value;
  let currentMonth = null;
  let start = 1;
  
  data.forEach((day, index) => {
    const date = new Date(day.date);
    const monthName = date.toLocaleDateString('en-US', { month: 'short' });
    
    if (monthName !== currentMonth) {
      if (currentMonth) {
        months.push({
          name: currentMonth,
          start,
          end: Math.floor(index / 7) + 1
        });
      }
      currentMonth = monthName;
      start = Math.floor(index / 7) + 1;
    }
  });
  
  if (currentMonth) {
    months.push({
      name: currentMonth,
      start,
      end: Math.ceil(data.length / 7) + 1
    });
  }
  
  return months;
});

const monthlySummary = computed(() => {
  if (props.period === 'weekly') return [];
  
  const monthlyData = {};
  displayData.value.forEach(day => {
    const date = new Date(day.date);
    const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
    const monthName = date.toLocaleDateString('en-US', { month: 'short' });
    
    if (!monthlyData[monthKey]) {
      monthlyData[monthKey] = {
        name: monthName,
        totalAttempts: 0,
        totalCorrect: 0,
        activeDays: 0,
        month: monthKey
      };
    }
    
    monthlyData[monthKey].totalAttempts += day.attempts;
    monthlyData[monthKey].totalCorrect += day.correct;
    if (day.attempts > 0) {
      monthlyData[monthKey].activeDays++;
    }
  });
  
  return Object.values(monthlyData).map(month => ({
    ...month,
    avgAccuracy: month.totalAttempts > 0 
      ? Math.round((month.totalCorrect / month.totalAttempts) * 100)
      : 0
  }));
});

// Methods
function getHeatmapColor(level) {
  const colors = [
    'bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700',
    'bg-green-200 dark:bg-green-900/40',
    'bg-green-300 dark:bg-green-800/60',
    'bg-green-400 dark:bg-green-700/80',
    'bg-green-500 dark:bg-green-600'
  ];
  return colors[level] || colors[0];
}

function getGridPosition(index) {
  const col = Math.floor(index / 7) + 1;
  const row = (index % 7) + 1;
  return {
    gridColumn: col,
    gridRow: row
  };
}

function showTooltip(event, day) {
  const rect = event.target.getBoundingClientRect();
  tooltip.show = true;
  tooltip.day = day;
  tooltip.style = {
    position: 'fixed',
    left: rect.left + rect.width / 2 + 'px',
    top: rect.top - 10 + 'px',
    transform: 'translate(-50%, -100%)',
    zIndex: 1000,
    pointerEvents: 'none'
  };
}

function hideTooltip() {
  tooltip.show = false;
  tooltip.day = null;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric'
  });
}

function formatDayOfWeek(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { weekday: 'short' });
}
</script>

<style scoped>
.heatmap-container {
  display: grid;
  grid-template-areas: 
    ". months"
    "days heatmap";
  grid-template-columns: auto 1fr;
  grid-template-rows: auto 1fr;
  gap: 8px;
}

.month-labels {
  grid-area: months;
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: minmax(0, 1fr);
  align-items: end;
  height: 20px;
}

.day-labels {
  grid-area: days;
  display: grid;
  grid-template-rows: repeat(7, 1fr);
  gap: 2px;
  padding-right: 8px;
  align-items: center;
}

.heatmap-grid {
  grid-area: heatmap;
  display: grid;
  grid-template-rows: repeat(7, 1fr);
  grid-auto-flow: column;
  grid-auto-columns: 12px;
  gap: 2px;
}

.heatmap-cell {
  width: 12px;
  height: 12px;
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.heatmap-cell:hover {
  transform: scale(1.1);
  z-index: 10;
}

.tooltip {
  position: absolute;
  z-index: 1000;
  pointer-events: none;
}

@media (max-width: 768px) {
  .heatmap-grid {
    grid-auto-columns: 10px;
  }
  
  .heatmap-cell {
    width: 10px;
    height: 10px;
  }
}
</style>