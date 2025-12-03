<template>
  <div class="space-y-3" ref="dropdownRef">
    <div class="relative">
      <!-- Search Input with Toggle Button -->
      <div class="relative flex items-center gap-2">
        <input
          v-model="searchQuery"
          @focus="isOpen = true"
          type="text"
          placeholder="Search and select topics..."
          class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all"
        />
        <button
          @click="isOpen = !isOpen"
          class="px-3 py-3 text-gray-400 hover:text-gray-300 transition-colors flex-shrink-0"
          title="Toggle dropdown"
        >
          <svg 
            class="w-5 h-5 transition-transform"
            :class="{ 'rotate-180': isOpen }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
      </div>

      <!-- Dropdown Menu -->
      <transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div 
          v-if="isOpen"
          class="absolute z-50 top-full left-0 right-0 mt-2 bg-gray-800 border border-gray-700 rounded-lg shadow-xl max-h-64 overflow-y-auto"
        >
          <!-- System Topics Section -->
          <div v-if="filteredSystemTopics.length > 0" class="px-0 py-2">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide sticky top-0 bg-gray-800/95 backdrop-blur-sm">
              System Topics
            </div>
            <button
              v-for="topic in filteredSystemTopics"
              :key="`system-${topic.id}`"
              @click="toggleTopic(topic.id)"
              class="w-full px-4 py-3 text-left hover:bg-gray-700 transition-colors flex items-center gap-3 group"
            >
              <div class="flex items-center gap-3 flex-1">
                <svg 
                  v-if="selectedTopics.includes(topic.id)"
                  class="w-5 h-5 text-indigo-400 flex-shrink-0"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <svg 
                  v-else
                  class="w-5 h-5 text-gray-500 flex-shrink-0 group-hover:text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                </svg>
                <div class="flex-1 min-w-0">
                  <div class="text-white font-medium truncate">{{ topic.name }}</div>
                  <div class="text-xs text-gray-400">{{ topic.words_count }} words</div>
                </div>
              </div>
            </button>
          </div>

          <!-- User Topics Section -->
          <div v-if="filteredUserTopics.length > 0" class="px-0 py-2 border-t border-gray-700">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide sticky top-0 bg-gray-800/95 backdrop-blur-sm">
              Your Topics
            </div>
            <button
              v-for="topic in filteredUserTopics"
              :key="`user-${topic.id}`"
              @click="toggleTopic(topic.id)"
              class="w-full px-4 py-3 text-left hover:bg-gray-700 transition-colors flex items-center gap-3 group"
            >
              <div class="flex items-center gap-3 flex-1">
                <svg 
                  v-if="selectedTopics.includes(topic.id)"
                  class="w-5 h-5 text-purple-400 flex-shrink-0"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <svg 
                  v-else
                  class="w-5 h-5 text-gray-500 flex-shrink-0 group-hover:text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                </svg>
                <div class="flex-1 min-w-0">
                  <div class="text-white font-medium truncate">{{ topic.name }}</div>
                  <div class="text-xs text-gray-400">{{ topic.words_count }} words</div>
                </div>
              </div>
            </button>
          </div>

          <!-- Empty State -->
          <div v-if="filteredSystemTopics.length === 0 && filteredUserTopics.length === 0" class="px-4 py-8 text-center text-gray-400">
            <p class="text-sm">No topics found</p>
          </div>
        </div>
      </transition>
    </div>

    <!-- Selected Tags -->
    <div v-if="selectedTopics.length > 0" class="flex flex-wrap gap-2">
      <div
        v-for="topicId in selectedTopics"
        :key="`tag-${topicId}`"
        class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 border border-indigo-500/50 text-indigo-300 rounded-full text-sm"
      >
        <span>{{ getTopicName(topicId) }}</span>
        <button
          @click="toggleTopic(topicId)"
          class="text-indigo-400 hover:text-indigo-200 transition-colors"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Status Text -->
    <p class="text-xs text-gray-400">
      {{ selectedTopics.length > 0 ? `${selectedTopics.length} topic${selectedTopics.length === 1 ? '' : 's'} selected` : 'All topics will be used' }}
    </p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  topics: {
    type: Object,
    required: true
  },
  selectedTopics: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update:selectedTopics']);

const isOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref(null);

// Computed filtered topics
const filteredSystemTopics = computed(() => {
  if (!props.topics.system) return [];
  if (!searchQuery.value) return props.topics.system;
  
  return props.topics.system.filter(topic =>
    topic.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const filteredUserTopics = computed(() => {
  if (!props.topics.user) return [];
  if (!searchQuery.value) return props.topics.user;
  
  return props.topics.user.filter(topic =>
    topic.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Get topic name by ID
function getTopicName(topicId) {
  const allTopics = [...(props.topics.system || []), ...(props.topics.user || [])];
  return allTopics.find(t => t.id === topicId)?.name || 'Unknown Topic';
}

// Toggle topic selection
function toggleTopic(topicId) {
  const currentTopics = [...props.selectedTopics];
  const index = currentTopics.indexOf(topicId);
  
  if (index > -1) {
    currentTopics.splice(index, 1);
  } else {
    currentTopics.push(topicId);
  }
  
  emit('update:selectedTopics', currentTopics);
}

// Handle click outside to close dropdown
function handleClickOutside(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false;
    searchQuery.value = '';
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
