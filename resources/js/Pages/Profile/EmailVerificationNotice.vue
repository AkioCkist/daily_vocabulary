<template>
  <div v-if="mustVerifyEmail && !user.email_verified_at"
       class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800">
    <form @submit.prevent="sendVerification" id="send-verification">
      <div class="flex flex-col sm:flex-row items-start sm:items-center text-sm text-gray-600 dark:text-gray-400">
        <div class="flex items-center gap-2 mb-2 sm:mb-0">
          <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="font-medium">Your email address is unverified.</p>
        </div>
        <button type="submit"
                class="sm:ml-4 underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900"
                :disabled="isSending">
          <span v-if="isSending">Sending...</span>
          <span v-else>Click here to re-send the verification email.</span>
        </button>
      </div>
      <p v-if="success"
         class="mt-2 text-sm text-green-600 dark:text-green-400 font-medium">
        A new verification link has been sent to your email address.
      </p>
      <p v-if="error"
         class="mt-2 text-sm text-red-600 dark:text-red-400 font-medium">
        {{ error }}
      </p>
    </form>
  </div>
</template>
<script setup>
import { useEmailVerification } from '@/composables/useEmailVerification';
const props = defineProps({
  mustVerifyEmail: Boolean,
  user: Object,
});
const { isSending, error, success, sendVerification } = useEmailVerification();
</script>
