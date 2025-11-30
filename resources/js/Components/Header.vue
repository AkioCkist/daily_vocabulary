<template>
  <header
    class="bg-white/90 dark:bg-[#0B0C10]/90 backdrop-blur-md sticky top-0 z-50 border-b border-gray-200 dark:border-gray-800 transition-colors duration-300"
  >
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 py-4">
      <div class="flex items-center justify-between h-14">
        <!-- Logo Section -->
        <HeaderLogo />

        <!-- Navigation Section -->
        <HeaderNav>
          <template #authenticated v-if="user">
            <UserDropdown :user="user" />
          </template>

          <template #guest v-else>
            <AuthLinks />
          </template>
        </HeaderNav>
      </div>
    </div>
  </header>
</template>

<script setup>
import HeaderLogo from './Header/HeaderLogo.vue';
import HeaderNav from './Header/HeaderNav.vue';
import UserDropdown from './Header/UserDropdown.vue';
import AuthLinks from './Header/AuthLinks.vue';

/**
 * Header Component
 * Main header component that orchestrates all sub-components
 * Handles layout and composition of header elements
 *
 * Props:
 *   - user: Object | null - The currently authenticated user or null
 */
defineProps({
  user: {
    type: Object,
    default: null,
  },
});
</script>