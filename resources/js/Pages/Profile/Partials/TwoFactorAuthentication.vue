<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import Button from '@/Components/Button.vue';
import DangerButton from '@/Components/DangerButton.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const status = computed(() => page.props.status);

const showRecoveryCodes = ref(false);

const confirmForm = useForm({
    code: '',
});

const submitConfirm = () => {
    confirmForm.post(route('two-factor.confirm'));
};

const enableForm = useForm({});

const submitEnable = () => {
    enableForm.post(route('two-factor.enable'));
};

const regenerateForm = useForm({});

const regenerateRecoveryCodes = () => {
    regenerateForm.post(route('two-factor.regenerate-recovery-codes'));
};

const disableForm = useForm({});

const submitDisable = () => {
    disableForm.delete(route('two-factor.disable'));
};

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        // You could add a toast notification here
    } catch (err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
    }
};
</script>

<template>
    <!-- Main Container - Bento Box Style with Deep Dark Background -->
    <div class="bg-white dark:bg-[#0B0C10] rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800/50 overflow-hidden transition-all duration-300 hover:shadow-indigo-500/10 hover:shadow-3xl">
        <!-- Header - Vibrant Gradient -->
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-700 px-8 py-6 relative overflow-hidden">
            <!-- Decorative Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-300 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>
            </div>
            
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg ring-2 ring-white/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Two-Factor Authentication</h3>
                    <p class="text-indigo-100 text-sm mt-0.5">Secure your account with an additional layer of protection</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Two-Factor Not Enabled -->
            <template v-if="!user.two_factor_confirmed_at">
                <!-- Confirm Two-Factor -->
                <div v-if="status === 'two-factor-authentication-enabled'" class="space-y-6">
                    <!-- Status Card - Bento Box Style -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/20 border-2 border-amber-300 dark:border-amber-700/50 rounded-xl p-5 shadow-lg shadow-amber-500/10">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 dark:from-amber-500 dark:to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.982 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-900 dark:text-amber-100 text-lg">Setup in Progress</h4>
                                <p class="text-sm text-amber-800 dark:text-amber-200 mt-1.5 leading-relaxed">
                                    Complete your two-factor authentication setup by scanning the QR code and entering the verification code.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitConfirm" class="space-y-6">
                        <!-- Instructions -->
                        <div class="prose prose-sm max-w-none dark:prose-invert">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                Scan the QR code below with your authenticator app (Google Authenticator, Authy, etc.) or manually enter the setup key, then provide the 6-digit code from your app.
                            </p>
                        </div>

                        <!-- QR Code Section - Bento Box -->
                        <div v-if="user.two_factor_qr_code_svg" class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/80 dark:to-gray-800/50 rounded-xl p-8 text-center border border-gray-200 dark:border-gray-700/50 shadow-inner">
                            <div class="inline-block p-5 bg-white dark:bg-gray-950 rounded-2xl shadow-2xl ring-1 ring-gray-200 dark:ring-gray-700" v-html="user.two_factor_qr_code_svg"></div>
                        </div>

                        <!-- Setup Key - Bento Box -->
                        <div v-if="user.two_factor_secret" class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/80 dark:to-gray-800/50 rounded-xl p-5 border border-gray-200 dark:border-gray-700/50 shadow-lg">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex-1">
                                    <label class="text-sm font-semibold text-gray-800 dark:text-gray-200">Setup Key:</label>
                                    <code class="block mt-2 text-sm font-mono bg-white dark:bg-[#0B0C10] px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-inner">{{ user.two_factor_secret }}</code>
                                </div>
                                <button 
                                    type="button" 
                                    @click="copyToClipboard(user.two_factor_secret)"
                                    class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-all duration-200 hover:scale-110"
                                    title="Copy to clipboard"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Code Input -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200">
                                Verification Code
                            </label>
                            <TextInput
                                type="text"
                                placeholder="Enter 6-digit code"
                                v-model="confirmForm.code"
                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500/20': confirmForm.errors.code }"
                            />
                            <p v-if="confirmForm.errors.code" class="text-sm text-red-600 dark:text-red-400 font-medium">
                                {{ confirmForm.errors.code }}
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <Button 
                                type="submit"
                                :disabled="confirmForm.processing"
                                class="min-w-[140px]"
                            >
                                <svg v-if="confirmForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ confirmForm.processing ? 'Confirming...' : 'Complete Setup' }}
                            </Button>
                        </div>
                    </form>
                </div>

                <!-- Enable Two-Factor -->
                <div v-else class="space-y-6">
                    <!-- Status Card - Bento Box Style -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/20 border-2 border-blue-300 dark:border-blue-700/50 rounded-xl p-5 shadow-lg shadow-blue-500/10">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 dark:from-blue-600 dark:to-indigo-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 dark:text-blue-100 text-lg">Enhanced Security Available</h4>
                                <p class="text-sm text-blue-800 dark:text-blue-200 mt-1.5 leading-relaxed">
                                    Two-factor authentication is currently disabled. Enable it to add an extra layer of security to your account.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits - Bento Box Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/20 rounded-xl border border-green-200 dark:border-green-800/50 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 dark:from-green-600 dark:to-emerald-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-gray-900 dark:text-white">Enhanced Security</h5>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Protect against unauthorized access</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-950/30 dark:to-cyan-950/20 rounded-xl border border-blue-200 dark:border-blue-800/50 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 dark:from-blue-600 dark:to-cyan-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-gray-900 dark:text-white">Mobile Apps</h5>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Works with Google Authenticator, Authy</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitEnable">
                        <div class="flex justify-end">
                            <Button 
                                type="submit"
                                :disabled="enableForm.processing"
                                class="min-w-[140px]"
                            >
                                <svg v-if="enableForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ enableForm.processing ? 'Enabling...' : 'Enable 2FA' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </template>

            <!-- Two-Factor Enabled -->
            <template v-else>
                <!-- Success Status - Bento Box -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/40 dark:to-emerald-950/20 border-2 border-green-300 dark:border-green-700/50 rounded-xl p-5 mb-6 shadow-lg shadow-green-500/10">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 dark:from-green-600 dark:to-emerald-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-green-900 dark:text-green-100 text-lg">Two-Factor Authentication Active</h4>
                            <p class="text-sm text-green-800 dark:text-green-200 mt-1.5 leading-relaxed">
                                Your account is now protected with two-factor authentication. You'll need your authenticator app to sign in.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recovery Codes Section - Bento Box -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/80 dark:to-gray-800/50 rounded-xl p-6 mb-6 border border-gray-200 dark:border-gray-700/50 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <h4 class="font-bold text-gray-900 dark:text-white text-lg">Recovery Codes</h4>
                        </div>
                        <button 
                            @click="showRecoveryCodes = !showRecoveryCodes"
                            class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold transition-all duration-200 hover:scale-105"
                        >
                            {{ showRecoveryCodes ? 'Hide' : 'Show' }} Codes
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
                        Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two-factor authentication device is lost.
                    </p>
                    
                    <div v-show="showRecoveryCodes" class="space-y-4">
                        <div v-if="user.two_factor_recovery_codes" class="bg-white dark:bg-[#0B0C10] border-2 border-gray-300 dark:border-gray-700 rounded-xl p-5 shadow-inner">
                            <div class="grid grid-cols-2 gap-3 font-mono text-sm">
                                <span 
                                    v-for="code in user.two_factor_recovery_codes" 
                                    :key="code"
                                    class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/80 dark:to-gray-800/50 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 font-semibold shadow-sm hover:shadow-md transition-shadow"
                                >
                                    {{ code }}
                                </span>
                            </div>
                        </div>
                        
                        <form @submit.prevent="regenerateRecoveryCodes" class="flex justify-end">
                            <button 
                                type="submit"
                                :disabled="regenerateForm.processing"
                                class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold underline underline-offset-2 transition-all duration-200 disabled:opacity-50 hover:scale-105"
                            >
                                {{ regenerateForm.processing ? 'Regenerating...' : 'Regenerate Codes' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Disable Section -->
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 pt-6 border-t-2 border-gray-300 dark:border-gray-700">
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Disable Two-Factor Authentication</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1.5 leading-relaxed">
                            This will remove the additional security layer from your account.
                        </p>
                    </div>
                    <form @submit.prevent="submitDisable">
                        <DangerButton 
                            type="submit"
                            :disabled="disableForm.processing"
                            class="min-w-[140px]"
                        >
                            <svg v-if="disableForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ disableForm.processing ? 'Disabling...' : 'Disable 2FA' }}
                        </DangerButton>
                    </form>
                </div>
            </template>
        </div>
    </div>
</template>
