<script setup>
// FIX 1: Added 'computed' to the import list from 'vue' to correctly define the 'user' prop for the Header.
import { ref, onMounted, computed } from 'vue'; 
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Header from '@/Components/Header.vue';
import TokenTable from '@/Components/TokenManager/TokenTable.vue';
import TokenForm from '@/Components/TokenManager/TokenForm.vue';
import TokenAlert from '@/Components/TokenManager/TokenAlert.vue';
import TokenGenerated from '@/Components/TokenManager/TokenGenerated.vue';

const page = usePage();
// FIX 2: Correctly define 'user' using computed.
const user = computed(() => page.props.auth.user); 
const tokens = ref([]);
const loading = ref(false);
const error = ref('');
const success = ref('');
const showForm = ref(false);
const copiedTokenId = ref(null);
const formData = ref({ name: '', scopes: [], expires_in_days: null });
const scopeOptions = [
    { value: '*', label: 'Full Access' },
    { value: 'read', label: 'Read' },
    { value: 'create', label: 'Create' },
    { value: 'update', label: 'Update' },
    { value: 'delete', label: 'Delete' },
];
// This will hold the token data structure expected by your TokenGenerated component
const newTokenData = ref(null); 

// --- START: Original Functional Logic Restored ---

const fetchTokens = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await axios.get('/api/tokens');
        tokens.value = response.data.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch tokens';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const createToken = async () => {
    // RESTORED: Client-side validation
    if (!formData.value.name.trim()) {
        error.value = 'Token name is required';
        return;
    }
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
        const response = await axios.post('/api/tokens', {
            name: formData.value.name,
            scopes: formData.value.scopes,
            expires_in_days: formData.value.expires_in_days,
        });
        // RESTORED: New token data structure for the TokenGenerated panel
        newTokenData.value = {
            token: response.data.token,
            name: formData.value.name,
            warning: response.data.warning,
        };
        formData.value = { name: '', scopes: [], expires_in_days: null };
        showForm.value = false;
        await fetchTokens(); // RESTORED: Refresh token list after creation
    } catch (err) {
        // RESTORED: Detailed error handling
        if (err.response?.data?.errors) {
            error.value = Object.values(err.response.data.errors).flat().join(', ');
        } else {
            error.value = err.response?.data?.message || 'Failed to create token';
        }
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const revokeToken = async (tokenId) => {
    // RESTORED: Confirmation dialog
    if (!confirm('Are you sure you want to revoke this token? This action cannot be undone.')) return;
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
        await axios.delete(`/api/tokens/${tokenId}`);
        success.value = 'Token revoked successfully';
        await fetchTokens(); // RESTORED: Refresh token list after revocation
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to revoke token';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const regenerateToken = async (tokenId) => {
    // RESTORED: Confirmation dialog
    if (!confirm('This will revoke the current token and create a new one. Continue?')) return;
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
        const response = await axios.patch(`/api/tokens/${tokenId}/regenerate`);
         // RESTORED: New token data structure for the TokenGenerated panel
        newTokenData.value = {
            token: response.data.token,
            warning: response.data.warning,
        };
        await fetchTokens(); // RESTORED: Refresh token list after regeneration
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to regenerate token';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const copyToken = (token) => {
    navigator.clipboard.writeText(token);
    copiedTokenId.value = token;
    setTimeout(() => {
        copiedTokenId.value = null;
    }, 2000);
};

const closeTokenPanel = () => {
    newTokenData.value = null;
};

// RESTORED: Utility functions using original token properties
const formatAbilities = (abilities) => {
    if (!abilities || abilities.length === 0) return 'N/A';
    if (abilities.includes('*')) return 'Full Access';
    return abilities.map(a => a.charAt(0).toUpperCase() + a.slice(1)).join(', ');
};
const getStatusClasses = (token) => {
    if (token.is_expired) return 'bg-red-500/10 text-red-400 border-red-500/20';
    if (token.last_used_at) return 'bg-green-500/10 text-green-400 border-green-500/20';
    return 'bg-gray-700/50 text-gray-400 border-gray-600/30';
};
const getStatusText = (token) => {
    if (token.is_expired) return 'Expired';
    if (token.last_used_at) return 'Active';
    return 'Never Used';
};

// --- END: Original Functional Logic Restored ---

onMounted(() => {
    fetchTokens();
});
</script>

<template>
    <Head title="API Tokens - DailyVocab" />

    <div class="min-h-screen bg-gray-50 dark:bg-[#0B0C10] text-slate-900 dark:text-slate-100 font-sans">
        
        <Header :user="user" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            
            <div class="mb-10 pt-4">
                <div class="flex items-center gap-3">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <h1 class="text-3xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                        API Tokens
                    </h1>
                </div>
                <p class="mt-1 ml-10 text-gray-500 dark:text-gray-400">Manage and create personal access tokens for API access.</p>
            </div>

            <div class="bg-white dark:bg-[#111216] shadow-xl rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                
                <TokenAlert :error="error" :success="success" @clear="error = ''; success = ''" />

                <TokenGenerated 
                    v-if="newTokenData" 
                    :newTokenData="newTokenData" 
                    :copiedTokenId="copiedTokenId" 
                    @copy="copyToken"
                    @close="closeTokenPanel" 
                />

                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            Active Tokens ({{ tokens.length }})
                        </h2>

                        <button 
                            @click="showForm = !showForm"
                            class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg 
                                    hover:bg-indigo-700 transition-colors duration-300 
                                    shadow-md shadow-indigo-600/30 dark:shadow-indigo-500/50"
                        >
                            {{ showForm ? 'Cancel Creation' : 'Create New Token' }}
                        </button>
                    </div>

                    <div v-if="showForm" class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800">
                        <TokenForm 
                            :scopeOptions="scopeOptions" 
                            :formData="formData" 
                            :loading="loading"
                            @create="createToken" 
                        />
                    </div>
                </div>

                <div v-if="loading" class="p-6 text-center text-gray-500">
                    Loading tokens...
                </div>
                <div v-else-if="tokens.length > 0" class="overflow-x-auto">
                    <TokenTable 
                        :tokens="tokens" 
                        :formatAbilities="formatAbilities" 
                        :getStatusClasses="getStatusClasses" 
                        :getStatusText="getStatusText" 
                        @regenerate="regenerateToken" 
                        @revoke="revokeToken" 
                    />
                </div>
                <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-800">
                    No API tokens found.
                </div>

                <div class="px-6 py-4 bg-gray-100 dark:bg-[#111216]/50 border-t border-gray-200 dark:border-gray-800">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            <strong>Security Note:</strong> API tokens grant access to your account resources. Treat them like passwords. If a token is compromised, revoke it immediately. Tokens are only shown once upon creation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>