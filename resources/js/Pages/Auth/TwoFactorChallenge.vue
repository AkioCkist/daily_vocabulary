<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const toggleRecovery = () => {
    recovery.value = !recovery.value;
    form.reset();
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Two Factor Authentication" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Two Factor Authentication
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <template v-if="!recovery">
                        Please confirm access to your account by entering the authentication code provided by your authenticator application.
                    </template>
                    <template v-else>
                        Please confirm access to your account by entering one of your emergency recovery codes.
                    </template>
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div v-if="!recovery" class="space-y-2">
                    <InputLabel for="code" value="Code" />

                    <TextInput
                        id="code"
                        type="text"
                        inputmode="numeric"
                        v-model="form.code"
                        required
                        autofocus
                        autocomplete="one-time-code"
                        placeholder="000000"
                    />

                    <InputError :message="form.errors.code" />
                </div>

                <div v-else class="space-y-2">
                    <InputLabel for="recovery_code" value="Recovery Code" />

                    <TextInput
                        id="recovery_code"
                        type="text"
                        v-model="form.recovery_code"
                        required
                        autocomplete="one-time-code"
                        placeholder="abcd-efgh"
                    />

                    <InputError :message="form.errors.recovery_code" />
                </div>

                <div class="flex items-center justify-end">
                    <button
                        type="button"
                        @click="toggleRecovery"
                        class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                    >
                        <template v-if="!recovery">
                            Use a recovery code
                        </template>
                        <template v-else>
                            Use an authentication code
                        </template>
                    </button>
                </div>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="w-full justify-center"
                >
                    Log in
                </PrimaryButton>
            </form>
        </div>
    </GuestLayout>
</template>
