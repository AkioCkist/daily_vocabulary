<template>
  <Head title="Word of the Day - DailyVocab" />
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-50 to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950 relative overflow-hidden">
    <!-- Background decoration for depth -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-purple-500/15 to-pink-500/15 rounded-full blur-3xl animate-floating"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-indigo-500/15 to-blue-500/15 rounded-full blur-3xl animate-floating" style="animation-delay: -1s;"></div>
    </div>
    
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-2xl mx-auto px-4 py-8 relative z-10">
      <!-- Header Section -->
      <div class="text-center mb-8 animate-fade-in">
        <div class="inline-flex items-center justify-center w-18 h-18 bg-gradient-to-br from-yellow-400 via-orange-400 to-orange-500 rounded-2xl mb-4 shadow-depth-3 animate-floating hover-lift relative">
          <div class="absolute inset-0 bg-gradient-to-br from-yellow-300/30 to-orange-400/30 rounded-2xl blur-sm"></div>
          <span class="text-4xl relative z-10 drop-shadow-sm">📚</span>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-violet-600 bg-clip-text text-transparent mb-2 dark:from-indigo-400 dark:via-purple-400 dark:to-violet-400 drop-shadow-sm">Vocabulary Explorer</h1>
        <p class="text-gray-600 dark:text-gray-400 font-medium">Discover, learn, and master new words</p>
      </div>

      <!-- Word Filter Section -->
      <div class="mb-6">
        <WordFilter @word-selected="handleWordSelected" />
      </div>

      <!-- Quick Actions -->
      <div v-if="user" class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <Link href="/learn" class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 text-white font-bold py-4 px-6 rounded-2xl shadow-depth-3 hover-lift btn-3d flex items-center justify-center gap-3 relative overflow-hidden group transition-all duration-300">
          <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
          </div>
          <span>Start Learning</span>
        </Link>
        
        <Link href="/test" class="bg-gradient-to-r from-green-500 via-emerald-500 to-teal-600 text-white font-bold py-4 px-6 rounded-2xl shadow-depth-3 hover-lift btn-3d flex items-center justify-center gap-3 relative overflow-hidden group transition-all duration-300">
          <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <span>Take Test</span>
        </Link>

        <Link href="/review" class="bg-gradient-to-r from-purple-500 via-pink-500 to-rose-600 text-white font-bold py-4 px-6 rounded-2xl shadow-depth-3 hover-lift btn-3d flex items-center justify-center gap-3 relative overflow-hidden group transition-all duration-300">
          <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
          </div>
          <span>Review Words</span>
        </Link>
      </div>

      <!-- Subscribe Section -->
      <div class="mt-10">
        <SubscribeForm />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import WordFilter from '@/Components/WordFilter.vue';
import SubscribeForm from '@/Components/SubscribeForm.vue';

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
});

/**
 * Handle word selection from filter
 */
function handleWordSelected(word) {
  // You can add logic here to handle the selected word
  console.log('Selected word:', word);
}
</script>
