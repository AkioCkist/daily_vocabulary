import { ref } from 'vue';
import { profileService } from '../services/profileService';

/**
 * Handles email verification resend logic
 */
export function useEmailVerification() {
  const isSending = ref(false);
  const error = ref(null);
  const success = ref(false);

  const sendVerification = async () => {
    isSending.value = true;
    error.value = null;
    success.value = false;
    try {
      await profileService.sendVerification();
      success.value = true;
    } catch (e) {
      error.value = e.message || 'Failed to send verification.';
    } finally {
      isSending.value = false;
    }
  };

  return { isSending, error, success, sendVerification };
}
