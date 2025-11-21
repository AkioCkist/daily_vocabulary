<template>
  <transition
    enter-active-class="transition-all duration-200 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition-all duration-150 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">Flashcard Training</h2>
          <button 
            @click="$emit('close')"
            class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          >
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Content - Scrollable -->
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">
          <!-- Mode Selection -->
          <ModeSelector v-model="settings.mode" />

          <!-- Flashcard Type Selection -->
          <FlashcardTypeSelector v-model="settings.flashcard_type" />

          <!-- Advanced Options -->
          <transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-[1000px]"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-[1000px]"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="settings.mode === 'advanced'" class="space-y-6">
              <AdvancedSettings
                :cefr-levels="dashboard.cefr_levels"
                v-model:selected-levels="settings.cefr_levels"
                v-model:word-count="settings.word_count"
              />

              <TopicSelector
                :topics="dashboard.available_topics"
                v-model:selected-topics="settings.topic_ids"
                @topic-created="handleTopicCreated"
              />
            </div>
          </transition>

          <!-- Summary -->
          <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              Summary
            </h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
              <div class="flex items-center gap-2">
                <span class="text-gray-600 dark:text-gray-400">Mode:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ settings.mode === 'basic' ? 'Quick' : 'Custom' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-600 dark:text-gray-400">Type:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ getFlashcardTypeLabel }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-600 dark:text-gray-400">Words:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ settings.word_count }}</span>
              </div>
              <div v-if="settings.mode === 'advanced'" class="flex items-center gap-2">
                <span class="text-gray-600 dark:text-gray-400">Topics:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ settings.topic_ids.length || 'All' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
          <button
            @click="$emit('close')"
            class="flex-1 py-2.5 px-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium"
          >
            Cancel
          </button>
          <button
            @click="startTraining"
            :disabled="!canStart"
            :class="[
              'flex-1 py-2.5 px-4 rounded-lg font-medium transition-all duration-200',
              canStart
                ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-md hover:shadow-lg'
                : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'
            ]"
          >
            Start Training
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { reactive, computed } from 'vue';
import ModeSelector from './Flashcard/ModeSelector.vue';
import FlashcardTypeSelector from './Flashcard/FlashcardTypeSelector.vue';
import AdvancedSettings from './Flashcard/AdvancedSettings.vue';
import TopicSelector from './Flashcard/TopicSelector.vue';

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

const getFlashcardTypeLabel = computed(() => {
  const types = {
    standard: 'Standard',
    fill_blank: 'Fill-Blank',
    mixed: 'Mixed'
  };
  return types[settings.flashcard_type] || 'Standard';
});

// Methods
function handleTopicCreated(topic) {
  // Add the new topic to the dashboard data
  if (!props.dashboard.available_topics.user) {
    props.dashboard.available_topics.user = [];
  }
  props.dashboard.available_topics.user.push(topic);
  
  // Automatically select the new topic
  settings.topic_ids.push(topic.id);
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