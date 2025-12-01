<template>
  <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 shadow-lg">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-white">Topics</h3>
      <button @click="$emit('open-topic-modal')" class="text-xs font-semibold text-indigo-300 hover:underline">View All</button>
    </div>
    <TopicsSection
      :topics="topics"
      :limit="3"
      @select="$emit('topic-selected', $event)"
      @manage="$emit('open-topic-modal')"
    />
  </div>
</template>

<script setup>
import TopicsSection from '@/Components/Dashboard/TopicsSection.vue';

defineProps({
  topics: {
    type: Object,
    default: () => ({
      system: [],
      user: []
    })
  }
});

defineEmits(['open-topic-modal', 'topic-selected']);
</script>
