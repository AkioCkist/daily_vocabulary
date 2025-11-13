<template>
  <div v-if="meta && meta.last_page > 1" class="flex items-center justify-center gap-2 mt-8">
    <!-- Previous Button -->
    <button
      @click="changePage(meta.current_page - 1)"
      :disabled="meta.current_page === 1"
      class="px-4 py-2 bg-white border-2 border-gray-200 rounded-xl font-semibold text-gray-700 hover:border-purple-400 hover:bg-purple-50 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:bg-white transition-all"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
      </svg>
    </button>

    <!-- Page Numbers -->
    <div class="flex gap-2">
      <button
        v-for="page in visiblePages"
        :key="page"
        @click="changePage(page)"
        :class="[
          'min-w-[2.5rem] h-10 rounded-xl font-bold transition-all',
          page === meta.current_page
            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg scale-110'
            : 'bg-white border-2 border-gray-200 text-gray-700 hover:border-purple-400 hover:bg-purple-50'
        ]"
      >
        {{ page }}
      </button>
    </div>

    <!-- Next Button -->
    <button
      @click="changePage(meta.current_page + 1)"
      :disabled="meta.current_page === meta.last_page"
      class="px-4 py-2 bg-white border-2 border-gray-200 rounded-xl font-semibold text-gray-700 hover:border-purple-400 hover:bg-purple-50 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:bg-white transition-all"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  meta: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['change-page']);

const visiblePages = computed(() => {
  const current = props.meta.current_page;
  const total = props.meta.last_page;
  const delta = 2;
  const range = [];
  
  for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
    range.push(i);
  }
  
  if (current - delta > 2) {
    range.unshift('...');
  }
  if (current + delta < total - 1) {
    range.push('...');
  }
  
  range.unshift(1);
  if (total > 1) {
    range.push(total);
  }
  
  return range.filter((v, i, a) => a.indexOf(v) === i);
});

function changePage(page) {
  if (page !== '...' && page >= 1 && page <= props.meta.last_page) {
    emit('change-page', page);
  }
}
</script>
