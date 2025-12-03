<template>
  <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800 flex-shrink-0">
    <h2 class="text-xl font-bold text-white flex items-center">
        <svg class="w-6 h-6 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5zM12 8v4l3 1"></path>
        </svg>
        Topic Manager
    </h2>
    <button 
      @click="$emit('close')"
      class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-700/50 transition-colors"
      title="Close"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
  </div>
</template>

<script setup>
defineEmits(['close']);
</script>