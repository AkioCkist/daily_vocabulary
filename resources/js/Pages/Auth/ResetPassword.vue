<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create new password</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Enter a new password for your account</p>
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
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />

                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />

                    <InputError
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <PrimaryButton
                    class="w-full"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Resetting...</span>
                    <span v-else>Reset password</span>
                </PrimaryButton>
            </form>
        </div>
    </GuestLayout>
</template>
