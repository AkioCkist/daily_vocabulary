<template>
  <div class="mt-8 bg-gradient-to-br from-indigo-500 to-purple-600 p-8 rounded-3xl shadow-2xl text-white animate-fade-in dark:from-indigo-600 dark:to-purple-700">
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 backdrop-blur rounded-2xl mb-3">
        <span class="text-3xl">✉️</span>
      </div>
      <h3 class="text-2xl font-black mb-2">Daily Word Delivery</h3>
      <p class="text-indigo-100 dark:text-purple-100">Level up your vocabulary! Get a new word every day 🚀</p>
    </div>
    
    <form @submit.prevent="subscribe" class="space-y-4">
      <div>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
            </svg>
          </div>
          <input
            v-model="form.email"
            type="email"
            placeholder="your.email@example.com"
            required
            class="w-full pl-12 pr-4 py-4 bg-white/95 backdrop-blur text-gray-900 rounded-2xl focus:ring-4 focus:ring-white/50 focus:outline-none font-medium placeholder-gray-400 shadow-lg"
          />
        </div>
        <p v-if="form.errors.email" class="text-red-200 text-sm mt-2 ml-2 font-medium">{{ form.errors.email }}</p>
      </div>
      
      <button
        type="submit"
        :disabled="form.processing || subscribed"
        class="w-full bg-white text-indigo-600 font-bold py-4 px-6 rounded-2xl hover:bg-indigo-50 transform hover:scale-105 transition-all duration-200 shadow-xl disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2"
      >
        <span v-if="!subscribed">{{ form.processing ? 'Subscribing...' : 'Start Learning Now!' }}</span>
        <span v-else class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          Subscribed!
        </span>
      </button>
      
      <div v-if="subscribed" class="bg-white/20 backdrop-blur rounded-2xl p-4 text-center animate-bounce-in">
        <p class="font-bold">🎉 You're all set!</p>
        <p class="text-sm text-indigo-100">Check your inbox for your first word</p>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const subscribed = ref(false);
const form = useForm({
  email: ''
});

function subscribe() {
  form.post('/subscribe', {
    onSuccess: () => {
      subscribed.value = true;
      form.reset();
    },
  });
}
</script>
