<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="space-y-6">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Verify your email</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Thanks for signing up! Please verify your email address by clicking the link we sent you.
                </p>
            </div>

            <div
                class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400"
                v-if="verificationLinkSent"
            >
                A new verification link has been sent to your email address.
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <PrimaryButton
                    class="w-full"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Sending...</span>
                    <span v-else>Resend verification email</span>
                </PrimaryButton>

                <div class="text-center">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors dark:text-gray-400 dark:hover:text-gray-100"
                    >
                        Log out
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
