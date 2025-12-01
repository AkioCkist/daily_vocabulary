<template>
  <transition name="fade-scale" mode="out-in">
    <div v-if="answered" :key="'feedback'" class="space-y-4">
      <div v-if="isCorrect" class="text-2xl font-bold text-green-400 flex items-center justify-center gap-2">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        Correct!
      </div>
      <div v-else :key="'incorrect'" class="text-center space-y-2">
        <p class="text-xl font-bold text-red-400 flex items-center justify-center gap-2">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
          Incorrect.
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">The correct word was:</p>
        <p class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-green-400 px-3 py-1 rounded-lg inline-block">
          {{ correctWord }}
        </p>
        <div v-if="partOfSpeech" class="text-md italic text-gray-500 dark:text-gray-400">
          ({{ partOfSpeech }})
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({
  answered: {
    type: Boolean,
    default: false
  },
  isCorrect: {
    type: Boolean,
    default: false
  },
  correctWord: {
    type: String,
    required: true
  },
  partOfSpeech: {
    type: String,
    default: null
  }
});
</script>

<style scoped>
.fade-scale-enter-active, .fade-scale-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-scale-enter-from, .fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
