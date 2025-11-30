<template>
  <div class="flex items-center gap-3">
    <!-- Login Link -->
    <Link
      href="/login"
      class="px-4 py-2 text-sm font-medium transition-all duration-300 text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400 rounded-lg"
    >
      Login
    </Link>

    <!-- Sign Up Button -->
    <Link
      href="/register"
      class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-300 shadow-md shadow-indigo-600/30 dark:shadow-indigo-500/50"
    >
      Sign Up Free
    </Link>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * AuthLinks Component
 * Displays authentication links for unauthenticated users
 * Shows Login and Sign Up buttons
 */
</script>
