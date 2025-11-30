import { ref } from 'vue';
import { fetchTokens, createToken, revokeToken, regenerateToken } from '@/services/tokenService';

export function useTokenManager() {
  const tokens = ref([]);
  const loading = ref(false);
  const error = ref('');
  const success = ref('');
  const showForm = ref(false);
  const copiedTokenId = ref(null);
  const formData = ref({ name: '', scopes: [], expires_in_days: null });
  const newTokenData = ref(null);

  const scopeOptions = [
    { value: '*', label: 'Full Access' },
    { value: 'read', label: 'Read' },
    { value: 'create', label: 'Create' },
    { value: 'update', label: 'Update' },
    { value: 'delete', label: 'Delete' },
  ];

  const fetchAllTokens = async () => {
    loading.value = true;
    error.value = '';
    try {
      const response = await fetchTokens();
      tokens.value = response.data.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch tokens';
    } finally {
      loading.value = false;
    }
  };

  const handleCreateToken = async () => {
    if (!formData.value.name.trim()) {
      error.value = 'Token name is required';
      return;
    }
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
      const response = await createToken(formData.value);
      newTokenData.value = {
        token: response.data.token,
        name: formData.value.name,
        warning: response.data.warning,
      };
      formData.value = { name: '', scopes: [], expires_in_days: null };
      showForm.value = false;
      await fetchAllTokens();
    } catch (err) {
      if (err.response?.data?.errors) {
        error.value = Object.values(err.response.data.errors).flat().join(', ');
      } else {
        error.value = err.response?.data?.message || 'Failed to create token';
      }
    } finally {
      loading.value = false;
    }
  };

  const handleRevokeToken = async (tokenId) => {
    if (!confirm('Are you sure you want to revoke this token? This action cannot be undone.')) return;
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
      await revokeToken(tokenId);
      success.value = 'Token revoked successfully';
      await fetchAllTokens();
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to revoke token';
    } finally {
      loading.value = false;
    }
  };

  const handleRegenerateToken = async (tokenId) => {
    if (!confirm('This will revoke the current token and create a new one. Continue?')) return;
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
      const response = await regenerateToken(tokenId);
      newTokenData.value = {
        token: response.data.token,
        warning: response.data.warning,
      };
      await fetchAllTokens();
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to regenerate token';
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

  // Utility functions
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

  return {
    tokens,
    loading,
    error,
    success,
    showForm,
    copiedTokenId,
    formData,
    scopeOptions,
    newTokenData,
    fetchAllTokens,
    handleCreateToken,
    handleRevokeToken,
    handleRegenerateToken,
    copyToken,
    closeTokenPanel,
    formatAbilities,
    getStatusClasses,
    getStatusText,
  };
}
