<template>
  <div 
    class="flex-1 flex items-center justify-center p-10 min-h-[320px] cursor-pointer bg-gray-900 hover:bg-gray-800 transition-colors duration-300 relative"
    @click="!isDefinitionVisible && $emit('toggle')"
  >
    <div class="text-center w-full">
      <transition name="fade-scale" mode="out-in">
        
        <!-- Word View -->
        <div v-if="!isDefinitionVisible" :key="'word'" class="space-y-4">
          <h2 class="text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-400 leading-tight">
            {{ word }}
          </h2>
          <div class="text-xl font-medium text-indigo-200/80">
            <span v-if="partOfSpeech" class="italic">{{ partOfSpeech }}</span>
          </div>
        </div>
        
        <!-- Definition View -->
        <div v-else :key="'definition'" class="space-y-6">
          <div class="flex items-center justify-center gap-3">
            <h2 class="text-5xl font-extrabold text-white leading-snug">
              {{ word }}
            </h2>
            <SpeakerButton :text="word" />
          </div>
          
          <p class="text-2xl text-gray-300 font-light leading-relaxed max-w-xl mx-auto">
            {{ definition }}
          </p>

          <div v-if="example" class="mt-4 max-w-xl mx-auto p-4 rounded-xl bg-gray-800 border border-gray-700 shadow-inner">
            <p class="text-sm text-indigo-400 italic mb-2 font-semibold">Example:</p>
            <p class="text-lg text-gray-400 mt-1">
              {{ example }}
            </p>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import SpeakerButton from '@/Components/Flashcard/SpeakerButton.vue';

defineProps({
  word: {
    type: String,
    required: true
  },
  definition: {
    type: String,
    required: true
  },
  partOfSpeech: {
    type: String,
    default: null
  },
  example: {
    type: String,
    default: null
  },
  isDefinitionVisible: {
    type: Boolean,
    default: false
  }
});

defineEmits(['toggle']);
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
