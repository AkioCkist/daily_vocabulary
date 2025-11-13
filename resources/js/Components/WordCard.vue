<template>
  <div class="bg-white rounded-3xl shadow-xl p-8 border-4 border-purple-100 hover:border-purple-300 transition-all duration-300 transform hover:-translate-y-1 animate-scale-in">
    <!-- Word Header -->
    <div class="mb-6">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
          <span class="text-2xl">💬</span>
        </div>
        <div class="flex-1">
          <h2 class="text-4xl font-black text-gray-900 tracking-tight">{{ word.word }}</h2>
        </div>
      </div>
      <span class="inline-block bg-purple-100 text-purple-700 px-4 py-1.5 rounded-full text-sm font-semibold">
        {{ word.part_of_speech }}
      </span>
    </div>

    <!-- Definition -->
    <div class="mb-6">
      <div class="flex items-start gap-2 mb-2">
        <span class="text-blue-500 font-bold text-lg">📖</span>
        <p class="text-xl text-gray-800 leading-relaxed font-medium">{{ word.definition }}</p>
      </div>
    </div>

    <!-- Example -->
    <div v-if="word.example" class="bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-2xl border-l-4 border-blue-500 shadow-sm">
      <div class="flex items-start gap-2">
        <span class="text-blue-600 font-bold">💡</span>
        <div class="flex-1">
          <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-1">Example</p>
          <p class="text-gray-700 italic leading-relaxed">"{{ word.example }}"</p>
        </div>
      </div>
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
