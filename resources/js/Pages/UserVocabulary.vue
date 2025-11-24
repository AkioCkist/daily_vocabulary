<script setup>
import { ref, computed } from 'vue';
import { useForm, Head, router, usePage } from '@inertiajs/vue3'; // Import 'router' and 'usePage' for clarity/consistency
import Header from '@/Components/Header.vue';
import WordList from '@/Components/WordList.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  userWords: {
    type: Array,
    required: true,
  },
  meta: {
    type: Object,
    required: true,
  },
  user: {
    type: Object,
    default: null
  },
  // Assuming totalWords is passed for the stats card
  totalWords: {
    type: Number,
    default: 0
  }
});

// The reactive state for the word list and pagination metadata
const userWords = ref(props.userWords);
const meta = ref(props.meta || {});

// Functionality RESTORED: Handle pagination by reloading data via Inertia
function changePage(page) {
    // Reload user words page using Inertia GET request
    router.get(route('user.vocabulary'), { page: page }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (p) => {
            // Update local state with new data if necessary, though Inertia handles most of this.
            // This is mainly to reflect the new state from the server response if full state preservation isn't used.
            userWords.value = p.props.userWords;
            meta.value = p.props.meta;
        }
    });
}

// Functionality RESTORED: Handle word removal
function removeWord(wordId) {
    if (!confirm('Are you sure you want to remove this word from your vocabulary?')) {
        return;
    }
    
    const form = useForm({});
    
    // Original functional logic for deletion and state update
    form.delete(route('user.vocabulary.destroy', wordId), {
        preserveScroll: true,
        onSuccess: () => {
            // Update the local list to remove the word immediately
            userWords.value = userWords.value.filter(word => word.id !== wordId);
            
            // Show a success message if you have a flash/toast component
            console.log(`Word with ID ${wordId} removed successfully.`);
            
            // Optional: If you also pass 'totalWords', update it
            if (props.totalWords > 0) {
              props.totalWords--;
            }
        },
        onError: (errors) => {
            console.error("Failed to remove word:", errors);
            alert('Failed to remove word. Please try again.');
        },
    });
}

// Stats calculation for the card (assuming totalWords is a prop or can be derived from meta)
const totalWordsLearned = computed(() => props.totalWords || meta.value.total || 0);
</script>

<template>
  <Head title="My Vocabulary - DailyVocab" />
  
  <div class="min-h-screen bg-gray-50 dark:bg-[#0B0C10] text-slate-900 dark:text-slate-100 font-sans">
    
    <Header :user="user" />

    <div class="max-w-4xl mx-auto px-4 py-8">
      
      <div class="mb-10 pt-4">
          <div class="flex items-center gap-3">
              <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.405 9.332 5 7.828 5 5.253 5 3.12 7.058 3 9.61c.075 1.54 1.343 2.87 2.843 3.39L12 18.75l6.157-5.75c1.5-1.4 2.843-2.7 2.843-4.2C21 6.8 17.5 3.3 12 3.3z" />
              </svg>
              <h1 class="text-3xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                  My Vocabulary
              </h1>
          </div>
          <p class="mt-1 ml-10 text-gray-500 dark:text-gray-400">Your personal collection of learned and saved words.</p>
      </div>

      <div 
        class="bg-white dark:bg-[#111216] p-6 mb-8 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 transition-shadow hover:shadow-lg"
      >
        <div class="flex items-center justify-between">
          <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              Vocabulary Size
            </span>
            <div class="flex items-center mt-1">
              <span class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">
                {{ totalWordsLearned }}
              </span>
              <p class="ml-3 text-sm text-gray-500 dark:text-gray-400">Words Learned</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Keep it up!</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">You're doing great 🚀</p>
          </div>
        </div>
      </div>

      <WordList 
        :words="userWords" 
        :show-remove="true" 
        @remove-word="removeWord" 
      />

      <Pagination
        v-if="userWords.length > 0 && meta.links"
        :meta="meta"
        @change-page="changePage"
      />
      
      <div v-if="!userWords || userWords.length === 0" class="text-center py-10">
          <p class="text-xl font-medium text-gray-500 dark:text-gray-400">
              Your personal vocabulary is empty.
          </p>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
              Start learning new words to populate this list!
          </p>
      </div>

    </div>
  </div>
</template>