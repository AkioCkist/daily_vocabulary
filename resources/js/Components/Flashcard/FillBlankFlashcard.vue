<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden min-h-[480px] flex flex-col">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 via-emerald-500 to-green-600 text-white p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
            {{ word.cefr_level }}
          </span>
          <span class="text-sm opacity-90">{{ word.topic }}</span>
        </div>
        <slot name="actions"></slot>
      </div>
    </div>

    <!-- Content -->
    <div class="flex-1 flex items-center justify-center p-8 min-h-[320px]">
      <div class="text-center w-full space-y-6 py-4">
        <!-- Definition -->
        <div class="space-y-4">
          <h3 class="text-3xl font-semibold text-gray-900 dark:text-white leading-relaxed pb-2">
            {{ word.definition }}
          </h3>
          
          <div v-if="word.example" class="max-w-lg mx-auto p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
            <p class="text-gray-700 dark:text-gray-300 italic font-mono">
              "{{ hiddenExample }}"
            </p>
          </div>
        </div>

        <!-- Hint Display -->
        <transition name="slide-down">
          <div v-if="currentHint" class="max-w-md mx-auto p-4 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
              </svg>
              <span class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Hint</span>
            </div>
            <div class="text-2xl font-mono font-bold text-yellow-900 dark:text-yellow-100 tracking-wider">
              {{ currentHint }}
            </div>
          </div>
        </transition>

        <!-- Input Field -->
        <transition name="fade-scale">
          <div v-if="!answered" class="max-w-md mx-auto">
            <input
              ref="answerInput"
              v-model="localAnswer"
              type="text"
              placeholder="Type the word here..."
              class="w-full px-6 py-4 text-2xl text-center rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
              @keyup.enter="$emit('submit')"
            >
          </div>
        </transition>

        <!-- Answer Feedback -->
        <transition name="fade-scale">
          <div v-if="answered" class="max-w-md mx-auto p-6 rounded-xl" :class="isCorrect ? 'bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20' : 'bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20'">
            <div class="flex items-center justify-center gap-3 mb-3">
              <svg v-if="isCorrect" class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
              </svg>
              <svg v-else class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              <span class="text-2xl font-bold" :class="isCorrect ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'">
                {{ isCorrect ? 'Correct!' : 'Incorrect' }}
              </span>
            </div>
            <div class="space-y-2">
              <div class="text-gray-700 dark:text-gray-300">
                <span class="font-medium">Correct answer:</span>
                <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">{{ word.word }}</span>
              </div>
              <div v-if="!isCorrect && localAnswer" class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Your answer:</span>
                <span class="ml-2 line-through">{{ localAnswer }}</span>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </div>

    <!-- Actions -->
    <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 border-t border-gray-200 dark:border-gray-600">
      <transition name="fade" mode="out-in">
        <div v-if="!answered" :key="'actions'" class="flex gap-3 justify-center flex-wrap">
          <button
            @click.stop="$emit('submit')"
            :disabled="!localAnswer.trim()"
            class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 disabled:scale-100 disabled:shadow-none"
          >
            Submit Answer
          </button>
          <button
            @click.stop="$emit('hint')"
            :disabled="maxHintsReached"
            class="bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 disabled:scale-100 disabled:shadow-none"
          >
            {{ maxHintsReached ? 'No More Hints' : 'Get Hint' }}
          </button>
          <button
            @click.stop="$emit('skip')"
            class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
          >
            Skip
          </button>
        </div>

        <div v-else :key="'next'" class="text-center">
          <button
            @click="$emit('next')"
            class="group bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 hover:from-indigo-600 hover:via-purple-600 hover:to-purple-700 text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 flex items-center justify-center gap-2 mx-auto"
          >
            Next Word
            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </button>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';

const props = defineProps({
  word: {
    type: Object,
    required: true
  },
  userAnswer: {
    type: String,
    default: ''
  },
  currentHint: {
    type: String,
    default: ''
  },
  maxHintsReached: {
    type: Boolean,
    default: false
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

const emit = defineEmits(['update:userAnswer', 'submit', 'hint', 'skip', 'next']);

const answerInput = ref(null);
const localAnswer = ref(props.userAnswer);

watch(() => props.userAnswer, (newVal) => {
  localAnswer.value = newVal;
});

watch(localAnswer, (newVal) => {
  emit('update:userAnswer', newVal);
});

const hiddenExample = computed(() => {
  if (!props.word.example || !props.word.word) return props.word.example;
  const regex = new RegExp(`\\b${props.word.word}\\b`, 'gi');
  return props.word.example.replace(regex, (match) => '_'.repeat(match.length));
});

defineExpose({
  focus: () => {
    nextTick(() => {
      answerInput.value?.focus();
    });
  }
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.fade-scale-enter-active, .fade-scale-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-scale-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(8px);
}
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-8px);
}

.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-15px);
}
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
