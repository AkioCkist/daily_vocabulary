<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="handleClose"
      ></div>

      <!-- Modal -->
      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/20 sm:mx-0 sm:h-10 sm:w-10">
              <BookmarkIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                Save Study Session
              </h3>
              
              <div class="mt-2">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                  Would you like to save this session for later review?
                </p>

                <!-- Session info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4">
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Total Cards:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ sessionData?.total_words || 0 }}</span>
                  </div>
                  
                  <div v-if="sessionData?.topic" class="flex items-center justify-between text-sm mt-2">
                    <span class="text-gray-600 dark:text-gray-300">Topic:</span>
                    <span class="font-medium text-blue-600 dark:text-blue-400">{{ sessionData.topic }}</span>
                  </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="saveSession" class="space-y-4">
                  <!-- Session Name -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Session Name
                    </label>
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      placeholder="Enter session name"
                    />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                      Leave empty to auto-generate based on date and topic
                    </p>
                  </div>

                  <!-- Topic (optional) -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Topic (Optional)
                    </label>
                    <input
                      v-model="form.topic"
                      type="text"
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      placeholder="e.g., Business English, Travel Vocabulary"
                    />
                  </div>

                  <!-- Error messages -->
                  <div v-if="errors.length > 0" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3">
                    <div class="flex">
                      <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
                      <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                          Please fix the following errors:
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                          <ul class="list-disc list-inside space-y-1">
                            <li v-for="error in errors" :key="error">{{ error }}</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="saveSession"
            :disabled="loading"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <SpinnerIcon v-if="loading" class="w-4 h-4 mr-2 animate-spin" />
            <BookmarkIcon v-else class="w-4 h-4 mr-2" />
            {{ loading ? 'Saving...' : 'Save Session' }}
          </button>
          
          <button
            @click="handleClose"
            :disabled="loading"
            type="button"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors disabled:opacity-50"
          >
            {{ loading ? 'Saving...' : 'Skip' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { 
  BookmarkIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

// Simple spinner component
const SpinnerIcon = {
  template: `<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
  </svg>`
};

const props = defineProps({
  sessionData: {
    type: Object,
    required: true
  }
});

defineEmits(['close', 'saved']);

// State
const loading = ref(false);
const errors = ref([]);
const form = ref({
  name: '',
  topic: ''
});

// Initialize form with suggested data
onMounted(() => {
  if (props.sessionData?.suggested_name) {
    form.value.name = props.sessionData.suggested_name;
  }
  if (props.sessionData?.topic) {
    form.value.topic = props.sessionData.topic;
  }
});

// Methods
const handleClose = () => {
  if (!loading.value) {
    $emit('close');
  }
};

const saveSession = async () => {
  if (loading.value) return;

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
        $emit('saved', page.props?.session);
        $emit('close');
      },
      onError: (errorData) => {
        // Handle validation errors
        if (errorData.name) errors.value.push(...(Array.isArray(errorData.name) ? errorData.name : [errorData.name]));
        if (errorData.topic) errors.value.push(...(Array.isArray(errorData.topic) ? errorData.topic : [errorData.topic]));
        if (errorData.flashcard_ids) errors.value.push(...(Array.isArray(errorData.flashcard_ids) ? errorData.flashcard_ids : [errorData.flashcard_ids]));
        
        // Generic error fallback
        if (errors.value.length === 0) {
          errors.value.push('An error occurred while saving the session.');
        }
      }
    });
  } catch (error) {
    console.error('Error saving session:', error);
    errors.value.push('An unexpected error occurred. Please try again.');
  } finally {
    loading.value = false;
  }
};
</script>