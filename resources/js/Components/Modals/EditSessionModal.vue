<template>
  <Modal :show="show" max-width="2xl" @close="$emit('close')">
    <div class="p-6 bg-gray-900 text-white rounded-lg">
      <h2 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400 mb-6">
        Edit Session: {{ initialSession.name }}
      </h2>
      
      <section class="mb-8">
        <h3 class="text-xl font-semibold text-white mb-3">Rename Session</h3>
        <form @submit.prevent="updateSessionName" class="flex gap-3">
          <TextInput
            id="name"
            type="text"
            v-model="nameForm.name"
            required
            autocomplete="off"
            class="flex-1 bg-gray-800/70 border-gray-700 text-white placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500"
          />
          <PrimaryButton 
            :class="{ 'opacity-50': nameForm.processing || !nameForm.isDirty }" 
            :disabled="nameForm.processing || !nameForm.isDirty"
          >
            Save Name
          </PrimaryButton>
        </form>
        <InputError class="mt-2" :message="nameForm.errors.name" />
      </section>

      <hr class="border-gray-700 mb-6">

      <section class="space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-xl font-semibold text-white">Words in Session ({{ words.length }})</h3>
          <PrimaryButton 
            @click="updateWordOrderAndContent"
            :class="{ 'opacity-50': orderForm.processing || !wordsDirty }" 
            :disabled="orderForm.processing || !wordsDirty"
          >
            <span v-if="orderForm.processing">Saving...</span>
            <span v-else>Save Order & Changes</span>
          </PrimaryButton>
        </div>

        <p v-if="wordsDirty" class="text-sm text-yellow-400 bg-yellow-900/50 p-3 rounded-lg flex items-center">
            <ExclamationTriangleIcon class="w-5 h-5 mr-2" />
            Order or word count has changed. Click **Save Order & Changes** to apply.
        </p>
        
        <div v-if="words.length > 0">
          <div class="text-sm text-gray-400 bg-gray-800/50 p-3 mb-3 rounded-lg flex items-center">
            <InformationCircleIcon class="w-5 h-5 mr-2 text-indigo-400" />
            Drag and drop the words below to change the order. Click **-** to remove a word.
          </div>
          
          <div class="space-y-2">
            <WordListItem
              v-for="(word, index) in words"
              :key="word.pivot_id"
              :word="word"
              @remove="removeWord(index)"
            />
          </div>
          </div>
        <div v-else class="text-center py-10 bg-gray-800/70 rounded-xl ring-1 ring-gray-700/50">
            <ClipboardDocumentListIcon class="w-10 h-10 text-gray-600 mx-auto mb-3" />
            <p class="text-lg text-gray-400">This session is empty. You should delete it from the main page.</p>
        </div>
      </section>
      
      <div class="mt-8 pt-4 border-t border-gray-700 flex justify-end">
          <SecondaryButton @click="$emit('close')">
              Done Editing
          </SecondaryButton>
      </div>

    </div>
  </Modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
// Assumed base components
import Modal from '@/Components/Modal.vue'; 
import PrimaryButton from '@/Components/PrimaryButton.vue'; 
import SecondaryButton from '@/Components/SecondaryButton.vue'; 
import TextInput from '@/Components/TextInput.vue'; 
import InputError from '@/Components/InputError.vue';
// Custom component
import WordListItem from '@/Pages/SavedSessions/WordListItem.vue'; 
// Icons
import { 
  ExclamationTriangleIcon, 
  InformationCircleIcon, 
  ClipboardDocumentListIcon 
} from '@heroicons/vue/24/outline'; 

// Note: For full drag/drop, you'd import: import draggable from 'vue-draggable-next';

const props = defineProps({
  show: Boolean,
  session: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'updated']);

const initialSession = props.session;

// Deep copy the flashcards for local manipulation (reordering/removal)
const words = ref([...initialSession.flashcards]);
const initialWordsOrder = JSON.stringify(initialSession.flashcards.map(w => w.pivot_id));

// --- 1. Session Renaming Form ---
const nameForm = useForm({
  name: initialSession.name,
});

const updateSessionName = () => {
  nameForm.patch(route('saved-sessions.update', initialSession.slug), {
    preserveScroll: true,
    onSuccess: () => {
        // Reset the form dirty state and notify parent to reload/update view
        nameForm.reset('name');
        emit('updated');
    },
  });
};


// --- 2. Word List / Order Management ---

// Tracks if the local words list has been modified from the initial state
const wordsDirty = computed(() => {
    // Check if the order has changed OR if the number of words has changed (due to removal)
    const currentOrder = JSON.stringify(words.value.map(w => w.pivot_id));
    return currentOrder !== initialWordsOrder || words.value.length !== initialSession.flashcard_count;
});

const orderForm = useForm({
  // Send the ordered list of pivot IDs to the backend
  word_order: computed(() => words.value.map(w => w.pivot_id)),
});

const updateWordOrderAndContent = () => {
  if (!wordsDirty.value) return;

  // Use PATCH request to update the session content (order and removed words)
  orderForm.patch(route('saved-sessions.update', initialSession.slug), {
    preserveScroll: true,
    onSuccess: () => {
      // Notify parent to reload session data, which will update our initial state
      emit('updated');
      // After a successful server update, we must wait for the parent to re-render,
      // which will naturally reset our local state via a new 'session' prop.
    },
  });
};

const removeWord = (index) => {
  if (confirm(`Are you sure you want to remove the word "${words.value[index].word}"?`)) {
    words.value.splice(index, 1);
    // The wordsDirty computed property will now be true.
    // The user must click "Save Order & Changes" to commit this change to the database.
  }
};

// Reset local state when modal is closed/reopened or when new session data is received
watch(() => props.show, (newVal) => {
    if (newVal) {
        // Reset local form and word list when modal is opened
        nameForm.name = initialSession.name;
        nameForm.reset();
        words.value = [...initialSession.flashcards];
    }
});

// Watch for props updates (e.g., after a successful save from parent reload)
watch(() => props.session, (newSession) => {
    // If the session prop changes, update the local state to match the new server data
    nameForm.name = newSession.name;
    words.value = [...newSession.flashcards];
}, { deep: true });
</script>