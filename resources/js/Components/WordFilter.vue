<template>
  <div class="bg-white/95 dark:bg-gray-800/95 rounded-3xl shadow-depth-4 p-8 border border-gray-200/50 dark:border-gray-600/50 hover-lift animate-scale-in relative overflow-hidden group">
    <!-- Background decoration -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/8 via-purple-600/8 to-pink-600/8 dark:from-indigo-400/8 dark:via-purple-400/8 dark:to-pink-400/8 opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
    
    <!-- Header -->
    <div class="mb-6 relative z-10">
      <div class="flex items-center gap-4 mb-4">
        <div class="relative">
          <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl opacity-30 animate-pulse-glow"></div>
          <div class="relative w-14 h-14 bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-depth-3 border border-white/20">
            <span class="text-3xl drop-shadow-sm">🔍</span>
          </div>
        </div>
        <div class="flex-1">
          <h2 class="text-3xl font-black text-gray-900 tracking-tight dark:text-white drop-shadow-sm">Find Words</h2>
          <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Search through our vocabulary collection</p>
        </div>
      </div>
    </div>

    <!-- Search Input -->
    <div class="mb-6 relative z-10">
      <div class="relative">
        <input
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search for words..."
          class="w-full px-6 py-4 pr-12 text-lg bg-gray-50/80 dark:bg-gray-700/40 border border-gray-200/40 dark:border-gray-600/30 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all duration-300 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
        />
        <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
          <svg v-if="!isSearching" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <svg v-else class="w-6 h-6 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- CEFR Level Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Level</label>
          <select 
            v-model="filters.cefr_level"
            @change="handleSearch"
            class="w-full px-4 py-3 bg-gray-50/80 dark:bg-gray-700/40 border border-gray-200/40 dark:border-gray-600/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all duration-300 text-gray-900 dark:text-white"
          >
            <option value="">All Levels</option>
            <option value="A1">A1 - Beginner</option>
            <option value="A2">A2 - Elementary</option>
            <option value="B1">B1 - Intermediate</option>
            <option value="B2">B2 - Upper Intermediate</option>
            <option value="C1">C1 - Advanced</option>
            <option value="C2">C2 - Proficient</option>
          </select>
        </div>

        <!-- Topic Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Topic</label>
          <select 
            v-model="filters.topic"
            @change="handleSearch"
            class="w-full px-4 py-3 bg-gray-50/80 dark:bg-gray-700/40 border border-gray-200/40 dark:border-gray-600/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all duration-300 text-gray-900 dark:text-white"
          >
            <option value="">All Topics</option>
            <option value="Technology">Technology</option>
            <option value="Business">Business</option>
            <option value="Travel">Travel</option>
            <option value="Food">Food</option>
            <option value="Health">Health</option>
            <option value="Education">Education</option>
            <option value="Sports">Sports</option>
            <option value="Entertainment">Entertainment</option>
            <option value="Science">Science</option>
            <option value="Nature">Nature</option>
          </select>
        </div>

        <!-- Source Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Source</label>
          <select 
            v-model="filters.source"
            @change="handleSearch"
            class="w-full px-4 py-3 bg-gray-50/80 dark:bg-gray-700/40 border border-gray-200/40 dark:border-gray-600/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all duration-300 text-gray-900 dark:text-white"
          >
            <option value="">All Sources</option>
            <option value="oxford">Oxford</option>
            <option value="cambridge">Cambridge</option>
            <option value="merriam-webster">Merriam-Webster</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Results -->
    <div v-if="searchResults.length > 0" class="relative z-10">
      <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Search Results</h3>
      <div class="space-y-3 max-h-96 overflow-y-auto">
        <div 
          v-for="word in searchResults" 
          :key="word.id"
          @click="selectWord(word)"
          class="p-4 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl border border-gray-200/40 dark:border-gray-600/30 hover:bg-indigo-50/80 dark:hover:bg-indigo-900/20 hover:border-indigo-300/60 dark:hover:border-indigo-600/60 cursor-pointer transition-all duration-300 group"
        >
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                  {{ word.word }}
                </h4>
                <span class="text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-1 rounded-full font-medium">
                  {{ word.cefr_level }}
                </span>
                <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 px-2 py-1 rounded-full font-medium">
                  {{ word.topic }}
                </span>
              </div>
              <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ word.definition }}</p>
            </div>
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button class="w-8 h-8 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="searchQuery && !isSearching" class="relative z-10 text-center py-8">
      <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
      <p class="text-gray-500 dark:text-gray-400 font-medium">No words found matching your search</p>
      <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Try adjusting your filters or search terms</p>
    </div>

    <!-- Default State -->
    <div v-else-if="!searchQuery" class="relative z-10 text-center py-8">
      <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
      <p class="text-gray-600 dark:text-gray-400 font-medium">Start typing to search for words</p>
      <p class="text-gray-500 dark:text-gray-500 text-sm mt-1">Use filters to narrow down your search</p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const emit = defineEmits(['word-selected']);

// Reactive state
const searchQuery = ref('');
const isSearching = ref(false);
const searchResults = ref([]);

const filters = reactive({
  cefr_level: '',
  topic: '',
  source: ''
});

let searchTimeout = null;

/**
 * Handle search with debouncing
 */
function handleSearch() {
  // Clear previous timeout
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  // If search is empty, clear results
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    return;
  }

  // Set loading state
  isSearching.value = true;

  // Debounce search
  searchTimeout = setTimeout(() => {
    performSearch();
  }, 300);
}

/**
 * Perform the actual search
 */
function performSearch() {
  const params = {
    search: searchQuery.value,
    ...filters
  };

  // Remove empty filters
  Object.keys(params).forEach(key => {
    if (params[key] === '') {
      delete params[key];
    }
  });

  router.get('/api/words/search', params, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (response) => {
      searchResults.value = response.props.words || [];
      isSearching.value = false;
    },
    onError: () => {
      searchResults.value = [];
      isSearching.value = false;
    }
  });
}

/**
 * Select a word
 */
function selectWord(word) {
  emit('word-selected', word);
  
  // You could also navigate to a word detail page
  // router.visit(`/words/${word.id}`);
}
</script>