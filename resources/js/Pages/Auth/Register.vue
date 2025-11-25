<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
// InputLabel is kept in script but will not be used in the template for a cleaner look
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Functionality Preserved
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Functionality Preserved
const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Sign up - DailyVocab" />

        <div class="w-full max-w-sm bg-[#0B0C10]/90 backdrop-blur-md p-6 sm:p-8 rounded-xl shadow-2xl shadow-indigo-900/40 ring-1 ring-indigo-900/50 text-white animate-fade-in">
            
            <h2 class="text-2xl font-semibold mb-6">Sign up</h2>

            <form @submit.prevent="submit" class="space-y-4">
                
                <div>
                    <TextInput
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Name"
                        class="w-full text-white bg-white/5 border-transparent placeholder-gray-400 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/80 focus:ring-offset-2 focus:ring-offset-[#0B0C10]"
                    />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div>
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="Email"
                        class="w-full text-white bg-white/5 border-transparent placeholder-gray-400 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/80 focus:ring-offset-2 focus:ring-offset-[#0B0C10]"
                    />
                    <InputError :message="form.errors.email" class="mt-1" />
                </div>

                <div>
                    <TextInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="Password"
                        class="w-full text-white bg-white/5 border-transparent placeholder-gray-400 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/80 focus:ring-offset-2 focus:ring-offset-[#0B0C10]"
                    />
                    <InputError :message="form.errors.password" class="mt-1" />
                </div>

                <div>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm Password"
                        class="w-full text-white bg-white/5 border-transparent placeholder-gray-400 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/80 focus:ring-offset-2 focus:ring-offset-[#0B0C10]"
                    />
                    <InputError
                        :message="form.errors.password_confirmation"
                        class="mt-1"
                    />
                </div>
                
                <PrimaryButton
                    class="w-full justify-center bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 active:from-indigo-700 active:to-purple-800 transition-all font-semibold py-2 mt-4 text-white rounded-lg shadow-lg shadow-indigo-600/50"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Creating account...</span>
                    <span v-else>Continue</span>
                </PrimaryButton>
                
                <div class="flex items-center space-x-2 py-2">
                    <div class="flex-grow border-t border-white/10"></div>
                    <span class="text-sm font-medium text-gray-400">or</span>
                    <div class="flex-grow border-t border-white/10"></div>
                </div>

                <div class="space-y-3">
                    <button type="button" class="w-full flex items-center justify-center space-x-3 py-2 border border-white/20 bg-black/20 rounded-lg text-sm font-medium text-white/90 hover:bg-white/10 transition-colors shadow-sm hover:shadow-indigo-500/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                        <span>Continue with GitHub</span>
                    </button>
                    <button type="button" class="w-full flex items-center justify-center space-x-3 py-2 border border-white/20 bg-black/20 rounded-lg text-sm font-medium text-white/90 hover:bg-white/10 transition-colors shadow-sm hover:shadow-indigo-500/10">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" /></svg>
                        <span>Continue with Google</span>
                    </button>
                </div>

                <div class="text-center pt-6 text-sm">
                <span class="text-gray-400">Already have an account? </span>
                <Link
                    :href="route('login')"
                    class="font-semibold text-indigo-400 hover:text-indigo-300 transition-colors"
                >
                    Sign in
                </Link>
            </div>
            </form>
        </div>
    </GuestLayout>
</template>