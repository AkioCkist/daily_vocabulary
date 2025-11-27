<template>
  <Head title="Saved Sessions - DailyVocab" />
  
  <div class="min-h-screen bg-[#0B0C10] text-slate-100 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    
    <div class="sticky top-0 z-50 bg-black/90 backdrop-blur-md border-b border-gray-800">
      <Header :user="user" />
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 relative z-20">
      
      <div class="mb-10">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">
              <BookmarkIcon class="w-8 h-8 inline mr-2 align-text-bottom" />
              Your Saved Sessions
            </h1>
            <p class="text-gray-400 mt-2 text-lg font-light">
              Review and manage your custom study sessions
            </p>
          </div>
          
          <Link 
            :href="route('home')"
            class="inline-flex items-center px-6 py-3 border border-gray-700 rounded-xl shadow-lg shadow-gray-900/40 text-sm font-semibold text-gray-300 bg-gray-800/70 hover:bg-gray-700/70 transition-all duration-200 hover:scale-[1.02] active:scale-95"
          >
            <ArrowLeftIcon class="w-5 h-5 mr-2" />
            Back to Dashboard
          </Link>
        </div>
      </div>

      <div v-if="sessions.data && sessions.data.length > 0" class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        <div 
          v-for="session in sessions.data" 
          :key="session.id"
          class="bg-gray-900/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-indigo-900/20 ring-1 ring-gray-700/50 p-6 flex flex-col justify-between transition-all duration-300 hover:ring-indigo-600 hover:shadow-2xl hover:shadow-indigo-900/40"
        >
          <div>
            <div class="flex items-start justify-between mb-4">
              <span class="px-3 py-1 text-xs font-semibold rounded-full"
                    :class="[session.topic ? 'bg-indigo-900/50 text-indigo-300' : 'bg-gray-700/50 text-gray-300']">
                {{ session.topic ? session.topic.name : 'Personal Session' }}
              </span>
              
              <div class="relative">
                <button
                  @click.stop="toggleDropdown(session.id)"
                  class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-700/50 transition-colors"
                >
                  <EllipsisVerticalIcon class="w-5 h-5" />
                </button>
                
                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <div 
                    v-if="activeDropdown === session.id" 
                    class="absolute right-0 mt-2 w-48 rounded-lg shadow-xl bg-gray-800 ring-1 ring-gray-700/50 z-10 origin-top-right"
                    @click.stop
                  >
                    <div class="py-1">
                      <a 
                        @click.prevent="viewSession(session)"
                        class="flex items-center px-4 py-2 text-sm text-indigo-300 hover:bg-gray-700/50 cursor-pointer transition-colors"
                      >
                        <PencilSquareIcon class="w-4 h-4 mr-2" />
                        View &amp; Edit
                      </a>
                      <a 
                        @click.prevent="deleteSession(session)"
                        class="flex items-center px-4 py-2 text-sm text-red-400 hover:bg-red-900/30 cursor-pointer transition-colors"
                      >
                        <TrashIcon class="w-4 h-4 mr-2" />
                        Delete Session
                      </a>
                    </div>
                  </div>
                </transition>
              </div>
            </div>

            <h2 class="text-2xl font-bold text-white mb-2 line-clamp-2">
              {{ session.name }}
            </h2>

            <div class="space-y-3 mt-4 text-sm text-gray-300">
              <p class="flex items-center gap-2">
                <TagIcon class="w-4 h-4 text-indigo-400" />
                <span class="font-medium">Words:</span> {{ session.flashcard_count }}
              </p>
              <p class="flex items-center gap-2">
                <CalendarIcon class="w-4 h-4 text-purple-400" />
                <span class="font-medium">Created:</span> {{ formatRelativeDate(session.created_at) }}
              </p>
            </div>
          </div>

          <button
            @click.prevent="reviewSession(session)"
            class="w-full mt-6 inline-flex items-center justify-center py-2.5 px-4 rounded-xl font-bold transition-all duration-200 
                   bg-gradient-to-r from-indigo-600 to-purple-700 text-white hover:from-indigo-700 hover:to-purple-800 shadow-lg shadow-indigo-600/30 hover:shadow-xl hover:scale-[1.02] active:scale-95"
          >
            <ArrowRightIcon class="w-5 h-5 mr-2" />
            Review Session
          </button>

        </div>

        <div v-if="sessions.links && sessions.links.length > 3" class="lg:col-span-3 xl:col-span-4 mt-8">
          <div class="flex justify-center space-x-2">
            <template v-for="(link, key) in sessions.links" :key="key">
              <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                class="px-4 py-2 rounded-lg text-sm transition-colors"
                :class="{
                  'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30': link.active,
                  'bg-gray-800 text-gray-300 hover:bg-gray-700/70': !link.active
                }"
              />
              <span
                v-else
                v-html="link.label"
                class="px-4 py-2 rounded-lg text-sm text-gray-500 cursor-not-allowed"
              />
            </template>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-20 bg-gray-900/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-indigo-900/20 ring-1 ring-gray-700/50">
        <BookmarkIcon class="w-12 h-12 text-gray-600 mx-auto mb-4" />
        <p class="text-xl font-semibold text-white mb-2">No Saved Sessions Found</p>
        <p class="text-gray-400 max-w-md mx-auto">
          Complete a flashcard session and choose to save it to see your saved sessions here.
        </p>
        <Link 
            :href="route('home')"
            class="inline-flex items-center mt-6 px-6 py-3 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 transition-all duration-200 hover:scale-[1.02] active:scale-95"
          >
            Start a New Session
        </Link>
      </div>
    </div>
  </div>
  
  <ReviewSessionModal
    :show="showReviewModal"
    :session="selectedSession"
    @close="showReviewModal = false; selectedSession = null"
    @start-review="startReview"
  />
  
  <ConfirmationModal
    :show="showDeleteModal"
    title="Delete Saved Session"
    message="Are you sure you want to delete this saved session? This action cannot be undone."
    confirm-text="Delete"
    cancel-text="Cancel"
    @confirm="confirmDelete"
    @close="showDeleteModal = false; sessionToDelete = null"
  />

