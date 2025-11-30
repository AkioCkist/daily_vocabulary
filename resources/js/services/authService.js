import { router } from '@inertiajs/vue3';

/**
 * Authentication Service
 * Handles all authentication-related API calls and redirects
 */
export const authService = {
  /**
   * Logout the current user
   * Sends POST request to logout endpoint and handles redirect
   */
  logout: async () => {
    return router.post(route('logout'));
  },
};
