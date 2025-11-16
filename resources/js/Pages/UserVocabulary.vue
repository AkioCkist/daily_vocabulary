<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-50 to-indigo-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950">
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full mb-4 shadow-lg">
          <span class="text-3xl">🎯</span>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2 dark:from-indigo-400 dark:to-purple-400">My Vocabulary</h1>
        <p class="text-gray-600 dark:text-gray-400">Your personal word collection</p>
      </div>

      <!-- Stats Card -->
      <div class="bg-white/95 rounded-2xl shadow-xl p-6 mb-8 border border-gray-200/60 ring-1 ring-gray-900/10 dark:bg-gray-800/95 dark:border-gray-600/60 dark:ring-white/15">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-2xl">✨</span>
            </div>
            <div>
              <p class="text-2xl font-black text-gray-900 dark:text-white">{{ userWords.length }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Words Learned</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Keep it up!</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">You're doing great 🚀</p>
          </div>
        </div>
      </div>

      <!-- Word List -->
      <WordList :words="userWords" :show-remove="true" @remove-word="removeWord" />

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
import { useForm } from '@inertiajs/vue3';
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

function removeWord(wordId) {
  const form = useForm({});
  form.delete(`/user/words/${wordId}`, {
    onSuccess: () => {
      // Remove word from local array
      userWords.value = userWords.value.filter(word => word.id !== wordId);
    },
    onError: (errors) => {
      console.error('Failed to remove word:', errors);
    }
  });
}
</script>
