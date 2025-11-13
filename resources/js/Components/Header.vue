<template>
  <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 shadow-sm dark:bg-gray-800/80 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <!-- Logo/Brand -->
        <Link href="/" class="flex items-center gap-3 group">
          <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
            <span class="text-xl">📚</span>
          </div>
          <div>
            <h1 class="text-xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">
              DailyVocab
            </h1>
            <p class="text-xs text-gray-500 -mt-1 dark:text-gray-400">Learn daily</p>
          </div>
        </Link>

        <!-- Navigation -->
        <nav class="flex items-center gap-3">
          <template v-if="user">
            <!-- Authenticated User Menu -->
            <Link 
              v-if="currentPath === '/user/words'"
              href="/" 
              class="px-4 py-2 text-gray-700 hover:text-indigo-600 font-medium transition-colors hidden sm:block dark:text-gray-300 dark:hover:text-indigo-400"
            >
              Browse Words
            </Link>
            <Link 
              v-if="currentPath !== '/user/words'"
              href="/user/words" 
              class="px-4 py-2 text-gray-700 hover:text-indigo-600 font-medium transition-colors hidden sm:block dark:text-gray-300 dark:hover:text-indigo-400"
            >
              My Vocabulary
            </Link>
            
            <!-- User Dropdown -->
            <div class="relative">
              <button 
                @click="showDropdown = !showDropdown"
                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-600 transition-all shadow-md"
              >
                <span class="hidden sm:inline">{{ user.name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <div 
                v-if="showDropdown"
                @click="showDropdown = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 py-2 animate-scale-in dark:bg-gray-800 dark:border-gray-700"
              >
                <Link 
                  href="/profile" 
                  class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors dark:text-gray-300 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400"
                >
                  Profile
                </Link>
                <Link 
                  v-if="currentPath !== '/user/words'"
                  href="/user/words" 
                  class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors sm:hidden dark:text-gray-300 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400"
                >
                  My Vocabulary
                </Link>
                <Link 
                  href="/words" 
                  class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors sm:hidden dark:text-gray-300 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400"
                >
                  Browse Words
                </Link>
                <hr class="my-2 dark:border-gray-700">
                <Link 
                  href="/logout" 
                  method="post" 
                  as="button"
                  class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition-colors dark:text-red-400 dark:hover:bg-red-900/30"
                >
                  Logout
                </Link>
              </div>
            </div>
          </template>

          <template v-else>
            <!-- Guest User Buttons -->
            <Link 
              href="/words" 
              class="px-4 py-2 text-gray-700 hover:text-indigo-600 font-medium transition-colors hidden sm:block dark:text-gray-300 dark:hover:text-indigo-400"
            >
              Browse Words
            </Link>
            <Link 
              href="/login" 
              class="px-4 py-2 text-gray-700 hover:text-indigo-600 font-semibold transition-colors dark:text-gray-300 dark:hover:text-indigo-400"
            >
              Login
            </Link>
            <Link 
              href="/register" 
              class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transform hover:-translate-y-0.5 transition-all shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40"
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
