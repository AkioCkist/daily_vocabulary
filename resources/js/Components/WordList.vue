<template>
  <div class="space-y-4">
    <div v-if="props.words.length === 0" class="text-center py-12">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4 dark:bg-gray-800">
        <span class="text-4xl">📚</span>
      </div>
      <p class="text-gray-500 text-lg dark:text-gray-400">No words yet!</p>
      <p class="text-gray-400 text-sm dark:text-gray-500">Start building your vocabulary</p>
    </div>

    <div 
      v-for="word in props.words" 
      :key="word.id" 
      class="bg-white/95 rounded-2xl shadow-xl p-6 border border-gray-200/60 ring-1 ring-gray-900/10 hover:border-indigo-400/70 hover:shadow-2xl transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group dark:bg-gray-800/95 dark:border-gray-600/60 dark:ring-white/15"
    >
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-2">
            <h3 class="text-2xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors dark:text-white dark:group-hover:text-indigo-400">
              {{ word.word }}
            </h3>
            <span class="inline-block bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold dark:bg-indigo-900/30 dark:text-indigo-300">
              {{ word.part_of_speech }}
            </span>
          </div>
          <p class="text-gray-700 leading-relaxed mb-3 dark:text-gray-300">{{ word.definition }}</p>
          <div v-if="word.example" class="bg-indigo-50 p-3 rounded-xl border-l-4 border-indigo-400 dark:bg-indigo-950/50 dark:border-indigo-500">
            <p class="text-sm text-gray-600 italic dark:text-gray-400">"{{ word.example }}"</p>
          </div>
        </div>
        <div v-if="props.showRemove" class="ml-4">
          <button 
            @click.stop="$emit('removeWord', word.id)"
            class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 hover:from-green-500 hover:to-emerald-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-300 dark:focus:ring-green-600"
            title="Mark as learned / Remove from vocabulary"
          >
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  words: {
    type: Array,
    required: true,
    default: () => []
  },
  showRemove: {
    type: Boolean,
    default: true
  }
});

defineEmits(['removeWord']);
</script>
