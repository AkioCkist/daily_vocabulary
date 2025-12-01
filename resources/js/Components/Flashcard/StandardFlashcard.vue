<template>
  <div class="bg-gray-900 rounded-3xl shadow-2xl shadow-indigo-900/40 ring-1 ring-gray-700 overflow-hidden min-h-[480px] flex flex-col text-white">
    
    <!-- Header with CEFR Level and Topic -->
    <FlashcardHeader 
      :cefr-level="word.cefr_level"
      :topic="word.topic"
    >
      <template #actions>
        <slot name="actions"></slot>
      </template>
    </FlashcardHeader>

    <!-- Main Content Area (Word/Definition) -->
    <FlashcardContent 
      :word="word.word"
      :definition="word.definition"
      :part-of-speech="word.part_of_speech"
      :example="word.example"
      :is-definition-visible="isDefinitionVisible"
      @toggle="toggleDefinition"
    />

    <!-- Action Buttons Footer -->
    <FlashcardFooter 
      :is-definition-visible="isDefinitionVisible"
      @answer="$emit('answer', $event)"
    />
  </div>
</template>

<script setup>
import { useFlashcardState } from '@/composables/useFlashcardState';
import FlashcardHeader from '@/Components/Flashcard/FlashcardHeader.vue';
import FlashcardContent from '@/Components/Flashcard/FlashcardContent.vue';
import FlashcardFooter from '@/Components/Flashcard/FlashcardFooter.vue';

const { isDefinitionVisible, toggleDefinition } = useFlashcardState();

defineProps({
  word: {
    type: Object,
    required: true
  }
});

defineEmits(['answer']);
</script>