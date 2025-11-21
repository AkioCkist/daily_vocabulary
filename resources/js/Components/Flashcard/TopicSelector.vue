<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Topics
      </label>
      <button
        @click="showCreateForm = !showCreateForm"
        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium flex items-center gap-1 transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Topic
      </button>
    </div>

    <!-- Quick Create Form -->
    <transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="showCreateForm" class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Create New Topic</h4>
        <div class="space-y-3">
          <input
            v-model="newTopic.name"
            type="text"
            placeholder="Topic name"
            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            @keyup.enter="createTopic"
          >
          <textarea
            v-model="newTopic.description"
            placeholder="Description (optional)"
            rows="2"
            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
          ></textarea>
          <div class="flex gap-2">
            <button
              @click="createTopic"
              :disabled="!newTopic.name.trim()"
              class="flex-1 px-4 py-2 bg-indigo-500 text-white rounded-lg font-medium hover:bg-indigo-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
            >
              Create
            </button>
            <button
              @click="cancelCreate"
              class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- System Topics -->
    <div v-if="topics.system && topics.system.length > 0" class="space-y-2">
      <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">System Topics</h4>
      <div class="grid grid-cols-2 gap-2">
        <button
          v-for="topic in topics.system"
          :key="`system-${topic.id}`"
          @click="toggleTopic(topic.id)"
          :class="[
            'p-3 rounded-lg text-left transition-all duration-200',
            selectedTopics.includes(topic.id)
              ? 'bg-green-500 text-white shadow-md scale-105'
              : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
          ]"
        >
          <div class="font-medium text-sm">{{ topic.name }}</div>
          <div class="text-xs opacity-75 mt-1">{{ topic.words_count }} words</div>
        </button>
      </div>
    </div>

    <!-- User Topics -->
    <div v-if="topics.user && topics.user.length > 0" class="space-y-2">
      <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Your Topics</h4>
      <div class="grid grid-cols-2 gap-2">
        <button
          v-for="topic in topics.user"
          :key="`user-${topic.id}`"
          @click="toggleTopic(topic.id)"
          :class="[
            'p-3 rounded-lg text-left transition-all duration-200',
            selectedTopics.includes(topic.id)
              ? 'bg-purple-500 text-white shadow-md scale-105'
              : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
          ]"
        >
          <div class="font-medium text-sm">{{ topic.name }}</div>
          <div class="text-xs opacity-75 mt-1">{{ topic.words_count }} words</div>
        </button>
      </div>
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400">
      {{ selectedTopics.length > 0 ? `${selectedTopics.length} topics selected` : 'All topics' }}
    </p>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

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

const emit = defineEmits(['update:selectedTopics', 'topicCreated']);

const showCreateForm = ref(false);
const newTopic = reactive({
  name: '',
  description: ''
});

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

async function createTopic() {
  if (!newTopic.name.trim()) return;

  try {
    const response = await fetch('/flashcards/topics/quick-create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({
        name: newTopic.name,
        description: newTopic.description
      })
    });

    const data = await response.json();

    if (data.success) {
      emit('topicCreated', data.topic);
      newTopic.name = '';
      newTopic.description = '';
      showCreateForm.value = false;
    }
  } catch (error) {
    console.error('Failed to create topic:', error);
  }
}

function cancelCreate() {
  newTopic.name = '';
  newTopic.description = '';
  showCreateForm.value = false;
}
</script>
