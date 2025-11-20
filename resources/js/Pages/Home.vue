<template>
  <Head title="Vocabulary Dashboard - DailyVocab" />
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-50 to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950 relative overflow-hidden">
    <!-- Background decoration for depth -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-purple-500/15 to-pink-500/15 rounded-full blur-3xl animate-floating"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-indigo-500/15 to-blue-500/15 rounded-full blur-3xl animate-floating" style="animation-delay: -1s;"></div>
    </div>
    
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-7xl mx-auto px-4 py-8 relative z-10">
      <!-- Welcome Section -->
      <div class="text-center mb-8 animate-fade-in">
        <div class="inline-flex items-center justify-center w-18 h-18 bg-gradient-to-br from-yellow-400 via-orange-400 to-orange-500 rounded-2xl mb-4 shadow-depth-3 animate-floating hover-lift relative">
          <div class="absolute inset-0 bg-gradient-to-br from-yellow-300/30 to-orange-400/30 rounded-2xl blur-sm"></div>
          <span class="text-4xl relative z-10 drop-shadow-sm">📊</span>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-violet-600 bg-clip-text text-transparent mb-2 dark:from-indigo-400 dark:via-purple-400 dark:to-violet-400 drop-shadow-sm">
          {{ user ? `Welcome back, ${user.name}!` : 'Vocabulary Dashboard' }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400 font-medium">
          {{ user ? 'Track your progress and continue learning' : 'Discover, learn, and master new words' }}
        </p>
      </div>

      <!-- Guest Content -->
      <div v-if="!user" class="max-w-2xl mx-auto">
        <!-- Word Filter Section -->
        <div class="mb-8">
          <WordFilter @word-selected="handleWordSelected" />
        </div>

        <!-- Subscribe Section -->
        <div class="mt-10">
          <SubscribeForm />
        </div>
      </div>

      <!-- User Dashboard -->
      <div v-else-if="dashboard" class="space-y-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Words Learning -->
          <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 hover-lift border border-white/20">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <span class="text-sm text-gray-500 dark:text-gray-400">Learning</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
              {{ dashboard.stats.words_learning }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Words in progress</div>
          </div>

          <!-- Accuracy Rate -->
          <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 hover-lift border border-white/20">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <span class="text-sm text-gray-500 dark:text-gray-400">Accuracy</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
              {{ dashboard.stats.accuracy_rate }}%
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ dashboard.stats.correct_answers }}/{{ dashboard.stats.total_attempts }}</div>
          </div>

          <!-- Learning Streak -->
          <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 hover-lift border border-white/20">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                </svg>
              </div>
              <span class="text-sm text-gray-500 dark:text-gray-400">Streak</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
              {{ dashboard.stats.learning_streak }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Days in a row</div>
          </div>

          <!-- Words Mastered -->
          <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 hover-lift border border-white/20">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
              </div>
              <span class="text-sm text-gray-500 dark:text-gray-400">Mastered</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
              {{ dashboard.stats.words_mastered }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total mastered</div>
          </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Learning Heatmap -->
          <div class="lg:col-span-2">
            <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 border border-white/20">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Learning Activity</h2>
                <div class="flex gap-2">
                  <button 
                    v-for="period in ['weekly', 'monthly', 'yearly']" 
                    :key="period"
                    @click="heatmapPeriod = period"
                    :class="[
                      'px-3 py-1 text-sm rounded-lg transition-all duration-200',
                      heatmapPeriod === period 
                        ? 'bg-indigo-500 text-white' 
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
                    ]"
                  >
                    {{ period.charAt(0).toUpperCase() + period.slice(1) }}
                  </button>
                </div>
              </div>
              <LearningHeatmap 
                :data="dashboard.learning_heatmap" 
                :period="heatmapPeriod"
              />
            </div>
          </div>

          <!-- Training & Quick Actions -->
          <div class="space-y-6">
            <!-- Training Mode -->
            <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 border border-white/20">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Training Mode</h2>
              
              <!-- Quick Flashcards -->
              <button 
                type="button"
                @click.prevent="startQuickFlashcards"
                class="w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 text-white font-bold py-4 px-6 rounded-xl shadow-depth-2 hover-lift btn-3d flex items-center justify-center gap-3 mb-4 transition-all duration-300"
              >
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                </div>
                <span>Quick Flashcards</span>
              </button>

              <!-- Advanced Flashcards -->
              <button 
                @click="showFlashcardModal = true"
                class="w-full bg-gradient-to-r from-green-500 via-emerald-500 to-teal-600 text-white font-bold py-3 px-6 rounded-xl shadow-depth-2 hover-lift flex items-center justify-center gap-3 transition-all duration-300"
              >
                <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                  </svg>
                </div>
                <span>Advanced Options</span>
              </button>
            </div>

            <!-- Topics -->
            <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 border border-white/20">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Topics</h2>
                <button 
                  @click="showTopicModal = true"
                  class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium text-sm"
                >
                  Manage
                </button>
              </div>
              
              <!-- Suggested Topics -->
              <div class="space-y-2 mb-4">
                <div 
                  v-for="topic in dashboard.available_topics.system.slice(0, 5)" 
                  :key="topic.id"
                  class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                  <div>
                    <div class="font-medium text-gray-900 dark:text-white">{{ topic.name }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ topic.words_count }} words</div>
                  </div>
                  <button 
                    @click="selectTopic(topic)"
                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm"
                  >
                    Study
                  </button>
                </div>
              </div>

              <button 
                @click="showTopicModal = true"
                class="w-full py-2 px-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
              >
                + Create Custom Topic
              </button>
            </div>

            <!-- Review Due -->
            <div v-if="dashboard.stats.words_due_for_review > 0" class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 border border-white/20">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Review Due</h2>
              <div class="text-center">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-2">
                  {{ dashboard.stats.words_due_for_review }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">Words need review</div>
                <button 
                  @click="startReview"
                  class="w-full bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold py-3 px-6 rounded-xl shadow-depth-2 hover-lift transition-all duration-300"
                >
                  Start Review
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl p-6 shadow-depth-2 border border-white/20">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Recent Activity</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div 
              v-for="activity in dashboard.recent_activity" 
              :key="`${activity.word}-${activity.created_at}`"
              class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50"
            >
              <div :class="[
                'w-3 h-3 rounded-full',
                activity.is_correct ? 'bg-green-500' : 'bg-red-500'
              ]"></div>
              <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-900 dark:text-white truncate">{{ activity.word }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">{{ activity.created_at }}</div>
              </div>
            </div>
          </div>
        </div>
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
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import WordFilter from '@/Components/WordFilter.vue';
import SubscribeForm from '@/Components/SubscribeForm.vue';
import LearningHeatmap from '@/Components/LearningHeatmap.vue';
import FlashcardModal from '@/Components/FlashcardModal.vue';
import TopicModal from '@/Components/TopicModal.vue';

