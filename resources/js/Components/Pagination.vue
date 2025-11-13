<template>
  <div class="flex justify-center mt-4 space-x-2">
    <button
      v-if="meta.prev_page_url"
      @click="$emit('change-page', meta.current_page - 1)"
      class="px-3 py-1 border rounded hover:bg-gray-100"
    >
      Prev
    </button>

    <span class="px-3 py-1 border rounded bg-gray-200">{{ meta.current_page }}</span>

    <button
      v-if="meta.next_page_url"
      @click="$emit('change-page', meta.current_page + 1)"
      class="px-3 py-1 border rounded hover:bg-gray-100"
    >
      Next
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  meta: Object,
});
</script>
