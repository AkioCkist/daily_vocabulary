<template>
  <Head title="Vocabulary Dashboard - DailyVocab" />
  
  <div class="min-h-screen bg-gray-50 dark:bg-[#0B0C10] text-slate-900 dark:text-slate-100 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    
    <div class="sticky top-0 z-50 bg-black/80 dark:bg-black/90 backdrop-blur-md border-b border-gray-800">
      <Header :user="user" />
    </div>
    
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 relative z-20">
      
      <div v-if="!user" class="min-h-[80vh] flex flex-col justify-center relative overflow-hidden">
        <video
          autoplay
          loop
          muted
          playsinline
          class="fixed inset-0 w-full h-full object-cover z-0"
          src="/videos/background.mp4"
        ></video>
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
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          
          <div class="md:col-span-2 relative overflow-hidden rounded-2xl bg-black/80 border border-gray-800 p-8 shadow-lg group hover:border-indigo-500/50 transition-colors duration-300">
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <h2 class="text-3xl font-bold mb-2 text-white drop-shadow">Welcome back, {{ user.name }}</h2>
                <p class="text-gray-200">You're on a <span class="text-yellow-400 font-semibold">{{ dashboard.stats.streak_days }}</span> day streak. Keep the momentum going.</p>
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

          <div 
            class="rounded-2xl bg-black/80 border border-gray-800 p-6 flex flex-col justify-between shadow-lg hover:border-purple-500/50 transition-colors cursor-pointer" 
            @click="viewMemoryDetails"
          >
              <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-bold text-white">Memory Level</h3>
                  <div class="flex items-center space-x-1 p-1 bg-black/60 rounded-md">
                      <button 
                          v-for="day in [1, 7, 30]" 
                          :key="day"
                          @click.stop="memoryDayRange = day"
                          :class="[
                              'px-2 py-1 text-xs font-semibold rounded-md transition-all',
                              memoryDayRange === day 
                                  ? 'bg-purple-700 text-white shadow-sm' 
                                  : 'text-gray-300 hover:text-white hover:bg-gray-700/50'
                          ]"
                      >
                          {{ day }}D
                      </button>
                  </div>
              </div>
              
              <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                  <div class="flex flex-col">
                      <p class="text-xl font-bold text-white">
                          <span v-if="isLoadingStats" class="text-gray-500">...</span>
                          <span v-else>{{ filteredStats?.total_study_sessions || '0' }}</span>
                      </p>
                      <p class="text-xs font-medium text-gray-400">Times Studied</p>
                  </div>
                  
                  <div class="flex flex-col">
                      <p class="text-xl font-bold text-white">
                          <span v-if="isLoadingStats" class="text-gray-500">...</span>
                          <span v-else>{{ filteredStats?.total_words_learned || '0' }}</span>
                      </p>
                      <p class="text-xs font-medium text-gray-400">Words Learned</p>
                  </div>
                  
                  <div class="flex flex-col">
                      <p class="text-xl font-bold">
                          <span v-if="isLoadingStats" class="text-gray-500">...</span>
                          <template v-else>
                              <span class="text-green-400">{{ filteredStats?.correct_answers || '0' }}</span> 
                              <span class="text-gray-500">/</span> 
                              <span class="text-red-400">{{ filteredStats?.incorrect_answers || '0' }}</span>
                          </template>
                      </p>
                      <p class="text-xs font-medium text-gray-400">
                          Correct / Wrong 
                          <span v-if="!isLoadingStats && accuracyPercentage !== null" class="text-indigo-400">
                              ({{ accuracyPercentage }}%)
                          </span>
                      </p>
                  </div>
                  
                  <div class="flex flex-col">
                      <p class="text-xl font-bold text-yellow-400">
                          <span v-if="isLoadingStats" class="text-gray-500">...</span>
                          <span v-else>{{ filteredStats?.streak_days || '0' }}</span>
                      </p>
                      <p class="text-xs font-medium text-gray-400">Day Streak</p>
                  </div>
              </div>
              
              <p class="mt-4 text-xs text-purple-400/80 hover:text-purple-300 transition-colors text-right">Click to view detailed report &rarr;</p>
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
                <h3 class="text-lg font-bold text-white mb-4">Learning Recap</h3>
                <p v-if="saved_sessions.length === 0" class="text-gray-400 text-sm italic">
                    No past sessions found. Start a study session to see your learning recap here.
                </p>
                <SavedSessionsSection v-else :sessions="saved_sessions" /> 
            </div>

          </div>

          <div class="space-y-6">
            
            <div class="rounded-2xl bg-indigo-900/90 p-6 text-white relative overflow-hidden">
              <video
                autoplay
                loop
                muted
                playsinline
                class="absolute inset-0 w-full h-full object-cover z-0"
                src="/videos/study_feature_background.webm"
              ></video>
              <div class="absolute inset-0 w-full h-full bg-black/40 z-0 pointer-events-none"></div>
              <h3 class="text-lg font-bold mb-4 relative z-10">Study Feature</h3>
              <button 
                @click="showFlashcardModal = true" 
                class="w-full justify-center flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-medium rounded-lg transition-all shadow-lg shadow-white/10 relative z-10"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-semibold text-base">Start Study</span>
              </button>
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

    <MemoryReportModal 
      v-if="showMemoryModal"
      :initial-day-range="memoryDayRange"
      @close="showMemoryModal = false"
    />

    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';

