<template>
  <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 shadow-lg flex flex-col">
    
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-white">Topics</h3>
      <button 
        @click="$emit('open-topic-modal')"
        class="inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-md"
        title="Manage all topics or create a new one"
      >
        <PlusIcon class="w-4 h-4 mr-1 -ml-0.5" />
        Create / Manage
      </button>
    </div>

    <TopicsSection
      :topics="topics"
      :limit="5" 
      @select="$emit('topic-selected', $event)"
    />
    
  </div>
</template>

<script setup>
import TopicsSection from '@/Components/Dashboard/TopicsSection.vue';
import { PlusIcon } from '@heroicons/vue/20/solid'; // Assumed import

defineProps({
  topics: {
    type: Object,
    default: () => ({
      system: [],
      user: []
    })
  }
});

// Emits:
// open-topic-modal: Fired by the new button to open the main topic/flashcard settings modal.
// topic-selected: Fired when a topic in the list is clicked.
defineEmits(['open-topic-modal', 'topic-selected']); 
</script>