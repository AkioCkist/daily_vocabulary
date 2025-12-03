<template>
  <div class="space-y-4">
    
    <div v-if="userTopicsToDisplay.length > 0" class="space-y-3">
      <div class="flex items-center justify-between">
        <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-wider">My Personal Topics</h4>
        <span v-if="hasMoreUserTopics" class="text-xs font-semibold text-indigo-400 bg-indigo-500/10 px-2 py-1 rounded">
          +{{ props.topics.user.length - 2 }} more
        </span>
      </div>
      
      <div 
        v-for="topic in userTopicsToDisplay" 
        :key="topic.id" 
        @click="$emit('select', topic)"
        class="flex items-center justify-between p-3 bg-gray-900/50 rounded-xl cursor-pointer hover:bg-gray-700/70 transition-colors border-l-4 border-indigo-600 shadow-md"
      >
        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium text-white truncate">{{ topic.name }}</p>
          <p class="text-xs text-gray-500">{{ topic.words_count }} words</p>
        </div>
        <button 
          class="flex-shrink-0 ml-3 text-xs font-semibold text-white bg-indigo-500/20 px-3 py-1 rounded-full hover:bg-indigo-500/30 transition-colors"
          title="Start flashcards with this topic"
        >
          Study
        </button>
      </div>
      
      <div v-if="hasMoreUserTopics" class="text-xs text-indigo-400 bg-indigo-500/5 border border-indigo-500/30 rounded-lg p-3 text-center">
        <p>Showing 2 of {{ props.topics.user.length }} personal topics</p>
        <p class="mt-1 text-indigo-300">Go to <strong>Create / Manage</strong> to view all your topics</p>
      </div>
    </div>
    
    <div 
      v-if="systemTopicsToDisplay.length > 0" 
      class="space-y-3" 
      :class="{'pt-4 border-t border-gray-800/50': userTopicsToDisplay.length > 0}"
    >
      <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider mb-2">General Topics</h4>
      
      <div 
        v-for="topic in systemTopicsToDisplay" 
        :key="topic.id" 
        @click="$emit('select', topic)"
        class="flex items-center justify-between p-3 bg-gray-900/50 rounded-xl cursor-pointer hover:bg-gray-700/70 transition-colors border-l-4 border-purple-600 shadow-md"
      >
        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium text-white truncate">{{ topic.name }}</p>
          <p class="text-xs text-gray-500">{{ topic.words_count }} words</p>
        </div>
        <button 
          class="flex-shrink-0 ml-3 text-xs font-semibold text-white bg-purple-500/20 px-3 py-1 rounded-full hover:bg-purple-500/30 transition-colors"
          title="Start flashcards with this topic"
        >
          Study
        </button>
      </div>
    </div>
    
    <div v-if="userTopicsToDisplay.length === 0 && systemTopicsToDisplay.length === 0" class="text-center py-4">
        <p class="text-gray-500 text-sm">No topics available. Click **Create / Manage** to start your first one!</p>
    </div>
    
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  topics: {
    type: Object,
    default: () => ({
      system: [],
      user: []
    })
  },
  limit: {
    type: Number,
    default: Infinity
  }
});

defineEmits(['select']);

const userTopicsToDisplay = computed(() => {
    return props.topics.user.slice(0, 2);
});

const hasMoreUserTopics = computed(() => {
    return props.topics.user.length > 2;
});

const systemTopicsToDisplay = computed(() => {
    // Calculate remaining limit after showing user topics
    const remainingLimit = props.limit - userTopicsToDisplay.value.length;
    
    if (remainingLimit <= 0) {
        return [];
    }
    
    // Show system topics up to the remaining limit.
    return props.topics.system.slice(0, remainingLimit);
});
</script>