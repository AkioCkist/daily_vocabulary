/**
 * Dashboard Stats Composable
 * Manages fetching and state for dashboard statistics
 */

import { ref, watch } from 'vue';
import { fetchStatsByDayRange } from '@/services/dashboardService';

export const useDashboardStats = (initialUser) => {
  const memoryDayRange = ref(7);
  const filteredStats = ref(null);
  const isLoadingStats = ref(false);

  const fetchStats = async (days) => {
    if (!initialUser) return;

    isLoadingStats.value = true;
    try {
      const data = await fetchStatsByDayRange(days);
      filteredStats.value = data;
    } catch (error) {
      console.error('useDashboardStats - Error:', error);
      filteredStats.value = null;
    } finally {
      isLoadingStats.value = false;
    }
  };

  // Watch for day range changes
  watch(memoryDayRange, (newDays) => {
    fetchStats(newDays);
  });

  // Initial fetch
  const initializeStats = () => {
    if (initialUser) {
      fetchStats(memoryDayRange.value);
    }
  };

  return {
    memoryDayRange,
    filteredStats,
    isLoadingStats,
    fetchStats,
    initializeStats
  };
};
