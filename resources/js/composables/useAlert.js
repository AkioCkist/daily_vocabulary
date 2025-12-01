import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Composable for managing alert/popup messages
 */
export function useAlert() {
  const page = usePage();
  
  const alert = ref({
    isVisible: false,
    type: 'error', // 'error', 'success', 'warning', 'info'
    title: '',
    message: '',
    details: null
  });

  const showAlert = (title, message, type = 'error', details = null) => {
    alert.value = {
      isVisible: true,
      type,
      title,
      message,
      details
    };
  };

  const closeAlert = () => {
    alert.value.isVisible = false;
    // Clear validation errors to prevent re-showing
    if (page.props.errors) {
      page.props.errors = {};
    }
  };

  const showError = (title, message, details = null) => {
    showAlert(title, message, 'error', details);
  };

  const showSuccess = (title, message) => {
    showAlert(title, message, 'success');
  };

  const showWarning = (title, message) => {
    showAlert(title, message, 'warning');
  };

  const showInfo = (title, message) => {
    showAlert(title, message, 'info');
  };

  return {
    alert,
    showAlert,
    closeAlert,
    showError,
    showSuccess,
    showWarning,
    showInfo
  };
}