// Layout Components
import Header from '@/Components/Header.vue';

// Dashboard Components
import GuestContent from '@/Components/Dashboard/GuestContent.vue';
import GitHubHeatmap from '@/Components/Dashboard/GitHubHeatmap.vue';
import TopicsSection from '@/Components/Dashboard/TopicsSection.vue';
import SavedSessionsSection from '@/Components/Dashboard/SavedSessionsSection.vue';

// Modal Components
import FlashcardModal from '@/Components/FlashcardModal.vue';
import TopicModal from '@/Components/TopicModal.vue';
import SaveSessionModal from '@/Components/SaveSessionModal.vue';
import MemoryReportModal from '@/Components/MemoryReportModal.vue';

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

// New state for Memory Level card
const memoryDayRange = ref(7);
const showMemoryModal = ref(false);
const filteredStats = ref(null);
const isLoadingStats = ref(false);

// Computed properties
const accuracyPercentage = computed(() => {
    if (!filteredStats.value) return null;
    const correct = filteredStats.value.correct_answers || 0;
    const incorrect = filteredStats.value.incorrect_answers || 0;
    const total = correct + incorrect;
    if (total === 0) return 0;
    return Math.round((correct / total) * 100);
});

// Handlers
const handleWordSelected = (word) => {
  console.log('Selected word:', word);
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

// Handler for Memory Level click
const viewMemoryDetails = () => {
    // This function will eventually open the modal for frequently forgotten/remembered words.
    showMemoryModal.value = true;
    console.log(`Open detailed memory report for last ${memoryDayRange.value} days`);
};

// Fetch stats by day range
const fetchStatsByDayRange = async (days) => {
    if (!props.user) return;
    
    isLoadingStats.value = true;
    try {
        const response = await fetch(`/dashboard/stats/${days}`);
        if (response.ok) {
            const data = await response.json();
            filteredStats.value = data;
        } else {
            console.error('Failed to fetch stats:', response.statusText);
        }
    } catch (error) {
        console.error('Error fetching stats:', error);
    } finally {
        isLoadingStats.value = false;
    }
};

// Watch for day range changes
watch(memoryDayRange, (newDays) => {
    fetchStatsByDayRange(newDays);
});

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
  
  // Fetch initial stats for the default day range
  if (props.user) {
    fetchStatsByDayRange(memoryDayRange.value);
  }
});
</script>