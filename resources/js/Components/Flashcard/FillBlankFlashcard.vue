<template>
  <div class="bg-gray-900 rounded-3xl shadow-2xl shadow-emerald-900/40 ring-1 ring-gray-700 overflow-hidden min-h-[480px] flex flex-col text-white">
    
    <!-- Header -->
    <FillBlankHeader 
      :cefr-level="word.cefr_level"
      :topic="word.topic"
    >
      <template #actions>
        <slot name="actions"></slot>
      </template>
    </FillBlankHeader>

    <!-- Content with Input and Hints -->
    <FillBlankContent 
      :definition="word.definition"
      :example="word.example"
      :hidden-example="hiddenExample"
      :user-answer="localUserAnswer"
      :answered="answered"
      :is-correct="isCorrect"
      @update:userAnswer="localUserAnswer = $event"
      @submit="$emit('submit')"
      ref="contentRef"
    >
      <template #hint>
        <HintDisplay 
          :hint="currentHint"
          :answered="answered"
        />
      </template>
      
      <!-- Feedback -->
      <FillBlankFeedback 
        :answered="answered"
        :is-correct="isCorrect"
        :correct-word="word.word"
        :part-of-speech="word.part_of_speech"
      />
    </FillBlankContent>

    <!-- Action Buttons -->
    <FillBlankActions 
      :answered="answered"
      :user-answer="localUserAnswer"
      :max-hints-reached="maxHintsReached"
      @submit="$emit('submit')"
      @hint="$emit('hint')"
      @skip="$emit('skip')"
      @next="$emit('next')"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { useHiddenExample } from '@/composables/useHiddenExample';
import FillBlankHeader from '@/Components/Flashcard/FillBlankHeader.vue';
import FillBlankContent from '@/Components/Flashcard/FillBlankContent.vue';
import FillBlankActions from '@/Components/Flashcard/FillBlankActions.vue';
import FillBlankFeedback from '@/Components/Flashcard/FillBlankFeedback.vue';
import HintDisplay from '@/Components/Flashcard/HintDisplay.vue';

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
const contentRef = ref(null);
const localUserAnswer = ref(props.userAnswer);

const { hiddenExample } = useHiddenExample(props.word.word, props.word.example);

// Sync local answer with parent prop
watch(() => props.userAnswer, (newVal) => {
  localUserAnswer.value = newVal;
});

// Emit updates to parent
watch(localUserAnswer, (newVal) => {
  emit('update:userAnswer', newVal);
});

defineExpose({
  focus: () => {
    nextTick(() => {
      contentRef.value?.$refs.answerInput?.focus();
    });
  }
});
</script>