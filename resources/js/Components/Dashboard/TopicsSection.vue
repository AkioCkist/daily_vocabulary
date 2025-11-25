<template>
  <div class="bg-[#1F2937] rounded-xl border border-indigo-800/50 overflow-hidden shadow-2xl shadow-black/50 transition-all duration-300">
    <button 
      @click="isExpanded = !isExpanded"
      class="w-full px-6 py-4 flex items-center justify-between cursor-pointer transition-all hover:bg-indigo-900/30"
    >
      <h2 class="text-xl font-bold text-white">
        Topics
      </h2>
      <svg 
        :class="['w-6 h-6 text-indigo-400 transition-transform', isExpanded ? 'rotate-180' : '']"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      leave-active-class="transition-all duration-200 ease-in"
      enter-from-class="opacity-0 max-h-0"
      enter-to-class="opacity-100 max-h-[500px]"
      leave-from-class="opacity-100 max-h-[500px]"
      leave-to-class="opacity-0 max-h-0"
    >
      <div 
        v-show="isExpanded"
        class="border-t border-indigo-700/50 px-6 py-4 space-y-2 bg-[#0B0C10] overflow-hidden"
      >
      <div 
        v-for="topic in displayTopics" 
        :key="topic.id"
        class="flex items-center justify-between p-3 rounded-lg bg-[#1F2937] transition-all cursor-pointer border border-gray-700 hover:border-indigo-500 hover:shadow-lg shadow-black/20"
        @click="$emit('select', topic)"
      >
        <div>
          <div class="font-medium text-white text-base">
            {{ topic.name }}
          </div>
          <div class="text-xs text-gray-400">
            {{ topic.words_count }} words
          </div>
        </div>
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>

      <button 
        @click="$emit('manage')"
        class="w-full py-2.5 px-4 border-2 border-dashed border-indigo-700 rounded-lg text-indigo-400 hover:border-indigo-500 hover:bg-indigo-900/20 transition-colors text-sm font-semibold mt-4"
      >
        + Create Custom Topic
      </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// --- PROPS PRESERVATION ---
const props = defineProps({
  topics: {
    type: Object,
    required: true
  },
  limit: {
    type: Number,
    default: 5
  }
});

// --- EMITS PRESERVATION ---
defineEmits(['select', 'manage']);

// --- STATE PRESERVATION ---
const isExpanded = ref(false);

// --- COMPUTED PROPERTY PRESERVATION ---
const displayTopics = computed(() => {
  // Logic preserved: slices the 'system' topics based on the 'limit' prop
  // Note: The original implementation only showed system topics.
  return props.topics?.system?.slice(0, props.limit) || [];
});
</script>