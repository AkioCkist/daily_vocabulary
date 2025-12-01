<template>
  <div class="bg-gradient-to-r from-green-600 via-emerald-600 to-green-700 p-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 bg-white/30 backdrop-blur-sm rounded-full text-sm font-bold text-gray-900">
          {{ cefrLevel }}
        </span>
        <span class="text-sm font-medium text-green-100/70">{{ topic }}</span>
      </div>
      <slot name="actions"></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  cefrLevel: {
    type: String,
    required: true
  },
  topic: {
    type: String,
    required: true
  }
});
</script>
