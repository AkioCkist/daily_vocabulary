<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-200">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
        <BookmarkIcon class="w-5 h-5 inline mr-2" />
        Saved Sessions
      </h2>
      
      <!-- Toggle visibility and view all -->
      <div class="flex items-center gap-2">
        <button 
          @click="isExpanded = !isExpanded"
          class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          :class="{ 
            'text-gray-400': !isExpanded, 
            'text-gray-600 dark:text-gray-400': isExpanded 
          }"
        >
          <ChevronDownIcon 
            class="w-4 h-4 transition-transform duration-200"
            :class="{ 'transform rotate-180': isExpanded }"
          />
        </button>
        
        <Link 
          :href="route('saved-sessions.index')"
          class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 font-medium"
        >
          View All
        </Link>
      </div>
    </div>

    <!-- Content -->
    <div v-if="isExpanded" class="space-y-3">
      <!-- No sessions message -->
      <div v-if="!sessions || sessions.length === 0" class="text-center py-8">
        <BookmarkIcon class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
        <p class="text-gray-500 dark:text-gray-400 text-sm">
          No saved sessions yet
        </p>
        <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
          Complete a study session to save it for later review
        </p>
      </div>

      <!-- Sessions list -->
      <div v-else class="space-y-2">
        <div 
          v-for="session in sessions" 
          :key="session.id"
          class="flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
        >
          <!-- Session info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <h3 class="font-medium text-gray-900 dark:text-white text-sm truncate">
                {{ session.name }}
              </h3>
              <span v-if="session.topic" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                {{ session.topic }}
              </span>
            </div>
            
            <div class="flex items-center gap-4 mt-1 text-xs text-gray-500 dark:text-gray-400">
              <span class="flex items-center gap-1">
                <Square3Stack3DIcon class="w-3 h-3" />
                {{ session.flashcard_count }} cards
              </span>
              <span>{{ formatDate(session.created_at) }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 ml-3">
            <button
              @click="reviewSession(session)"
              class="p-1.5 text-green-600 hover:text-green-800 hover:bg-green-50 dark:text-green-400 dark:hover:text-green-200 dark:hover:bg-green-900/20 rounded transition-colors"
              title="Review this session"
            >
              <PlayIcon class="w-4 h-4" />
            </button>
            
            <Link
              :href="route('saved-sessions.show', session.slug)"
              class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded transition-colors"
              title="View details"
            >
              <EyeIcon class="w-4 h-4" />
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Collapsed preview -->
    <div v-else class="text-sm text-gray-500 dark:text-gray-400">
      {{ sessions && sessions.length > 0 ? `${sessions.length} saved session${sessions.length > 1 ? 's' : ''}` : 'No saved sessions' }}
    </div>
  </div>

  <!-- Review Modal -->
  <ReviewSessionModal 
    v-if="showReviewModal"
    :session="selectedSession"
    @close="showReviewModal = false"
    @start="startReview"
  />
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { 
  BookmarkIcon, 
  ChevronDownIcon, 
  PlayIcon, 
  EyeIcon,
  Square3Stack3DIcon
} from '@heroicons/vue/24/outline';

// Import the review modal component
import ReviewSessionModal from './ReviewSessionModal.vue';

const props = defineProps({
  sessions: {
    type: Array,
    default: () => []
  }
});

// State
const isExpanded = ref(true);
const showReviewModal = ref(false);
const selectedSession = ref(null);

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now - date);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 1) return 'Today';
  if (diffDays === 2) return 'Yesterday';
  if (diffDays <= 7) return `${diffDays - 1} days ago`;
  
  return date.toLocaleDateString();
};

const reviewSession = (session) => {
  selectedSession.value = session;
  showReviewModal.value = true;
};

const startReview = (settings) => {
  router.post(route('saved-sessions.review', selectedSession.value.slug), settings);
};
</script>