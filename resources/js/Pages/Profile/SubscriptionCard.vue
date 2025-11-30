<template>
  <div class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg">
    <SubscriptionSettings :initial="initial" />
  </div>
</template>
<script setup>
import SubscriptionSettings from './Partials/SubscriptionSettings.vue';
const props = defineProps({
  initial: Object,
});
</script>
