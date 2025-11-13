<template>
  <div class="space-y-4">
    <div v-if="words.length === 0" class="text-center py-12">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
        <span class="text-4xl">📚</span>
      </div>
      <p class="text-gray-500 text-lg">No words yet!</p>
      <p class="text-gray-400 text-sm">Start building your vocabulary</p>
    </div>

    <div v-for="word in words" :key="word.id" 
         class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100 hover:border-purple-300 transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-2">
            <h3 class="text-2xl font-bold text-gray-900 group-hover:text-purple-600 transition-colors">
              {{ word.word }}
            </h3>
            <span class="inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold">
              {{ word.part_of_speech }}
            </span>
          </div>
          <p class="text-gray-700 leading-relaxed mb-3">{{ word.definition }}</p>
          <div v-if="word.example" class="bg-blue-50 p-3 rounded-xl border-l-4 border-blue-400">
            <p class="text-sm text-gray-600 italic">"{{ word.example }}"</p>
          </div>
        </div>
        <div class="ml-4">
          <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  words: {
    type: Array,
    required: true,
    default: () => []
  }
});
</script>
