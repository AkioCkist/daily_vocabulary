<template>
    <Head title="Filter Words - DailyVocab" />
    <div class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-50 to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950">
        <!-- Header -->
        <Header :user="user" />
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-indigo-100/50">
                    <div class="p-6 bg-gradient-to-br from-white to-indigo-50/30 border-b border-indigo-100/50">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Filter Vocabulary Words</h2>
                        
                        <!-- Filter Form -->
                        <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Topic Filter -->
                                <div>
                                    <label for="topic" class="block text-sm font-medium text-gray-700 mb-2">
                                        Topic
                                    </label>
                                    <select
                                        id="topic"
                                        v-model="filterForm.topic"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="">All Topics</option>
                                        <option v-for="topic in topics" :key="topic" :value="topic">
                                            {{ topic }}
                                        </option>
                                    </select>
                                </div>

                                <!-- CEFR Level Filter -->
                                <div>
                                    <label for="cefr_level" class="block text-sm font-medium text-gray-700 mb-2">
                                        CEFR Level
                                    </label>
                                    <select
                                        id="cefr_level"
                                        v-model="filterForm.cefr_level"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="">All Levels</option>
                                        <option v-for="level in cefrLevels" :key="level" :value="level">
                                            {{ level }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Word Search -->
                                <div>
                                    <label for="word_search" class="block text-sm font-medium text-gray-700 mb-2">
                                        Search Word
                                    </label>
                                    <input
                                        id="word_search"
                                        type="text"
                                        v-model="filterForm.word_search"
                                        placeholder="Enter word to search..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>

                                <!-- Meaning Search -->
                                <div>
                                    <label for="meaning_search" class="block text-sm font-medium text-gray-700 mb-2">
                                        Search Meaning
                                    </label>
                                    <input
                                        id="meaning_search"
                                        type="text"
                                        v-model="filterForm.meaning_search"
                                        placeholder="Search in meanings..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>

                                <!-- Filter Buttons -->
                                <div class="md:col-span-2 lg:col-span-4 flex justify-between items-center pt-4">
                                    <div class="flex space-x-3">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                                        >
                                            Apply Filters
                                        </button>
                                        <button
                                            type="button"
                                            @click="clearFilters"
                                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                        >
                                            Clear Filters
                                        </button>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ words.total }} words found
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Results -->
                        <div v-if="words.data.length > 0">
                            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                <div
                                    v-for="word in words.data"
                                    :key="word.id"
                                    class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow"
                                >
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-xl font-bold text-indigo-600">{{ word.word }}</h3>
                                        <div class="flex space-x-2">
                                            <span
                                                v-if="word.cefr_level"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            >
                                                {{ word.cefr_level }}
                                            </span>
                                            <span
                                                v-if="word.topic"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                            >
                                                {{ word.topic }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div v-if="word.pronunciation" class="text-sm text-gray-600 mb-2">
                                        <strong>Pronunciation:</strong> {{ word.pronunciation }}
                                    </div>
                                    
                                    <div class="text-gray-700 mb-3">
                                        <strong>Definition:</strong> {{ word.definition }}
                                    </div>
                                    
                                    <div v-if="word.meaning" class="text-gray-700 mb-3">
                                        <strong>Meaning:</strong> {{ word.meaning }}
                                    </div>
                                    
                                    <div class="text-gray-600 text-sm mb-3">
                                        <strong>Example:</strong> <em>{{ word.example }}</em>
                                    </div>
                                    
                                    <div v-if="word.source" class="text-xs text-gray-500">
                                        Source: {{ word.source }}
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-8 flex justify-center">
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <Link
                                        v-for="link in words.links"
                                        :key="link.label"
                                        :href="link.url"
                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                        :class="{
                                            'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
                                            'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active,
                                            'cursor-not-allowed opacity-50': !link.url
                                        }"
                                        v-html="link.label"
                                    />
                                </nav>
                            </div>
                        </div>

                        <!-- No Results -->
                        <div v-else class="text-center py-12">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.44-.971-5.966-2.536C5.44 11.971 5.04 10.84 5.04 9.666A7.966 7.966 0 0112 2c2.34 0 4.44.971 5.966 2.536A7.966 7.966 0 0120.04 9.666c0 1.174-.4 2.305-.994 3.798-.297.743-.646 1.477-1.046 2.196z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No words found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters to see more results.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive, watch } from 'vue';
import Header from '@/Components/Header.vue';

const props = defineProps({
    words: Object,
    topics: Array,
    cefrLevels: Array,
    filters: Object,
    user: {
        type: Object,
        default: null
    },
});

const filterForm = reactive({
    topic: props.filters.topic || '',
    cefr_level: props.filters.cefr_level || '',
    word_search: props.filters.word_search || '',
    meaning_search: props.filters.meaning_search || '',
});

const applyFilters = () => {
    const query = {};
    
    Object.keys(filterForm).forEach(key => {
        if (filterForm[key]) {
            query[key] = filterForm[key];
        }
    });
    
    router.get(route('words.filter'), query, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    Object.keys(filterForm).forEach(key => {
        filterForm[key] = '';
    });
    
    router.get(route('words.filter'), {}, {
        preserveState: true,
        replace: true,
    });
};

// Watch for changes and apply filters automatically (optional - debounced)
let debounceTimer = null;
watch(filterForm, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 500);
}, { deep: true });
</script>