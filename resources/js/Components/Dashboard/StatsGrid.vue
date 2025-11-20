<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <StatsCard
      type="learning"
      :value="stats.words_learning"
      label="Learning"
      description="Words in progress"
    />
    
    <StatsCard
      type="accuracy"
      :value="`${stats.accuracy_rate}%`"
      label="Accuracy"
      :description="`${stats.correct_answers}/${stats.total_attempts}`"
    />
    
    <StatsCard
      type="streak"
      :value="stats.learning_streak"
      label="Streak"
      description="Days in a row"
    />
    
    <StatsCard
      type="mastered"
      :value="stats.words_mastered"
      label="Mastered"
      description="Total mastered"
    />
  </div>
</template>

<script setup>
import StatsCard from './StatsCard.vue';

defineProps({
  stats: {
    type: Object,
    required: true
  }
});
</script>
