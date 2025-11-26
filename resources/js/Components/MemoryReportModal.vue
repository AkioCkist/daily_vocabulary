<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="$emit('close')">
    <div class="bg-[#1a1d29] rounded-2xl shadow-2xl border border-gray-700 w-full max-w-6xl max-h-[90vh] overflow-hidden">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-700">
        <div>
          <h2 class="text-2xl font-bold text-white">Memory Report</h2>
          <p class="text-sm text-gray-400 mt-1">Detailed analysis of your learning performance</p>
        </div>
        
        <div class="flex items-center gap-4">
          <!-- Day Range Filter -->
          <div class="flex items-center space-x-1 p-1 bg-black/60 rounded-md">
            <button 
              v-for="day in [1, 7, 30]" 
              :key="day"
              @click="selectedDayRange = day"
              :class="[
                'px-3 py-1.5 text-xs font-semibold rounded-md transition-all',
                selectedDayRange === day 
                  ? 'bg-purple-700 text-white shadow-sm' 
                  : 'text-gray-300 hover:text-white hover:bg-gray-700/50'
              ]"
            >
              {{ day }}D
            </button>
          </div>
          
          <!-- Export Button -->
          <button 
            @click="exportReport"
            :disabled="isLoading || !reportData"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition-all shadow-lg"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export CSV
          </button>
          
          <button @click="$emit('close')" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
        <div v-if="isLoading" class="flex items-center justify-center py-12">
          <div class="text-gray-400">
            <svg class="animate-spin h-8 w-8 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading report...
          </div>
        </div>

        <div v-else-if="reportData" class="space-y-8">
          <!-- Summary Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-black/40 rounded-lg p-4 border border-gray-700">
              <p class="text-xs text-gray-400 mb-1">Total Attempts</p>
              <p class="text-2xl font-bold text-white">{{ reportData.summary.total_attempts }}</p>
            </div>
            <div class="bg-black/40 rounded-lg p-4 border border-gray-700">
              <p class="text-xs text-gray-400 mb-1">Accuracy</p>
              <p class="text-2xl font-bold text-green-400">{{ reportData.summary.accuracy }}%</p>
            </div>
            <div class="bg-black/40 rounded-lg p-4 border border-gray-700">
              <p class="text-xs text-gray-400 mb-1">Words Practiced</p>
              <p class="text-2xl font-bold text-indigo-400">{{ reportData.summary.words_practiced }}</p>
            </div>
            <div class="bg-black/40 rounded-lg p-4 border border-gray-700">
              <p class="text-xs text-gray-400 mb-1">Study Sessions</p>
              <p class="text-2xl font-bold text-purple-400">{{ reportData.summary.study_sessions }}</p>
            </div>
          </div>

          <!-- Performance Chart -->
          <div v-if="reportData.daily_performance && reportData.daily_performance.length > 0" class="bg-black/40 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              Daily Performance Trends
            </h3>
            <div class="relative h-64">
              <Line :data="chartData" :options="chartOptions" />
            </div>
          </div>

          <!-- Frequently Forgotten Words -->
          <div>
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              Frequently Forgotten Words
            </h3>
            
            <div v-if="reportData.frequently_forgotten.length === 0" class="text-gray-400 text-sm italic bg-black/20 rounded-lg p-6 text-center">
              No forgotten words in this period. Great job! 🎉
            </div>
            
            <div v-else class="overflow-x-auto rounded-lg border border-gray-700">
              <table class="w-full">
                <thead class="bg-black/40 border-b border-gray-700">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Word</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Definition</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Attempts</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Correct</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Incorrect</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Accuracy</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                  <tr v-for="word in reportData.frequently_forgotten" :key="word.word_id" class="hover:bg-black/20 transition-colors">
                    <td class="px-4 py-3 text-white font-medium">{{ word.word }}</td>
                    <td class="px-4 py-3 text-gray-300 text-sm max-w-xs truncate">{{ word.definition }}</td>
                    <td class="px-4 py-3 text-center text-gray-300">{{ word.total_attempts }}</td>
                    <td class="px-4 py-3 text-center text-green-400">{{ word.correct_count }}</td>
                    <td class="px-4 py-3 text-center text-red-400">{{ word.incorrect_count }}</td>
                    <td class="px-4 py-3 text-center">
                      <span :class="[
                        'px-2 py-1 rounded text-xs font-semibold',
                        word.accuracy >= 70 ? 'bg-green-500/20 text-green-400' :
                        word.accuracy >= 50 ? 'bg-yellow-500/20 text-yellow-400' :
                        'bg-red-500/20 text-red-400'
                      ]">
                        {{ word.accuracy }}%
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Frequently Remembered Words -->
          <div>
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Frequently Remembered Words
            </h3>
            
            <div v-if="reportData.frequently_remembered.length === 0" class="text-gray-400 text-sm italic bg-black/20 rounded-lg p-6 text-center">
              No remembered words in this period yet. Keep practicing! 💪
            </div>
            
            <div v-else class="overflow-x-auto rounded-lg border border-gray-700">
              <table class="w-full">
                <thead class="bg-black/40 border-b border-gray-700">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Word</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Definition</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Attempts</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Correct</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Incorrect</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Accuracy</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                  <tr v-for="word in reportData.frequently_remembered" :key="word.word_id" class="hover:bg-black/20 transition-colors">
                    <td class="px-4 py-3 text-white font-medium">{{ word.word }}</td>
                    <td class="px-4 py-3 text-gray-300 text-sm max-w-xs truncate">{{ word.definition }}</td>
                    <td class="px-4 py-3 text-center text-gray-300">{{ word.total_attempts }}</td>
                    <td class="px-4 py-3 text-center text-green-400">{{ word.correct_count }}</td>
                    <td class="px-4 py-3 text-center text-red-400">{{ word.incorrect_count }}</td>
                    <td class="px-4 py-3 text-center">
                      <span class="px-2 py-1 rounded text-xs font-semibold bg-green-500/20 text-green-400">
                        {{ word.accuracy }}%
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-12 text-gray-400">
          <p>No data available for the selected period.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

