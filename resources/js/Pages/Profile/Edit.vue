<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import TwoFactorAuthentication from './Partials/TwoFactorAuthentication.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const status = computed(() => page.props.status);

const verificationForm = useForm({});

const sendVerification = () => {
    verificationForm.post(route('verification.send'));
};

onMounted(() => {
    if (status.value && ['two-factor-authentication-enabled', 'two-factor-authentication-confirmed'].includes(status.value)) {
        const element = document.getElementById('two-factor-authentication');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Profile
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <!-- Email Verification Notice -->
                <div
                    v-if="mustVerifyEmail && !user.email_verified_at"
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <form @submit.prevent="sendVerification" id="send-verification">
                        <div class="flex flex-row items-center text-2xl text-gray-600 dark:text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-400 ml-2">
                                Your email address is unverified.

                                <button 
                                    type="submit"
                                    class="underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                >
                                    Click here to re-send the verification email.
                                </button>
                            </p>
                        </div>
                        <p 
                            v-if="status === 'verification-link-sent'" 
                            class="mt-2 px-2 text-sm text-gray-600 dark:text-gray-400"
                        >
                            A new verification link has been sent to your email address.
                        </p>
                    </form>
                </div>

                <!-- Profile Information -->
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Update Password -->
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- Two Factor Authentication -->
                <div
                    id="two-factor-authentication"
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <header class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Two Factor Authentication
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Add additional security to your account using two factor authentication.
                        </p>
                    </header>
                    <div class="max-w-xl">
                        <TwoFactorAuthentication />
                    </div>
                </div>

                <!-- Delete User -->
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <div class="max-w-xl">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
