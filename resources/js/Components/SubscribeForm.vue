<template>
  <div class="mt-8 relative overflow-hidden">
    <!-- Main container with enhanced glass effect -->
    <div class="bg-gradient-to-br from-indigo-500 via-purple-600 to-violet-700 p-8 rounded-3xl shadow-depth-4 text-white animate-fade-in dark:from-indigo-600 dark:via-purple-700 dark:to-violet-800 relative border border-white/20">
      <!-- Background decorations -->
      <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent rounded-3xl"></div>
      <div class="absolute -top-20 -right-20 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
      <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-purple-300/20 rounded-full blur-xl"></div>
      
      <div class="relative z-10">
        <div class="text-center mb-8">
          <div class="relative inline-block mb-4">
            <div class="absolute inset-0 bg-white/30 backdrop-blur-md rounded-2xl blur-sm animate-pulse-glow"></div>
            <div class="relative w-16 h-16 bg-white/25 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-depth-2 border border-white/30">
              <span class="text-4xl drop-shadow-sm animate-floating">✉️</span>
            </div>
          </div>
          <h3 class="text-3xl font-black mb-3 drop-shadow-sm">Daily Word Delivery</h3>
          <p class="text-indigo-100 dark:text-purple-100 text-lg font-medium">Level up your vocabulary! Get a new word every day 🚀</p>
        </div>
        
        <!-- Subscribe Form - Show if not subscribed -->
        <form v-if="!isSubscribed && !isLoading" @submit.prevent="subscribe" class="space-y-6">
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-white text-indigo-600 font-bold py-5 px-8 rounded-2xl shadow-depth-3 hover-lift btn-3d disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-3 relative overflow-hidden group"
          >
            <!-- Button shimmer effect -->
            <div class="absolute inset-0 -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
            
            <div class="relative z-10 flex items-center gap-3">
              <div v-if="!form.processing" class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <div v-else class="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
              <span class="text-lg">{{ form.processing ? 'Subscribing...' : 'Start Learning Now!' }}</span>
            </div>
          </button>
        </form>

        <!-- Already Subscribed - Show if subscribed -->
        <div v-else-if="isSubscribed && !isLoading" class="space-y-6">
          <div class="bg-white/25 backdrop-blur-md rounded-2xl p-6 text-center shadow-depth-2 border border-white/30">
            <div class="w-16 h-16 bg-white/30 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-depth-1">
              <span class="text-4xl">🎉</span>
            </div>
            <p class="font-bold text-xl mb-2 drop-shadow-sm">You're all set!</p>
            <p class="text-indigo-100 font-medium">{{ userEmail }} is subscribed to Daily Vocabulary</p>
          </div>
          
          <button
            type="button"
            @click="unsubscribe"
            :disabled="unsubscribeForm.processing"
            class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-5 px-8 rounded-2xl shadow-depth-3 hover-lift btn-3d disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-3 relative overflow-hidden group"
          >
            <!-- Button shimmer effect -->
            <div class="absolute inset-0 -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
            
            <div class="relative z-10 flex items-center gap-3">
              <div v-if="!unsubscribeForm.processing" class="w-8 h-8 bg-red-200/30 rounded-full flex items-center justify-center backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </div>
              <div v-else class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              <span class="text-lg">{{ unsubscribeForm.processing ? 'Unsubscribing...' : 'Unsubscribe' }}</span>
            </div>
          </button>
        </div>

        <!-- Loading state -->
        <div v-else-if="isLoading" class="text-center py-8">
          <div class="w-12 h-12 border-3 border-white/30 border-t-white rounded-full animate-spin mx-auto mb-4"></div>
          <p class="text-indigo-100 font-medium text-lg">Loading...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
  user: {
    type: Object,
    default: null,
  },
});

const isSubscribed = ref(false);
const isLoading = ref(true);
const userEmail = ref('');

const form = useForm({
  email: '',
});

const unsubscribeForm = useForm({
  email: ''
});

// Check subscription status on mount
onMounted(async () => {
  try {
    const response = await fetch('/auth-subscription-status');
    const data = await response.json();
    
    if (data.email) {
      userEmail.value = data.email;
      form.email = data.email;
      isSubscribed.value = data.subscribed;
      if (data.subscribed) {
        unsubscribeForm.email = data.email;
      }
    }
  } catch (error) {
    console.error('Error checking subscription status:', error);
  } finally {
    isLoading.value = false;
  }
});

function subscribe() {
  console.log('Subscribe function called', { email: form.email, userEmail: userEmail.value });
  form.post('/subscribe', {
    onSuccess: () => {
      console.log('Subscription successful');
      isSubscribed.value = true;
      unsubscribeForm.email = userEmail.value;
    },
    onError: (errors) => {
      console.error('Subscription failed', errors);
    },
  });
}

function unsubscribe() {
  unsubscribeForm.post('/unsubscribe', {
    onSuccess: () => {
      isSubscribed.value = false;
      form.reset();
      unsubscribeForm.reset();
    },
  });
}
</script>
