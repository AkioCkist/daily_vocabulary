<template>
  <transition :name="showForFirstTime ? '' : 'hint-slide'" mode="out-in">
    <div v-if="hint && !answered" :key="hint" class="rounded-2xl bg-gradient-to-br from-yellow-500/20 via-amber-500/20 to-orange-500/20 border-2 border-yellow-400/60 shadow-xl shadow-yellow-500/20 backdrop-blur-sm">
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
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
  hint: {
    type: String,
    default: ''
  },
  answered: {
    type: Boolean,
    default: false
  }
});

const showForFirstTime = ref(false);
const displayedHint = ref('');

watch(() => props.hint, (newHint) => {
  if (newHint) {
    showForFirstTime.value = true;
  }
  displayedHint.value = newHint || '';
}, { immediate: true });
</script>

<style scoped>
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
