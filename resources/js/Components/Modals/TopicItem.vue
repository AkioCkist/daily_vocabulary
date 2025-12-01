<template>
  <div 
    class="p-4 bg-[#1F2937] rounded-xl border transition-all shadow-md group"
    :class="borderClass"
  >
    <div class="flex items-start justify-between">
      <div class="flex-1">
        <h4 class="font-semibold text-white transition-colors" :class="titleHoverClass">
          {{ topic.name }}
        </h4>
        <p v-if="topic.description" class="text-sm text-gray-400 mt-1">
          {{ topic.description }}
        </p>
        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
          <span class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            {{ topic.words_count || 0 }} words
          </span>
        </div>
      </div>
      
      <div v-if="isCustom" class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
        <button
          @click="$emit('study', topic)"
          class="px-3 py-1 text-sm bg-purple-900/40 text-purple-400 rounded-lg font-medium hover:bg-purple-800/60 transition-colors shadow-md"
        >
          Study
        </button>
        <button
          @click="$emit('edit', topic)"
          class="p-1 text-gray-400 hover:text-indigo-300 transition-colors"
          title="Edit"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
        </button>
        <button
          @click="$emit('delete', topic)"
          class="p-1 text-red-500 hover:text-red-400 transition-colors"
          title="Delete"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
        </button>
      </div>
      
      <div v-else class="flex-shrink-0">
        <button
          @click="$emit('study', topic)"
          class="flex-shrink-0 px-3 py-1 text-sm bg-blue-900/40 text-blue-400 rounded-lg font-medium hover:bg-blue-800/60 transition-colors opacity-0 group-hover:opacity-100 shadow-md"
        >
          Study
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  topic: {
    type: Object,
    required: true
  },
  isCustom: {
    type: Boolean,
    default: false
  },
  borderClass: {
    type: String,
    required: true
  },
  titleHoverClass: {
    type: String,
    required: true
  }
});

defineEmits(['study', 'edit', 'delete']);
</script>
