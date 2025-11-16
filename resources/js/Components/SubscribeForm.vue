<template>
  <div class="mt-8 relative overflow-hidden">
    <!-- Main container with subtle dark theme -->
    <div class="bg-gradient-to-br from-slate-800/95 via-indigo-900/90 to-slate-900/95 p-8 rounded-2xl shadow-2xl text-white backdrop-blur-sm relative border border-indigo-700/30">
      <!-- Subtle background decorations -->
      <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/15 to-transparent rounded-2xl"></div>
      <div class="absolute -top-16 -right-16 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-purple-500/15 rounded-full blur-2xl"></div>
      
      <div class="relative z-10">
        <div class="text-center mb-8">
          <div class="relative inline-block mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-600/40 to-slate-700/60 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg border border-indigo-500/30">
              <span class="text-3xl">📚</span>
            </div>
          </div>
          <h3 class="text-2xl font-bold mb-3 bg-gradient-to-r from-white to-indigo-200 bg-clip-text text-transparent">Daily Word Delivery</h3>
          <p class="text-slate-300 text-base font-medium">Expand your vocabulary with a carefully curated word each day</p>
        </div>
        
        <!-- Subscribe Form - Show if not subscribed -->
        <form v-if="!isSubscribed && !isLoading" @submit.prevent="subscribe" class="space-y-6">
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-indigo-500/20 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3 border border-indigo-400/40"
          >
            <div class="flex items-center gap-3">
              <div v-if="!form.processing" class="w-6 h-6 bg-yellow-400/30 border border-yellow-300/40 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <div v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              <span class="text-base">{{ form.processing ? 'Subscribing...' : 'Start Learning' }}</span>
            </div>
          </button>
        </form>

        <!-- Already Subscribed - Show if subscribed -->
        <div v-else-if="isSubscribed && !isLoading" class="space-y-6">
          <div class="bg-gradient-to-br from-slate-700/40 to-emerald-900/20 backdrop-blur-sm rounded-xl p-6 text-center shadow-lg border border-emerald-500/30">
            <div class="w-12 h-12 bg-emerald-500/30 rounded-xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/40">
              <span class="text-2xl text-emerald-300">✓</span>
            </div>
            <p class="font-semibold text-lg mb-2 text-slate-100">You're subscribed!</p>
            <p class="text-slate-300 text-sm">{{ userEmail }} will receive daily vocabulary words</p>
          </div>
          
          <button
            type="button"
            @click="unsubscribe"
            :disabled="unsubscribeForm.processing"
            class="w-full bg-gradient-to-r from-red-600/80 to-red-700/80 hover:from-red-500/90 hover:to-red-600/90 text-red-100 hover:text-white font-medium py-3 px-6 rounded-xl shadow-lg hover:shadow-red-500/20 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3 border border-red-500/40"
          >
            <div class="flex items-center gap-3">
              <div v-if="!unsubscribeForm.processing" class="w-5 h-5 bg-red-400/30 border border-red-300/40 rounded-lg flex items-center justify-center">
                <svg class="w-3 h-3 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </div>
              <div v-else class="w-5 h-5 border-2 border-red-300 border-t-transparent rounded-full animate-spin"></div>
              <span class="text-sm">{{ unsubscribeForm.processing ? 'Unsubscribing...' : 'Unsubscribe' }}</span>
            </div>
          </button>
        </div>

        <!-- Loading state -->
        <div v-else-if="isLoading" class="text-center py-8">
          <div class="w-8 h-8 border-2 border-slate-400/50 border-t-slate-300 rounded-full animate-spin mx-auto mb-4"></div>
          <p class="text-slate-300 font-medium text-base">Loading...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';



const isSubscribed = ref(false);
const isLoading = ref(true);
const userEmail = ref('');

const form = useForm({
  email: '',
});

const unsubscribeForm = useForm({
  email: ''
});

// Get props to access user data
const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
});

// Check subscription status on mount
onMounted(async () => {
  console.log('SubscribeForm mounted. User prop:', props.user);
  
  try {
    const response = await fetch('/auth-subscription-status');
    const data = await response.json();
    
    console.log('Subscription status response:', data);
    
    if (data.email) {
      userEmail.value = data.email;
      form.email = data.email;
      isSubscribed.value = data.subscribed;
      if (data.subscribed) {
        unsubscribeForm.email = data.email;
      }
    } else if (props.user && props.user.email) {
      // If no subscription data but user is logged in, use user email
      userEmail.value = props.user.email;
      form.email = props.user.email;
      isSubscribed.value = false;
    }
    
    console.log('Final component state:', {
      userEmail: userEmail.value,
      formEmail: form.email,
      isSubscribed: isSubscribed.value,
      isLoading: isLoading.value
    });
  } catch (error) {
    console.error('Error checking subscription status:', error);
  } finally {
    isLoading.value = false;
  }
});

function subscribe() {
  console.log('Subscribe function called', { email: form.email, userEmail: userEmail.value });
  
  // Ensure we have an email before subscribing
  if (!form.email && props.user && props.user.email) {
    form.email = props.user.email;
    userEmail.value = props.user.email;
  }
  
  // Double check we have an email
  if (!form.email) {
    console.error('No email available for subscription');
    return;
  }
  
  console.log('Submitting subscription with email:', form.email);
  
  form.post('/subscribe', {
    onSuccess: () => {
      console.log('Subscription successful');
      isSubscribed.value = true;
      unsubscribeForm.email = form.email || userEmail.value;
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
