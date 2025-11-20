<template>
  <Head title="Learning - DailyVocab" />
  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Learning Session</h1>
                <p class="text-gray-600 dark:text-gray-400">Practice and learn new vocabulary words</p>
              </div>
              <div v-if="currentSession && currentSession.words" class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">Session Progress</div>
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                  {{ Math.min(currentWordIndex + 1, currentSession.words.length) }}/{{ currentSession.words.length }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error</h3>
              <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ error }}</p>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="message" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ message }}</p>
            </div>
          </div>
        </div>

        <!-- Learning Filter Board (Show when no active session) -->
        <div v-if="!currentSession" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Your Learning Session</h2>
                <p class="text-gray-600 dark:text-gray-400">Customize your learning experience with specific filters</p>
              </div>
            </div>
            
            <form @submit.prevent="generateCustomSession">
              <!-- Filter Categories -->
              <div class="space-y-8 mb-8">
                <!-- CEFR Level -->
                <div>
                  <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 CEFR Level</label>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
                    <button
                      type="button"
                      @click="customSessionForm.cefr_level = ''"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200',
                        customSessionForm.cefr_level === ''
                          ? 'border-gray-500 bg-gray-50 dark:bg-gray-700'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-center">
                        <div class="text-gray-600 dark:text-gray-400 font-bold text-sm">ALL</div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">All Levels</div>
                      </div>
                    </button>
                    
                    <button
                      type="button"
                      v-for="level in [
                        { value: 'A1', label: 'Beginner', color: 'green' },
                        { value: 'A2', label: 'Elementary', color: 'blue' },
                        { value: 'B1', label: 'Intermediate', color: 'yellow' },
                        { value: 'B2', label: 'Upper Int.', color: 'orange' },
                        { value: 'C1', label: 'Advanced', color: 'red' },
                        { value: 'C2', label: 'Proficient', color: 'purple' }
                      ]"
                      :key="level.value"
                      @click="customSessionForm.cefr_level = customSessionForm.cefr_level === level.value ? '' : level.value"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200',
                        customSessionForm.cefr_level === level.value
                          ? `border-${level.color}-500 bg-${level.color}-50 dark:bg-${level.color}-900/20`
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-center">
                        <div :class="`text-${level.color}-600 dark:text-${level.color}-400 font-bold text-lg`">{{ level.value }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ level.label }}</div>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Topic -->
                <div>
                  <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">🏷️ Topic</label>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <button
                      type="button"
                      @click="customSessionForm.topic = ''"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200 text-left',
                        customSessionForm.topic === ''
                          ? 'border-gray-500 bg-gray-50 dark:bg-gray-700'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-2xl mb-2">🌍</div>
                      <div class="font-medium text-gray-900 dark:text-white text-sm">All Topics</div>
                    </button>
                    
                    <button
                      type="button"
                      v-for="topic in [
                        { value: 'Business', label: 'Business', icon: '💼' },
                        { value: 'Culture', label: 'Culture', icon: '🎭' },
                        { value: 'Education', label: 'Education', icon: '📚' },
                        { value: 'Environment', label: 'Environment', icon: '🌱' },
                        { value: 'Health', label: 'Health', icon: '🏥' },
                        { value: 'Politics', label: 'Politics', icon: '🏛️' },
                        { value: 'Science', label: 'Science', icon: '🔬' },
                        { value: 'Technology', label: 'Technology', icon: '💻' }
                      ]"
                      :key="topic.value"
                      @click="customSessionForm.topic = customSessionForm.topic === topic.value ? '' : topic.value"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200 text-left',
                        customSessionForm.topic === topic.value
                          ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-2xl mb-2">{{ topic.icon }}</div>
                      <div class="font-medium text-gray-900 dark:text-white text-sm">{{ topic.label }}</div>
                    </button>
                  </div>
                </div>

                <!-- Word Count -->
                <div>
                  <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">⚙️ Word Count</label>
                  <div class="grid grid-cols-4 gap-2 max-w-md">
                    <button
                      type="button"
                      v-for="count in [5, 10, 15, 20]"
                      :key="count"
                      @click="customSessionForm.word_count = count.toString()"
                      :class="[
                        'py-3 px-4 rounded-lg border-2 font-medium transition-all duration-200',
                        customSessionForm.word_count === count.toString()
                          ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 text-gray-700 dark:text-gray-300'
                      ]"
                    >
                      {{ count }}
                    </button>
                  </div>
                </div>

                <!-- Session Type -->
                <div>
                  <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">🎯 Session Type</label>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 max-w-2xl">
                    <button
                      type="button"
                      v-for="type in [
                        { value: 'mixed', label: 'Mixed', desc: 'New & review words', icon: '🔄' },
                        { value: 'new', label: 'New Words', desc: 'Learn new vocabulary', icon: '✨' },
                        { value: 'review', label: 'Review', desc: 'Practice known words', icon: '📝' }
                      ]"
                      :key="type.value"
                      @click="customSessionForm.session_type = type.value"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200 text-left',
                        customSessionForm.session_type === type.value
                          ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-2xl mb-2">{{ type.icon }}</div>
                      <div class="font-medium text-gray-900 dark:text-white text-sm">{{ type.label }}</div>
                      <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ type.desc }}</div>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-600">
                <button 
                  type="button"
                  @click="startQuickSession"
                  :disabled="isGenerating"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2"
                >
                  <span>⚡</span>
                  {{ isGenerating ? 'Starting...' : 'Quick Session (10 Words)' }}
                </button>
                
                <button 
                  type="submit"
                  :disabled="isGenerating"
                  class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2"
                >
                  <svg v-if="isGenerating" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isGenerating ? 'Generating...' : 'Generate Custom Session' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- No Words Available Message -->
        <div v-if="currentSession && (!currentSession.words || currentSession.words.length === 0)" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-8 w-8 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-200 mb-2">No Words Available</h3>
              <p class="text-yellow-700 dark:text-yellow-300 mb-4">
                Sorry, we couldn't find any words matching your criteria. Try adjusting your filters or check back later when more words are added to the database.
              </p>
              <button 
                @click="resetSession"
                class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
              >
                Try Different Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Learning Card -->
        <div v-if="currentWord" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <!-- Word Display -->
            <div class="text-center mb-8">
              <h2 class="text-5xl font-bold text-gray-900 dark:text-white mb-4">{{ currentWord.word }}</h2>
              <p v-if="currentWord.pronunciation" class="text-lg text-gray-600 dark:text-gray-400 mb-2">{{ currentWord.pronunciation }}</p>
              <div class="flex justify-center gap-2 mb-4">
                <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full text-sm font-medium">
                  {{ currentWord.cefr_level }}
                </span>
                <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded-full text-sm font-medium">
                  {{ currentWord.topic }}
                </span>
              </div>
            </div>

            <!-- Definition -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Definition</h3>
              <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">{{ currentWord.definition }}</p>
            </div>

            <!-- Example -->
            <div v-if="currentWord.example" class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Example</h3>
              <p class="text-gray-700 dark:text-gray-300 text-lg italic leading-relaxed">"{{ currentWord.example }}"</p>
            </div>

            <!-- Actions -->
            <div class="flex justify-center gap-4">
              <button
                @click="markAsLearned"
                :disabled="isProcessing"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50"
              >
                <span v-if="!isProcessing">✓ Mark as Learned</span>
                <span v-else>Processing...</span>
              </button>
              
              <button
                @click="addToReview"
                :disabled="isProcessing"
                class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50"
              >
                📝 Add to Review
              </button>
              
              <button
                @click="nextWord"
                :disabled="isProcessing"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50"
              >
                Next Word →
              </button>
            </div>
          </div>
        </div>

        <!-- Session Complete -->
        <div v-if="currentSession && currentSession.words && currentWordIndex >= currentSession.words.length" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-8 text-center">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Session Complete!</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Great job! You've completed this learning session.</p>
            
            <div class="flex justify-center gap-4">
              <button
                @click="resetSession"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
              >
                Start New Session
              </button>
              <Link href="/test" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                Take a Test
              </Link>
              <Link href="/words" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                Browse Words
              </Link>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div v-if="currentSession && currentSession.words" class="mt-6">
          <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div 
              class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
              :style="{ width: progressPercentage + '%' }"
            ></div>
          </div>
          <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-2">
            {{ progressPercentage.toFixed(0) }}% Complete
          </p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  session: {
    type: Object,
    default: null
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  error: {
    type: String,
    default: null
  },
  message: {
    type: String,
    default: null
  }
});

