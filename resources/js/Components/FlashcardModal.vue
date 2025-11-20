<template>
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Flashcard Training</h2>
        <button 
          @click="$emit('close')"
          class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        >
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-6">
        <!-- Mode Selection -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Training Mode</label>
          <div class="grid grid-cols-2 gap-3">
            <button
              @click="settings.mode = 'basic'"
              :class="[
                'p-4 rounded-xl border-2 text-left transition-all duration-200',
                settings.mode === 'basic'
                  ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                  : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
              ]"
            >
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">Quick Start</span>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-400">10 random words from your learning list</p>
            </button>

            <button
              @click="settings.mode = 'advanced'"
              :class="[
                'p-4 rounded-xl border-2 text-left transition-all duration-200',
                settings.mode === 'advanced'
                  ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                  : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
              ]"
            >
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                  </svg>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">Custom Settings</span>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Choose topics, levels, and word count</p>
            </button>
          </div>
        </div>

        <!-- Flashcard Type Selection -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Flashcard Type</label>
          <div class="grid grid-cols-2 gap-3 mb-3">
            <button
              @click="settings.flashcard_type = 'standard'"
              :class="[
                'p-4 rounded-xl border-2 text-left transition-all duration-200',
                settings.flashcard_type === 'standard'
                  ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                  : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
              ]"
            >
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4L5 6v12a2 2 0 002 2h10a2 2 0 002-2V6l-2-2"></path>
                  </svg>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">Standard Flashcards</span>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Word → Definition with "I don't remember" option</p>
            </button>

            <button
              @click="settings.flashcard_type = 'fill_blank'"
              :class="[
                'p-4 rounded-xl border-2 text-left transition-all duration-200',
                settings.flashcard_type === 'fill_blank'
                  ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                  : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
              ]"
            >
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                  </svg>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">Fill-in-the-Blank</span>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Definition → Type the word with progressive hints</p>
            </button>
          </div>
          
          <button
            @click="settings.flashcard_type = 'mixed'"
            :class="[
              'w-full p-4 rounded-xl border-2 text-left transition-all duration-200',
              settings.flashcard_type === 'mixed'
                ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
            ]"
          >
            <div class="flex items-center gap-3 mb-2">
              <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
              </div>
              <span class="font-medium text-gray-900 dark:text-white">Mixed Mode</span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Randomly alternate between both modes for variety</p>
          </button>
        </div>

        <!-- Advanced Options -->
        <div v-if="settings.mode === 'advanced'" class="space-y-6">
          <!-- CEFR Level Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">CEFR Level</label>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
              <button
                v-for="(label, level) in dashboard.cefr_levels"
                :key="level"
                @click="toggleCEFRLevel(level)"
                :class="[
                  'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                  settings.cefr_levels.includes(level)
                    ? 'bg-indigo-500 text-white'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                ]"
              >
                {{ level }}
              </button>
            </div>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Selected: {{ settings.cefr_levels.length > 0 ? settings.cefr_levels.join(', ') : 'All levels' }}
            </div>
          </div>

          <!-- Topic Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Topics</label>
            
            <!-- System Topics -->
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">System Topics</h4>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                <button
                  v-for="topic in dashboard.available_topics.system"
                  :key="`system-${topic.id}`"
                  @click="toggleTopic(topic.id)"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm text-left transition-all duration-200',
                    settings.topic_ids.includes(topic.id)
                      ? 'bg-green-500 text-white'
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                  ]"
                >
                  <div class="font-medium">{{ topic.name }}</div>
                  <div class="text-xs opacity-75">{{ topic.words_count }} words</div>
                </button>
              </div>
            </div>

            <!-- User Topics -->
            <div v-if="dashboard.available_topics.user && dashboard.available_topics.user.length > 0">
              <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Your Topics</h4>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                <button
                  v-for="topic in dashboard.available_topics.user"
                  :key="`user-${topic.id}`"
                  @click="toggleTopic(topic.id)"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm text-left transition-all duration-200',
                    settings.topic_ids.includes(topic.id)
                      ? 'bg-purple-500 text-white'
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                  ]"
                >
                  <div class="font-medium">{{ topic.name }}</div>
                  <div class="text-xs opacity-75">{{ topic.words_count }} words</div>
                </button>
              </div>
            </div>

            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Selected: {{ settings.topic_ids.length > 0 ? settings.topic_ids.length + ' topics' : 'All topics' }}
            </div>
          </div>

          <!-- Word Count -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              Number of Words: {{ settings.word_count }}
            </label>
            <input
              type="range"
              v-model="settings.word_count"
              min="5"
              max="50"
              step="5"
              class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer slider"
            >
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
              <span>5</span>
              <span>25</span>
              <span>50</span>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
          <h3 class="font-medium text-gray-900 dark:text-white mb-2">Training Summary</h3>
          <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
            <div>Mode: {{ settings.mode === 'basic' ? 'Quick Start' : 'Custom Settings' }}</div>
            <div>Type: {{ settings.flashcard_type === 'standard' ? 'Standard Flashcards' : settings.flashcard_type === 'fill_blank' ? 'Fill-in-the-Blank' : 'Mixed Mode' }}</div>
            <div>Words: {{ settings.word_count }}</div>
            <div v-if="settings.mode === 'advanced'">
              CEFR Levels: {{ settings.cefr_levels.length > 0 ? settings.cefr_levels.join(', ') : 'All' }}
            </div>
            <div v-if="settings.mode === 'advanced'">
              Topics: {{ settings.topic_ids.length > 0 ? settings.topic_ids.length + ' selected' : 'All' }}
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 mt-6">
          <button
            @click="$emit('close')"
            class="flex-1 py-3 px-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="startTraining"
            :disabled="!canStart"
            :class="[
              'flex-1 py-3 px-4 rounded-xl font-medium transition-all duration-200',
              canStart
                ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700'
                : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'
            ]"
          >
            Start Training
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue';

const props = defineProps({
  dashboard: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'start']);

// Training settings
const settings = reactive({
  mode: 'basic',
  flashcard_type: 'standard',
  word_count: 10,
  cefr_levels: [],
  topic_ids: []
});

// Computed properties
const canStart = computed(() => {
  return settings.word_count >= 5 && settings.word_count <= 50;
});

// Methods
function toggleCEFRLevel(level) {
  const index = settings.cefr_levels.indexOf(level);
  if (index > -1) {
    settings.cefr_levels.splice(index, 1);
  } else {
    settings.cefr_levels.push(level);
  }
}

function toggleTopic(topicId) {
  const index = settings.topic_ids.indexOf(topicId);
  if (index > -1) {
    settings.topic_ids.splice(index, 1);
  } else {
    settings.topic_ids.push(topicId);
  }
}

function startTraining() {
  if (!canStart.value) return;

  const trainingSettings = {
    mode: settings.mode,
    flashcard_type: settings.flashcard_type,
    word_count: settings.word_count
  };

  if (settings.mode === 'advanced') {
    if (settings.cefr_levels.length > 0) {
      trainingSettings.cefr_levels = settings.cefr_levels;
    }
    if (settings.topic_ids.length > 0) {
      trainingSettings.topic_ids = settings.topic_ids;
    }
  }

  emit('start', trainingSettings);
  emit('close');
}
</script>

<style scoped>
.slider::-webkit-slider-thumb {
  appearance: none;
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #6366f1;
  cursor: pointer;
}

.slider::-moz-range-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #6366f1;
  cursor: pointer;
  border: none;
}
</style>