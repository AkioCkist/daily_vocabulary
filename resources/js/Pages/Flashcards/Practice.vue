<template>
  <Head title="Flashcard Practice - DailyVocab" />
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-50 to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950">
    <Header :user="$page.props.auth.user" />

    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Progress Bar -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Flashcard Practice</h1>
          <div class="text-sm text-gray-600 dark:text-gray-400">
            {{ currentIndex + 1 }} of {{ words.length }}
          </div>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div 
            class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${((currentIndex + 1) / words.length) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Flashcard -->
      <div v-if="currentWord" class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden min-h-[400px] flex flex-col">
          <!-- Card Header -->
          <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-6">
            <div class="flex items-center justify-between">
              <div class="text-sm opacity-90">{{ currentWord.cefr_level }} • {{ currentWord.topic }}</div>
              <button
                @click="toggleCard"
                class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition-colors"
              >
                {{ showAnswer ? 'Show Word' : 'Show Meaning' }}
              </button>
            </div>
          </div>

          <!-- Card Content -->
          <div class="flex-1 flex items-center justify-center p-8">
            <div class="text-center">
              <div v-if="!showAnswer" class="space-y-4">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white">
                  {{ currentWord.word }}
                </h2>
                <div v-if="currentWord.pronunciation" class="text-lg text-gray-600 dark:text-gray-400">
                  /{{ currentWord.pronunciation }}/
                </div>
              </div>
              
              <div v-else class="space-y-4">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ currentWord.definition }}
                </h3>
                <div v-if="currentWord.example" class="text-lg text-gray-600 dark:text-gray-400 italic">
                  "{{ currentWord.example }}"
                </div>
              </div>
            </div>
          </div>

          <!-- Card Actions -->
          <div class="p-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
            <div v-if="showAnswer" class="flex gap-4 justify-center">
              <button
                @click="() => { console.log('CLICKED: Didn\'t Know button'); markAnswer(false); }"
                class="flex-1 max-w-xs bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Didn't Know
              </button>
              <button
                @click="() => { console.log('CLICKED: Got It Right button'); markAnswer(true); }"
                class="flex-1 max-w-xs bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Got It Right
              </button>
            </div>
            <div v-else class="text-center">
              <p class="text-gray-600 dark:text-gray-400 mb-4">Think about the meaning, then reveal the answer</p>
              <button
                @click="toggleCard"
                class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-3 px-8 rounded-xl transition-colors"
              >
                Reveal Answer
              </button>
            </div>
          </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center mt-6">
          <button
            @click="previousCard"
            :disabled="currentIndex === 0"
            class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Previous
          </button>

          <Link
            :href="route('home')"
            class="px-6 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
          >
            Exit Practice
          </Link>

          <button
            @click="nextCard"
            :disabled="currentIndex >= words.length - 1"
            class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Session Complete -->
      <div v-else class="max-w-2xl mx-auto text-center">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8">
          <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Session Complete!</h2>
          <p class="text-gray-600 dark:text-gray-400 mb-8">
            Great job! You've completed {{ words.length }} flashcards.
          </p>
          <div class="flex gap-4 justify-center">
            <Link
              :href="route('home')"
              class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold py-3 px-8 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-colors"
            >
              Back to Dashboard
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
// Simple route helper with fallback URLs
const route = (name, params) => {
  const routes = {
    'home': '/',
    'flashcards.answer': '/flashcards/answer',
    'flashcards.complete': '/flashcards/complete'
  };
  
  return routes[name] || '#';
};

const props = defineProps({
  words: {
    type: Array,
    required: true
  },
  settings: {
    type: Object,
    required: true
  }
});

// Debug props
console.log('=== PRACTICE COMPONENT INITIALIZED ===');
console.log('Practice component props:', props);
console.log('Words array:', props.words);
console.log('Settings:', props.settings);

// Add global error handler
window.addEventListener('error', (e) => {
  console.error('🚨 GLOBAL ERROR:', e.error);
  console.error('🚨 ERROR MESSAGE:', e.message);
  console.error('🚨 ERROR STACK:', e.error?.stack);
});

// Add unhandled promise rejection handler
window.addEventListener('unhandledrejection', (e) => {
  console.error('🚨 UNHANDLED PROMISE REJECTION:', e.reason);
});

