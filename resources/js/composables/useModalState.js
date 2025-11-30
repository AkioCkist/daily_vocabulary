/**
 * Modal State Composable
 * Centralized management of all modal visibility states
 */

import { ref } from 'vue';

export const useModalState = () => {
  const showFlashcardModal = ref(false);
  const showTopicModal = ref(false);
  const showSaveSessionModal = ref(false);
  const showMemoryModal = ref(false);
  const saveSessionData = ref(null);

  const openFlashcardModal = () => {
    showFlashcardModal.value = true;
  };

  const closeFlashcardModal = () => {
    showFlashcardModal.value = false;
  };

  const openTopicModal = () => {
    showTopicModal.value = true;
  };

  const closeTopicModal = () => {
    showTopicModal.value = false;
  };

  const openSaveSessionModal = (sessionData) => {
    saveSessionData.value = sessionData;
    showSaveSessionModal.value = true;
  };

  const closeSaveSessionModal = () => {
    showSaveSessionModal.value = false;
    saveSessionData.value = null;
  };

  const openMemoryModal = () => {
    showMemoryModal.value = true;
  };

  const closeMemoryModal = () => {
    showMemoryModal.value = false;
  };

  return {
    // States
    showFlashcardModal,
    showTopicModal,
    showSaveSessionModal,
    showMemoryModal,
    saveSessionData,
    // Handlers
    openFlashcardModal,
    closeFlashcardModal,
    openTopicModal,
    closeTopicModal,
    openSaveSessionModal,
    closeSaveSessionModal,
    openMemoryModal,
    closeMemoryModal
  };
};
