<template>
  <Head title="Saved Sessions - DailyVocab" />
  
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
              <BookmarkIcon class="w-6 h-6 inline mr-2" />
              Saved Sessions
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
              Your saved study sessions for future review
            </p>
          </div>
          
          <Link 
            :href="route('home')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            <ArrowLeftIcon class="w-4 h-4 mr-2" />
            Back to Dashboard
          </Link>
        </div>
      </div>

      <!-- Sessions Grid -->
      <div v-if="sessions.data && sessions.data.length > 0" class="space-y-6">
        <!-- Sessions List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="session in sessions.data" 
            :key="session.id"
            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-200 group"
          >
            <!-- Session Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-white text-lg truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                  {{ session.name }}
                </h3>
                
                <div v-if="session.topic" class="mt-2">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ session.topic }}
                  </span>
                </div>
              </div>

              <!-- Dropdown Menu -->
              <div class="relative">
                <button
                  @click="toggleDropdown(session.id)"
                  class="p-1 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                  <EllipsisVerticalIcon class="w-5 h-5" />
                </button>

                <!-- Dropdown Menu -->
                <div 
                  v-if="activeDropdown === session.id"
                  @click.stop
                  class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10"
                >
                  <div class="py-1">
                    <Link
                      :href="route('saved-sessions.show', session.slug)"
                      class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                      <EyeIcon class="w-4 h-4 mr-2" />
                      View Details
                    </Link>
                    
                    <button
                      @click="reviewSession(session)"
                      class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                      <PlayIcon class="w-4 h-4 mr-2" />
                      Review Session
                    </button>
                    
                    <button
                      @click="deleteSession(session)"
                      class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                    >
                      <TrashIcon class="w-4 h-4 mr-2" />
                      Delete Session
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Session Stats -->
            <div class="space-y-3 mb-4">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                  <Square3Stack3DIcon class="w-4 h-4 mr-1" />
                  Flashcards
                </span>
                <span class="font-medium text-gray-900 dark:text-white">
                  {{ session.flashcard_count }}
                </span>
              </div>
              
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                  <CalendarIcon class="w-4 h-4 mr-1" />
                  Created
                </span>
                <span class="font-medium text-gray-900 dark:text-white">
                  {{ formatDate(session.created_at) }}
                </span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
              <button
                @click="reviewSession(session)"
                class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
              >
                <PlayIcon class="w-4 h-4 mr-2" />
                Review
              </button>
              
              <Link
                :href="route('saved-sessions.show', session.slug)"
                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                <EyeIcon class="w-4 h-4" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="sessions.links && sessions.links.length > 3" class="flex justify-center">
          <nav class="flex items-center space-x-1">
            <Link
              v-for="link in sessions.links"
              :key="link.label"
              :href="link.url"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                link.active 
                  ? 'bg-indigo-600 text-white' 
                  : link.url 
                    ? 'text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700' 
                    : 'text-gray-400 dark:text-gray-600 cursor-not-allowed'
              ]"
              v-html="link.label"
            />
          </nav>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <BookmarkIcon class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
          No saved sessions yet
        </h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">
          Complete some study sessions and save them for later review.
        </p>
        <Link 
          :href="route('home')"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
        >
          Start Learning
        </Link>
      </div>
    </div>

    <!-- Review Modal -->
    <ReviewSessionModal 
      v-if="showReviewModal"
      :session="selectedSession"
      @close="showReviewModal = false"
      @start="startReview"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal 
      v-if="showDeleteModal"
      :title="`Delete ${sessionToDelete?.name}`"
      :message="'Are you sure you want to delete this saved session? This action cannot be undone.'"
      :confirm-text="'Delete'"
      :confirm-class="'bg-red-600 hover:bg-red-700 focus:ring-red-500'"
      @confirm="confirmDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
  BookmarkIcon,
  ArrowLeftIcon,
  PlayIcon,
  EyeIcon,
  TrashIcon,
  EllipsisVerticalIcon,
  Square3Stack3DIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline';

// Layout Components
import Header from '@/Components/Header.vue';

// Modal Components
import ReviewSessionModal from '@/Components/Dashboard/ReviewSessionModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  sessions: {
    type: Object,
    required: true
  }
});

// State
const activeDropdown = ref(null);
const showReviewModal = ref(false);
const showDeleteModal = ref(false);
const selectedSession = ref(null);
const sessionToDelete = ref(null);

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

const toggleDropdown = (sessionId) => {
  activeDropdown.value = activeDropdown.value === sessionId ? null : sessionId;
};

const reviewSession = (session) => {
  selectedSession.value = session;
  showReviewModal.value = true;
  activeDropdown.value = null;
};

const startReview = (settings) => {
  router.post(route('saved-sessions.review', selectedSession.value.slug), settings);
};

const deleteSession = (session) => {
  sessionToDelete.value = session;
  showDeleteModal.value = true;
  activeDropdown.value = null;
};

const confirmDelete = () => {
  if (sessionToDelete.value) {
    router.delete(route('saved-sessions.destroy', sessionToDelete.value.slug), {
      onSuccess: () => {
        showDeleteModal.value = false;
        sessionToDelete.value = null;
      }
    });
  }
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    activeDropdown.value = null;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>