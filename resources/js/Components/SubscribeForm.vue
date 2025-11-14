<template>
  <div class="mt-8 bg-gradient-to-br from-indigo-500 to-purple-600 p-8 rounded-3xl shadow-2xl text-white animate-fade-in dark:from-indigo-600 dark:to-purple-700">
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 backdrop-blur rounded-2xl mb-3">
        <span class="text-3xl">✉️</span>
      </div>
      <h3 class="text-2xl font-black mb-2">Daily Word Delivery</h3>
      <p class="text-indigo-100 dark:text-purple-100">Level up your vocabulary! Get a new word every day 🚀</p>
    </div>
    
    <!-- Subscribe Form - Show if not subscribed -->
    <form v-if="!isSubscribed && !isLoading" @submit.prevent="subscribe" class="space-y-4">
      <button
        type="submit"
        :disabled="form.processing"
        class="w-full bg-white text-indigo-600 font-bold py-4 px-6 rounded-2xl hover:bg-indigo-50 transform hover:scale-105 transition-all duration-200 shadow-xl disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2"
      >
        {{ form.processing ? 'Subscribing...' : 'Start Learning Now!' }}
      </button>
    </form>

    <!-- Already Subscribed - Show if subscribed -->
    <div v-else-if="isSubscribed && !isLoading" class="space-y-4">
      <div class="bg-white/20 backdrop-blur rounded-2xl p-4 text-center">
        <p class="font-bold text-lg mb-2">🎉 You're all set!</p>
        <p class="text-sm text-indigo-100">{{ userEmail }} is subscribed to Daily Vocabulary</p>
      </div>
      
      <button
        type="button"
        @click="unsubscribe"
        :disabled="unsubscribeForm.processing"
        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 px-6 rounded-2xl transform hover:scale-105 transition-all duration-200 shadow-xl disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2"
      >
        <svg v-if="!unsubscribeForm.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        {{ unsubscribeForm.processing ? 'Unsubscribing...' : 'Unsubscribe' }}
      </button>
    </div>

    <!-- Loading state -->
    <div v-else-if="isLoading" class="text-center">
      <p class="text-indigo-100">Loading...</p>
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
