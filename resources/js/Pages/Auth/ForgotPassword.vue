<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reset password</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Enter your email and we'll send you a link to reset your password
                </p>
            </div>

            <div
                v-if="status"
                class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="space-y-2">
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@example.com"
                    />

                    <InputError :message="form.errors.email" />
                </div>

                <PrimaryButton
                    class="w-full"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Sending...</span>
                    <span v-else>Send reset link</span>
                </PrimaryButton>

                <div class="text-center">
                    <Link
                        :href="route('login')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                        Back to sign in
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
