<template>
  <div v-if="error" class="px-6 pt-6">
    <div class="p-4 bg-red-900/20 border border-red-500/20 text-red-200 rounded-lg flex items-start">
      <svg class="h-5 w-5 text-red-400 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ error }}</span>
    </div>
  </div>
  <div v-if="success" class="px-6 pt-6">
    <div class="p-4 bg-green-900/20 border border-green-500/20 text-green-200 rounded-lg flex items-start">
      <svg class="h-5 w-5 text-green-400 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <span>{{ success }}</span>
    </div>
  </div>
</template>
<script setup>
defineProps({
  error: String,
  success: String,
});
</script>
