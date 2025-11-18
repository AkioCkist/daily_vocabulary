<template>
  <Head title="Review - DailyVocab" />
  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Review Session</h1>
                <p class="text-gray-600 dark:text-gray-400">Review and practice words you've learned</p>
              </div>
              <div v-if="reviewWords.length > 0" class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">Words to Review</div>
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ reviewWords.length }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Review Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">{{ stats.learned_words }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400">Words Learned</div>
            </div>
          </div>
          
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">{{ stats.review_words }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400">In Review</div>
            </div>
          </div>
          
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ stats.mastered_words }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400">Mastered</div>
            </div>
          </div>
        </div>

        <!-- Review Mode Selection -->
        <div v-if="!currentReviewMode" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Choose Review Mode</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Flashcard Review -->
              <div class="border-2 border-purple-200 dark:border-purple-800 rounded-lg p-6 hover:border-purple-500 transition-colors cursor-pointer" @click="startFlashcardReview">
                <div class="text-center">
                  <div class="text-4xl mb-4">📚</div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Flashcard Review</h3>
                  <p class="text-gray-600 dark:text-gray-400 mb-4">Practice with digital flashcards</p>
                  <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg">
                    Start Flashcards
                  </button>
                </div>
              </div>

              <!-- Quiz Review -->
              <div class="border-2 border-blue-200 dark:border-blue-800 rounded-lg p-6 hover:border-blue-500 transition-colors cursor-pointer" @click="startQuizReview">
                <div class="text-center">
                  <div class="text-4xl mb-4">🧠</div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Quiz Review</h3>
                  <p class="text-gray-600 dark:text-gray-400 mb-4">Test yourself with multiple choice</p>
                  <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Start Quiz
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Flashcard Review -->
        <div v-if="currentReviewMode === 'flashcard' && currentWord" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <div class="text-center mb-6">
              <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                Card {{ currentWordIndex + 1 }} of {{ reviewWords.length }}
              </div>
              <div class="text-6xl mb-6">📖</div>
            </div>

            <!-- Flashcard -->
            <div 
              @click="flipCard" 
              class="bg-gradient-to-br from-purple-100 to-blue-100 dark:from-purple-900 dark:to-blue-900 rounded-xl p-8 min-h-[300px] flex items-center justify-center cursor-pointer transition-all duration-300 hover:scale-105 mb-6"
            >
              <div v-if="!isFlipped" class="text-center">
                <div class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ currentWord.word }}</div>
                <div class="text-lg text-gray-600 dark:text-gray-400">{{ currentWord.pronunciation }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-500 mt-4">Click to reveal definition</div>
              </div>
              
              <div v-else class="text-center">
                <div class="text-xl text-gray-800 dark:text-gray-200 mb-4">{{ currentWord.definition }}</div>
                <div v-if="currentWord.example" class="text-lg text-gray-600 dark:text-gray-400 italic">"{{ currentWord.example }}"</div>
              </div>
            </div>

            <!-- Difficulty Rating -->
            <div v-if="isFlipped" class="text-center mb-6">
              <p class="text-gray-700 dark:text-gray-300 mb-4">How well do you know this word?</p>
              <div class="flex justify-center gap-4">
                <button @click="rateWord(1)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                  😕 Hard
                </button>
                <button @click="rateWord(3)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors">
                  🤔 Medium
                </button>
                <button @click="rateWord(5)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                  😊 Easy
                </button>
              </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-between">
              <button
                v-if="currentWordIndex > 0"
                @click="previousWord"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors"
              >
                ← Previous
              </button>
              <div v-else></div>

              <button
                @click="nextWord"
                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-colors"
              >
                <span v-if="currentWordIndex < reviewWords.length - 1">Next →</span>
                <span v-else>Finish Review</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Review Complete -->
        <div v-if="reviewComplete" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-8 text-center">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Review Complete!</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Great job reviewing your vocabulary!</p>
            
            <div class="flex justify-center gap-4">
              <button 
                @click="resetReview"
                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
              >
                Review Again
              </button>
              <Link href="/learn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                Learn New Words
              </Link>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="reviewWords.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-8 text-center">
            <div class="text-6xl mb-4">📚</div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">No Words to Review</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Start learning some words first, then come back to review them!</p>
            
            <Link href="/learn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
              Start Learning
            </Link>
          </div>
        </div>

        <!-- Progress Bar -->
        <div v-if="currentReviewMode && reviewWords.length > 0 && !reviewComplete" class="mt-6">
          <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div 
              class="bg-purple-600 h-2 rounded-full transition-all duration-300"
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
  reviewWords: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({
      learned_words: 0,
      review_words: 0,
      mastered_words: 0
    })
  }
});

// State
const currentReviewMode = ref('');
const currentWordIndex = ref(0);
const isFlipped = ref(false);
const reviewComplete = ref(false);

// Computed
const currentWord = computed(() => {
  return props.reviewWords[currentWordIndex.value] || null;
});

const progressPercentage = computed(() => {
  if (props.reviewWords.length === 0) return 0;
  return ((currentWordIndex.value + 1) / props.reviewWords.length) * 100;
});

// Methods
function startFlashcardReview() {
  currentReviewMode.value = 'flashcard';
  currentWordIndex.value = 0;
  isFlipped.value = false;
  reviewComplete.value = false;
}

function startQuizReview() {
  currentReviewMode.value = 'quiz';
  // Quiz implementation would go here
  // For now, fallback to flashcard
  startFlashcardReview();
}

function flipCard() {
  isFlipped.value = !isFlipped.value;
}

function rateWord(difficulty) {
  if (!currentWord.value) return;

  const form = useForm({
    word_id: currentWord.value.id,
    difficulty: difficulty
  });

  form.post('/review/answer', {
    onSuccess: () => {
      nextWord();
    }
  });
}

function previousWord() {
  if (currentWordIndex.value > 0) {
    currentWordIndex.value--;
    isFlipped.value = false;
  }
}

function nextWord() {
  if (currentWordIndex.value < props.reviewWords.length - 1) {
    currentWordIndex.value++;
    isFlipped.value = false;
  } else {
    reviewComplete.value = true;
  }
}

function resetReview() {
  currentReviewMode.value = '';
  currentWordIndex.value = 0;
  isFlipped.value = false;
  reviewComplete.value = false;
}
</script>