</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue'; 
import ReviewSessionModal from '@/Components/Modals/ReviewSessionModal.vue'; 
import ConfirmationModal from '@/Components/Modals/ConfirmationModal.vue'; 
import { BookmarkIcon, ArrowLeftIcon, EllipsisVerticalIcon, PencilSquareIcon, TrashIcon, TagIcon, CalendarIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'; 

const props = defineProps({
  user: Object,
  sessions: Object, // Inertia Paginator data
  // REMOVED: route: Function, 
});

const showReviewModal = ref(false);
const selectedSession = ref(null);
const showDeleteModal = ref(false);
const sessionToDelete = ref(null);
const activeDropdown = ref(null);

// Utility function to format date
const formatRelativeDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  // Set both dates to midnight to compare days accurately
  const dateOnly = new Date(date.getFullYear(), date.getMonth(), date.getDate());
  const nowOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

  const diffTime = Math.abs(nowOnly - dateOnly);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) return 'Today';
  if (diffDays === 1) return 'Yesterday';
  if (diffDays <= 7) return `${diffDays} days ago`; 
  
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const toggleDropdown = (sessionId) => {
  activeDropdown.value = activeDropdown.value === sessionId ? null : sessionId;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  // Check if the click is outside the dropdown trigger and content
  if (activeDropdown.value && event.target.closest('.relative') === null) {
      activeDropdown.value = null;
  }
};

const reviewSession = (session) => {
  selectedSession.value = session;
  showReviewModal.value = true;
  activeDropdown.value = null;
};

const viewSession = (session) => {
  activeDropdown.value = null;
  router.visit(route('saved-sessions.show', session.slug));
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
    // CHANGED: Using global route() function directly
    router.delete(route('saved-sessions.destroy', sessionToDelete.value.slug), {
      onSuccess: () => {
        showDeleteModal.value = false;
        sessionToDelete.value = null;
        // Reload page data to remove deleted session from list
        router.reload({ only: ['sessions'] });
      },
      onFinish: () => {
        showDeleteModal.value = false;
        sessionToDelete.value = null;
      }
    });
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
/* Scoped styles can be used for custom animations or utility overrides */
</style>