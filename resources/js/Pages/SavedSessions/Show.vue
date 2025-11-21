<template>
  <Head :title="`${session.name} - Saved Sessions`" />
  
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <Header :user="user" />

    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <Link 
              :href="route('saved-sessions.index')"
              class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
              <ArrowLeftIcon class="w-5 h-5 mr-2" />
              Back to Sessions
            </Link>
          </div>
          
          <div class="flex items-center gap-2">
            <button
              @click="showReviewModal = true"
              class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
            >
              <PlayIcon class="w-4 h-4 mr-2" />
              Review Session
            </button>
            
            <button
              @click="showDeleteModal = true"
              class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 dark:border-red-600 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors"
            >
              <TrashIcon class="w-4 h-4 mr-2" />
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Session Info Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
              {{ session.name }}
            </h1>
            
            <div v-if="session.topic" class="mb-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                <TagIcon class="w-4 h-4 mr-1" />
                {{ session.topic }}
              </span>
            </div>
          </div>

          <button
            @click="showEditModal = true"
            class="inline-flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
          >
            <PencilIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="flex items-center">
              <Square3Stack3DIcon class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cards</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ session.items?.length || 0 }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="flex items-center">
              <CalendarIcon class="w-8 h-8 text-green-600 dark:text-green-400" />
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ formatDate(session.created_at) }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="flex items-center">
              <ClockIcon class="w-8 h-8 text-orange-600 dark:text-orange-400" />
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ formatDate(session.updated_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Flashcards List -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Flashcards ({{ session.items?.length || 0 }})
          </h2>
        </div>

        <div v-if="session.items && session.items.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
          <div 
            v-for="(item, index) in session.items" 
            :key="item.id"
            class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start gap-4 flex-1">
                <!-- Position Number -->
                <div class="flex-shrink-0">
                  <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 text-sm font-medium">
                    {{ item.position }}
                  </span>
                </div>

                <!-- Word Info -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                      {{ item.word?.word || `Word #${item.flashcard_id}` }}
                    </h3>
                    <span v-if="item.word?.cefr_level" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                      {{ item.word.cefr_level }}
                    </span>
                  </div>
                  
                  <p v-if="item.word?.definition" class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                    {{ item.word.definition }}
                  </p>
                  
                  <p v-if="item.word?.pronunciation" class="text-sm text-gray-500 dark:text-gray-400 italic">
                    /{{ item.word.pronunciation }}/
                  </p>
                  
                  <p v-if="!item.word" class="text-sm text-gray-500 dark:text-gray-400">
                    Flashcard ID: {{ item.flashcard_id }}
                  </p>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center gap-2 ml-4">
                <button
                  @click="moveItemUp(item, index)"
                  :disabled="index === 0"
                  class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  title="Move up"
                >
                  <ChevronUpIcon class="w-4 h-4" />
                </button>
                
                <button
                  @click="moveItemDown(item, index)"
                  :disabled="index === session.items.length - 1"
                  class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  title="Move down"
                >
                  <ChevronDownIcon class="w-4 h-4" />
                </button>
                
                <button
                  @click="removeItem(item)"
                  class="p-1 text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors"
                  title="Remove from session"
                >
                  <XMarkIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="p-8 text-center">
          <Square3Stack3DIcon class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
          <p class="text-gray-500 dark:text-gray-400">
            No flashcards in this session
          </p>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <ReviewSessionModal 
      v-if="showReviewModal"
      :session="session"
      @close="showReviewModal = false"
      @start="startReview"
    />

    <EditSessionModal 
      v-if="showEditModal"
      :session="session"
      @close="showEditModal = false"
      @updated="handleSessionUpdated"
    />

    <ConfirmationModal 
      v-if="showDeleteModal"
      :title="`Delete ${session.name}`"
      :message="'Are you sure you want to delete this saved session? This action cannot be undone.'"
      :confirm-text="'Delete'"
      :confirm-class="'bg-red-600 hover:bg-red-700 focus:ring-red-500'"
      @confirm="confirmDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
  ArrowLeftIcon,
  PlayIcon,
  TrashIcon,
  PencilIcon,
  TagIcon,
  Square3Stack3DIcon,
  CalendarIcon,
  ClockIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline';

// Layout Components
import Header from '@/Components/Header.vue';

// Modal Components
import ReviewSessionModal from '@/Components/Dashboard/ReviewSessionModal.vue';
import EditSessionModal from '@/Components/EditSessionModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  session: {
    type: Object,
    required: true
  }
});

// State
const showReviewModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const startReview = (settings) => {
  router.post(route('saved-sessions.review', props.session.slug), settings);
};

const moveItemUp = (item, index) => {
  console.log('moveItemUp called:', { item, index });
  
  if (index === 0) {
    console.log('Cannot move up - already at top');
    return;
  }
  
  const newPosition = props.session.items[index - 1].position;
  console.log('Moving up to position:', newPosition);
  moveItem(item.id, newPosition);
};

const moveItemDown = (item, index) => {
  console.log('moveItemDown called:', { item, index });
  
  if (index === props.session.items.length - 1) {
    console.log('Cannot move down - already at bottom');
    return;
  }
  
  const newPosition = props.session.items[index + 1].position;
  console.log('Moving down to position:', newPosition);
  moveItem(item.id, newPosition);
};

const moveItem = (itemId, newPosition) => {
  console.log('Moving item:', { itemId, newPosition });
  
  router.put(
    route('saved-sessions.items.move', { slug: props.session.slug, itemId }),
    { new_position: newPosition },
    { 
      preserveScroll: true,
      onBefore: () => {
        console.log('About to move item');
        return true;
      },
      onSuccess: (page) => {
        console.log('Item moved successfully', page);
        console.log('About to reload page...');
        // Refresh the page data to show the new order
        router.reload({
          onStart: () => console.log('Page reload started'),
          onFinish: () => console.log('Page reload finished'),
          onSuccess: () => console.log('Page reload successful'),
          onError: (errors) => console.error('Page reload error:', errors)
        });
      },
      onError: (errors) => {
        console.error('Error moving item:', errors);
        alert('Error moving item: ' + (errors.message || 'Unknown error'));
      },
      onFinish: () => {
        console.log('Move request finished');
      }
    }
  );
};

const removeItem = (item) => {
  if (confirm(`Remove this flashcard from the session?`)) {
    router.delete(
      route('saved-sessions.items.destroy', { slug: props.session.slug, itemId: item.id }),
      { preserveScroll: true }
    );
  }
};

const handleSessionUpdated = () => {
  showEditModal.value = false;
  // Refresh page data
  router.reload({ only: ['session'] });
};

const confirmDelete = () => {
  router.delete(route('saved-sessions.destroy', props.session.slug), {
    onSuccess: () => {
      router.visit(route('saved-sessions.index'));
    }
  });
};
</script>