const props = defineProps({
  initialDayRange: {
    type: Number,
    default: 7
  }
});

const emit = defineEmits(['close']);

const selectedDayRange = ref(props.initialDayRange);
const reportData = ref(null);
const isLoading = ref(false);

const fetchReportData = async (days) => {
  isLoading.value = true;
  try {
    const response = await fetch(`/dashboard/memory-report/${days}`);
    if (response.ok) {
      const data = await response.json();
      reportData.value = data;
    } else {
      console.error('Failed to fetch memory report:', response.statusText);
    }
  } catch (error) {
    console.error('Error fetching memory report:', error);
  } finally {
    isLoading.value = false;
  }
};

const exportReport = () => {
  const url = `/dashboard/memory-report/${selectedDayRange.value}/export`;
  window.location.href = url;
};

// Chart data computed property
const chartData = computed(() => {
  if (!reportData.value || !reportData.value.daily_performance) {
    return { labels: [], datasets: [] };
  }

  const dailyData = reportData.value.daily_performance;
  const labels = dailyData.map(day => {
    const date = new Date(day.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  });

  return {
    labels,
    datasets: [
      {
        label: 'Accuracy (%)',
        data: dailyData.map(day => day.accuracy),
        borderColor: 'rgb(99, 102, 241)',
        backgroundColor: 'rgba(99, 102, 241, 0.1)',
        tension: 0.4,
        fill: true,
        yAxisID: 'y',
      },
      {
        label: 'Attempts',
        data: dailyData.map(day => day.attempts),
        borderColor: 'rgb(168, 85, 247)',
        backgroundColor: 'rgba(168, 85, 247, 0.1)',
        tension: 0.4,
        fill: true,
        yAxisID: 'y1',
      }
    ]
  };
});

// Chart options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: 'index',
    intersect: false,
  },
  plugins: {
    legend: {
      display: true,
      position: 'top',
      labels: {
        color: 'rgb(209, 213, 219)',
        font: {
          size: 12,
          family: 'Inter, system-ui, sans-serif'
        },
        padding: 15,
        usePointStyle: true,
      }
    },
    tooltip: {
      backgroundColor: 'rgba(17, 24, 39, 0.95)',
      titleColor: 'rgb(243, 244, 246)',
      bodyColor: 'rgb(209, 213, 219)',
      borderColor: 'rgb(75, 85, 99)',
      borderWidth: 1,
      padding: 12,
      displayColors: true,
      callbacks: {
        label: function(context) {
          let label = context.dataset.label || '';
          if (label) {
            label += ': ';
          }
          if (context.parsed.y !== null) {
            if (context.dataset.label === 'Accuracy (%)') {
              label += context.parsed.y + '%';
            } else {
              label += context.parsed.y;
            }
          }
          return label;
        }
      }
    }
  },
  scales: {
    x: {
      grid: {
        color: 'rgba(75, 85, 99, 0.3)',
        drawBorder: false,
      },
      ticks: {
        color: 'rgb(156, 163, 175)',
        font: {
          size: 11
        }
      }
    },
    y: {
      type: 'linear',
      display: true,
      position: 'left',
      grid: {
        color: 'rgba(75, 85, 99, 0.3)',
        drawBorder: false,
      },
      ticks: {
        color: 'rgb(99, 102, 241)',
        font: {
          size: 11
        },
        callback: function(value) {
          return value + '%';
        }
      },
      title: {
        display: true,
        text: 'Accuracy (%)',
        color: 'rgb(99, 102, 241)',
        font: {
          size: 12,
          weight: 'bold'
        }
      }
    },
    y1: {
      type: 'linear',
      display: true,
      position: 'right',
      grid: {
        drawOnChartArea: false,
      },
      ticks: {
        color: 'rgb(168, 85, 247)',
        font: {
          size: 11
        }
      },
      title: {
        display: true,
        text: 'Attempts',
        color: 'rgb(168, 85, 247)',
        font: {
          size: 12,
          weight: 'bold'
        }
      }
    }
  }
};

watch(selectedDayRange, (newDays) => {
  fetchReportData(newDays);
});

onMounted(() => {
  fetchReportData(selectedDayRange.value);
});
</script>
