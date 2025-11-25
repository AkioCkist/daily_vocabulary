<template>
  <div class="space-y-4">
    <!-- Template Selector -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-300">
        Load Template
      </label>
      <div class="flex gap-2">
        <select
          v-model="selectedTemplateId"
          @change="loadSelectedTemplate"
          class="flex-1 px-4 py-2.5 bg-gray-800/90 border border-gray-700 text-white rounded-lg 
                 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 
                 hover:bg-gray-800 hover:border-gray-600
                 transition-all duration-200 ease-in-out
                 cursor-pointer appearance-none
                 bg-[url('data:image/svg+xml;charset=UTF-8,%3csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22none%22%3E%3cpath%20d%3D%22M7%207l3-3%203%203m0%206l-3%203-3-3%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%221.5%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3c%2Fsvg%3E')]
                 bg-[length:1.25rem] bg-[right_0.5rem_center] bg-no-repeat
                 pr-10"
        >
          <option :value="null" disabled selected class="bg-gray-800 text-gray-400">-- Select a template --</option>
          <option
            v-for="template in templates"
            :key="template.id"
            :value="template.id"
            class="bg-gray-800 text-white hover:bg-indigo-600 py-2"
          >
            {{ template.name }}
          </option>
        </select>
        <button
          v-if="selectedTemplateId"
          @click="deleteCurrentTemplate"
          class="px-3 py-2 bg-red-500/20 text-red-400 rounded-lg 
                 hover:bg-red-500/30 hover:scale-105
                 transition-all duration-200 ease-in-out
                 active:scale-95"
          title="Delete template"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Template Actions -->
    <div class="flex gap-2">
      <!-- Save Template -->
      <button
        @click="showSaveForm = !showSaveForm"
        class="flex-1 px-4 py-2 bg-indigo-500/20 text-indigo-400 rounded-lg 
               hover:bg-indigo-500/30 hover:scale-105
               transition-all duration-200 ease-in-out
               active:scale-95
               flex items-center justify-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
        </svg>
        Save
      </button>

      <!-- Import Template -->
      <label class="flex-1 px-4 py-2 bg-green-500/20 text-green-400 rounded-lg 
                    hover:bg-green-500/30 hover:scale-105
                    transition-all duration-200 ease-in-out
                    active:scale-95
                    flex items-center justify-center gap-2 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        Import
        <input
          type="file"
          accept=".json"
          @change="importTemplate"
          class="hidden"
          ref="fileInput"
        >
      </label>

      <!-- Export Template -->
      <button
        v-if="selectedTemplateId"
        @click="exportTemplate"
        class="flex-1 px-4 py-2 bg-purple-500/20 text-purple-400 rounded-lg 
               hover:bg-purple-500/30 hover:scale-105
               transition-all duration-200 ease-in-out
               active:scale-95
               flex items-center justify-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-12"/>
        </svg>
        Export
      </button>
    </div>

    <!-- Save Template Form -->
    <transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="showSaveForm" class="p-4 bg-indigo-900/20 rounded-xl border border-indigo-800">
        <h4 class="text-sm font-medium text-white mb-3">Save Current Settings as Template</h4>
        <div class="space-y-3">
          <input
            v-model="newTemplateName"
            type="text"
            placeholder="Template name"
            class="w-full px-3 py-2 rounded-lg border border-gray-600 bg-gray-800 text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            @keyup.enter="saveTemplate"
          >
          <div class="flex gap-2">
            <button
              @click="saveTemplate"
              :disabled="!newTemplateName.trim()"
              class="flex-1 px-4 py-2 bg-indigo-500 text-white rounded-lg font-medium hover:bg-indigo-600 disabled:bg-gray-600 disabled:cursor-not-allowed transition-colors"
            >
              Save
            </button>
            <button
              @click="cancelSave"
              class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  currentSettings: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['templateLoaded']);

const templates = ref([]);
const selectedTemplateId = ref(null);
const showSaveForm = ref(false);
const newTemplateName = ref('');
const fileInput = ref(null);

// Load templates on mount
onMounted(async () => {
  await fetchTemplates();
});

async function fetchTemplates() {
  try {
    const response = await fetch('/flashcards/templates');
    const data = await response.json();
    if (data.success) {
      templates.value = data.templates;
    }
  } catch (error) {
    console.error('Failed to fetch templates:', error);
  }
}

async function saveTemplate() {
  if (!newTemplateName.value.trim()) return;

  try {
    const response = await fetch('/flashcards/templates', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({
        name: newTemplateName.value,
        settings: props.currentSettings
      })
    });

    const data = await response.json();

    if (data.success) {
      templates.value.push(data.template);
      selectedTemplateId.value = data.template.id;
      newTemplateName.value = '';
      showSaveForm.value = false;
      // Show success message (you can use a toast notification library)
      alert('Template saved successfully!');
    } else {
      alert(data.message || 'Failed to save template');
    }
  } catch (error) {
    console.error('Failed to save template:', error);
    alert('Failed to save template');
  }
}

function cancelSave() {
  newTemplateName.value = '';
  showSaveForm.value = false;
}

async function loadSelectedTemplate() {
  if (!selectedTemplateId.value) return;

  try {
    const response = await fetch(`/flashcards/templates/${selectedTemplateId.value}`);
    const data = await response.json();

    if (data.success) {
      emit('templateLoaded', data.template.settings);
    }
  } catch (error) {
    console.error('Failed to load template:', error);
  }
}

async function deleteCurrentTemplate() {
  if (!selectedTemplateId.value) return;
  
  if (!confirm('Are you sure you want to delete this template?')) return;

  try {
    const response = await fetch(`/flashcards/templates/${selectedTemplateId.value}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    const data = await response.json();

    if (data.success) {
      templates.value = templates.value.filter(t => t.id !== selectedTemplateId.value);
      selectedTemplateId.value = null;
      alert('Template deleted successfully!');
    }
  } catch (error) {
    console.error('Failed to delete template:', error);
  }
}

async function importTemplate(event) {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('file', file);

  try {
    const response = await fetch('/flashcards/templates/import', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      templates.value.push(data.template);
      selectedTemplateId.value = data.template.id;
      emit('templateLoaded', data.template.settings);
      alert('Template imported successfully!');
    } else {
      alert(data.message || 'Failed to import template');
    }
  } catch (error) {
    console.error('Failed to import template:', error);
    alert('Failed to import template');
  }

  // Reset file input
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}

async function exportTemplate() {
  if (!selectedTemplateId.value) return;

  try {
    const response = await fetch(`/flashcards/templates/${selectedTemplateId.value}/export`);
    const data = await response.json();

    // Create a blob and download
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `flashcard_template_${data.name.replace(/\s+/g, '_').toLowerCase()}.json`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
  } catch (error) {
    console.error('Failed to export template:', error);
    alert('Failed to export template');
  }
}
</script>
