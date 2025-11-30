/**
 * Accuracy Composable
 * Calculates accuracy percentage from stats
 */

import { computed } from 'vue';

export const useAccuracy = (stats) => {
  const accuracyPercentage = computed(() => {
    if (!stats.value) return null;
    
    const correct = stats.value.correct_answers || 0;
    const incorrect = stats.value.incorrect_answers || 0;
    const total = correct + incorrect;
    
    if (total === 0) return 0;
    return Math.round((correct / total) * 100);
  });

  return {
    accuracyPercentage
  };
};
