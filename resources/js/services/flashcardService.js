/**
 * Flashcard Service
 * Handles all flashcard-related API calls and routing
 */

import { router } from '@inertiajs/vue3';

export const startFlashcards = (settings) => {
  return router.post('/flashcards/start', settings, {
    onError: (errors) => {
      // Errors are passed to the calling component via the returned promise
      // This allows components to handle validation errors gracefully
      if (errors.words) {
        throw new Error(errors.words);
      }
      throw new Error('Failed to start flashcards');
    }
  });
};

export const startReview = (wordCount) => {
  return router.post('/flashcards/start', {
    mode: 'review',
    flashcard_type: 'standard',
    word_count: wordCount
  });
};

export const selectTopic = (topicId, wordCount = 10) => {
  return router.post('/flashcards/start', {
    mode: 'topic',
    flashcard_type: 'standard',
    topic_ids: [topicId],
    word_count: wordCount
  });
};

