<template>
  <div>
    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
      <div class="w-6 h-6 bg-gradient-to-br rounded-lg flex items-center justify-center shadow" :class="gradientClass">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" :viewBox="iconViewBox">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath"></path>
        </svg>
      </div>
      {{ title }}
    </h3>
    
    <div class="space-y-3">
      <slot>
        <div v-if="!topics.length" class="text-center py-8 text-gray-500">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="emptyIconPath"></path>
          </svg>
          <p>{{ emptyMessage }}</p>
          <p v-if="showCreateHint" class="text-sm mt-1">Create your first topic above!</p>
        </div>
      </slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  topics: {
    type: Array,
    default: () => []
  },
  gradientClass: {
    type: String,
    required: true
  },
  iconViewBox: {
    type: String,
    default: '0 0 24 24'
  },
  iconPath: {
    type: String,
    required: true
  },
  emptyMessage: {
    type: String,
    required: true
  },
  emptyIconPath: {
    type: String,
    required: true
  },
  showCreateHint: {
    type: Boolean,
    default: false
  }
});
</script>
