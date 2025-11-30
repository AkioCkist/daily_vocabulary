import { onMounted } from 'vue';

/**
 * Handles status logic and smooth scroll for 2FA section
 */
export function useProfileStatus(status) {
  const scrollToTwoFactor = () => {
    if (status && ['two-factor-authentication-enabled', 'two-factor-authentication-confirmed'].includes(status)) {
      const element = document.getElementById('two-factor-authentication');
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
      }
    }
  };

  onMounted(scrollToTwoFactor);

  return { scrollToTwoFactor };
}
