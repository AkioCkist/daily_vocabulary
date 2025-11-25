<template>
  <div class="bg-gray-900 rounded-3xl shadow-2xl shadow-emerald-900/40 ring-1 ring-gray-700 overflow-hidden min-h-[480px] flex flex-col text-white">
    
    <div class="bg-gradient-to-r from-green-600 via-emerald-600 to-green-700 p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="px-3 py-1 bg-white/30 backdrop-blur-sm rounded-full text-sm font-bold text-gray-900">
            {{ word.cefr_level }}
          </span>
          <span class="text-sm font-medium text-green-100/70">{{ word.topic }}</span>
        </div>
        <slot name="actions"></slot>
      </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-10 min-h-[320px] bg-gray-900">
      <div class="text-center w-full space-y-6 py-4">
        
        <div class="space-y-4">
          <div class="flex items-center justify-center gap-2">
            <h3 class="text-3xl font-light text-gray-300 leading-relaxed">
              {{ word.definition }}
            </h3>
            <SpeakerButton :text="word.definition" />
          </div>
          
          <div v-if="word.example" class="max-w-xl mx-auto p-6 bg-gray-800 border border-gray-700 rounded-xl shadow-inner">
            <p class="text-xl italic font-light text-gray-300 whitespace-pre-line">
              {{ hiddenExample }}
            </p>
          </div>
        </div>

        <div class="max-w-md mx-auto space-y-4 pt-4">
          
          <div :class="{ 
            'bg-gray-800/80 border-gray-700': !answered,
            'bg-green-700/20 border-green-500/50 shadow-green-900/50': answered && isCorrect,
            'bg-red-700/20 border-red-500/50 shadow-red-900/50': answered && !isCorrect 
          }" class="rounded-xl p-3 border transition-all duration-300 shadow-xl">
            
            <input
              type="text"
              ref="answerInput"
              :value="localAnswer"
              @input="localAnswer = $event.target.value"
              :disabled="answered"
              @keyup.enter="!answered && $emit('submit')"
              class="w-full text-center text-2xl font-semibold bg-transparent border-none focus:ring-0 placeholder-gray-500 dark:text-white disabled:opacity-100 disabled:cursor-not-allowed p-2"
              placeholder="Type your answer here..."
            />
          </div>

          <!-- Hint Display -->
          <transition name="hint-slide" mode="out-in">
            <div v-if="currentHint && !answered" :key="currentHint" class="rounded-2xl bg-gradient-to-br from-yellow-500/20 via-amber-500/20 to-orange-500/20 border-2 border-yellow-400/60 shadow-xl shadow-yellow-500/20 backdrop-blur-sm">
              <div class="p-6 flex items-start gap-4">
                <!-- Lightbulb icon -->
                <div class="flex-shrink-0">
                  <svg class="w-8 h-8 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"/>
                  </svg>
                </div>
                
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-yellow-300 mb-2 uppercase tracking-wide">Hint</p>
                  <p class="text-2xl font-semibold text-white leading-relaxed">
                    <span
                      v-for="(char, index) in displayedHint"
                      :key="index"
                      class="inline-block char-pop"
                      :style="{ animationDelay: `${index * 0.03}s` }"
                    >
                      {{ char === ' ' ? '\u00A0' : char }}
                    </span>
                  </p>
                </div>
              </div>
            </div>
          </transition>

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
                  {{ word.word }}
                </p>
                <div v-if="word.part_of_speech" class="text-md italic text-gray-500 dark:text-gray-400">
                  ({{ word.part_of_speech }})
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>

    <div class="p-8 border-t border-gray-800 bg-gray-900/70">
      <div class="flex justify-center space-x-4">
        
        <div v-if="!answered" class="flex justify-center space-x-4">
          <button
            @click.stop="$emit('skip')"
            class="bg-gray-700 hover:bg-gray-600 text-gray-300 font-semibold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            Skip
          </button>
          
          <button
            @click.stop="$emit('hint')"
            :disabled="maxHintsReached"
            :class="[
              'font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2',
              maxHintsReached 
                ? 'bg-gray-600 text-gray-400' 
                : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-500/30'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ maxHintsReached ? 'No More Hints' : 'Hint' }}
          </button>
          
          <button
            @click.stop="$emit('submit')"
            :disabled="!localAnswer.trim()"
            class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-green-500/30 hover:shadow-xl hover:scale-105 active:scale-95 disabled:opacity-50 flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Submit
          </button>
        </div>
        
        <div v-else class="flex justify-center">
          <button
            @click.stop="$emit('next')"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-12 rounded-xl transition-all duration-300 shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:scale-105 active:scale-95 flex items-center gap-2"
          >
            Next Word
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Logic remains unchanged
import { computed, ref, watch, nextTick } from 'vue';
import SpeakerButton from '@/Components/Flashcard/SpeakerButton.vue';

const props = defineProps({
  word: {
    type: Object,
    required: true
  },
  userAnswer: {
    type: String,
    default: ''
  },
  showHint: {
    type: Boolean,
    default: false
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
const displayedHint = ref('');

watch(() => props.userAnswer, (newVal) => {
  localAnswer.value = newVal;
});

watch(localAnswer, (newVal) => {
  emit('update:userAnswer', newVal);
});

// Update displayed hint when currentHint changes
watch(() => props.currentHint, (newHint) => {
  displayedHint.value = newHint || '';
}, { immediate: true });

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
/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.fade-scale-enter-active, .fade-scale-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-scale-enter-from, .fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Hint slide animation */
.hint-slide-enter-active {
  animation: slideInDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.hint-slide-leave-active {
  animation: slideOutUp 0.4s cubic-bezier(0.4, 0, 1, 1);
}

@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-30px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes slideOutUp {
  from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  to {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
}

/* Character pop animation */
@keyframes charPop {
  0% {
    opacity: 0;
    transform: scale(0.5) translateY(10px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.char-pop {
  animation: charPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
  opacity: 0;
}
</style>