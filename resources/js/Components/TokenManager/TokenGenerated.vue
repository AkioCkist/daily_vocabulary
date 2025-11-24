<template>
  <div v-if="newTokenData" class="px-6 pt-6 animate-fade-in">
    <div class="bg-indigo-900/20 border border-indigo-500/30 rounded-lg p-6 relative overflow-hidden">
      <div class="absolute top-0 right-0 -mr-10 -mt-10 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
      <div class="relative z-10">
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 bg-indigo-500/20 p-2 rounded-full">
            <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 19l-1 1-1-1-1 1-1-1-1 1-1-1-1 1-1-1-6-6a6 6 0 017.743-5.743A2 2 0 0115 7z" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-medium text-white">Token Generated Successfully</h3>
            <p class="text-gray-400 text-sm mt-1">{{ newTokenData.warning }}</p>
            <div class="mt-4 flex items-center gap-2">
              <div class="flex-1 bg-gray-900 border border-gray-700 rounded-lg p-3 font-mono text-sm text-indigo-300 break-all select-all">
                {{ newTokenData.token }}
              </div>
              <button @click="$emit('copy', newTokenData.token)" class="flex-shrink-0 px-4 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition-colors text-sm font-medium flex items-center gap-2 shadow-md">
                <span v-if="copiedTokenId === newTokenData.token" class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  Copied
                </span>
                <span v-else>Copy</span>
              </button>
            </div>
            <div class="mt-4 flex justify-end">
              <button @click="$emit('close')" class="text-sm text-gray-400 hover:text-white transition-colors underline decoration-gray-600 underline-offset-2">
                I have saved this token
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
defineProps({
  newTokenData: Object,
  copiedTokenId: String,
});
</script>
