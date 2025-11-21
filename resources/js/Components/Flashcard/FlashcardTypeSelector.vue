<template>
  <div class="space-y-3">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
      Flashcard Type
    </label>
    
    <div class="space-y-2">
      <button
        @click="$emit('update:modelValue', 'standard')"
        :class="[
          'w-full p-4 rounded-xl border-2 text-left transition-all duration-200',
          modelValue === 'standard'
            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-md'
            : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700'
        ]"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div class="flex-1">
            <div class="font-semibold text-gray-900 dark:text-white mb-1">Standard Flashcards</div>
            <p class="text-sm text-gray-600 dark:text-gray-400">See the word, recall the definition</p>
          </div>
        </div>
      </button>

      <button
        @click="$emit('update:modelValue', 'fill_blank')"
        :class="[
          'w-full p-4 rounded-xl border-2 text-left transition-all duration-200',
          modelValue === 'fill_blank'
            ? 'border-green-500 bg-green-50 dark:bg-green-900/20 shadow-md'
            : 'border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-700'
        ]"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
          </div>
          <div class="flex-1">
            <div class="font-semibold text-gray-900 dark:text-white mb-1">Fill-in-the-Blank</div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Type the word from definition with hints</p>
          </div>
        </div>
      </button>

      <button
        @click="$emit('update:modelValue', 'mixed')"
        :class="[
          'w-full p-4 rounded-xl border-2 text-left transition-all duration-200',
          modelValue === 'mixed'
            ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 shadow-md'
            : 'border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700'
        ]"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
          </div>
          <div class="flex-1">
            <div class="font-semibold text-gray-900 dark:text-white mb-1">Mixed Mode</div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Random mix of both types</p>
          </div>
        </div>
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
