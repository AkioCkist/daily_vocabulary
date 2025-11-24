<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Header from '@/Components/Header.vue';
import TokenTable from '@/Components/TokenManager/TokenTable.vue';
import TokenForm from '@/Components/TokenManager/TokenForm.vue';
import TokenAlert from '@/Components/TokenManager/TokenAlert.vue';
import TokenGenerated from '@/Components/TokenManager/TokenGenerated.vue';

const page = usePage();
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
const newTokenData = ref(null);

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
        newTokenData.value = {
            token: response.data.token,
            name: formData.value.name,
            warning: response.data.warning,
        };
        formData.value = { name: '', scopes: [], expires_in_days: null };
        showForm.value = false;
        await fetchTokens();
    } catch (err) {
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
    if (!confirm('Are you sure you want to revoke this token? This action cannot be undone.')) return;
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
        await axios.delete(`/api/tokens/${tokenId}`);
        success.value = 'Token revoked successfully';
        await fetchTokens();
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to revoke token';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const regenerateToken = async (tokenId) => {
    if (!confirm('This will revoke the current token and create a new one. Continue?')) return;
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
        const response = await axios.patch(`/api/tokens/${tokenId}/regenerate`);
        newTokenData.value = {
            token: response.data.token,
            warning: response.data.warning,
        };
        await fetchTokens();
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

onMounted(() => {
    fetchTokens();
});
</script>


<template>
  <div class="min-h-screen bg-gray-900 font-sans text-gray-300">
    <Header :user="page.props.auth?.user" />
    <div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
      <div class="bg-gray-800 border border-gray-700 shadow-xl rounded-xl overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">API Access Tokens</h1>
            <p class="text-sm text-gray-400 mt-1">Manage authentication tokens for third-party services.</p>
          </div>
          <button @click="showForm = !showForm" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500" :class="showForm ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-indigo-500 text-white hover:bg-indigo-600 shadow-lg shadow-indigo-500/20'">
            <span v-if="showForm">Cancel</span>
            <span v-else class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              Create Token
            </span>
          </button>
        </div>
        <TokenAlert :error="error" :success="success" />
        <TokenGenerated v-if="newTokenData" :newTokenData="newTokenData" :copiedTokenId="copiedTokenId" @copy="copyToken" @close="() => newTokenData.value = null" />
        <TokenForm v-show="showForm" :formData="formData" :scopeOptions="scopeOptions" :loading="loading" @create="createToken" />
        <TokenTable :tokens="tokens" :loading="loading" :formatAbilities="formatAbilities" :getStatusClasses="getStatusClasses" :getStatusText="getStatusText" @regenerate="regenerateToken" @revoke="revokeToken" />
        <div class="px-6 py-4 bg-gray-900/50 border-t border-gray-700">
          <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-xs text-gray-500 leading-relaxed">
              <strong>Security Note:</strong> API tokens grant access to your account resources. Treat them like passwords. If a token is compromised, revoke it immediately. Tokens are only shown once upon creation.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Optional: specific scrollbar styling for the scopes box to match dark theme */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
::-webkit-scrollbar-track {
    background: #111827; 
}
::-webkit-scrollbar-thumb {
    background: #374151; 
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: #4b5563; 
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>