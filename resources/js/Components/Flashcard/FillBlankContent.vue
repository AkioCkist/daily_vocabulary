<template>
  <div class="flex-1 flex items-center justify-center p-10 min-h-[320px] bg-gray-900">
    <div class="text-center w-full space-y-6 py-4">
      
      <div class="space-y-4">
        <div class="flex items-center justify-center gap-2">
          <h3 class="text-3xl font-light text-gray-300 leading-relaxed">
            {{ definition }}
          </h3>
          <SpeakerButton :text="definition" />
        </div>
        
        <div v-if="example" class="max-w-xl mx-auto p-6 bg-gray-800 border border-gray-700 rounded-xl shadow-inner">
          <p class="text-xl italic font-light text-gray-300 whitespace-pre-line">
            {{ hiddenExample }}
          </p>
        </div>
      </div>

      <div class="max-w-md mx-auto space-y-4 pt-4">
        
        <!-- Answer Input Field -->
        <div :class="{ 
          'bg-gray-800/80 border-gray-700': !answered,
          'bg-green-700/20 border-green-500/50 shadow-green-900/50': answered && isCorrect,
          'bg-red-700/20 border-red-500/50 shadow-red-900/50': answered && !isCorrect 
        }" class="rounded-xl p-3 border transition-all duration-300 shadow-xl">
          
          <input
            type="text"
            ref="answerInput"
            :value="userAnswer"
            @input="$emit('update:userAnswer', $event.target.value)"
            :disabled="answered"
            @keyup.enter="!answered && $emit('submit')"
            class="w-full text-center text-2xl font-semibold bg-transparent border-none focus:ring-0 placeholder-gray-500 dark:text-white disabled:opacity-100 disabled:cursor-not-allowed p-2"
            placeholder="Type your answer here..."
          />
        </div>

        <!-- Hint Display -->
        <slot name="hint"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import SpeakerButton from '@/Components/Flashcard/SpeakerButton.vue';

defineProps({
  definition: {
    type: String,
    required: true
  },
  example: {
    type: String,
    default: null
  },
  hiddenExample: {
    type: String,
    default: null
  },
  userAnswer: {
    type: String,
    default: ''
  },
  answered: {
    type: Boolean,
    default: false
  },
  isCorrect: {
    type: Boolean,
    default: false
  }
});

defineEmits(['update:userAnswer', 'submit']);

const answerInput = ref(null);

defineExpose({
  focus: () => {
    answerInput.value?.focus();
  }
});
</script>
