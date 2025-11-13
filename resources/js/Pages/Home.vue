<template>
  <div class="p-4 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Word of the Day</h1>

    <WordCard :word="wordOfTheDay" />

    <button
      v-if="!added"
      @click="addWord"
      class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
    >
      Add to My List
    </button>

    <p v-if="added" class="mt-2 text-green-600 font-medium">Added!</p>

    <SubscribeForm />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import WordCard from '@/Components/WordCard.vue';
import SubscribeForm from '@/Components/SubscribeForm.vue';

const props = defineProps({
  wordOfTheDay: Object,
});

const added = ref(false);
const form = useForm({ word_id: props.wordOfTheDay.id });

function addWord() {
  form.post('/user/words', {
    onSuccess: () => (added.value = true),
  });
}
</script>
