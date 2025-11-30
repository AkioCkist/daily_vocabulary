import { ref } from 'vue';

/**
 * Composable for managing dropdown state and interactions
 * Handles open/close logic with a clean interface
 */
export function useDropdown() {
  const isOpen = ref(false);

  const toggle = () => {
    isOpen.value = !isOpen.value;
  };

  const close = () => {
    isOpen.value = false;
  };

  const open = () => {
    isOpen.value = true;
  };

  return {
    isOpen,
    toggle,
    close,
    open,
  };
}