// Reactive state
const currentIndex = ref(0);
const showAnswer = ref(false);
const startTime = ref(null);

// Computed properties
const currentWord = computed(() => {
  return props.words[currentIndex.value] || null;
});

// Methods
function toggleCard() {
  if (!showAnswer.value) {
    startTime.value = Date.now();
  }
  showAnswer.value = !showAnswer.value;
}

function markAnswer(isCorrect) {
  console.log('=== MARK ANSWER START ===');
  console.log('markAnswer called with:', isCorrect);
  console.log('currentWord:', currentWord.value);
  console.log('currentIndex:', currentIndex.value);
  console.log('props.words length:', props.words?.length);
  console.log('showAnswer.value:', showAnswer.value);
  console.log('startTime.value:', startTime.value);
  
  if (!currentWord.value) {
    console.error('No current word available');
    return;
  }
  
  const responseTime = startTime.value ? Date.now() - startTime.value : null;
  
  console.log('Submitting answer to:', route('flashcards.answer'));
  console.log('Data:', {
    word_id: currentWord.value.id,
    is_correct: isCorrect,
    response_time: responseTime
  });
  
  try {
    // Submit answer using fetch instead of Inertia to avoid navigation issues
    console.log('About to submit answer via fetch');
    
    fetch(route('flashcards.answer'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        word_id: currentWord.value.id,
        is_correct: isCorrect,
        response_time: responseTime
      })
    })
    .then(response => {
      console.log('✅ Answer submission response status:', response.status);
      if (response.ok) {
        console.log('✅ Answer submission successful');
        nextCard();
      } else {
        console.error('❌ Answer submission failed with status:', response.status);
        return response.text().then(text => {
          console.error('❌ Response text:', text);
        });
      }
    })
    .catch(error => {
      console.error('❌ Answer submission network error:', error);
    });
    
    console.log('fetch call initiated');
  } catch (error) {
    console.error('❌ Exception in markAnswer:', error);
  }
  
  console.log('=== MARK ANSWER END ===');
}

function nextCard() {
  console.log('nextCard called');
  console.log('currentIndex:', currentIndex.value);
  console.log('total words:', props.words.length);
  
  if (currentIndex.value < props.words.length - 1) {
    console.log('Moving to next card');
    currentIndex.value++;
    showAnswer.value = false;
    startTime.value = null;
    console.log('New currentIndex:', currentIndex.value);
  } else {
    console.log('Completing session - showing completion screen');
    // Show completion screen first
    currentIndex.value = props.words.length;
    console.log('Completion index set to:', currentIndex.value);
    // Then complete session in background
    router.post(route('flashcards.complete'), {}, {
      preserveState: true,
      preserveScroll: true,
      onStart: () => console.log('Session completion started'),
      onSuccess: () => console.log('Session completion successful'),
      onError: (errors) => console.error('Session completion failed:', errors)
    });
  }
}

function previousCard() {
  if (currentIndex.value > 0) {
    currentIndex.value--;
    showAnswer.value = false;
    startTime.value = null;
  }
}

// Keyboard shortcuts
onMounted(() => {
  console.log('=== PRACTICE COMPONENT MOUNTED ===');
  console.log('Final props check - words:', props.words);
  console.log('Final props check - settings:', props.settings);
  console.log('currentWord on mount:', currentWord.value);
  const handleKeydown = (event) => {
    switch (event.key) {
      case ' ':
      case 'Enter':
        event.preventDefault();
        if (!showAnswer.value) {
          toggleCard();
        }
        break;
      case '1':
        if (showAnswer.value) {
          event.preventDefault();
          markAnswer(false);
        }
        break;
      case '2':
        if (showAnswer.value) {
          event.preventDefault();
          markAnswer(true);
        }
        break;
      case 'ArrowLeft':
        event.preventDefault();
        previousCard();
        break;
      case 'ArrowRight':
        event.preventDefault();
        if (showAnswer.value) {
          nextCard();
        } else {
          toggleCard();
        }
        break;
    }
  };

  document.addEventListener('keydown', handleKeydown);
  
  return () => {
    document.removeEventListener('keydown', handleKeydown);
  };
});
</script>