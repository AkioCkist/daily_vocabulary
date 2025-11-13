<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import Button from '@/Components/Button.vue';
import DangerButton from '@/Components/DangerButton.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const status = computed(() => page.props.status);

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
    regenerateForm.post(route('two-factor.recovery-codes'));
};

const disableForm = useForm({});

const submitDisable = () => {
    disableForm.delete(route('two-factor.disable'));
};
</script>

<template>
    <section>
        <!-- Two-Factor Not Enabled -->
        <template v-if="!user.two_factor_confirmed_at">
            <!-- Confirm Two-Factor -->
            <form 
                v-if="status === 'two-factor-authentication-enabled'" 
                @submit.prevent="submitConfirm" 
                class="grid grid-cols-1 gap-6"
            >
                <div class="flex flex-col text-on-surface-600">
                    <span class="mb-1 font-bold">
                        Finish enabling two-factor authentication.
                    </span>
                    <span class="text-sm mb-1">
                        When two-factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                    </span>
                    <span class="text-sm font-bold">
                        To finish enabling two-factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.
                    </span>
                </div>

                <div class="p-4 bg-white w-fit" v-if="user.two_factor_qr_code_svg" v-html="user.two_factor_qr_code_svg"></div>

                <div class="flex flex-row text-on-surface-600 font-bold" v-if="user.two_factor_secret">
                    <span>Setup Key:</span>
                    <span class="ml-2">{{ user.two_factor_secret }}</span>
                </div>

                <TextInput
                    type="text"
                    name="code"
                    :label="'Code'"
                    v-model="confirmForm.code"
                    :error="confirmForm.errors.code"
                />

                <div class="flex items-center gap-4">
                    <Button 
                        type="submit"
                        :disabled="confirmForm.processing"
                    >
                        {{ confirmForm.processing ? 'Confirming...' : 'Confirm' }}
                    </Button>
                </div>
            </form>

            <!-- Enable Two-Factor -->
            <form 
                v-else 
                @submit.prevent="submitEnable" 
                class="grid grid-cols-1 gap-6"
            >
                <div class="flex flex-col text-on-surface-600">
                    <span class="mb-1 font-bold">
                        You have not enabled two-factor authentication.
                    </span>
                    <span class="text-sm">
                        When two-factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <Button 
                        type="submit"
                        :disabled="enableForm.processing"
                    >
                        {{ enableForm.processing ? 'Enabling...' : 'Enable' }}
                    </Button>
                </div>
            </form>
        </template>

        <!-- Two-Factor Enabled -->
        <template v-else>
            <div class="flex flex-col mb-4 text-on-surface-600">
                <span class="mb-4 font-bold">You have enabled two-factor authentication.</span>
                <span class="text-sm">When two-factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.</span>
            </div>

            <div>
                <details class="text-on-surface-500 mt-2 mb-4 text-sm font-bold">
                    <summary class="cursor-pointer">Recovery Codes</summary>
                    <div>
                        <span class="text-on-surface-600 text-sm font-normal">Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two-factor authentication device is lost.</span>
                        
                        <div class="flex flex-col p-4 bg-gray-100 rounded-lg mt-2 text-sm" v-if="user.two_factor_recovery_codes">
                            <span 
                                v-for="code in user.two_factor_recovery_codes" 
                                :key="code"
                                class="font-normal"
                            >
                                {{ code }}
                            </span>
                        </div>
                        
                        <form @submit.prevent="regenerateRecoveryCodes" class="mt-2 w-full flex justify-end">
                            <Button 
                                type="submit" 
                                class="bg-transparent p-0 underline underline-offset-4 text-on-surface-600 hover:bg-transparent font-normal -mr-4 -mt-2"
                                :disabled="regenerateForm.processing"
                            >
                                {{ regenerateForm.processing ? 'Regenerating...' : 'Regenerate Recovery Code' }}
                            </Button>
                        </form>
                    </div>
                </details>
            </div>

            <div class="flex flex-row py-4">
                <form @submit.prevent="submitDisable">
                    <DangerButton 
                        type="submit"
                        :disabled="disableForm.processing"
                    >
                        {{ disableForm.processing ? 'Disabling...' : 'Disable 2FA' }}
                    </DangerButton>
                </form>
            </div>
        </template>
    </section>
</template>
