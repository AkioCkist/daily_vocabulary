<template>
  <div class="github-heatmap">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
        <span class="font-medium text-gray-900 dark:text-white">
          {{ data.summary.total_active_days }} contributions {{ summaryText }}
        </span>
        <span class="text-gray-400">•</span>
        <span>{{ data.summary.current_streak }} day streak</span>
      </div>
      
      <!-- Legend -->
      <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
        <span>Less</span>
        <div class="flex gap-1">
          <div 
            v-for="level in 5" 
            :key="level"
            :class="getSquareColor(level - 1)"
            class="w-2.5 h-2.5 rounded-sm"
          />
        </div>
        <span>More</span>
      </div>
    </div>

    <!-- Month Labels -->
    <div class="flex gap-1 mb-2 ml-8">
      <div 
        v-for="month in monthLabels" 
        :key="month.name"
        class="text-xs text-gray-500 dark:text-gray-400 flex-1 text-left"
        :style="{ minWidth: month.width + 'px' }"
      >
        {{ month.name }}
      </div>
    </div>

    <!-- Grid Container -->
    <div class="flex gap-2">
      <!-- Day Labels -->
      <div class="flex flex-col justify-around text-xs text-gray-500 dark:text-gray-400 pr-1">
        <div>Mon</div>
        <div>Wed</div>
        <div>Fri</div>
      </div>

      <!-- Contribution Squares -->
      <div class="flex-1 overflow-hidden">
        <div class="inline-flex gap-1">
          <div 
            v-for="week in groupedByWeeks" 
            :key="week.weekStart"
            class="flex flex-col gap-1"
          >
            <div
              v-for="day in week.days"
              :key="day.date"
              :class="[
                'contribution-square',
                getSquareColor(day.level)
              ]"
              :data-date="day.date"
              :data-count="day.attempts"
              @mouseenter="showTooltip($event, day)"
              @mouseleave="hideTooltip"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Tooltip -->
    <Teleport to="body">
      <div
        v-if="tooltip.show"
        class="fixed z-50 pointer-events-none"
        :style="tooltip.style"
      >
        <div class="bg-gray-900 text-white text-xs rounded-lg px-3 py-2 shadow-xl border border-gray-700 min-w-[180px]">
          <div class="font-semibold mb-1">{{ formatDate(tooltip.day.date) }}</div>
          <div class="space-y-0.5 text-gray-300">
            <div class="flex justify-between gap-4">
              <span>Attempts:</span>
              <span class="font-medium text-white">{{ tooltip.day.attempts }}</span>
            </div>
            <div v-if="tooltip.day.attempts > 0" class="flex justify-between gap-4">
              <span>Accuracy:</span>
              <span class="font-medium text-green-400">{{ tooltip.day.accuracy }}%</span>
            </div>
            <div v-if="tooltip.day.attempts > 0" class="flex justify-between gap-4">
              <span>Correct:</span>
              <span class="font-medium text-white">{{ tooltip.day.correct }}</span>
            </div>
            <div v-if="tooltip.day.attempts === 0" class="text-gray-400 italic">
              No activity
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, reactive } from 'vue';

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  period: {
    type: String,
    default: 'yearly'
  }
});

// Tooltip state
const tooltip = reactive({
  show: false,
  day: null,
  style: {}
});

// Group data by weeks (starting Monday)
const groupedByWeeks = computed(() => {
  const weeks = [];
  let data = props.data.data;
  
  // Filter data based on period
  const now = new Date();
  if (props.period === 'weekly') {
    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
    data = data.filter(day => new Date(day.date) >= weekAgo);
  } else if (props.period === 'monthly') {
    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
    data = data.filter(day => new Date(day.date) >= monthAgo);
  }
  
  // Group by weeks
  let currentWeek = { weekStart: null, days: [] };
  
  data.forEach((day, index) => {
    const date = new Date(day.date);
    const dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, etc.
    
    // Start new week on Monday (dayOfWeek === 1) or first day
    if (dayOfWeek === 1 || index === 0) {
      if (currentWeek.days.length > 0) {
        weeks.push(currentWeek);
      }
      currentWeek = { weekStart: day.date, days: [] };
    }
    
    currentWeek.days.push(day);
  });
  
  // Add last week
  if (currentWeek.days.length > 0) {
    weeks.push(currentWeek);
  }
  
  return weeks;
});

// Month labels
const monthLabels = computed(() => {
  const months = [];
  let currentMonth = null;
  let weekCount = 0;
  
  groupedByWeeks.value.forEach((week) => {
    const date = new Date(week.weekStart);
    const monthName = date.toLocaleDateString('en-US', { month: 'short' });
    
    if (monthName !== currentMonth) {
      if (currentMonth && weekCount > 0) {
        months.push({
          name: currentMonth,
          width: weekCount * 11 // 10px width + 1px gap
        });
      }
      currentMonth = monthName;
      weekCount = 1;
    } else {
      weekCount++;
    }
  });
  
  // Add last month
  if (currentMonth && weekCount > 0) {
    months.push({
      name: currentMonth,
      width: weekCount * 11
    });
  }
  
  return months;
});

// Summary text based on period
const summaryText = computed(() => {
  if (props.period === 'weekly') {
    return 'in the last week';
  } else if (props.period === 'monthly') {
    return 'in the last month';
  }
  return 'in the last year';
});

// Color coding
function getSquareColor(level) {
  const colors = [
    'bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700',
    'bg-green-200 dark:bg-green-900',
    'bg-green-400 dark:bg-green-700',
    'bg-green-500 dark:bg-green-600',
    'bg-green-600 dark:bg-green-500'
  ];
  return colors[Math.min(level, 4)] || colors[0];
}

// Tooltip handlers
function showTooltip(event, day) {
  const rect = event.target.getBoundingClientRect();
  const tooltipWidth = 180;
  
  // Calculate position
  let left = rect.left + rect.width / 2;
  const top = rect.top - 10;
  
  // Adjust if tooltip would go off screen
  if (left + tooltipWidth / 2 > window.innerWidth) {
    left = window.innerWidth - tooltipWidth / 2 - 10;
  } else if (left - tooltipWidth / 2 < 0) {
    left = tooltipWidth / 2 + 10;
  }
  
  tooltip.show = true;
  tooltip.day = day;
  tooltip.style = {
    left: left + 'px',
    top: top + 'px',
    transform: 'translate(-50%, -100%)'
  };
}

function hideTooltip() {
  tooltip.show = false;
  tooltip.day = null;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}
</script>

<style scoped>
.contribution-square {
  width: 10px;
  height: 10px;
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.1s ease;
}

.contribution-square:hover {
  outline: 2px solid rgba(99, 102, 241, 0.5);
  outline-offset: 1px;
}

.github-heatmap {
  user-select: none;
  overflow: hidden;
}

/* Hide scrollbar completely */
.github-heatmap ::-webkit-scrollbar {
  display: none;
}

.github-heatmap {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
