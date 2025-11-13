<template>
  <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <!-- Logo/Brand -->
        <Link href="/" class="flex items-center gap-3 group">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
            <span class="text-xl">📚</span>
          </div>
          <div>
            <h1 class="text-xl font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
              DailyVocab
            </h1>
            <p class="text-xs text-gray-500 -mt-1">Learn daily</p>
          </div>
        </Link>

        <!-- Navigation -->
        <nav class="flex items-center gap-3">
          <template v-if="user">
            <!-- Authenticated User Menu -->
            <Link 
              href="/" 
              class="px-4 py-2 text-gray-700 hover:text-purple-600 font-medium transition-colors hidden sm:block"
            >
              Browse Words
            </Link>
            <Link 
              href="/user/words" 
              class="px-4 py-2 text-gray-700 hover:text-purple-600 font-medium transition-colors hidden sm:block"
            >
              My Vocabulary
            </Link>
            
            <!-- User Dropdown -->
            <div class="relative">
              <button 
                @click="showDropdown = !showDropdown"
                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl font-semibold hover:from-purple-600 hover:to-pink-600 transition-all shadow-md"
              >
                <span class="hidden sm:inline">{{ user.name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <div 
                v-if="showDropdown"
                @click="showDropdown = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 py-2 animate-scale-in"
              >
                <Link 
                  href="/profile" 
                  class="block px-4 py-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors"
                >
                  Profile
                </Link>
                <Link 
                  href="/user/words" 
                  class="block px-4 py-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors sm:hidden"
                >
                  My Vocabulary
                </Link>
                <Link 
                  href="/words" 
                  class="block px-4 py-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors sm:hidden"
                >
                  Browse Words
                </Link>
                <hr class="my-2">
                <Link 
                  href="/logout" 
                  method="post" 
                  as="button"
                  class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition-colors"
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
              class="px-4 py-2 text-gray-700 hover:text-purple-600 font-medium transition-colors hidden sm:block"
            >
              Browse Words
            </Link>
            <Link 
              href="/login" 
              class="px-4 py-2 text-gray-700 hover:text-purple-600 font-semibold transition-colors"
            >
              Login
            </Link>
            <Link 
              href="/register" 
              class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-bold hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all shadow-md hover:shadow-lg"
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
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  user: {
    type: Object,
    default: null
  }
});

const showDropdown = ref(false);
</script>
