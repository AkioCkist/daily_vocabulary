import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * Composable for managing topic creation form
 */
export function useTopicForm() {
  const isCreating = ref(false);
  const createError = ref('');

  const formData = reactive({
    name: '',
    description: ''
  });

  const resetForm = () => {
    formData.name = '';
    formData.description = '';
    createError.value = '';
  };

  const submitCreate = (topicData) => {
    return new Promise((resolve, reject) => {
      isCreating.value = true;
      createError.value = '';

      router.post('/topics', topicData, {
        onSuccess: () => {
          resetForm();
          resolve();
        },
        onError: (errors) => {
          createError.value = errors.name?.[0] || 'Failed to create topic';
          reject(errors);
        },
        onFinish: () => {
          isCreating.value = false;
        }
      });
    });
  };

  return {
    formData,
    isCreating,
    createError,
    resetForm,
    submitCreate
  };
}
