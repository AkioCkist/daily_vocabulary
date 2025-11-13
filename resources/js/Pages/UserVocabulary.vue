<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50">
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full mb-4 shadow-lg">
          <span class="text-3xl">🎯</span>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">My Vocabulary</h1>
        <p class="text-gray-600">Your personal word collection</p>
      </div>

      <!-- Stats Card -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-2 border-purple-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
              <span class="text-2xl">✨</span>
            </div>
            <div>
              <p class="text-2xl font-black text-gray-900">{{ userWords.length }}</p>
              <p class="text-sm text-gray-500">Words Learned</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-lg font-bold text-purple-600">Keep it up!</p>
            <p class="text-xs text-gray-500">You're doing great 🚀</p>
          </div>
        </div>
      </div>

      <!-- Word List -->
      <WordList :words="userWords" />

      <!-- Pagination -->
      <Pagination
        :meta="meta"
        @change-page="changePage"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Header from '@/Components/Header.vue';
import WordList from '@/Components/WordList.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  userWords: Array,
  meta: Object,
  user: {
    type: Object,
    default: null
  }
});

const userWords = ref(props.userWords);
const meta = ref(props.meta || {});

function changePage(page) {
  // Reload user words page
  // Implement Inertia GET to `/user/words?page=${page}`
}
</script>
