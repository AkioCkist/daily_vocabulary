<template>
  <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-[#1F2937] rounded-2xl shadow-2xl shadow-black/60 max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col border border-indigo-700/50">
      
      <div class="flex items-center justify-between p-6 border-b border-indigo-700/50">
        <h2 class="text-2xl font-extrabold text-white">Topic Management</h2>
        <button 
          @click="$emit('close')"
          class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:bg-indigo-700/30 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
          title="Close"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="flex-1 overflow-y-auto p-6">
        
        <div class="mb-8 p-6 bg-[#0B0C10] rounded-xl border border-indigo-700 shadow-xl">
          <h3 class="text-xl font-semibold text-indigo-300 mb-4">Create Custom Topic</h3>
          
          <form @submit.prevent="createTopic" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Topic Name</label>
                <input
                  v-model="newTopic.name"
                  type="text"
                  placeholder="e.g., Medical Terms"
                  required
                  class="w-full px-4 py-2 border border-indigo-700 rounded-lg bg-[#0B0C10] text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-inner"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
                <input
                  v-model="newTopic.description"
                  type="text"
                  placeholder="Brief description of the topic"
                  class="w-full px-4 py-2 border border-indigo-700 rounded-lg bg-[#0B0C10] text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-inner"
                >
              </div>
            </div>
            
            <div class="flex justify-end pt-2">
              <button
                type="submit"
                :disabled="!newTopic.name.trim() || isCreating"
                :class="[
                  'px-6 py-2 rounded-lg font-bold transition-all duration-200 shadow-lg',
                  !newTopic.name.trim() || isCreating
                    ? 'bg-gray-700 text-gray-400 cursor-not-allowed'
                    : 'bg-gradient-to-r from-indigo-600 to-purple-700 text-white hover:from-indigo-700 hover:to-purple-800'
                ]"
              >
                {{ isCreating ? 'Creating...' : 'Create Topic' }}
              </button>
            </div>
          </form>
          
          <div v-if="createError" class="mt-4 p-3 bg-red-900/40 border border-red-700 rounded-lg">
            <p class="text-sm text-red-400 font-medium">{{ createError }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <div>
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              System Topics
            </h3>
            
            <div class="space-y-3">
              <div 
                v-for="topic in topics?.system || []" 
                :key="`system-${topic.id}`"
                class="p-4 bg-[#1F2937] rounded-xl border border-gray-700 hover:border-blue-500 transition-all shadow-md group"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h4 class="font-semibold text-white group-hover:text-blue-400 transition-colors">
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
                  <button
                    @click="studyTopic(topic)"
                    class="flex-shrink-0 px-3 py-1 text-sm bg-blue-900/40 text-blue-400 rounded-lg font-medium hover:bg-blue-800/60 transition-colors opacity-0 group-hover:opacity-100 shadow-md"
                  >
                    Study
                  </button>
                </div>
              </div>
              
              <div v-if="!topics?.system?.length" class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p>No system topics available</p>
              </div>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center shadow">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              Your Custom Topics
            </h3>
            
            <div class="space-y-3">
              <div 
                v-for="topic in topics?.user || []" 
                :key="`user-${topic.id}`"
                class="p-4 bg-[#1F2937] rounded-xl border border-gray-700 hover:border-purple-500 transition-all shadow-md group"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h4 class="font-semibold text-white group-hover:text-purple-400 transition-colors">
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
                  
                  <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <button
                      @click="studyTopic(topic)"
                      class="px-3 py-1 text-sm bg-purple-900/40 text-purple-400 rounded-lg font-medium hover:bg-purple-800/60 transition-colors shadow-md"
                    >
                      Study
                    </button>
                    <button
                      @click="editTopic(topic)"
                      class="p-1 text-gray-400 hover:text-indigo-300 transition-colors"
                      title="Edit"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="deleteTopic(topic)"
                      class="p-1 text-red-500 hover:text-red-400 transition-colors"
                      title="Delete"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              
              <div v-if="!topics?.user?.length" class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <p>No custom topics yet</p>
                <p class="text-sm mt-1">Create your first topic above!</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="p-6 border-t border-indigo-700/50 bg-[#1F2937] shadow-inner">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-400">
            Total: <span class="font-semibold text-white">{{ (topics?.system?.length || 0) + (topics?.user?.length || 0) }}</span> topics
          </div>
          <button
            @click="$emit('close')"
            class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium shadow-md"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <div v-if="editingTopic" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-60">
      <div class="bg-[#1F2937] rounded-xl shadow-2xl shadow-black/60 max-w-md w-full mx-4 border border-indigo-700/50">
        <div class="p-6">
          <h3 class="text-xl font-semibold text-white mb-4">Edit Topic</h3>
          
          <form @submit.prevent="updateTopic" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Topic Name</label>
              <input
                v-model="editingTopic.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-indigo-700 rounded-lg bg-[#0B0C10] text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-inner"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
              <input
                v-model="editingTopic.description"
                type="text"
                class="w-full px-4 py-2 border border-indigo-700 rounded-lg bg-[#0B0C10] text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-inner"
              >
            </div>
            
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="editingTopic = null"
                class="flex-1 py-2 px-4 border border-gray-700 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors font-medium"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isUpdating"
                class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors font-bold disabled:opacity-50 shadow-md"
              >
                {{ isUpdating ? 'Updating...' : 'Update' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
// Note: router is a necessary dependency from the original code
import { router } from '@inertiajs/vue3';

// --- PROPS PRESERVATION ---
const props = defineProps({
  topics: {
    type: Object,
    default: () => ({})
  }
});

// --- EMITS PRESERVATION ---
const emit = defineEmits(['close', 'refresh']);

// --- STATE PRESERVATION ---
const newTopic = reactive({
  name: '',
  description: ''
});

const editingTopic = ref(null);
const isCreating = ref(false);
const isUpdating = ref(false);
const createError = ref('');

// --- FUNCTIONALITY PRESERVATION ---
function createTopic() {
  if (!newTopic.name.trim()) return;
  
  isCreating.value = true;
  createError.value = '';
  
  // Logic using router.post preserved
  router.post('/topics', {
    name: newTopic.name.trim(),
    description: newTopic.description.trim() || null
  }, {
    onSuccess: () => {
      // Reset form
      newTopic.name = '';
      newTopic.description = '';
      emit('refresh'); // Event emission preserved
    },
    onError: (errors) => {
      createError.value = errors.name?.[0] || 'Failed to create topic';
    },
    onFinish: () => {
      isCreating.value = false;
    }
  });
}

function editTopic(topic) {
  // Logic for setting editingTopic state preserved
  editingTopic.value = {
    id: topic.id,
    name: topic.name,
    description: topic.description || ''
  };
}

function updateTopic() {
  if (!editingTopic.value?.name.trim()) return;
  
  isUpdating.value = true;
  
  // Logic using router.put preserved
  router.put(`/topics/${editingTopic.value.id}`, {
    name: editingTopic.value.name.trim(),
    description: editingTopic.value.description.trim() || null
  }, {
    onSuccess: () => {
      editingTopic.value = null;
      emit('refresh'); // Event emission preserved
    },
    onFinish: () => {
      isUpdating.value = false;
    }
  });
}

function deleteTopic(topic) {
  if (!confirm(`Are you sure you want to delete "${topic.name}"?`)) return;
  
  // Logic using router.delete preserved
  router.delete(`/topics/${topic.id}`, {
    onSuccess: () => {
      emit('refresh'); // Event emission preserved
    }
  });
}

function studyTopic(topic) {
  // Logic using router.post for starting flashcards preserved
  router.post('/flashcards/start', {
    mode: 'topic',
    topic_ids: [topic.id],
    word_count: 10
  });
}
</script>