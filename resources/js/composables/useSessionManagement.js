/**
 * Session Management Composable
 * Handles session saving, refreshing, and modal logic
 */

import { router } from '@inertiajs/vue3';

export const useSessionManagement = () => {
  const refreshDashboard = () => {
    return router.reload({ only: ['dashboard', 'saved_sessions'] });
  };

  const handleSessionSaved = (onSessionSaved) => {
    // Callback to be invoked after session is saved
    if (onSessionSaved) {
      onSessionSaved();
    }
    refreshDashboard();
  };

  return {
    refreshDashboard,
    handleSessionSaved
  };
};
