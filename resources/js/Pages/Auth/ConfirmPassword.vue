<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Panel from '@/Components/Panel.vue';
import TextInput from '@/Components/TextInput.vue';
import Button from '@/Components/Button.vue';
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
    <Head title="Confirm Password" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Confirm Password
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Panel 
                    class="max-w-xl mx-auto bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                    :header="'Confirm Password'"
                    :subHeader="'This is a secure area of the application. Please confirm your password before continuing.'"
                >
                <form @submit.prevent="submit">
                    <TextInput
                        class="block mt-1 w-full"
                        type="password"
                        name="password"
                        :label="'Password'"
                        v-model="form.password"
                        :error="form.errors.password"
                    />

                    <div class="flex justify-end mt-4">
                        <Button 
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Confirming...' : 'Confirm' }}
                        </Button>
                    </div>
                </form>
                </Panel>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
