<template>
  <transition
    enter-active-class="transition-all duration-200 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition-all duration-150 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div v-if="isVisible" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
      <div :class="['rounded-xl shadow-2xl max-w-md w-full p-6 border', getAlertClasses()]">
        
        <!-- Header with Icon -->
        <div class="flex items-start gap-4 mb-4">
          <div :class="['flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center', getIconBackground()]">
            <svg v-if="type === 'error'" class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <svg v-else-if="type === 'success'" class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <svg v-else-if="type === 'warning'" class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-bold text-white">{{ title }}</h3>
          </div>
          
          <button
            @click="$emit('close')"
            class="flex-shrink-0 text-gray-400 hover:text-white transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Message -->
        <p class="text-gray-300 mb-4">{{ message }}</p>

        <!-- Details (if available) -->
        <div v-if="details" class="mb-4 p-3 bg-black/30 rounded-lg border border-gray-700">
          <p class="text-sm text-gray-400 font-mono">{{ details }}</p>
        </div>

        <!-- Close Button -->
        <div class="flex justify-end">
          <button
            @click="$emit('close')"
            :class="['px-4 py-2 rounded-lg font-medium transition-colors', getButtonClasses()]"
          >
            Got It
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'error',
    validator: (val) => ['error', 'success', 'warning', 'info'].includes(val)
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  details: {
    type: String,
    default: null
  }
});

defineEmits(['close']);

const getAlertClasses = () => {
  const baseClasses = 'bg-[#1F2937]';
  const borderClasses = {
    error: 'border-red-500/50',
    success: 'border-green-500/50',
    warning: 'border-yellow-500/50',
    info: 'border-blue-500/50'
  };
  return `${baseClasses} ${borderClasses[props.type]}`;
};

const getIconBackground = () => {
  const backgroundClasses = {
    error: 'bg-red-900/30',
    success: 'bg-green-900/30',
    warning: 'bg-yellow-900/30',
    info: 'bg-blue-900/30'
  };
  return backgroundClasses[props.type];
};

const getButtonClasses = () => {
  const buttonClasses = {
    error: 'bg-red-600 hover:bg-red-700 text-white',
    success: 'bg-green-600 hover:bg-green-700 text-white',
    warning: 'bg-yellow-600 hover:bg-yellow-700 text-white',
    info: 'bg-blue-600 hover:bg-blue-700 text-white'
  };
  return buttonClasses[props.type];
};
</script>
