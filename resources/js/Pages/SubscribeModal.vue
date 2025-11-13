<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md transform animate-scale-in">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 rounded-t-3xl text-white">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
              <span class="text-2xl">✉️</span>
            </div>
            <h2 class="text-2xl font-black">Subscribe Now</h2>
          </div>
          <button @click="close" class="w-8 h-8 hover:bg-white/20 rounded-full transition-colors flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6">
        <SubscribeForm @success="close" />
      </div>
    </div>
  </div>
</template>

<script setup>
import SubscribeForm from '@/Components/SubscribeForm.vue';
import { ref, defineEmits } from 'vue';

const emit = defineEmits(['close']);

function close() {
  emit('close');
}
</script>
