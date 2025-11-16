<template>
  <header class="bg-white/70 backdrop-blur-xl border-b border-white/20 sticky top-0 z-50 shadow-lg shadow-purple-500/5 dark:bg-gray-900/70 dark:border-purple-500/20">
    <div class="max-w-7xl mx-auto px-4 py-3">
      <div class="flex items-center justify-between">
        <!-- Logo/Brand with Enhanced Glassy Effect -->
        <Link href="/" class="flex items-center gap-3 group">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl blur-md opacity-50 group-hover:opacity-70 transition-opacity"></div>
            <div class="relative w-12 h-12 bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-105 transition-all duration-300 border border-white/30 dark:border-white/20">
              <span class="text-2xl">📚</span>
            </div>
          </div>
          <div>
            <h1 class="text-2xl font-black bg-gradient-to-r from-indigo-600 via-purple-600 to-violet-600 bg-clip-text text-transparent dark:from-indigo-400 dark:via-purple-400 dark:to-violet-400 group-hover:scale-105 transition-transform origin-left">
              DailyVocab
            </h1>
            <p class="text-xs text-gray-500 -mt-0.5 font-medium dark:text-gray-400">Learn & Master Daily</p>
          </div>
        </Link>

        <!-- Navigation -->
        <nav class="flex items-center gap-2">
          <template v-if="user">
            <!-- Authenticated User Menu with Glassy Buttons -->
            <Link 
              v-if="currentPath === '/user/words'"
              href="/" 
              class="px-5 py-2.5 text-gray-700 hover:text-indigo-600 font-semibold transition-all duration-300 hidden sm:block dark:text-gray-300 dark:hover:text-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-indigo-500/10 rounded-xl backdrop-blur-sm"
            >
              Browse Words
            </Link>
            <Link 
              v-if="currentPath !== '/user/words'"
              href="/user/words" 
              class="px-5 py-2.5 text-gray-700 hover:text-indigo-600 font-semibold transition-all duration-300 hidden sm:block dark:text-gray-300 dark:hover:text-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-indigo-500/10 rounded-xl backdrop-blur-sm"
            >
              My Vocabulary
            </Link>
            
            <!-- User Dropdown with Classy Glassy Effect -->
            <div class="relative">
              <button 
                @click="showDropdown = !showDropdown"
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 dark:from-indigo-500/40 dark:via-purple-500/35 dark:to-pink-500/40 backdrop-blur-md border border-indigo-300/60 dark:border-indigo-400/50 text-gray-800 dark:text-white rounded-xl font-semibold hover:from-indigo-200 hover:via-purple-200 hover:to-pink-200 dark:hover:from-indigo-500/55 dark:hover:via-purple-500/50 dark:hover:to-pink-500/55 hover:border-indigo-400/70 dark:hover:border-indigo-300/60 transition-all duration-300 shadow-md shadow-purple-300/40 dark:shadow-purple-500/25 hover:shadow-lg hover:shadow-purple-400/50 dark:hover:shadow-purple-400/35 hover:scale-105"
              >
                <span class="hidden sm:inline">{{ user.name }}</span>
                <span class="sm:hidden w-8 h-8 bg-gradient-to-br from-indigo-200 via-purple-200 to-pink-200 dark:from-indigo-400 dark:via-purple-400 dark:to-pink-400 rounded-full flex items-center justify-center font-bold text-sm text-gray-800 dark:text-white">
                  {{ user.name.charAt(0).toUpperCase() }}
                </span>
                <svg 
                  class="w-4 h-4 transition-transform duration-300" 
                  :class="{ 'rotate-180': showDropdown }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <div 
                v-if="showDropdown"
                @click="showDropdown = false"
                class="absolute right-0 mt-3 w-52 bg-white/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/30 py-2 animate-scale-in dark:bg-gray-800/80 dark:border-purple-500/20 overflow-hidden"
              >
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 pointer-events-none"></div>
                <Link 
                  href="/profile" 
                  class="relative block px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 transition-all duration-300 font-medium dark:text-gray-300 dark:hover:from-indigo-900/30 dark:hover:to-purple-900/30 dark:hover:text-indigo-400"
                >
                  <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                  </div>
                </Link>
                <Link 
                  v-if="currentPath !== '/user/words'"
                  href="/user/words" 
                  class="relative block px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 transition-all duration-300 sm:hidden font-medium dark:text-gray-300 dark:hover:from-indigo-900/30 dark:hover:to-purple-900/30 dark:hover:text-indigo-400"
                >
                  <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    My Vocabulary
                  </div>
                </Link>
                <Link 
                  href="/words" 
                  class="relative block px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 transition-all duration-300 sm:hidden font-medium dark:text-gray-300 dark:hover:from-indigo-900/30 dark:hover:to-purple-900/30 dark:hover:text-indigo-400"
                >
                  <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Browse Words
                  </div>
                </Link>
                <hr class="my-2 border-gray-200/50 dark:border-gray-700/50">
                <Link 
                  href="/logout" 
                  method="post" 
                  as="button"
                  class="relative block w-full text-left px-4 py-3 text-red-600 hover:bg-red-50/50 transition-all duration-300 font-medium dark:text-red-400 dark:hover:bg-red-900/30"
                >
                  <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                  </div>
                </Link>
              </div>
            </div>
          </template>

          <template v-else>
            <!-- Guest User Buttons with Enhanced Glassy Effect -->
            <Link 
              href="/login" 
              class="px-5 py-2.5 text-gray-700 hover:text-indigo-600 font-bold transition-all duration-300 dark:text-gray-300 dark:hover:text-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-indigo-500/10 rounded-xl backdrop-blur-sm"
            >
              Login
            </Link>
            <Link 
              href="/register" 
              class="relative px-6 py-2.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-violet-600 text-white rounded-xl font-bold hover:from-indigo-600 hover:via-purple-600 hover:to-violet-700 transform hover:-translate-y-1 transition-all duration-300 shadow-lg shadow-purple-500/40 hover:shadow-xl hover:shadow-purple-500/50 border border-white/20 backdrop-blur-sm overflow-hidden group"
            >
              <span class="relative z-10">Sign Up Free</span>
              <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </Link>
          </template>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

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
</script>

<style scoped>
@keyframes scale-in {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-10px);
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