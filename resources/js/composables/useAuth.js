import { router } from '@inertiajs/vue3';
import { authService } from '../services/authService';

/**
 * Composable for authentication-related actions
 * Handles user logout and authentication flows
 */
export function useAuth() {
  const logout = async () => {
    try {
      await authService.logout();
      // authService handles the redirect via router.post()
    } catch (error) {
      console.error('Logout failed:', error);
    }
  };

  return {
    logout,
  };
}
