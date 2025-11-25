<template>
  <transition
    enter-active-class="transition-all duration-200 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition-all duration-150 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
      
      <div class="bg-[#0B0C10]/90 backdrop-blur-lg rounded-2xl shadow-2xl shadow-indigo-900/40 ring-1 ring-indigo-900/50 max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col text-white">
        
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
          <h2 class="text-xl font-bold">How would you like to study ?</h2>
          <button 
            @click="$emit('close')"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-700/50 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">
          
          <!-- Template Manager -->
          <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Template Settings</h3>
            <TemplateManager
              :current-settings="getCurrentSettings()"
              @template-loaded="loadTemplate"
            />
          </div>

          <div class="border-t border-gray-800 pt-6 space-y-6">
            <!-- Flashcard Type -->
            <div>
              <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Flashcard Type</h3>
              <FlashcardTypeSelector v-model="settings.flashcard_type" />
            </div>

            <!-- CEFR Level Selection (moved from advanced) -->
            <div class="space-y-3">
              <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">CEFR Level</h3>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="(label, level) in dashboard.cefr_levels"
                  :key="level"
                  @click="toggleLevel(level)"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                    settings.cefr_levels.includes(level)
                      ? 'bg-indigo-500 text-white shadow-md scale-105'
                      : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
                  ]"
                >
                  {{ level }}
                </button>
              </div>
              <p class="text-xs text-gray-400">
                {{ settings.cefr_levels.length > 0 ? settings.cefr_levels.join(', ') : 'All levels' }}
              </p>
            </div>

            <!-- Topics (moved from advanced) -->
            <div>
              <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Topics</h3>
              <TopicSelector
                :topics="dashboard.available_topics"
                v-model:selected-topics="settings.topic_ids"
                @topic-created="handleTopicCreated"
              />
            </div>
          </div>

          <!-- Advanced Settings (Collapsible) -->
          <div class="pt-2">
            <button
              @click="showAdvancedSettings = !showAdvancedSettings"
              class="w-full flex items-center justify-between p-3 rounded-xl bg-gray-800/70 hover:bg-gray-700/70 transition-colors text-sm font-semibold"
            >
              <span>Advanced Filters (Optional)</span>
              <svg 
                class="w-4 h-4 transition-transform" 
                :class="{'rotate-180': showAdvancedSettings}" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>

          <transition
            enter-active-class="transition-all duration-300 ease-out overflow-hidden"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-[1000px]"
            leave-active-class="transition-all duration-200 ease-in overflow-hidden"
            leave-from-class="opacity-100 max-h-[1000px]"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="showAdvancedSettings" class="space-y-6 p-4 border border-gray-700 rounded-xl bg-gray-900/50">
              <h4 class="text-md font-bold text-indigo-400">Advanced Options</h4>
              <AdvancedSettings
                v-model:word-count="settings.word_count"
                v-model:difficulty-filter="settings.difficulty_filter"
                v-model:mastery-filter="settings.mastery_filter"
                v-model:time-filter="settings.time_filter"
                v-model:sort-by="settings.sort_by"
              />
            </div>
          </transition>

        </div>

        <div class="flex gap-3 px-6 py-4 border-t border-gray-800 bg-gray-900/50">
          <button
            @click="$emit('close')"
            class="flex-1 py-2.5 px-4 border border-gray-700 text-gray-300 rounded-lg hover:bg-gray-800 transition-colors font-medium"
          >
            Cancel
          </button>
          <button
            @click="startTraining"
            :disabled="!canStart"
            :class="[
              'flex-1 py-2.5 px-4 rounded-lg font-medium transition-all duration-200',
              canStart
                ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-600/30'
                : 'bg-gray-600 text-gray-400 cursor-not-allowed opacity-70'
            ]"
          >
            Start Training ({{ settings.word_count }} Words)
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { reactive, computed, ref } from 'vue';
import FlashcardTypeSelector from './Flashcard/FlashcardTypeSelector.vue';
import AdvancedSettings from './Flashcard/AdvancedSettings.vue';
import TopicSelector from './Flashcard/TopicSelector.vue';
import TemplateManager from './Flashcard/TemplateManager.vue';

const props = defineProps({
  dashboard: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'start']);

// New state for advanced settings toggle
const showAdvancedSettings = ref(false);

// Training settings
const settings = reactive({
  flashcard_type: 'standard',
  word_count: 10,
  cefr_levels: [],
  topic_ids: [],
  difficulty_filter: 'all',
  mastery_filter: 'all',
  time_filter: 'all',
  sort_by: 'random'
});

// Computed properties
const canStart = computed(() => {
  return settings.word_count >= 5 && settings.word_count <= 50;
});

// Methods
function toggleLevel(level) {
  const index = settings.cefr_levels.indexOf(level);
  
  if (index > -1) {
    settings.cefr_levels.splice(index, 1);
  } else {
    settings.cefr_levels.push(level);
  }
}

function handleTopicCreated(topic) {
  if (!props.dashboard.available_topics.user) {
    props.dashboard.available_topics.user = [];
  }
  props.dashboard.available_topics.user.push(topic);
  
  settings.topic_ids.push(topic.id);
}

function getCurrentSettings() {
  return {
    flashcard_type: settings.flashcard_type,
    word_count: settings.word_count,
    cefr_levels: settings.cefr_levels,
    topic_ids: settings.topic_ids,
    difficulty_filter: settings.difficulty_filter,
    mastery_filter: settings.mastery_filter,
    time_filter: settings.time_filter,
    sort_by: settings.sort_by
  };
}

function loadTemplate(templateSettings) {
  // Load all settings from template
  settings.flashcard_type = templateSettings.flashcard_type || 'standard';
  settings.word_count = templateSettings.word_count || 10;
  settings.cefr_levels = templateSettings.cefr_levels || [];
  settings.topic_ids = templateSettings.topic_ids || [];
  settings.difficulty_filter = templateSettings.difficulty_filter || 'all';
  settings.mastery_filter = templateSettings.mastery_filter || 'all';
  settings.time_filter = templateSettings.time_filter || 'all';
  settings.sort_by = templateSettings.sort_by || 'random';
}

function startTraining() {
  if (!canStart.value) return;

  const trainingSettings = {
    mode: 'advanced',
    flashcard_type: settings.flashcard_type,
    word_count: settings.word_count,
  };

  // Only include optional filters if they are selected
  if (settings.cefr_levels.length > 0) {
    trainingSettings.cefr_levels = settings.cefr_levels;
  }
  if (settings.topic_ids.length > 0) {
    trainingSettings.topic_ids = settings.topic_ids;
  }
  
  // Include advanced filters
  if (settings.difficulty_filter !== 'all') {
    trainingSettings.difficulty_filter = settings.difficulty_filter;
  }
  if (settings.mastery_filter !== 'all') {
    trainingSettings.mastery_filter = settings.mastery_filter;
  }
  if (settings.time_filter !== 'all') {
    trainingSettings.time_filter = settings.time_filter;
  }
  if (settings.sort_by !== 'random') {
    trainingSettings.sort_by = settings.sort_by;
  }

  emit('start', trainingSettings);
  emit('close');
}
</script>