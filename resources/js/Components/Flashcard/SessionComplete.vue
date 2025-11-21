<template>
  <transition name="scale-fade">
    <div class="max-w-2xl mx-auto text-center">
      <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-10">
        <!-- Success Icon -->
        <div class="relative mb-8">
          <div class="w-24 h-24 bg-gradient-to-br from-green-400 via-emerald-500 to-green-600 rounded-full flex items-center justify-center mx-auto shadow-lg animate-bounce-slow">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-32 h-32 bg-green-400 rounded-full opacity-20 animate-ping-slow"></div>
          </div>
        </div>
        
        <!-- Title -->
        <h2 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 mb-3">
          Session Complete!
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-10 text-lg">
          Great job! You've completed {{ totalWords }} flashcards.
        </p>
        
        <!-- Statistics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
          <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ totalWords }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">Total Words</div>
          </div>
          <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ correctCount }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">Correct</div>
          </div>
          <div class="bg-gradient-to-br from-red-50 to-rose-100 dark:from-red-900/20 dark:to-rose-900/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ incorrectCount }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">Incorrect</div>
          </div>
          <div class="bg-gradient-to-br from-yellow-50 to-amber-100 dark:from-yellow-900/20 dark:to-amber-900/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ hintsUsed }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">Hints Used</div>
          </div>
        </div>

        <!-- Accuracy Badge -->
        <div v-if="accuracy >= 0" class="mb-8">
          <div class="inline-flex items-center gap-2 px-6 py-3 rounded-full" :class="accuracyClass">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-bold text-lg">{{ accuracy }}% Accuracy</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 justify-center">
          <button
            @click="completeSession"
            :disabled="isCompleting"
            class="group bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 hover:from-indigo-600 hover:via-purple-600 hover:to-purple-700 text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="isCompleting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>{{ isCompleting ? 'Completing...' : 'Back to Dashboard' }}</span>
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  totalWords: {
    type: Number,
    required: true
  },
  correctCount: {
    type: Number,
    default: 0
  },
  incorrectCount: {
    type: Number,
    default: 0
  },
  hintsUsed: {
    type: Number,
    default: 0
  },
  route: {
    type: Function,
    required: true
  }
});

const accuracy = computed(() => {
  const total = props.correctCount + props.incorrectCount;
  if (total === 0) return 0;
  return Math.round((props.correctCount / total) * 100);
});

const accuracyClass = computed(() => {
  if (accuracy.value >= 80) {
    return 'bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 text-green-700 dark:text-green-300';
  } else if (accuracy.value >= 60) {
    return 'bg-gradient-to-r from-yellow-100 to-amber-100 dark:from-yellow-900/30 dark:to-amber-900/30 text-yellow-700 dark:text-yellow-300';
  } else {
    return 'bg-gradient-to-r from-red-100 to-rose-100 dark:from-red-900/30 dark:to-rose-900/30 text-red-700 dark:text-red-300';
  }
});

// State for completion
const isCompleting = ref(false);

// Complete session and redirect
const completeSession = async () => {
  if (isCompleting.value) {
    console.log('SessionComplete: Already completing, ignoring duplicate call');
    return;
  }
  
  console.log('SessionComplete: ==> Starting completion process');
  console.log('SessionComplete: Props received:', props);
  console.log('SessionComplete: Available routes:', {
    home: props.route ? props.route('home') : 'route function not available',
    flashcardsComplete: props.route ? props.route('flashcards.complete') : 'route function not available'
  });
  
  isCompleting.value = true;
  
  try {
    const completeUrl = props.route('flashcards.complete');
    console.log('SessionComplete: Calling complete endpoint:', completeUrl);
    
    // Call the complete endpoint to process session and potentially show save popup
    await router.post(completeUrl, {}, {
      preserveState: false,
      preserveScroll: false,
      onBefore: (visit) => {
        console.log('SessionComplete: About to make request:', visit);
        return true;
      },
      onStart: (visit) => {
        console.log('SessionComplete: Request started:', visit);
      },
      onProgress: (progress) => {
        console.log('SessionComplete: Request progress:', progress);
      },
      onSuccess: (page) => {
        console.log('SessionComplete: Success response received');
        console.log('SessionComplete: Page props:', page.props);
        console.log('SessionComplete: Page component:', page.component);
        console.log('SessionComplete: Page url:', page.url);
        // The controller will handle redirect with save popup data if needed
      },
      onError: (errors) => {
        console.error('SessionComplete: Error completing session:', errors);
        // Fallback: just redirect to home
        console.log('SessionComplete: Falling back to direct home redirect');
        router.visit(props.route('home'));
      },
      onFinish: () => {
        console.log('SessionComplete: Request finished');
        isCompleting.value = false;
      }
    });
  } catch (error) {
    console.error('SessionComplete: Exception during completion:', error);
    // Fallback: just redirect to home
    console.log('SessionComplete: Exception fallback - redirecting to home');
    router.visit(props.route('home'));
    isCompleting.value = false;
  }
};
</script>

<style scoped>
.scale-fade-enter-active {
  animation: scale-fade-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scale-fade-in {
  0% {
    opacity: 0;
    transform: scale(0.8) translateY(20px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce-slow {
  animation: bounce-slow 2s ease-in-out infinite;
}

@keyframes ping-slow {
  0% {
    transform: scale(1);
    opacity: 0.5;
  }
  100% {
    transform: scale(2);
    opacity: 0;
  }
}

.animate-ping-slow {
  animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}
</style>
