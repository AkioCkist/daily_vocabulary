<template>
  <div class="relative">
    <button
      @click="showDropdown = !showDropdown"
      class="w-9 h-9 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 transition-colors"
      title="Add to personal topic"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
      </svg>
    </button>
    
    <!-- Topic Dropdown -->
    <transition name="dropdown">
      <div v-if="showDropdown" class="absolute right-0 top-12 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-1">
            <h3 class="font-semibold text-gray-900 dark:text-white">Add to Topic</h3>
            <button
              @click.stop="showCreateForm = !showCreateForm"
              class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium flex items-center gap-1 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              New
            </button>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400">Save this word to your collection</p>
        </div>

        <!-- Quick Create Form -->
        <transition name="slide-down">
          <div v-if="showCreateForm" class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border-b border-indigo-200 dark:border-indigo-800">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Create New Topic</h4>
            <div class="space-y-2">
              <input
                v-model="newTopicName"
                type="text"
                placeholder="Topic name"
                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                @keyup.enter="createTopic"
                @keyup.escape="cancelCreate"
              >
              <textarea
                v-model="newTopicDescription"
                placeholder="Description (optional)"
                rows="2"
                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
                @keyup.escape="cancelCreate"
              ></textarea>
              <div class="flex gap-2">
                <button
                  @click.stop="createTopic"
                  :disabled="!newTopicName.trim() || creating"
                  class="flex-1 px-3 py-2 bg-indigo-500 text-white text-sm rounded-lg font-medium hover:bg-indigo-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2"
                >
                  <svg v-if="creating" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  <span>{{ creating ? 'Creating...' : 'Create' }}</span>
                </button>
                <button
                  @click.stop="cancelCreate"
                  class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </transition>
        
        <div class="max-h-64 overflow-y-auto">
          <div
            v-for="topic in userTopics"
            :key="topic.id"
            class="flex items-center justify-between group border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            <button
              @click.stop="$emit('add-to-topic', topic.id)"
              :disabled="addingToTopic === topic.id || topicsWithWord.has(topic.id)"
              class="flex-1 px-4 py-3 text-left disabled:opacity-50 disabled:cursor-not-allowed"
              :title="topicsWithWord.has(topic.id) ? 'Word already in this topic' : 'Add word to this topic'"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-900 dark:text-white">{{ topic.name }}</span>
                    <span v-if="topicsWithWord.has(topic.id)" class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium rounded-full">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      Added
                    </span>
                  </div>
                  <div v-if="topic.description" class="text-sm text-gray-500 dark:text-gray-400 truncate">
                    {{ topic.description }}
                  </div>
                </div>
                <svg v-if="addingToTopic === topic.id" class="w-5 h-5 text-indigo-600 animate-spin flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg v-else class="w-5 h-5 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
              </div>
            </button>
            <button
              @click.stop="confirmDeleteTopic(topic.id, topic.name)"
              :disabled="deletingTopic === topic.id"
              class="px-3 py-3 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 opacity-0 group-hover:opacity-100 transition-opacity disabled:opacity-50"
              title="Delete topic"
            >
              <svg v-if="deletingTopic === topic.id" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
          
          <div v-if="!userTopics || userTopics.length === 0" class="p-6 text-center border-t border-gray-200 dark:border-gray-700">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">No topics yet</p>
            <button
              @click.stop="showCreateForm = true"
              class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium"
            >
              Create your first topic
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  userTopics: {
    type: Array,
    default: () => []
  },
  addingToTopic: {
    type: Number,
    default: null
  },
  wordId: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['add-to-topic', 'topic-created', 'topic-deleted']);

const showDropdown = ref(false);
const showCreateForm = ref(false);
const newTopicName = ref('');
const newTopicDescription = ref('');
const creating = ref(false);
const deletingTopic = ref(null);
const topicsWithWord = ref(new Set());
const loadingTopics = ref(false);

const handleClickOutside = (e) => {
  if (!e.target.closest('.relative')) {
    showDropdown.value = false;
    showCreateForm.value = false;
  }
};

const handleWordAddedToTopic = (event) => {
  const { wordId, topicId } = event.detail;
  if (wordId === props.wordId) {
    topicsWithWord.value.add(topicId);
  }
};

async function toggleDropdown() {
  showDropdown.value = !showDropdown.value;
  
  if (showDropdown.value && props.wordId) {
    await fetchWordTopics();
  }
}

async function fetchWordTopics() {
  if (loadingTopics.value) return;
  
  loadingTopics.value = true;
  
  try {
    const response = await fetch(`/flashcards/words/${props.wordId}/topics`, {
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    });
    
    if (response.ok) {
      const result = await response.json();
      
      // Create a Set of topic IDs that already have this word
      const topicIds = new Set();
      result.topics.forEach(topic => {
        if (topic.is_added) {
          topicIds.add(topic.id);
        }
      });
      
      topicsWithWord.value = topicIds;
    }
  } catch (error) {
    console.error('Error fetching word topics:', error);
  } finally {
    loadingTopics.value = false;
  }
}

async function createTopic() {
  if (!newTopicName.value.trim() || creating.value) return;

  creating.value = true;

  try {
    const response = await fetch('/flashcards/topics/quick-create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        name: newTopicName.value,
        description: newTopicDescription.value
      })
    });

    const result = await response.json();

    if (response.ok && result.success) {
      // Emit event to parent to update the topics list
      emit('topic-created', result.topic);
      
      // Reset form
      newTopicName.value = '';
      newTopicDescription.value = '';
      showCreateForm.value = false;
      
      // Show success feedback (you could use a toast notification here)
      console.log('Topic created successfully:', result.topic);
    } else {
      alert(result.message || 'Failed to create topic');
    }
  } catch (error) {
    console.error('Error creating topic:', error);
    alert('Network error. Please try again.');
  } finally {
    creating.value = false;
  }
}

function cancelCreate() {
  newTopicName.value = '';
  newTopicDescription.value = '';
  showCreateForm.value = false;
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  window.addEventListener('word-added-to-topic', handleWordAddedToTopic);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  window.removeEventListener('word-added-to-topic', handleWordAddedToTopic);
});
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.2s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-down-enter-from {
  opacity: 0;
  max-height: 0;
  transform: translateY(-10px);
}
.slide-down-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateY(-10px);
}
</style>
