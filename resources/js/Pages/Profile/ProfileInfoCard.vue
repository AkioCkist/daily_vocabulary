<template>
  <div class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg">
    <UpdateProfileInformationForm
      :must-verify-email="mustVerifyEmail"
      :status="status"
      class="max-w-xl"
    />
  </div>
</template>
<script setup>
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
const props = defineProps({
  mustVerifyEmail: Boolean,
  status: String,
});
</script>
