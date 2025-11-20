<template>
  <div class="max-w-2xl mx-auto space-y-8">
    <!-- Word Filter Section -->
    <div>
      <WordFilter @word-selected="handleWordSelected" />
    </div>

    <!-- Subscribe Section -->
    <div>
      <SubscribeForm />
    </div>
  </div>
</template>

<script setup>
import WordFilter from '@/Components/WordFilter.vue';
import SubscribeForm from '@/Components/SubscribeForm.vue';

const emit = defineEmits(['wordSelected']);

const handleWordSelected = (word) => {
  emit('wordSelected', word);
};
</script>
