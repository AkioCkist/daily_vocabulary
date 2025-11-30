import { router } from '@inertiajs/vue3';

/**
 * Profile API service
 */
export const profileService = {
  sendVerification: async () => {
    return router.post(route('verification.send'));
  },
  // Add more profile-related API calls here
};
