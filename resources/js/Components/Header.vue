<template>
  <header class="bg-white/90 dark:bg-[#0B0C10]/90 backdrop-blur-md sticky top-0 z-50 border-b border-gray-200 dark:border-gray-800 transition-colors duration-300">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 py-4">
      <div class="flex items-center justify-between h-14">
        
        <Link href="/" class="flex items-center gap-3 group">
          <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg transition-transform group-hover:scale-105 duration-300">
            <span class="text-xl text-white font-bold">V</span> </div>
          
          <div>
            <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400 transition-colors">
              DailyVocab
            </h1>
            <p class="text-xs text-gray-500 -mt-0.5 font-medium dark:text-gray-400 hidden sm:block">Vocabulary Mastery Platform</p>
          </div>
        </Link>

        <nav class="flex items-center gap-4">
          <template v-if="user">         
            <div class="relative">
              <button 
                @click="showDropdown = !showDropdown"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white rounded-lg font-semibold text-sm transition-all duration-300 hover:bg-gray-200 dark:hover:bg-gray-700"
              >
                <span class="hidden sm:inline">{{ user.name }}</span>
                <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs 
                             bg-indigo-500 text-white dark:bg-indigo-600 dark:text-gray-100 shadow-sm">
                  {{ user.name.charAt(0).toUpperCase() }}
                </span>
                
                <svg 
                  class="w-3 h-3 text-gray-500 dark:text-gray-400 transition-transform duration-300" 
                  :class="{ 'rotate-180': showDropdown }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <div 
                v-if="showDropdown"
                @click="showDropdown = false"
                class="absolute right-0 mt-3 w-56 bg-white dark:bg-[#111216] rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 py-2 origin-top-right animate-scale-in"
              >
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500">
                    Account
                </div>
                
                <Link 
                  href="/profile" 
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 dark:text-gray-300 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-300 transition-colors duration-200"
                >
                  <div class="flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profile Settings
                  </div>
                </Link>
                
                <Link 
                  href="/tokens" 
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 dark:text-gray-300 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-300 transition-colors duration-200"
                >
                  <div class="flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    API Tokens
                  </div>
                </Link>
                
                <hr class="my-2 border-gray-100 dark:border-gray-700">
                <button
                  @click="logout"
                  class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors duration-200"
                >
                  <div class="flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                  </div>
                </button>
              </div>
            </div>
          </template>

          <template v-else>
            
            <Link 
              href="/login" 
              class="px-4 py-2 text-sm font-medium transition-all duration-300 
                     text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400 rounded-lg"
            >
              Login
            </Link>
            
            <Link 
              href="/register" 
              class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg 
                     hover:bg-indigo-700 transition-colors duration-300 
                     shadow-md shadow-indigo-600/30 dark:shadow-indigo-500/50"
            >
              Sign Up Free
            </Link>
          </template>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

defineProps({
  user: {
    type: Object,
    default: null
  }
});

const showDropdown = ref(false);
const page = usePage();

// Get current URL path
const currentPath = computed(() => page.url);

// Handle logout
const logout = () => {
  router.post(route('logout'));
};
</script>

<style scoped>
@keyframes scale-in {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-5px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-scale-in {
  animation: scale-in 0.2s ease-out;
}
</style>