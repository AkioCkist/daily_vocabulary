<template>
  <Head title="Vocabulary Dashboard - DailyVocab" />
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-7xl mx-auto px-4 py-12">
      <!-- Hero Section -->
      <HeroSection 
        :user="user" 
        :stats="dashboard?.stats" 
      />

      <!-- Guest Content -->
      <div v-if="!user" class="min-h-[60vh] flex items-center justify-center">
        <GuestContent 
          @word-selected="handleWordSelected" 
        />
      </div>

      <!-- User Dashboard -->
      <div v-else-if="dashboard" class="space-y-6">
        <!-- Compact Stats (Collapsible) -->
        <CompactStats :stats="dashboard.stats" />

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Learning Heatmap (GitHub Style) -->
          <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-200">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                  Learning Activity
                </h2>
                <!-- Period Selector -->
                <div class="flex gap-2">
                  <button 
                    v-for="period in ['weekly', 'monthly', 'yearly']" 
                    :key="period"
                    @click="heatmapPeriod = period"
                    :class="[
                      'px-3 py-1.5 text-sm font-medium rounded-lg transition-all',
                      heatmapPeriod === period 
                        ? 'bg-indigo-500 text-white' 
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                    ]"
                  >
                    {{ period.charAt(0).toUpperCase() + period.slice(1) }}
                  </button>
                </div>
              </div>
              <GitHubHeatmap 
                :data="dashboard.learning_heatmap" 
                :period="heatmapPeriod"
              />
            </div>
          </div>

          <!-- Sidebar - Quick Actions -->
          <div class="space-y-6">
            <QuickActions 
              :words-due="dashboard.stats.words_due_for_review"
              @start-quick="startQuickFlashcards"
              @start-review="startReview"
              @open-advanced="showFlashcardModal = true"
            />

            <!-- Topics (Collapsible) -->
            <TopicsSection 
              :topics="dashboard.available_topics"
              :limit="3"
              @select="selectTopic"
              @manage="showTopicModal = true"
            />

            <!-- Saved Sessions -->
            <SavedSessionsSection 
              :sessions="saved_sessions"
            />
          </div>
        </div>

        <!-- Recent Activity (Collapsible) -->
        <RecentActivity 
          :activities="dashboard.recent_activity" 
        />
      </div>
    </div>

    <!-- Modals -->
    <FlashcardModal 
      v-if="showFlashcardModal"
      :dashboard="dashboard"
      @close="showFlashcardModal = false"
      @start="startFlashcards"
    />

    <TopicModal 
      v-if="showTopicModal"
      :topics="dashboard?.available_topics"
      @close="showTopicModal = false"
      @refresh="refreshDashboard"
    />

    <SaveSessionModal 
      v-if="showSaveSessionModal && saveSessionData"
      :session-data="saveSessionData"
      @close="closeSaveSessionModal"
      @saved="handleSessionSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';

// Layout Components
import Header from '@/Components/Header.vue';

// Dashboard Components
import HeroSection from '@/Components/Dashboard/HeroSection.vue';
import GuestContent from '@/Components/Dashboard/GuestContent.vue';
import CompactStats from '@/Components/Dashboard/CompactStats.vue';
import GitHubHeatmap from '@/Components/Dashboard/GitHubHeatmap.vue';
import QuickActions from '@/Components/Dashboard/QuickActions.vue';
import TopicsSection from '@/Components/Dashboard/TopicsSection.vue';
import RecentActivity from '@/Components/Dashboard/RecentActivity.vue';
import SavedSessionsSection from '@/Components/Dashboard/SavedSessionsSection.vue';

// Modal Components
import FlashcardModal from '@/Components/FlashcardModal.vue';
import TopicModal from '@/Components/TopicModal.vue';
import SaveSessionModal from '@/Components/SaveSessionModal.vue';

const props = defineProps({
  user: {
    type: Object,
    default: null
  },
  dashboard: {
    type: Object,
    default: null
  },
  saved_sessions: {
    type: Array,
    default: () => []
  }
});

// State
const heatmapPeriod = ref('yearly');
const showFlashcardModal = ref(false);
const showTopicModal = ref(false);
const showSaveSessionModal = ref(false);
const saveSessionData = ref(null);

// Handlers
const handleWordSelected = (word) => {
  console.log('Selected word:', word);
};

const startQuickFlashcards = () => {
  router.post('/flashcards/start', {
    mode: 'quick',
    flashcard_type: 'standard',
    word_count: 10
  });
};

const startFlashcards = (settings) => {
  router.post('/flashcards/start', settings);
};

const selectTopic = (topic) => {
  router.post('/flashcards/start', {
    mode: 'topic',
    flashcard_type: 'standard',
    topic_ids: [topic.id],
    word_count: 10
  });
};

const startReview = () => {
  router.post('/flashcards/start', {
    mode: 'review',
    flashcard_type: 'standard',
    word_count: props.dashboard.stats.words_due_for_review
  });
};

const refreshDashboard = () => {
  router.reload({ only: ['dashboard', 'saved_sessions'] });
};

const closeSaveSessionModal = () => {
  showSaveSessionModal.value = false;
  saveSessionData.value = null;
};

const handleSessionSaved = (session) => {
  // Session saved successfully, refresh data
  refreshDashboard();
  closeSaveSessionModal();
};

// Check for save session data on component mount
onMounted(() => {
  console.log('Home: Component mounted, checking for save session data');
  
  // Check if there's save session data from flashcard completion
  const urlParams = new URLSearchParams(window.location.search);
  const showSavePopup = urlParams.get('show_save_popup') === 'true';
  
  console.log('Home: URL params:', {
    showSavePopup,
    allParams: Object.fromEntries(urlParams.entries())
  });
  
  if (showSavePopup) {
    console.log('Home: Should show save popup');
    
    // Get session data from URL params or session storage
    try {
      const sessionDataParam = urlParams.get('save_session_data');
      console.log('Home: Save session data param:', sessionDataParam);
      
      if (sessionDataParam) {
        const parsedData = JSON.parse(decodeURIComponent(sessionDataParam));
        console.log('Home: Parsed save session data:', parsedData);
        
        saveSessionData.value = parsedData;
        showSaveSessionModal.value = true;
        
        console.log('Home: Save session modal should now be visible');
        
        // Clean up URL
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    } catch (error) {
      console.error('Home: Error parsing save session data:', error);
    }
  } else {
    console.log('Home: No save popup needed');
  }
});
</script>
