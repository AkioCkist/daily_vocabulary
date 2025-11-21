<template>
  <div class="space-y-3">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
      Training Mode
    </label>
    
    <div class="grid grid-cols-2 gap-3">
      <button
        @click="$emit('update:modelValue', 'basic')"
        :class="[
          'group relative p-4 rounded-xl border-2 transition-all duration-200',
          modelValue === 'basic'
            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 shadow-md'
            : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700'
        ]"
      >
        <div class="flex items-center gap-3 mb-2">
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 dark:text-white">Quick Start</span>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
          10 random words to get started quickly
        </p>
      </button>

      <button
        @click="$emit('update:modelValue', 'advanced')"
        :class="[
          'group relative p-4 rounded-xl border-2 transition-all duration-200',
          modelValue === 'advanced'
            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 shadow-md'
            : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700'
        ]"
      >
        <div class="flex items-center gap-3 mb-2">
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 dark:text-white">Custom</span>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
          Customize topics, levels & word count
        </p>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    required: true
  }
});

defineEmits(['update:modelValue']);
</script>
