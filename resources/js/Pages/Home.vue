<template>
  <Head title="Vocabulary Dashboard - DailyVocab" />
  
  <div class="min-h-screen bg-gray-50 dark:bg-[#0B0C10] text-slate-900 dark:text-slate-100 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    
    <div class="sticky top-0 z-50 bg-black/80 dark:bg-black/90 backdrop-blur-md border-b border-gray-800">
      <Header :user="user" />
    </div>
    
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 relative z-20">
      
      <div v-if="!user" class="min-h-[80vh] flex flex-col justify-center relative overflow-hidden">
        <!-- Background Video for Guest View -->
        <video
          autoplay
          loop
          muted
          playsinline
          class="fixed inset-0 w-full h-full object-cover z-0"
          src="/videos/background.mp4"
        ></video>
          <!-- Black Opacity Overlay -->
          <div class="fixed inset-0 w-full h-full bg-black/28 z-10 pointer-events-none"></div>
          
        <div class="grid lg:grid-cols-2 gap-12 items-center relative z-10">
          <div class="space-y-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black/70 border border-indigo-800 text-indigo-200 text-xs font-semibold tracking-wide uppercase">
              <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
              #newwaytolearn
            </div>
            
            <h1 class="text-5xl lg:text-7xl font-bold tracking-tight leading-[1.1] text-white drop-shadow-lg">
              Master words <br />
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-400">
                Built for Developers.
              </span>
            </h1>
            
            <p class="text-xl text-gray-200 max-w-lg leading-relaxed drop-shadow">
              Stop forgetting what you learn. A spaced-repetition system designed with the precision of a code editor.
            </p>

            <GuestContent @word-selected="handleWordSelected" />
          </div>

           <div class="relative hidden lg:block">
             <div class="aspect-square rounded-3xl bg-black/70 border border-gray-700 flex items-center justify-center shadow-2xl">
               <span class="text-gray-300 font-mono text-sm">[ Insert 3D Abstract Hero Image Here ]</span>
             </div>
           </div>
        </div>
      </div>


      <div v-else-if="dashboard" class="space-y-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
          
          <div class="lg:col-span-8 relative overflow-hidden rounded-2xl bg-black/80 border border-gray-800 p-8 shadow-lg group hover:border-indigo-500/50 transition-colors duration-300">
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <h2 class="text-3xl font-bold mb-2 text-white drop-shadow">Welcome back, {{ user.name }}</h2>
                <p class="text-gray-200">You're on a {{ dashboard.stats.streak_days }} day streak. Keep the momentum going.</p>
              </div>
              <div class="mt-8">
                 <button @click="startReview" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all shadow-lg shadow-indigo-500/30">
                    Start Daily Review ({{ dashboard.stats.words_due_for_review }})
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                 </button>
              </div>
            </div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-bl-full -mr-10 -mt-10"></div>
          </div>

           <div class="lg:col-span-4 grid grid-rows-2 gap-6">
            <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 flex items-center justify-between shadow-lg">
              <div>
                <p class="text-sm font-medium text-gray-300 uppercase tracking-wider">Total Words</p>
                <p class="text-3xl font-bold text-white mt-1">{{ dashboard.stats.total_words_learned }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-indigo-900/40 flex items-center justify-center text-indigo-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
              </div>
            </div>

            <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 flex items-center justify-between shadow-lg">
              <div>
                <p class="text-sm font-medium text-gray-300 uppercase tracking-wider">Accuracy</p>
                <p class="text-3xl font-bold text-white mt-1">{{ dashboard.stats.accuracy_rate }}%</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-green-900/40 flex items-center justify-center text-green-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <div class="lg:col-span-2 space-y-6">
            
            <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 shadow-lg">
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-black/60 rounded-md">
                    <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                  </div>
                  <h3 class="text-lg font-bold text-white">Learning Activity</h3>
                </div>
                
                <div class="flex p-1 bg-black/60 rounded-lg">
                  <button 
                    v-for="period in ['weekly', 'monthly', 'yearly']" 
                    :key="period"
                    @click="heatmapPeriod = period"
                    :class="[
                      'px-4 py-1.5 text-xs font-semibold rounded-md transition-all',
                      heatmapPeriod === period 
                        ? 'bg-indigo-700 text-white shadow-sm' 
                        : 'text-gray-300 hover:text-white'
                    ]"
                  >
                    {{ period.charAt(0).toUpperCase() + period.slice(1) }}
                  </button>
                </div>
              </div>
              
              <div class="w-full overflow-x-auto">
                <GitHubHeatmap 
                  :data="dashboard.learning_heatmap" 
                  :period="heatmapPeriod"
                />
              </div>
            </div>

            <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 shadow-lg">
              <h3 class="text-lg font-bold text-white mb-4">Recent Activity</h3>
              <RecentActivity :activities="dashboard.recent_activity" />
            </div>

          </div>

          <div class="space-y-6">
            
            <div class="rounded-2xl bg-indigo-900/90 p-6 text-white relative overflow-hidden">
               <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
               
               <h3 class="text-lg font-bold mb-4 relative z-10">Quick Start</h3>
               <div class="grid grid-cols-2 gap-3 relative z-10">
                  <button @click="startQuickFlashcards" class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl p-4 text-left transition-all">
                    <div class="mb-2 opacity-80">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <span class="text-sm font-semibold block">Blitz Mode</span>
                    <span class="text-xs opacity-70">10 Words</span>
                  </button>
                  
                  <button @click="showFlashcardModal = true" class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl p-4 text-left transition-all">
                    <div class="mb-2 opacity-80">
                       <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    </div>
                    <span class="text-sm font-semibold block">Custom</span>
                    <span class="text-xs opacity-70">Setup</span>
                  </button>
               </div>
            </div>

            <div class="rounded-2xl bg-black/80 border border-gray-800 p-6 shadow-lg">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">Topics</h3>
                <button @click="showTopicModal = true" class="text-xs font-semibold text-indigo-300 hover:underline">View All</button>
              </div>
              <TopicsSection 
                :topics="dashboard.available_topics"
                :limit="3"
                @select="selectTopic"
                @manage="showTopicModal = true"
              />
            </div>

            <div v-if="saved_sessions.length > 0" class="rounded-2xl bg-black/80 border border-gray-800 p-6 shadow-lg">
              <h3 class="text-lg font-bold text-white mb-4">Saved Sessions</h3>
              <SavedSessionsSection :sessions="saved_sessions" />
            </div>

          </div>
        </div>
      </div>
    </div>

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
import GuestContent from '@/Components/Dashboard/GuestContent.vue';
import GitHubHeatmap from '@/Components/Dashboard/GitHubHeatmap.vue';
import TopicsSection from '@/Components/Dashboard/TopicsSection.vue';
import RecentActivity from '@/Components/Dashboard/RecentActivity.vue';
import SavedSessionsSection from '@/Components/Dashboard/SavedSessionsSection.vue';

// Modal Components
import FlashcardModal from '@/Components/FlashcardModal.vue';
import TopicModal from '@/Components/TopicModal.vue';
import SaveSessionModal from '@/Components/SaveSessionModal.vue';

// Note: CompactStats, QuickActions, and HeroSection components 
// are intentionally replaced by inline markup for the new layout structure,
// but you can re-import them if you wish to wrap the inline code back into components.

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
  refreshDashboard();
  closeSaveSessionModal();
};

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const showSavePopup = urlParams.get('show_save_popup') === 'true';
  
  if (showSavePopup) {
    try {
      const sessionDataParam = urlParams.get('save_session_data');
      if (sessionDataParam) {
        const parsedData = JSON.parse(decodeURIComponent(sessionDataParam));
        saveSessionData.value = parsedData;
        showSaveSessionModal.value = true;
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    } catch (error) {
      console.error('Home: Error parsing save session data:', error);
    }
  }
});
</script>