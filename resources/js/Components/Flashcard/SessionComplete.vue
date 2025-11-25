<template>
  <transition name="scale-fade">
    <div class="max-w-2xl mx-auto text-center p-4">
      <div class="bg-gray-900/80 backdrop-blur-md rounded-3xl shadow-2xl shadow-indigo-900/40 ring-1 ring-gray-700 p-10 md:p-12">
        
        <div class="relative mb-10">
          <div class="w-24 h-24 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto shadow-inner border border-gray-700/50">
            <div class="w-20 h-20 bg-gradient-to-br from-green-500 via-emerald-500 to-green-600 rounded-full flex items-center justify-center shadow-xl shadow-green-600/30 animate-bounce-slow">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
          </div>
          <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-32 h-32 bg-green-500 rounded-full opacity-10 animate-ping-slow"></div>
          </div>
        </div>
        
        <h2 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400 mb-4">
          Session Complete!
        </h2>
        <p class="text-gray-400 mb-12 text-lg font-light">
          Fantastic work! You successfully reviewed <strong class="text-white font-bold">{{ totalWords }}</strong> flashcards.
        </p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
          
          <div class="p-4 rounded-xl bg-indigo-900/30 border border-indigo-700/50 shadow-lg hover:shadow-indigo-900/50 transition-all duration-300">
            <div class="text-4xl font-bold text-indigo-400">{{ totalWords }}</div>
            <div class="text-sm text-indigo-300 mt-1 font-medium">Total Words</div>
          </div>
          
          <div class="p-4 rounded-xl bg-emerald-900/30 border border-emerald-700/50 shadow-lg hover:shadow-emerald-900/50 transition-all duration-300">
            <div class="text-4xl font-bold text-emerald-400">{{ correctCount }}</div>
            <div class="text-sm text-emerald-300 mt-1 font-medium">Correct</div>
          </div>
          
          <div class="p-4 rounded-xl bg-red-900/30 border border-red-700/50 shadow-lg hover:shadow-red-900/50 transition-all duration-300">
            <div class="text-4xl font-bold text-red-400">{{ incorrectCount }}</div>
            <div class="text-sm text-red-300 mt-1 font-medium">Incorrect</div>
          </div>
          
          <div class="p-4 rounded-xl bg-amber-900/30 border border-amber-700/50 shadow-lg hover:shadow-amber-900/50 transition-all duration-300">
            <div class="text-4xl font-bold text-amber-400">{{ accuracyRate }}%</div>
            <div class="text-sm text-amber-300 mt-1 font-medium">Accuracy</div>
          </div>
        </div>
        
        <div class="flex justify-center">
          <button
            @click.prevent="completeSession"
            class="inline-flex items-center justify-center gap-2 py-3 px-12 rounded-xl font-bold transition-all duration-200 
                   bg-gradient-to-r from-indigo-600 to-purple-700 text-white hover:from-indigo-700 hover:to-purple-800 shadow-lg shadow-indigo-600/30 hover:shadow-xl hover:scale-[1.02] active:scale-95 disabled:opacity-50"
            :disabled="isCompleting"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
  // The word IDs reviewed in this session (needed for saving)
  reviewedWords: {
    type: Array,
    default: () => []
  },
  // The original settings that started the session
  settings: {
    type: Object,
    required: true
  },
  // Inertia route helper passed from controller
  route: { 
    type: Function,
    required: true
  }
});

const emit = defineEmits(['save']);

const isCompleting = ref(false);

const accuracyRate = computed(() => {
  if (props.totalWords === 0) return 0;
  return Math.round((props.correctCount / props.totalWords) * 100);
});

// Data payload to be passed when saving the session
const sessionData = computed(() => ({
    settings: props.settings,
    totalWords: props.totalWords,
    correctCount: props.correctCount,
    incorrectCount: props.incorrectCount,
    reviewedWords: props.reviewedWords,
}));

// Function to notify backend that the session is complete and redirect
const completeSession = () => {
  if (isCompleting.value) return;
  isCompleting.value = true;
  
  try {
    // Send the data to the backend to update mastery levels and log the session
    router.post(props.route('flashcards.complete'), sessionData.value, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: (page) => {
        console.log('SessionComplete: Backend processing complete');
        
        // Check for specific payload from backend for save session prompt
        if (page.props.flashcards.show_save_popup) {
            // If the backend wants to show the save session modal/popup on the home screen,
            // we redirect with the data in the URL query parameters.
            const data = page.props.flashcards.save_session_data;
            const encodedData = encodeURIComponent(JSON.stringify(data));
            router.visit(
                `${props.route('home')}?show_save_popup=true&save_session_data=${encodedData}`,
                { preserveScroll: true, preserveState: true }
            );
        } else {
            // Standard redirect to home page
            router.visit(props.route('home'));
        }
      },
      onError: (errors) => {
        console.error('SessionComplete: Error completing session:', errors);
        // Fallback: just redirect to home
        router.visit(props.route('home'));
      },
      onFinish: () => {
        isCompleting.value = false;
      }
    });
  } catch (error) {
    console.error('SessionComplete: Exception during completion:', error);
    // Fallback: just redirect to home
    router.visit(props.route('home'));
    isCompleting.value = false;
  }
};</script>

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
  animation: bounce-slow 4s infinite ease-in-out;
}

@keyframes ping-slow {
  0% {
    transform: scale(0.9);
    opacity: 0.1;
  }
  75%, 100% {
    transform: scale(1.5);
    opacity: 0;
  }
}

.animate-ping-slow {
  animation: ping-slow 5s cubic-bezier(0, 0, 0.2, 1) infinite;
}
</style>