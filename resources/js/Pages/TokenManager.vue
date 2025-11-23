<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const tokens = ref([]);
const loading = ref(false);
const error = ref('');
const success = ref('');
const showForm = ref(false);
const copiedTokenId = ref(null);

// Form state
const formData = ref({
    name: '',
    scopes: ['*'],
    expires_in_days: null,
});

const scopeOptions = [
    { value: '*', label: 'Full Access' },
    { value: 'read', label: 'Read' },
    { value: 'create', label: 'Create' },
    { value: 'update', label: 'Update' },
    { value: 'delete', label: 'Delete' },
];

const newTokenData = ref(null);

/**
 * Fetch all tokens for the user
 */
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

/**
 * Create a new token
 */
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

        // Reset form
        formData.value = {
            name: '',
            scopes: ['*'],
            expires_in_days: null,
        };

        // Refresh token list
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

/**
 * Revoke a token
 */
const revokeToken = async (tokenId) => {
    if (!confirm('Are you sure you want to revoke this token? This action cannot be undone.')) {
        return;
    }

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

/**
 * Regenerate a token
 */
const regenerateToken = async (tokenId) => {
    if (!confirm('This will revoke the current token and create a new one. Continue?')) {
        return;
    }

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

/**
 * Copy token to clipboard
 */
const copyToken = (token) => {
    navigator.clipboard.writeText(token);
    copiedTokenId.value = token;
    setTimeout(() => {
        copiedTokenId.value = null;
    }, 2000);
};

/**
 * Format abilities array
 */
const formatAbilities = (abilities) => {
    if (!abilities || abilities.length === 0) return 'N/A';
    if (abilities.includes('*')) return 'Full Access';
    return abilities.join(', ');
};

/**
 * Get status badge color
 */
const getStatusColor = (token) => {
    if (token.is_expired) return 'bg-red-100 text-red-800';
    if (token.last_used_at) return 'bg-green-100 text-green-800';
    return 'bg-gray-100 text-gray-800';
};

/**
 * Get status text
 */
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
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            API Token Manager
                        </h1>
                        <button
                            @click="showForm = !showForm"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            <span>{{ showForm ? 'Cancel' : 'Create Token' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Error Messages -->
                <div v-if="error" class="mx-6 mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    {{ error }}
                </div>

                <!-- Success Messages -->
                <div v-if="success" class="mx-6 mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ success }}
                </div>

                <!-- New Token Display -->
                <div v-if="newTokenData" class="mx-6 mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-yellow-800">
                                {{ newTokenData.warning }}
                            </h3>
                            <div class="mt-3 bg-white border border-yellow-300 rounded p-3 font-mono text-sm break-all">
                                {{ newTokenData.token }}
                            </div>
                            <div class="mt-3">
                                <button
                                    @click="copyToken(newTokenData.token)"
                                    class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors text-sm"
                                >
                                    {{ copiedTokenId === newTokenData.token ? 'Copied!' : 'Copy to Clipboard' }}
                                </button>
                                <button
                                    @click="newTokenData = null"
                                    class="ml-2 inline-flex items-center px-3 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors text-sm"
                                >
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Token Form -->
                <div v-if="showForm" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create New Token</h2>
                    
                    <div class="space-y-4">
                        <!-- Token Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Token Name
                            </label>
                            <input
                                v-model="formData.name"
                                type="text"
                                placeholder="e.g., Mobile App, Third-party Integration"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Scopes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Scopes
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input
                                        v-model="formData.scopes"
                                        type="checkbox"
                                        value="*"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Full Access (*)</span>
                                </label>
                                <label v-for="scope in scopeOptions.slice(1)" :key="scope.value" class="flex items-center">
                                    <input
                                        v-model="formData.scopes"
                                        type="checkbox"
                                        :value="scope.value"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        :disabled="formData.scopes.includes('*')"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ scope.label }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Expiration -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Expires In (days)
                            </label>
                            <input
                                v-model.number="formData.expires_in_days"
                                type="number"
                                placeholder="Leave empty for no expiration"
                                min="1"
                                max="365"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Maximum 365 days. Leave empty for no expiration.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-2">
                            <button
                                @click="createToken"
                                :disabled="loading"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 transition-colors"
                            >
                                {{ loading ? 'Creating...' : 'Create Token' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tokens List -->
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Your API Tokens ({{ tokens.length }})
                    </h2>

                    <div v-if="loading && tokens.length === 0" class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Loading tokens...</p>
                    </div>

                    <div v-else-if="tokens.length === 0" class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">You haven't created any API tokens yet.</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Name</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Scopes</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Created</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Last Used</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="token in tokens" :key="token.id" class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="py-3 px-4 text-gray-900 dark:text-white">{{ token.name }}</td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ formatAbilities(token.abilities) }}</td>
                                    <td class="py-3 px-4">
                                        <span :class="['px-2 py-1 rounded text-xs font-semibold', getStatusColor(token)]">
                                            {{ getStatusText(token) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ token.created_at }}</td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ token.last_used_at || 'Never' }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <button
                                                @click="regenerateToken(token.id)"
                                                :disabled="loading"
                                                title="Regenerate token (creates new, revokes old)"
                                                class="px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600 disabled:bg-gray-400 transition-colors"
                                            >
                                                Regenerate
                                            </button>
                                            <button
                                                @click="revokeToken(token.id)"
                                                :disabled="loading"
                                                title="Revoke this token"
                                                class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 disabled:bg-gray-400 transition-colors"
                                            >
                                                Revoke
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        <strong>Security Note:</strong> API tokens should be treated like passwords. 
                        Keep them secure and never share them. Always use HTTPS when transmitting tokens.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
