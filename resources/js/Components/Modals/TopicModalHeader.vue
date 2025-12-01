<template>
  <div class="flex items-center justify-between p-6 border-b border-indigo-700/50">
    <h2 class="text-2xl font-extrabold text-white">Topic Management</h2>
    <button 
      @click="$emit('close')"
      class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:bg-indigo-700/30 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
      title="Close"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
  </div>
</template>

<script setup>
defineEmits(['close']);
</script>
