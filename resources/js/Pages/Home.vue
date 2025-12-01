<template>
  <Head title="Vocabulary Dashboard - DailyVocab" />

  <div class="min-h-screen bg-gray-50 dark:bg-[#0B0C10] text-slate-900 dark:text-slate-100 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    
    <!-- Header -->
    <div class="sticky top-0 z-50 bg-black/80 dark:bg-black/90 backdrop-blur-md border-b border-gray-800">
      <Header :user="user" />
    </div>

    <!-- Main Content -->
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 relative z-20">
      
      <!-- Guest Landing Section -->
      <GuestLanding
        v-if="!user"
        :user="user"
        @word-selected="handleWordSelected"
      />

      <!-- Dashboard Section -->
      <div v-else-if="dashboard" class="space-y-8">
        
        <!-- Top Row: Welcome & Memory Level -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <WelcomeCard
            :user="user"
            :streak-days="dashboard.stats.streak_days"
            :words-due-for-review="dashboard.stats.words_due_for_review"
            @start-review="handleStartReview"
            class="md:col-span-2"
          />

          <MemoryLevelCard
            :day-range="memoryDayRange"
            :stats="filteredStats"
            :is-loading="isLoadingStats"
            @view-details="openMemoryModal"
            @update:dayRange="memoryDayRange = $event"
          />
        </div>

        <!-- Bottom Row: Activity, Recap, Study & Topics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Left Column -->
          <div class="lg:col-span-2 space-y-6">
            <LearningActivityCard
              :data="dashboard.learning_heatmap"
              :active-period="heatmapPeriod"
              @update:period="heatmapPeriod = $event"
            />

            <LearningRecapCard :sessions="saved_sessions" />
          </div>

          <!-- Right Column -->
          <div class="space-y-6">
            <StudyFeatureCard @open-flashcard-modal="openFlashcardModal" />

            <TopicsCard
              :topics="dashboard.available_topics"
              @open-topic-modal="openTopicModal"
              @topic-selected="handleSelectTopic"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <FlashcardModal
      v-if="showFlashcardModal"
      :dashboard="dashboard"
      @close="closeFlashcardModal"
      @start="handleStartFlashcards"
    />

    <TopicModal
      v-if="showTopicModal"
      :topics="dashboard?.available_topics"
      @close="closeTopicModal"
      @refresh="refreshDashboard"
    />

    <SaveSessionModal
      v-if="showSaveSessionModal && saveSessionData"
      :session-data="saveSessionData"
      @close="closeSaveSessionModal"
      @saved="handleSessionSaved"
    />

    <MemoryReportModal
      v-if="showMemoryModal"
      :initial-day-range="memoryDayRange"
      @close="closeMemoryModal"
    />

    <!-- Alert Modal -->
    <AlertModal
      :is-visible="alert.isVisible"
      :type="alert.type"
      :title="alert.title"
      :message="alert.message"
      :details="alert.details"
      @close="closeAlert"
    />

  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';

// Layout Components
import Header from '@/Components/Header.vue';

// Dashboard Components
import GuestLanding from '@/Components/Dashboard/GuestLanding.vue';
import WelcomeCard from '@/Components/Dashboard/WelcomeCard.vue';
import MemoryLevelCard from '@/Components/Dashboard/MemoryLevelCard.vue';
import LearningActivityCard from '@/Components/Dashboard/LearningActivityCard.vue';
import LearningRecapCard from '@/Components/Dashboard/LearningRecapCard.vue';
import StudyFeatureCard from '@/Components/Dashboard/StudyFeatureCard.vue';
import TopicsCard from '@/Components/Dashboard/TopicsCard.vue';

// Modal Components
import FlashcardModal from '@/Components/FlashcardModal.vue';
import TopicModal from '@/Components/TopicModal.vue';
import SaveSessionModal from '@/Components/SaveSessionModal.vue';
import MemoryReportModal from '@/Components/MemoryReportModal.vue';
import AlertModal from '@/Components/Modals/AlertModal.vue';

// Composables
import { useDashboardStats } from '@/composables/useDashboardStats';
import { useModalState } from '@/composables/useModalState';
import { useFlashcardActions } from '@/composables/useFlashcardActions';
import { useSessionManagement } from '@/composables/useSessionManagement';
import { useURLParams } from '@/composables/useURLParams';
import { useAlert } from '@/composables/useAlert';

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

// Composables
const { memoryDayRange, filteredStats, isLoadingStats, initializeStats } = useDashboardStats(props.user);
const {
  showFlashcardModal,
  showTopicModal,
  showSaveSessionModal,
  showMemoryModal,
  saveSessionData,
  openFlashcardModal,
  closeFlashcardModal,
  openTopicModal,
  closeTopicModal,
  openSaveSessionModal,
  closeSaveSessionModal,
  openMemoryModal,
  closeMemoryModal
} = useModalState();
const {
  handleStartFlashcards: startFlashcardsFromComposable,
  handleStartReview: startReviewFromComposable,
  handleSelectTopic,
  handleWordSelected
} = useFlashcardActions();
const { refreshDashboard, handleSessionSaved: performSessionSaved } = useSessionManagement();
const { alert, showError, closeAlert } = useAlert();

// Error handler wrapper for starting flashcards
const handleStartFlashcards = (settings) => {
  try {
    return startFlashcardsFromComposable(settings);
  } catch (error) {
    console.error('Error starting flashcards:', error);
    showError(
      'Unable to Start Training',
      'There was an error starting your flashcard training. Please try again.'
    );
  }
};

// Heatmap period state
import { ref } from 'vue';
const heatmapPeriod = ref('yearly');

// URL Parameters
useURLParams((sessionData) => {
  openSaveSessionModal(sessionData);
}).initializeFromURL();

// Handlers
const handleStartReview = () => {
  startReviewFromComposable(props.dashboard.stats.words_due_for_review);
};

const handleSessionSaved = () => {
  performSessionSaved(() => {
    closeSaveSessionModal();
  });
  refreshDashboard();
};

// Watch for validation errors from flashcard service
const page = usePage();
watch(
  () => page.props.errors,
  (errors) => {
    if (errors?.words) {
      showError(
        'No Words Available',
        errors.words
      );
    }
  },
  { deep: true }
);

// Initialize
onMounted(() => {
  initializeStats();
});
</script>