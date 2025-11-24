<script setup>
import Header from '@/Components/Header.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import TwoFactorAuthentication from './Partials/TwoFactorAuthentication.vue';
import SubscriptionSettings from './Partials/SubscriptionSettings.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    subscriptionPreferences: Object,
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
    <Head title="Profile Settings - DailyVocab" />
    
    <div class="min-h-screen bg-gray-50 dark:bg-[#0B0C10] text-slate-900 dark:text-slate-100 font-sans">
        
        <Header :user="user" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            
            <div class="mb-10 pt-4">
                <div class="flex items-center gap-3">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.942 3.313.804 2.454 2.288a1.724 1.724 0 00.58 2.378c1.5 1.488 1.5 3.518 0 5.006a1.724 1.724 0 00-.58 2.378c.859 1.484-.91 3.23-2.454 2.288a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.942-3.313-.804-2.454-2.288a1.724 1.724 0 00-.58-2.378c-1.5-1.488-1.5-3.518 0-5.006a1.724 1.724 0 00.58-2.378c-.859-1.484.91-3.23 2.454-2.288a1.724 1.724 0 002.573-1.066z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h1 class="text-3xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                        Account Settings
                    </h1>
                </div>
                <p class="mt-1 ml-10 text-gray-500 dark:text-gray-400">Manage your profile, security, and preferences.</p>
            </div>

            <div class="space-y-6">
                
                <div
                    v-if="mustVerifyEmail && !user.email_verified_at"
                    class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800"
                >
                    <form @submit.prevent="sendVerification" id="send-verification">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2 mb-2 sm:mb-0">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="font-medium">
                                    Your email address is unverified.
                                </p>
                            </div>

                            <button 
                                type="submit"
                                class="sm:ml-4 underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900"
                            >
                                Click here to re-send the verification email.
                            </button>
                        </div>
                        <p 
                            v-if="status === 'verification-link-sent'" 
                            class="mt-2 text-sm text-green-600 dark:text-green-400 font-medium"
                        >
                            A new verification link has been sent to your email address.
                        </p>
                    </form>
                </div>

                <div
                    class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg"
                >
                    <SubscriptionSettings :initial="props.subscriptionPreferences || undefined" />
                </div>

                <div
                    id="two-factor-authentication"
                    class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg"
                >
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

                <div
                    class="bg-white dark:bg-[#111216] p-6 sm:p-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg"
                >
                    <div class="max-w-xl">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>