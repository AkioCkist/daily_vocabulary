/**
 * Flashcard Actions Composable
 * Handles flashcard-related navigation and actions
 */

import { startFlashcards, startReview, selectTopic } from '@/services/flashcardService';

export const useFlashcardActions = () => {
  const handleStartFlashcards = (settings) => {
    try {
      return startFlashcards(settings);
    } catch (error) {
      console.error('useFlashcardActions - Error starting flashcards:', error);
      throw error;
    }
  };

  const handleStartReview = (wordCount) => {
    try {
      return startReview(wordCount);
    } catch (error) {
      console.error('useFlashcardActions - Error starting review:', error);
      throw error;
    }
  };

  const handleSelectTopic = (topic, wordCount = 10) => {
    try {
      return selectTopic(topic.id, wordCount);
    } catch (error) {
      console.error('useFlashcardActions - Error selecting topic:', error);
      throw error;
    }
  };

  const handleWordSelected = (word) => {
    console.log('Word selected:', word);
    // Can be extended with analytics or other logic
  };

  return {
    handleStartFlashcards,
    handleStartReview,
    handleSelectTopic,
    handleWordSelected
  };
};
