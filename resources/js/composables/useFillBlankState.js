import { ref, watch } from 'vue';

/**
 * Composable for managing fill-in-the-blank flashcard state
 */
export function useFillBlankState(initialWord) {
  const userAnswer = ref('');
  const currentHint = ref('');
  const answered = ref(false);
  const isCorrect = ref(false);
  const hintsUsed = ref(0);

  /**
   * Reset state for a new word
   */
  const reset = () => {
    userAnswer.value = '';
    currentHint.value = '';
    answered.value = false;
    isCorrect.value = false;
    hintsUsed.value = 0;
  };

  /**
   * Check answer and set correctness
   */
  const submitAnswer = (correctWord) => {
    answered.value = true;
    // Normalize comparison (case-insensitive, trim whitespace)
    const normalizedAnswer = userAnswer.value.toLowerCase().trim();
    const normalizedCorrect = correctWord.toLowerCase().trim();
    isCorrect.value = normalizedAnswer === normalizedCorrect;
    return isCorrect.value;
  };

  /**
   * Add a hint
   */
  const addHint = (hint) => {
    currentHint.value = hint;
    hintsUsed.value++;
  };

  /**
   * Check if max hints reached
   */
  const maxHintsReached = (maxHints) => {
    return hintsUsed.value >= maxHints;
  };

  /**
   * Skip to next word
   */
  const skip = () => {
    reset();
  };

  // Reset when word changes
  watch(() => initialWord, () => {
    reset();
  });

  return {
    userAnswer,
    currentHint,
    answered,
    isCorrect,
    hintsUsed,
    reset,
    submitAnswer,
    addHint,
    maxHintsReached,
    skip
  };
}