const props = defineProps({
  user: {
    type: Object,
    default: null
  },
  dashboard: {
    type: Object,
    default: null
  }
});

// Reactive state
const heatmapPeriod = ref('monthly');
const showFlashcardModal = ref(false);
const showTopicModal = ref(false);

/**
 * Handle word selection from filter (for guests)
 */
function handleWordSelected(word) {
  console.log('Selected word:', word);
}

/**
 * Start quick flashcards with default settings (10 random words)
 */
function startQuickFlashcards() {
  console.log('Starting quick flashcards...');
  router.post('/flashcards/start', {
    mode: 'quick',
    flashcard_type: 'standard',
    word_count: 10
  }, {
    onStart: () => console.log('Request started'),
    onSuccess: (page) => console.log('Success:', page),
    onError: (errors) => console.error('Errors:', errors),
    onFinish: () => console.log('Request finished')
  });
}

/**
 * Start flashcards with custom settings
 */
function startFlashcards(settings) {
  console.log('🚀 Starting flashcards with settings:', settings);
  router.post('/flashcards/start', settings, {
    onStart: () => console.log('📤 Flashcard request started'),
    onSuccess: (page) => console.log('✅ Flashcard request success:', page),
    onError: (errors) => console.error('❌ Flashcard request errors:', errors),
    onFinish: () => console.log('🏁 Flashcard request finished')
  });
}

/**
 * Select a topic for quick study
 */
function selectTopic(topic) {
  router.post('/flashcards/start', {
    mode: 'topic',
    flashcard_type: 'standard',
    topic_ids: [topic.id],
    word_count: 10
  });
}

/**
 * Start review session for words due for review
 */
function startReview() {
  console.log('Starting review session...');
  console.log('Dashboard stats:', props.dashboard.stats);
  console.log('Words due for review:', props.dashboard.stats.words_due_for_review);
  
  router.post('/flashcards/start', {
    mode: 'review',
    flashcard_type: 'standard',
    word_count: props.dashboard.stats.words_due_for_review
  }, {
    onStart: () => console.log('Review session started'),
    onSuccess: (page) => console.log('Review session success:', page),
    onError: (errors) => console.error('Review session errors:', errors),
    onFinish: () => console.log('Review session request finished')
  });
}

/**
 * Refresh dashboard data
 */
function refreshDashboard() {
  router.reload({ only: ['dashboard'] });
}
</script>
