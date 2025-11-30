<template>
  <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 shadow-lg">
    <h3 class="text-lg font-bold text-white mb-4">Learning Recap</h3>
    <p v-if="sessions.length === 0" class="text-gray-400 text-sm italic">
      No past sessions found. Start a study session to see your learning recap here.
    </p>
    <SavedSessionsSection v-else :sessions="sessions" />
  </div>
</template>

<script setup>
import SavedSessionsSection from '@/Components/Dashboard/SavedSessionsSection.vue';

defineProps({
  sessions: {
    type: Array,
    default: () => []
  }
});
</script>
