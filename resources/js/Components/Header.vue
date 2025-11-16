<template>
  <header class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm sticky top-0 z-50 shadow-depth-3 border-b border-indigo-700/30 dark:border-indigo-700/30">
    <div class="max-w-7xl mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <!-- Logo/Brand with Enhanced Depth Effects -->
        <Link href="/" class="flex items-center gap-4 group hover-lift">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-violet-500/8 rounded-2xl blur-lg opacity-40 group-hover:opacity-60 transition-opacity animate-pulse-glow"></div>
            <div class="relative w-14 h-14 bg-gradient-to-br from-slate-100 via-indigo-50 to-violet-50 dark:from-slate-800 dark:via-indigo-900/50 dark:to-violet-900/50 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-all duration-300 border border-indigo-700/30 dark:border-indigo-700/30">
              <span class="text-3xl drop-shadow-sm animate-floating">📚</span>
            </div>
          </div>
          <div>
            <h1 class="text-2xl font-black bg-gradient-to-r from-indigo-600 via-violet-600 to-violet-600 bg-clip-text text-transparent dark:from-indigo-600 dark:via-violet-600 dark:to-violet-600 group-hover:scale-105 transition-transform origin-left drop-shadow-sm">
              DailyVocab
            </h1>
            <p class="text-xs text-gray-600 -mt-0.5 font-semibold dark:text-gray-400">Learn & Master Daily</p>
          </div>
        </Link>

        <!-- Navigation -->
        <nav class="flex items-center gap-3">
          <template v-if="user">
            <!-- Navigation Links with Enhanced Glass Effect -->
            <Link 
              v-if="currentPath === '/user/words'"
              href="/" 
              class="px-6 py-3 text-gray-800 hover:text-indigo-600 font-semibold transition-all duration-300 hidden sm:block dark:text-gray-200 dark:hover:text-indigo-600 bg-gray-100/80 dark:bg-gray-800/80 hover:bg-gray-200/90 dark:hover:bg-gray-700/90 rounded-xl border border-indigo-700/30 dark:border-indigo-700/30 shadow-depth-1 hover-lift"
            >
              Browse Words
            </Link>
            <Link 
              v-if="currentPath !== '/user/words'"
              href="/user/words" 
              class="px-6 py-3 text-gray-800 hover:text-indigo-600 font-semibold transition-all duration-300 hidden sm:block dark:text-gray-200 dark:hover:text-indigo-600 bg-gray-100/80 dark:bg-gray-800/80 hover:bg-gray-200/90 dark:hover:bg-gray-700/90 rounded-xl border border-indigo-700/30 dark:border-indigo-700/30 shadow-depth-1 hover-lift"
            >
              My Vocabulary
            </Link>
            
            <!-- Enhanced User Dropdown -->
            <div class="relative">
              <button 
                @click="showDropdown = !showDropdown"
                class="flex items-center gap-3 px-6 py-3 bg-gradient-to-br from-slate-100/90 via-indigo-100/80 to-violet-100/80 dark:from-slate-700/90 dark:via-indigo-800/60 dark:to-violet-800/60 border border-indigo-700/40 dark:border-indigo-600/50 text-gray-900 dark:text-white rounded-xl font-semibold shadow-xl hover:shadow-2xl hover-lift btn-3d transition-all duration-300"
              >
                <span class="hidden sm:inline drop-shadow-sm">{{ user.name }}</span>
                <span class="sm:hidden w-10 h-10 bg-gradient-to-br from-slate-200 via-indigo-100 to-violet-100 dark:from-slate-700 dark:via-indigo-800 dark:to-violet-800 rounded-full flex items-center justify-center font-bold text-sm text-gray-800 dark:text-white shadow-lg">
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
                class="absolute right-0 mt-3 w-52 bg-white/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-indigo-700/30 py-2 animate-scale-in dark:bg-gray-800/80 dark:border-indigo-700/30 overflow-hidden"
              >
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-violet-500/5 pointer-events-none"></div>
                <Link 
                  href="/profile" 
                  class="relative block px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-violet-50 hover:text-indigo-600 transition-all duration-300 font-medium dark:text-gray-300 dark:hover:from-indigo-900/30 dark:hover:to-violet-900/30 dark:hover:text-indigo-600"
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
                  class="relative block px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-violet-50 hover:text-indigo-600 transition-all duration-300 sm:hidden font-medium dark:text-gray-300 dark:hover:from-indigo-900/30 dark:hover:to-violet-900/30 dark:hover:text-indigo-600"
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
                  class="relative block px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-violet-50 hover:text-indigo-600 transition-all duration-300 sm:hidden font-medium dark:text-gray-300 dark:hover:from-indigo-900/30 dark:hover:to-violet-900/30 dark:hover:text-indigo-600"
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
              class="px-5 py-2.5 text-gray-700 hover:text-indigo-600 font-bold transition-all duration-300 dark:text-gray-300 dark:hover:text-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-500/10 rounded-xl backdrop-blur-sm"
            >
              Login
            </Link>
            <Link 
              href="/register" 
              class="relative px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl font-bold hover:from-indigo-500 hover:to-violet-500 transform hover:-translate-y-1 transition-all duration-300 shadow-lg shadow-violet-600/40 hover:shadow-xl hover:shadow-violet-600/50 border border-indigo-700/30 backdrop-blur-sm overflow-hidden group"
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