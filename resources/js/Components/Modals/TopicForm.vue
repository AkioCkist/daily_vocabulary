<template>
  <div class="mb-8 p-6 bg-[#0B0C10] rounded-xl border border-indigo-700 shadow-xl">
    <h3 class="text-xl font-semibold text-indigo-300 mb-4">Create Custom Topic</h3>
    
    <form @submit.prevent="onSubmit" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2">Topic Name</label>
          <input
            v-model="formData.name"
            type="text"
            placeholder="e.g., Medical Terms"
            required
            class="w-full px-4 py-2 border border-indigo-700 rounded-lg bg-[#0B0C10] text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-inner"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
          <input
            v-model="formData.description"
            type="text"
            placeholder="Brief description of the topic"
            class="w-full px-4 py-2 border border-indigo-700 rounded-lg bg-[#0B0C10] text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-inner"
          >
        </div>
      </div>
      
      <div class="flex justify-end pt-2">
        <button
          type="submit"
          :disabled="!formData.name.trim() || isLoading"
          :class="[
            'px-6 py-2 rounded-lg font-bold transition-all duration-200 shadow-lg',
            !formData.name.trim() || isLoading
              ? 'bg-gray-700 text-gray-400 cursor-not-allowed'
              : 'bg-gradient-to-r from-indigo-600 to-purple-700 text-white hover:from-indigo-700 hover:to-purple-800'
          ]"
        >
          {{ isLoading ? 'Creating...' : 'Create Topic' }}
        </button>
      </div>
    </form>
    
    <div v-if="error" class="mt-4 p-3 bg-red-900/40 border border-red-700 rounded-lg">
      <p class="text-sm text-red-400 font-medium">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

const props = defineProps({
  isLoading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['submit']);

const formData = reactive({
  name: '',
  description: ''
});

const onSubmit = () => {
  if (!formData.name.trim()) return;
  
  emit('submit', {
    name: formData.name.trim(),
    description: formData.description.trim() || null
  });
  
  formData.name = '';
  formData.description = '';
};
</script>
