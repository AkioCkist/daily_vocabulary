<template>
  <div class="md:col-span-2 relative overflow-hidden rounded-2xl bg-black/80 border border-gray-800 p-8 shadow-lg group hover:border-indigo-500/50 transition-colors duration-300">
    <div class="relative z-10 flex flex-col h-full justify-between">
      <div>
        <h2 class="text-3xl font-bold mb-2 text-white drop-shadow">Welcome back, {{ user.name }}</h2>
        <p class="text-gray-200">
          You're on a <span class="text-yellow-400 font-semibold">{{ streakDays }}</span> day streak. Keep the momentum going.
        </p>
      </div>
      <div class="mt-8">
        <button
          @click="$emit('start-review')"
          class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all shadow-lg shadow-indigo-500/30"
        >
          Start Daily Review ({{ wordsDueForReview }})
          <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </button>
      </div>
    </div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-bl-full -mr-10 -mt-10"></div>
  </div>
</template>

<script setup>
defineProps({
  user: {
    type: Object,
    required: true
  },
  streakDays: {
    type: Number,
    default: 0
  },
  wordsDueForReview: {
    type: Number,
    default: 0
  }
});

defineEmits(['start-review']);
</script>
