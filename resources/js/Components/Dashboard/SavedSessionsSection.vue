<template>
  <div 
    class="rounded-2xl bg-gray-900/70 border border-indigo-700/50 p-6 shadow-2xl shadow-indigo-900/30 flex flex-col transition-all duration-300 hover:shadow-indigo-800/40"
  >
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-white flex items-center">
        <BookmarkIcon class="w-5 h-5 inline mr-2 text-indigo-400" />
        Saved Sessions
      </h3>
      
      <Link 
        :href="route('saved-sessions.index')"
        class="text-xs font-semibold text-indigo-300 hover:underline"
      >
        View All ({{ sessions.length }})
      </Link>
    </div>

    <div v-if="sessions.length > 0" class="space-y-3">
      <SavedSessionItem
        v-for="session in sessions.slice(0, 3)"
        :key="session.id"
        :session="session"
        @review="handleReview"
      />
      
      <div v-if="sessions.length > 3" class="text-center pt-2">
        <Link 
          :href="route('saved-sessions.index')"
          class="text-xs font-medium text-gray-400 hover:text-indigo-400 transition-colors"
        >
          + {{ sessions.length - 3 }} more sessions
        </Link>
      </div>
    </div>

    <div v-else class="text-center py-4">
      <p class="text-gray-500 text-sm">
        No sessions saved yet. Start practicing and save your progress!
      </p>
    </div>
  </div>

  <ReviewSessionModal 
    :show="showReviewModal"
    :session="selectedSession"
    @close="showReviewModal = false"
    @start-review="startReview"
  />
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { BookmarkIcon } from '@heroicons/vue/24/outline';

// Assuming this component exists to handle the individual session item display and actions
import SavedSessionItem from './SavedSessionItem.vue'; 
import ReviewSessionModal from '@/Components/Modals/ReviewSessionModal.vue'; // Assuming location

const props = defineProps({
  sessions: {
    type: Array,
    default: () => [] // Expected: [{ id, name, word_count, created_at, slug, ... }]
  }
});

// State
const showReviewModal = ref(false);
const selectedSession = ref(null);

// Methods
const handleReview = (session) => {
  selectedSession.value = session;
  showReviewModal.value = true;
};

const startReview = (settings) => {
  // Use the session slug/id to route to the review page
  if (selectedSession.value) {
    router.post(route('saved-sessions.review', selectedSession.value.slug), settings);
  }
};
</script>