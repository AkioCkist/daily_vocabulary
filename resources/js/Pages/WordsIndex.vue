<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50">
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full mb-4 shadow-lg">
          <span class="text-3xl">🔍</span>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent mb-2">Explore Words</h1>
        <p class="text-gray-600">Discover and learn new vocabulary</p>
      </div>

      <!-- Search Bar -->
      <div class="mb-8">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <input
            v-model="query"
            @input="search"
            type="text"
            placeholder="Search for words..."
            class="w-full pl-14 pr-6 py-5 bg-white border-2 border-gray-200 rounded-2xl text-lg font-medium placeholder-gray-400 focus:ring-4 focus:ring-green-200 focus:border-green-500 focus:outline-none shadow-lg transition-all"
          />
        </div>
      </div>

      <!-- Word List -->
      <WordList :words="words" />

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
  words: Array,
  meta: Object,
  query: String,
  user: {
    type: Object,
    default: null
  }
});

const words = ref(props.words);
const meta = ref(props.meta || {});
const query = ref(props.query || '');

const form = useForm({ q: query.value, page: 1 });

function search() {
  form.get('/words', {
    preserveState: true,
    onSuccess: (page) => {
      words.value = page.props.words;
      meta.value = page.props.meta || {};
    },
  });
}

function changePage(page) {
  form.page = page;
  form.get('/words', { preserveState: true });
}
</script>
