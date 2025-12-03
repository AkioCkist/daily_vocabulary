<template>
  <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-[#0B0C10] rounded-2xl shadow-2xl shadow-black/60 max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col border border-indigo-700/50">
      
      <!-- Header -->
      <TopicModalHeader @close="$emit('close')" />

      <!-- Main Content -->
      <div class="flex-1 overflow-y-auto p-6">
        
        <!-- Create Form -->
        <TopicForm 
          :is-loading="isCreating"
          :error="createError"
          @submit="handleCreateTopic"
        />

        <!-- Topic Lists -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- System Topics -->
          <TopicListSection 
            title="System Topics"
            :topics="topics?.system || []"
            gradient-class="from-blue-500 to-indigo-600"
            icon-path="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
            empty-message="No system topics available"
            empty-icon-path="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
          >
            <template #default>
              <div v-for="topic in topics?.system || []" :key="`system-${topic.id}`">
                <TopicItem 
                  :topic="topic"
                  :is-custom="false"
                  border-class="border-gray-700 hover:border-blue-500"
                  title-hover-class="group-hover:text-blue-400"
                  @study="handleStudyTopic"
                />
              </div>
              
              <div v-if="!topics?.system?.length" class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p>No system topics available</p>
              </div>
            </template>
          </TopicListSection>

          <!-- Custom Topics -->
          <TopicListSection 
            title="Your Custom Topics"
            :topics="topics?.user || []"
            gradient-class="from-purple-500 to-pink-600"
            icon-path="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
            empty-message="No custom topics yet"
            :show-create-hint="true"
            empty-icon-path="M12 6v6m0 0v6m0-6h6m-6 0H6"
          >
            <template #default>
              <div v-for="topic in topics?.user || []" :key="`user-${topic.id}`">
                <TopicItem 
                  :topic="topic"
                  :is-custom="true"
                  border-class="border-gray-700 hover:border-purple-500"
                  title-hover-class="group-hover:text-purple-400"
                  @study="handleStudyTopic"
                  @edit="handleEditTopic"
                  @delete="handleDeleteTopic"
                />
              </div>
              
              <div v-if="!topics?.user?.length" class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <p>No custom topics yet</p>
                <p class="text-sm mt-1">Create your first topic above!</p>
              </div>
            </template>
          </TopicListSection>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-6 border-t border-indigo-700/50 bg-[#0B0C10] shadow-inner">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-400">
            Total: <span class="font-semibold text-white">{{ (topics?.system?.length || 0) + (topics?.user?.length || 0) }}</span> topics
          </div>
          <button
            @click="$emit('close')"
            class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium shadow-md"
          >
            Close
          </button>
        </div>
      </div>

      <!-- Edit Modal -->
      <EditTopicModal 
        v-if="editingTopic"
        :topic="editingTopic"
        :is-loading="isUpdating"
        @submit="handleUpdateTopic"
        @cancel="cancelEdit"
      />
    </div>
  </div>
</template>

<script setup>
import { useTopicForm } from '@/composables/useTopicForm';
import { useTopicActions } from '@/composables/useTopicActions';
import TopicModalHeader from '@/Components/Modals/TopicModalHeader.vue';
import TopicForm from '@/Components/Modals/TopicForm.vue';
import TopicListSection from '@/Components/Modals/TopicListSection.vue';
import TopicItem from '@/Components/Modals/TopicItem.vue';
import EditTopicModal from '@/Components/Modals/EditTopicModal.vue';

const props = defineProps({
  topics: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['close', 'refresh']);

const { isCreating, createError, submitCreate } = useTopicForm();
const { isUpdating, editingTopic, startEdit, submitUpdate, cancelEdit, deleteTopic, studyTopic } = useTopicActions();

const handleCreateTopic = async (topicData) => {
  try {
    await submitCreate(topicData);
    emit('refresh');
  } catch (error) {
    // Error is handled by composable
  }
};

const handleEditTopic = (topic) => {
  startEdit(topic);
};

const handleUpdateTopic = async (topicData) => {
  try {
    await submitUpdate(topicData);
    emit('refresh');
  } catch (error) {
    // Error is handled by composable
  }
};

const handleDeleteTopic = async (topic) => {
  try {
    await deleteTopic(topic);
    emit('refresh');
  } catch (error) {
    // Cancelled by user or error
  }
};

const handleStudyTopic = (topic) => {
  studyTopic(topic);
};
</script>