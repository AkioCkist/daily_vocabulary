<template>
  <div class="border-b border-gray-700 bg-gray-800/50 transition-all duration-300 ease-in-out">
    <div class="px-6 py-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="col-span-1 md:col-span-2">
          <label class="block text-sm font-medium text-gray-300 mb-2">Token Name</label>
          <input v-model="formData.name" type="text" placeholder="e.g. Production Server, Mobile App" class="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 placeholder-gray-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2">Expires In (Days)</label>
          <input v-model.number="formData.expires_in_days" type="number" placeholder="Leave empty for never" class="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 placeholder-gray-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2">Capabilities</label>
          <div class="bg-gray-900 border border-gray-700 rounded-lg p-3 space-y-2 max-h-32 overflow-y-auto">
            <label class="flex items-center cursor-pointer group">
              <input v-model="formData.scopes" type="checkbox" value="*" class="w-4 h-4 text-indigo-500 bg-gray-800 border-gray-600 rounded focus:ring-indigo-500 ring-offset-gray-900" />
              <span class="ml-2 text-sm text-gray-300 group-hover:text-white transition-colors">Full Access</span>
            </label>
            <label v-for="scope in scopeOptions.slice(1)" :key="scope.value" class="flex items-center cursor-pointer group">
              <input v-model="formData.scopes" type="checkbox" :value="scope.value" :disabled="formData.scopes.includes('*')" class="w-4 h-4 text-indigo-500 bg-gray-800 border-gray-600 rounded focus:ring-indigo-500 ring-offset-gray-900 disabled:opacity-50" />
              <span class="ml-2 text-sm text-gray-300 group-hover:text-white transition-colors" :class="{'opacity-50': formData.scopes.includes('*')}">{{ scope.label }}</span>
            </label>
          </div>
        </div>
      </div>
      <div class="mt-6 flex justify-end">
        <button @click="$emit('create')" :disabled="loading" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-indigo-500 rounded-lg hover:bg-indigo-600 focus:ring-4 focus:outline-none focus:ring-indigo-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-lg shadow-indigo-500/20">
          <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Create Token
        </button>
      </div>
    </div>
  </div>
</template>
<script setup>
defineProps({
  formData: Object,
  scopeOptions: Array,
  loading: Boolean,
});
</script>
