<template>
  <div class="mt-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
    <h3 class="text-xl font-semibold mb-3">Subscribe for Daily Words</h3>
    <p class="text-sm text-gray-600 mb-4">Get a new word delivered to your email every day!</p>
    
    <form @submit.prevent="subscribe" class="space-y-3">
      <div>
        <input
          v-model="form.email"
          type="email"
          placeholder="Enter your email"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
      </div>
      
      <button
        type="submit"
        :disabled="form.processing"
        class="w-full bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ form.processing ? 'Subscribing...' : 'Subscribe' }}
      </button>
      
      <p v-if="subscribed" class="text-green-600 text-sm text-center">Successfully subscribed!</p>
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
