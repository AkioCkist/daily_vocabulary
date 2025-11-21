<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="flex items-center justify-center w-full h-full">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="$emit('close')"
      ></div>

      <!-- Modal -->
      <div class="bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full max-w-lg">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/20 sm:mx-0 sm:h-10 sm:w-10">
              <PlayIcon class="h-6 w-6 text-green-600 dark:text-green-400" />
            </div>
            
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                Review Session
              </h3>
              
              <div class="mt-2">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                  Start reviewing: <strong>{{ session?.name }}</strong>
                </p>

                <!-- Session info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4">
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Cards:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ session?.flashcard_count }}</span>
                  </div>
                  
                  <div v-if="session?.topic" class="flex items-center justify-between text-sm mt-2">
                    <span class="text-gray-600 dark:text-gray-300">Topic:</span>
                    <span class="font-medium text-blue-600 dark:text-blue-400">{{ session.topic }}</span>
                  </div>
                  
                  <div class="flex items-center justify-between text-sm mt-2">
                    <span class="text-gray-600 dark:text-gray-300">Created:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ formatDate(session?.created_at) }}</span>
                  </div>
                </div>

                <!-- Options -->
                <div class="space-y-4">
                  <!-- Shuffle option -->
                  <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                      <ShuffleIcon class="w-4 h-4 mr-2" />
                      Shuffle Cards
                    </label>
                    <input
                      v-model="settings.shuffle"
                      type="checkbox"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                    />
                  </div>


                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="startReview"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
          >
            <PlayIcon class="w-4 h-4 mr-2" />
            Start Review
          </button>
          
          <button
            @click="emit('close')"
            type="button"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { PlayIcon } from '@heroicons/vue/24/outline';
import { ArrowPathIcon as ShuffleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  session: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'start']);

// Settings for the review session
const settings = ref({
  shuffle: false,
  flashcard_type: 'standard'
});

// Methods
const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString();
};

const startReview = () => {
  // Emit the start event with settings
  emit('start', settings.value);
};
</script>