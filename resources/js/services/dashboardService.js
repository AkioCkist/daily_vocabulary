/**
 * Dashboard Service
 * Handles all API calls related to dashboard stats and data
 */

export const fetchStatsByDayRange = async (days) => {
  try {
    const response = await fetch(`/dashboard/stats/${days}`);
    
    if (!response.ok) {
      throw new Error(`Failed to fetch stats: ${response.statusText}`);
    }
    
    return await response.json();
  } catch (error) {
    console.error('Dashboard Service - Error fetching stats:', error);
    throw error;
  }
};
