<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Sign in to your account to continue</p>
            </div>

            <div v-if="status" class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400">
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

                <div class="space-y-2">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />

                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center group cursor-pointer">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors dark:text-gray-400 dark:group-hover:text-gray-300"
                            >Remember me</span
                        >
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                        Forgot password?
                    </Link>
                </div>

                <PrimaryButton
                    class="w-full"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Signing in...</span>
                    <span v-else>Sign in</span>
                </PrimaryButton>

                <div class="text-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Don't have an account? </span>
                    <Link
                        :href="route('register')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                        Sign up
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
