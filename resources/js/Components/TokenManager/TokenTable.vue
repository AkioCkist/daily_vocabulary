<template>
  <div class="relative overflow-x-auto">
    <div v-if="loading && tokens.length === 0" class="p-8 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-500"></div>
      <p class="mt-2 text-sm text-gray-500">Loading tokens...</p>
    </div>
    <div v-else-if="tokens.length === 0" class="p-12 text-center">
      <div class="mx-auto h-12 w-12 text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M8 12h8"></path>
        </svg>
      </div>
      <h3 class="mt-2 text-sm font-medium text-white">No tokens found</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by creating your first API token.</p>
    </div>
    <table v-else class="w-full text-left text-sm text-gray-400">
      <thead class="bg-gray-900/50 text-xs uppercase text-gray-500 border-b border-gray-700">
        <tr>
          <th scope="col" class="px-6 py-4 font-medium tracking-wider">Name</th>
          <th scope="col" class="px-6 py-4 font-medium tracking-wider">Permissions</th>
          <th scope="col" class="px-6 py-4 font-medium tracking-wider">Status</th>
          <th scope="col" class="px-6 py-4 font-medium tracking-wider">Last Used</th>
          <th scope="col" class="px-6 py-4 font-medium tracking-wider text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-700">
        <tr v-for="token in tokens" :key="token.id" class="hover:bg-gray-700/30 transition-colors">
          <td class="px-6 py-4 font-medium text-white">{{ token.name }}</td>
          <td class="px-6 py-4">
            <div class="max-w-xs truncate text-xs" :title="formatAbilities(token.abilities)">
              {{ formatAbilities(token.abilities) }}
            </div>
          </td>
          <td class="px-6 py-4">
            <span :class="['px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusClasses(token)]">
              {{ getStatusText(token) }}
            </span>
          </td>
          <td class="px-6 py-4 text-gray-500">{{ token.last_used_at || 'Never' }}</td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              <button @click="$emit('regenerate', token.id)" :disabled="loading" class="text-xs px-3 py-1.5 rounded bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors border border-transparent hover:border-gray-500" title="Regenerate Token">Regenerate</button>
              <button @click="$emit('revoke', token.id)" :disabled="loading" class="text-xs px-3 py-1.5 rounded bg-gray-700 text-red-400 hover:bg-red-900/30 hover:text-red-300 transition-colors border border-transparent hover:border-red-900/50" title="Revoke Token">Revoke</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  tokens: Array,
  loading: Boolean,
  formatAbilities: Function,
  getStatusClasses: Function,
  getStatusText: Function,
});
</script>