// State
const currentWordIndex = ref(0);
const isProcessing = ref(false);
const isGenerating = ref(false);
const currentSession = ref(props.session);

// Custom session form
const customSessionForm = ref({
  cefr_level: '',
  topic: '',
  word_count: '10',
  session_type: 'mixed'
});

// Computed
const currentWord = computed(() => {
  if (!currentSession.value || !currentSession.value.words || currentWordIndex.value >= currentSession.value.words.length) {
    return null;
  }
  return currentSession.value.words[currentWordIndex.value];
});

const progressPercentage = computed(() => {
  if (!currentSession.value || !currentSession.value.words || currentSession.value.words.length === 0) {
    return 0;
  }
  
  // When session is complete, show 100%
  if (currentWordIndex.value >= currentSession.value.words.length) {
    return 100;
  }
  
  // Normal progress calculation
  return ((currentWordIndex.value + 1) / currentSession.value.words.length) * 100;
});

// Methods
function startQuickSession() {
  if (isGenerating.value) return;
  
  isGenerating.value = true;
  
  const form = useForm({});
  form.post('/learn/generate-quick', {
    onSuccess: (page) => {
      currentSession.value = page.props.session;
      currentWordIndex.value = 0;
    },
    onFinish: () => {
      isGenerating.value = false;
    }
  });
}

function generateCustomSession() {
  if (isGenerating.value) return;
  
  isGenerating.value = true;
  
  const form = useForm(customSessionForm.value);
  form.post('/learn/generate', {
    onSuccess: (page) => {
      currentSession.value = page.props.session;
      currentWordIndex.value = 0;
    },
    onFinish: () => {
      isGenerating.value = false;
    }
  });
}

function markAsLearned() {
  if (!currentWord.value || isProcessing.value) return;
  
  isProcessing.value = true;
  
  const form = useForm({
    word_id: currentWord.value.id
  });

  form.post('/learn/mark-learned', {
    onSuccess: () => {
      nextWord();
    },
    onFinish: () => {
      isProcessing.value = false;
    }
  });
}

function addToReview() {
  if (!currentWord.value || isProcessing.value) return;
  
  isProcessing.value = true;
  
  const form = useForm({
    word_id: currentWord.value.id
  });

  form.post('/learn/add-to-review', {
    onSuccess: () => {
      nextWord();
    },
    onFinish: () => {
      isProcessing.value = false;
    }
  });
}

function nextWord() {
  if (currentSession.value && currentSession.value.words && currentWordIndex.value < currentSession.value.words.length - 1) {
    currentWordIndex.value++;
  } else {
    // Session complete - increment to show completion message
    currentWordIndex.value++;
  }
}

function resetSession() {
  currentSession.value = null;
  currentWordIndex.value = 0;
}
</script>