<template>
  <div>
    <WordCard v-for="word in words" :key="word.id" :word="word" />
  </div>
</template>

<script setup>
import WordCard from './WordCard.vue';
const props = defineProps({ words: Array });
</script>
