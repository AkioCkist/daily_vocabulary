<template>
  <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-60">
    <div class="bg-[#1F2937] rounded-xl shadow-2xl shadow-black/60 max-w-md w-full mx-4 border border-indigo-700/50">
      <div class="p-6">
        <h3 class="text-xl font-semibold text-white mb-4">Edit Topic</h3>
        
        <form @submit.prevent="onSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Topic Name</label>
            <input
              v-model="formData.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-indigo-700 rounded-lg bg-[#0B0C10] text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-inner"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
            <input
              v-model="formData.description"
              type="text"
              class="w-full px-4 py-2 border border-indigo-700 rounded-lg bg-[#0B0C10] text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-inner"
            >
          </div>
          
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="$emit('cancel')"
              class="flex-1 py-2 px-4 border border-gray-700 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors font-medium"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isLoading"
              class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors font-bold disabled:opacity-50 shadow-md"
            >
              {{ isLoading ? 'Updating...' : 'Update' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';

const props = defineProps({
  topic: {
    type: Object,
    required: true
  },
  isLoading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['submit', 'cancel']);

const formData = reactive({
  name: props.topic?.name || '',
  description: props.topic?.description || ''
});

const onSubmit = () => {
  if (!formData.name.trim()) return;
  
  emit('submit', {
    id: props.topic.id,
    name: formData.name.trim(),
    description: formData.description.trim() || null
  });
};
</script>
