<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-2xl mx-auto px-4 py-8">
      <!-- Header Section -->
      <div class="text-center mb-8 animate-fade-in">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full mb-4 shadow-lg">
          <span class="text-3xl">📚</span>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">Word of the Day</h1>
        <p class="text-gray-600">Expand your vocabulary, one word at a time</p>
      </div>

      <!-- Word Card -->
      <WordCard :word="wordOfTheDay" />

      <!-- Action Button -->
      <div class="mt-6 animate-slide-up">
        <button
          v-if="!added"
          @click="addWord"
          class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-4 px-6 rounded-2xl hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Add to My Vocabulary
        </button>

        <!-- Success Message -->
        <div v-if="added" class="bg-green-50 border-2 border-green-500 rounded-2xl p-4 flex items-center gap-3 animate-bounce-in">
          <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <div>
            <p class="text-green-800 font-bold">Awesome! 🎉</p>
            <p class="text-green-700 text-sm">Word added to your vocabulary</p>
          </div>
        </div>
      </div>

      <!-- Subscribe Section -->
      <SubscribeForm />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
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
const form = useForm({ word_id: props.wordOfTheDay.id });

function addWord() {
  form.post('/user/words', {
    onSuccess: () => (added.value = true),
  });
}
</script>
