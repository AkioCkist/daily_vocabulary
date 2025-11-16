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
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-violet-600 bg-clip-text text-transparent mb-2 dark:from-indigo-400 dark:via-purple-400 dark:to-violet-400 drop-shadow-sm">Word of the Day</h1>
        <p class="text-gray-600 dark:text-gray-400 font-medium">Expand your vocabulary, one word at a time</p>
      </div>

      <!-- Word Card -->
      <div class="mb-6">
        <WordCard :word="wordOfTheDay" />
      </div>

      <!-- Action Button -->
      <div class="mt-8 animate-slide-up">
        <div class="relative h-20 flex items-center justify-center">
          <button
            v-if="!added"
            @click="addWord"
            class="w-full bg-gradient-to-r from-green-500 via-emerald-500 to-emerald-600 text-white font-bold py-5 px-8 rounded-2xl shadow-depth-3 hover-lift btn-3d flex items-center justify-center gap-3 relative overflow-hidden group animate-scale-in transition-all duration-300"
            :class="{ 'opacity-0 scale-95': added }"
          >
          <!-- Button background animation -->
          <div class="absolute inset-0 bg-gradient-to-r from-green-400 via-emerald-400 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          
          <!-- Button content -->
          <div class="relative z-10 flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </div>
            <span class="text-lg">Add to My Vocabulary</span>
          </div>
          
          <!-- Shimmer effect -->
          <div class="absolute inset-0 -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
          </button>

          <!-- Success Message -->
          <div v-if="added" class="absolute inset-0 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200/50 rounded-2xl p-4 flex items-center gap-4 shadow-depth-2 dark:from-green-900/30 dark:to-emerald-900/30 dark:border-green-700/50 backdrop-blur-sm animate-bounce-in transition-all duration-500" :class="{ 'opacity-0 scale-95': isHiding, 'opacity-100 scale-100': !isHiding }">
            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-depth-2 flex-shrink-0">
              <svg class="w-5 h-5 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div>
              <p class="text-green-800 font-bold dark:text-green-400">Awesome! 🎉</p>
              <p class="text-green-700 dark:text-green-300 text-sm font-medium">Word added to your vocabulary</p>
            </div>
          </div>
        </div>
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
import { useForm, Head } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import WordCard from '@/Components/WordCard.vue';
import SubscribeForm from '@/Components/SubscribeForm.vue';

const props = defineProps({
  wordOfTheDay: Object,
  user: {
    type: Object,
    default: null
  }
});

const added = ref(false);
const isHiding = ref(false);
const form = useForm({ word_id: props.wordOfTheDay.id });

function addWord() {
  form.post('/user/words', {
    onSuccess: () => {
      added.value = true;
      isHiding.value = false;
      // Start fade out animation after 1.8 seconds
      setTimeout(() => {
        isHiding.value = true;
      }, 1800);
      // Hide success message and restore button after 2.3 seconds
      setTimeout(() => {
        added.value = false;
        isHiding.value = false;
      }, 2300);
    },
  });
}
</script>
