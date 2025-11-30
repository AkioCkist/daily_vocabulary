<script setup>
import Header from '@/Components/Header.vue';
import ProfileHeader from './ProfileHeader.vue';
import EmailVerificationNotice from './EmailVerificationNotice.vue';
import ProfileInfoCard from './ProfileInfoCard.vue';
import PasswordCard from './PasswordCard.vue';
import SubscriptionCard from './SubscriptionCard.vue';
import TwoFactorCard from './TwoFactorCard.vue';
import DeleteUserCard from './DeleteUserCard.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  mustVerifyEmail: Boolean,
  status: String,
  subscriptionPreferences: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const status = computed(() => page.props.status);
</script>

<template>
  <Head title="Profile Settings - DailyVocab" />
  <div class="min-h-screen bg-gray-50 dark:bg-[#0B0C10] text-slate-900 dark:text-slate-100 font-sans">
    <Header :user="user" />
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
      <ProfileHeader />
      <div class="space-y-6">
        <EmailVerificationNotice :must-verify-email="mustVerifyEmail" :user="user" />
        <ProfileInfoCard :must-verify-email="mustVerifyEmail" :status="status" />
        <PasswordCard />
        <SubscriptionCard :initial="props.subscriptionPreferences || undefined" />
        <TwoFactorCard :status="status" />
        <DeleteUserCard />
      </div>
    </div>
  </div>
</template>