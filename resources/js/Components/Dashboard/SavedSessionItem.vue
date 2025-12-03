<template>
  <div 
    class="flex items-center justify-between p-3 bg-gray-800/60 rounded-xl transition-all border-l-4 border-indigo-600 shadow-md hover:bg-gray-700/70"
  >
    <div class="min-w-0 flex-1 cursor-pointer" @click="$emit('review', session)">
      <p class="text-sm font-medium text-white truncate hover:text-indigo-400 transition-colors">
        {{ session.name }}
      </p>
      
      <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
        <span class="flex items-center gap-1">
          <BookOpenIcon class="w-3 h-3 text-gray-600" />
          {{ session.word_count || 0 }} words
        </span>
        
        <span class="flex items-center gap-1">
          <CalendarIcon class="w-3 h-3 text-gray-600" />
          {{ formatDate(session.created_at) }}
        </span>
      </div>
    </div>
    
    <button 
      @click.stop="$emit('review', session)"
      class="flex-shrink-0 ml-3 inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-md hover:shadow-lg"
      title="Start Review Session"
    >
      <PlayIcon class="w-3 h-3 mr-1" />
      Review
    </button>
  </div>
</template>

<script setup>
import { PlayIcon, BookOpenIcon, CalendarIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  session: {
    type: Object,
    required: true
  }
});

defineEmits(['review']);

// Helper function to format the date string
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now - date);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 1) return 'Today';
  if (diffDays === 2) return 'Yesterday';
  if (diffDays <= 7) return `${diffDays - 1} days ago`;
  
  return date.toLocaleDateString();
};
</script>