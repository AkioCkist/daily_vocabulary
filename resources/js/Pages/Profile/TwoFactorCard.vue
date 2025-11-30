<template>
  <div id="two-factor-authentication"
       class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg">
    <header class="mb-6">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
        Two Factor Authentication
      </h2>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Add additional security to your account using two factor authentication.
      </p>
    </header>
    <div class="max-w-xl">
      <TwoFactorAuthentication />
    </div>
  </div>
</template>
<script setup>
import { useProfileStatus } from '@/composables/useProfileStatus';
import TwoFactorAuthentication from './Partials/TwoFactorAuthentication.vue';
const props = defineProps({ status: String });
useProfileStatus(props.status);
</script>
