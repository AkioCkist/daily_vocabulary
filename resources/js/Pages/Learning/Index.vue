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
              <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">Session Progress</div>
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ currentWordIndex + 1 }}/{{ sessionWords.length }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Learning Card -->
        <div v-if="currentWord" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <!-- Word Display -->
            <div class="text-center mb-8">
              <h2 class="text-5xl font-bold text-gray-900 dark:text-white mb-4">{{ currentWord.word }}</h2>
              <p class="text-lg text-gray-600 dark:text-gray-400 mb-2">{{ currentWord.pronunciation }}</p>
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
        <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-8 text-center">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Session Complete!</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Great job! You've completed this learning session.</p>
            
            <div class="flex justify-center gap-4">
              <Link href="/learn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                Start New Session
              </Link>
              <Link href="/test" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                Take a Test
              </Link>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-6">
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
import { ref, computed, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  sessionWords: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

// State
const currentWordIndex = ref(0);
const isProcessing = ref(false);
const sessionWordsData = ref(props.sessionWords || []);

// Computed
const currentWord = computed(() => {
  return sessionWordsData.value[currentWordIndex.value] || null;
});

const progressPercentage = computed(() => {
  if (sessionWordsData.value.length === 0) return 0;
  return ((currentWordIndex.value + 1) / sessionWordsData.value.length) * 100;
});

// Methods
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
  if (currentWordIndex.value < sessionWordsData.value.length - 1) {
    currentWordIndex.value++;
  } else {
    // Session complete
    currentWordIndex.value = sessionWordsData.value.length;
  }
}

// Generate session words if none provided
onMounted(() => {
  if (sessionWordsData.value.length === 0) {
    // Start a new learning session
    const form = useForm(props.filters);
    
    form.post('/learn/start', {
      onSuccess: (response) => {
        if (response.props.sessionWords) {
          sessionWordsData.value = response.props.sessionWords;
        }
      }
    });
  }
});
</script>