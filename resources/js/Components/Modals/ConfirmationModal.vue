<template>
  <transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div 
          class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity"
          @click="$emit('close')"
        ></div>

        <div class="inline-block align-bottom bg-[#0B0C10]/90 backdrop-blur-lg rounded-2xl text-left overflow-hidden shadow-2xl shadow-red-900/40 ring-1 ring-red-900/50 transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full text-white">
          
          <div class="px-6 py-5 border-b border-gray-800">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-700/30 sm:mx-0 sm:h-10 sm:w-10 border border-red-700">
                <ExclamationTriangleIcon class="h-6 w-6 text-red-400" />
              </div>
              
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-xl font-bold leading-6 text-white" id="modal-title">
                  {{ title }}
                </h3>
              </div>
            </div>
          </div>
          
          <div class="px-6 py-6">
            <p class="text-sm text-gray-300">
              {{ message }}
            </p>
          </div>

          <div class="px-6 py-4 bg-gray-900/50 flex justify-end gap-3 border-t border-gray-800">
            <button
              type="button"
              @click="$emit('close')"
              class="inline-flex justify-center py-2.5 px-4 border border-gray-700 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 transition-colors"
            >
              {{ cancelText }}
            </button>
            
            <button
              type="button"
              @click="$emit('confirm')"
              class="inline-flex justify-center py-2.5 px-4 border border-transparent text-sm font-bold rounded-xl shadow-lg transition-all duration-200 hover:scale-[1.02] active:scale-95
                     bg-gradient-to-r from-red-600 to-rose-700 text-white hover:from-red-700 hover:to-rose-800 shadow-red-600/30 disabled:opacity-50 disabled:shadow-none"
            >
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'; // Assumed icon

defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Confirmation'
  },
  message: {
    type: String,
    default: 'Are you sure you want to perform this action?'
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  }
});

defineEmits(['close', 'confirm']);
</script>

<style scoped>
/* Scoped styles for transitions (optional) */
</style>