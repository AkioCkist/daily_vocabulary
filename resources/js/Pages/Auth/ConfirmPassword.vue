<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Confirm your password</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    This is a secure area. Please confirm your password to continue.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="space-y-2">
                    <InputLabel for="password" value="Password" />
                    
                    <TextInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="••••••••"
                    />
                    
                    <InputError :message="form.errors.password" />
                </div>

                <PrimaryButton
                    class="w-full"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Confirming...</span>
                    <span v-else>Confirm</span>
                </PrimaryButton>
            </form>
        </div>
    </GuestLayout>
</template>
