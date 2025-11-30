<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import TokenAlert from '@/Components/TokenManager/TokenAlert.vue';
import TokenGenerated from '@/Components/TokenManager/TokenGenerated.vue';
import TokenForm from '@/Components/TokenManager/TokenForm.vue';
import TokenTable from '@/Components/TokenManager/TokenTable.vue';
import EmptyState from '@/Components/EmptyState.vue';
import SecurityNote from '@/Components/SecurityNote.vue';
import { useTokenManager } from '@/composables/useTokenManager';

const page = usePage();
const user = computed(() => page.props.auth.user);

const {
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
} = useTokenManager();

fetchAllTokens();
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
              @create="handleCreateToken" 
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
            @regenerate="handleRegenerateToken" 
            @revoke="handleRevokeToken" 
          />
        </div>
        <EmptyState v-else />
        <SecurityNote />
      </div>
    </div>
  </div>
</template>
