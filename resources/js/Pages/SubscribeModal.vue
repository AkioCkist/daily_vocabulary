<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded p-6 w-96">
      <h2 class="text-xl font-bold mb-4">Subscribe</h2>
      <SubscribeForm @success="close" />
      <button @click="close" class="mt-4 text-gray-500 hover:text-gray-700">Cancel</button>
    </div>
  </div>
</template>

<script setup>
import SubscribeForm from '@/Components/SubscribeForm.vue';
import { ref, defineEmits } from 'vue';

const emit = defineEmits(['close']);

function close() {
  emit('close');
}
</script>
