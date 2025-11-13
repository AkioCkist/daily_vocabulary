<template>
  <div class="p-4 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Words</h1>

    <input
      v-model="query"
      @input="search"
      type="text"
      placeholder="Search words..."
      class="w-full border p-2 rounded mb-4"
    />

    <WordList :words="words" />

    <Pagination
      :meta="meta"
      @change-page="changePage"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import WordList from '@/Components/WordList.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  words: Array,
  meta: Object,
  query: String,
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
