<template>
  <div class="p-4 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">My Vocabulary</h1>

    <WordList :words="userWords" />

    <Pagination
      :meta="meta"
      @change-page="changePage"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import WordList from '@/Components/WordList.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  userWords: Array,
  meta: Object,
});

const userWords = ref(props.userWords);
const meta = ref(props.meta || {});

function changePage(page) {
  // Reload user words page
  // Implement Inertia GET to `/user/words?page=${page}`
}
</script>
