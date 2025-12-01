import { computed } from 'vue';

/**
 * Composable for masking word in example text
 */
export function useHiddenExample(word, example) {
  const hiddenExample = computed(() => {
    if (!example || !word) return example;
    const regex = new RegExp(`\\b${word}\\b`, 'gi');
    return example.replace(regex, (match) => '_'.repeat(match.length));
  });

  return {
    hiddenExample
  };
}
