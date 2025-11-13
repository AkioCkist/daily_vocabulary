<template>
  <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ word.word }}</h2>
    <p class="text-sm text-gray-500 italic mb-4">{{ word.part_of_speech }}</p>
    <p class="text-lg text-gray-700 mb-3">{{ word.definition }}</p>
    <div v-if="word.example" class="bg-gray-50 p-3 rounded border-l-4 border-blue-500">
      <p class="text-sm text-gray-600"><span class="font-semibold">Example:</span> {{ word.example }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  word: {
    type: Object,
    required: true
  }
});
</script>
