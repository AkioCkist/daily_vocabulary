<template>
  <transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div 
          class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity"
          @click="handleClose"
        ></div>

        <div class="inline-block align-bottom bg-[#0B0C10]/90 backdrop-blur-lg rounded-2xl text-left overflow-hidden shadow-2xl shadow-indigo-900/40 ring-1 ring-indigo-900/50 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full text-white">
          
          <div class="px-6 py-5 border-b border-gray-800">
            <div class="sm:flex sm:items-start">
              
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-700/30 sm:mx-0 sm:h-10 sm:w-10 border border-indigo-700">
                <BookmarkIcon class="h-6 w-6 text-indigo-400" />
              </div>
              
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-xl font-bold leading-6 text-white" id="modal-title">
                  Save Study Session
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                  Save your recent session to track your progress and review these exact words later.
                </p>
              </div>
            </div>
          </div>
          
          <div class="px-6 py-6 space-y-6">
            
            <div v-if="errors.length > 0" class="bg-red-900/30 border border-red-700 text-red-300 p-4 rounded-lg text-sm">
                <h4 class="font-bold mb-1">Could not save session:</h4>
                <ul class="list-disc list-inside space-y-0.5">
                    <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                </ul>
            </div>

            <div>
              <label for="session-name" class="block text-sm font-medium text-gray-300 mb-2">Session Name</label>
              <input
                id="session-name"
                type="text"
                v-model="form.name"
                :placeholder="sessionData?.suggested_name || 'e.g., C1 Vocab Review Week 3'"
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
              >
            </div>



            <div class="mt-4 p-4 rounded-lg bg-gradient-to-r from-indigo-900/30 to-purple-900/30 border border-indigo-700/50">
                <div class="flex items-center justify-center gap-2">
                    <span class="text-sm text-gray-400">This session contains</span>
                    <span class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">
                        {{ sessionData?.total_words }}
                    </span>
                    <span class="text-sm text-gray-400">{{ sessionData?.total_words === 1 ? 'word' : 'words' }}</span>
                </div>
            </div>

          </div>

          <div class="px-6 py-4 bg-gray-900/50 flex justify-end gap-3 border-t border-gray-800">
            <button
              type="button"
              @click="handleClose"
              :disabled="loading"
              class="inline-flex justify-center py-2.5 px-4 border border-gray-700 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 transition-colors"
            >
              Cancel
            </button>
            
            <button
              type="button"
              :disabled="loading"
              @click="saveSession"
              class="inline-flex justify-center py-2.5 px-4 border border-transparent text-sm font-bold rounded-lg shadow-lg transition-all duration-200 hover:scale-[1.02] active:scale-95
                     bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-indigo-600/30 disabled:opacity-50 disabled:shadow-none"
            >
              <span v-if="loading" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Saving...
              </span>
              <span v-else>
                Save Session
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from 'vue';
import { BookmarkIcon } from '@heroicons/vue/24/outline'; // Assuming this icon is available
import { router } from '@inertiajs/vue3'; // Assuming Inertia.js is used

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  topics: {
    type: Array,
    required: true // List of available topics for the user
  },
  sessionData: {
    type: Object,
    default: () => null,
    // Expected structure: { total_words: Number, flashcard_ids: Array, suggested_name: String, topic: String }
  }
});

const emit = defineEmits(['close', 'saved']);

const loading = ref(false);
const errors = ref([]);

const form = ref({
    name: props.sessionData?.suggested_name || `Study Session - ${new Date().toLocaleDateString()}`,
    topic: null,
});

// Watch for prop changes to reset form when new data is passed
watch(() => props.sessionData, (newSessionData) => {
    if (newSessionData) {
        form.value.name = newSessionData.suggested_name || `Study Session - ${new Date().toLocaleDateString()}`;
        form.value.topic = newSessionData.topic || null;
    }
}, { immediate: true });


const handleClose = () => {
    // Only allow closing if not loading
    if (!loading.value) {
        emit('close');
    }
};

const saveSession = async () => {
  if (loading.value) return;
  if (!props.sessionData?.flashcard_ids || props.sessionData.flashcard_ids.length === 0) {
    errors.value = ['Cannot save an empty session.'];
    return;
  }

  loading.value = true;
  errors.value = [];

  try {
    // Prepare data for saving
    const saveData = {
      name: form.value.name || props.sessionData?.suggested_name || `Study Session - ${new Date().toLocaleDateString()}`,
      topic: form.value.topic || null,
      flashcard_ids: props.sessionData?.flashcard_ids || []
    };

    // Make API call to save session
    await router.post(route('saved-sessions.store'), saveData, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page) => {
        // Session saved successfully
        emit('saved', page.props?.session);
        emit('close');
      },
      onError: (errorData) => {
        // Handle validation errors
        let newErrors = [];
        if (errorData.name) newErrors.push(...(Array.isArray(errorData.name) ? errorData.name : [errorData.name]));
        if (errorData.topic) newErrors.push(...(Array.isArray(errorData.topic) ? errorData.topic : [errorData.topic]));
        if (errorData.flashcard_ids) newErrors.push(...(Array.isArray(errorData.flashcard_ids) ? errorData.flashcard_ids : [errorData.flashcard_ids]));
        
        // Generic error fallback
        if (newErrors.length === 0) {
          newErrors.push('An error occurred while saving the session.');
        }
        errors.value = newErrors;
      }
    });
  } catch (error) {
    console.error('SaveSessionModal: Exception during save:', error);
    errors.value = ['Network error. Please try again.'];
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Modal transition styling, matches FlashcardModal.vue */
.scale-fade-enter-active {
  animation: scale-fade-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scale-fade-in {
  0% {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>