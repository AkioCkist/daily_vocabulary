import { ref, computed } from 'vue';

/**
 * Composable for managing flashcard state
 * Handles visibility toggle and definition state
 */
export function useFlashcardState() {
  const isDefinitionVisible = ref(false);

  /**
   * Toggle visibility of the definition
   */
  const toggleDefinition = () => {
    isDefinitionVisible.value = !isDefinitionVisible.value;
  };

  /**
   * Show the definition
   */
  const showDefinition = () => {
    isDefinitionVisible.value = true;
  };

  /**
   * Hide the definition
   */
  const hideDefinition = () => {
    isDefinitionVisible.value = false;
  };

  /**
   * Reset to initial state
   */
  const reset = () => {
    isDefinitionVisible.value = false;
  };

  return {
    isDefinitionVisible: computed(() => isDefinitionVisible.value),
    toggleDefinition,
    showDefinition,
    hideDefinition,
    reset
  };
}
