import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { selectTopic } from '@/services/flashcardService';

/**
 * Composable for managing topic CRUD operations
 */
export function useTopicActions() {
  const isUpdating = ref(false);
  const editingTopic = ref(null);

  const startEdit = (topic) => {
    editingTopic.value = {
      id: topic.id,
      name: topic.name,
      description: topic.description || ''
    };
  };

  const submitUpdate = (topicData) => {
    return new Promise((resolve, reject) => {
      isUpdating.value = true;

      router.put(`/topics/${topicData.id}`, {
        name: topicData.name,
        description: topicData.description
      }, {
        onSuccess: () => {
          editingTopic.value = null;
          resolve();
        },
        onError: (errors) => {
          reject(errors);
        },
        onFinish: () => {
          isUpdating.value = false;
        }
      });
    });
  };

  const cancelEdit = () => {
    editingTopic.value = null;
  };

  const deleteTopic = (topic) => {
    if (!confirm(`Are you sure you want to delete "${topic.name}"?`)) {
      return Promise.reject('Cancelled by user');
    }

    return new Promise((resolve, reject) => {
      router.delete(`/topics/${topic.id}`, {
        onSuccess: () => {
          resolve();
        },
        onError: (errors) => {
          reject(errors);
        }
      });
    });
  };

  const studyTopic = (topic) => {
    selectTopic(topic.id, 10);
  };

  return {
    isUpdating,
    editingTopic,
    startEdit,
    submitUpdate,
    cancelEdit,
    deleteTopic,
    studyTopic
  };
}
