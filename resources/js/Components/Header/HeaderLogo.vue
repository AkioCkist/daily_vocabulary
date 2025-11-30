<template>
  <Link href="/" class="flex items-center gap-3 group">
    <div
      class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg transition-transform group-hover:scale-105 duration-300"
    >
      <span class="text-xl text-white font-bold">V</span>
    </div>

    <div>
      <h1
        class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400 transition-colors"
      >
        DailyVocab
      </h1>
      <p
        class="text-xs text-gray-500 -mt-0.5 font-medium dark:text-gray-400 hidden sm:block"
      >
        Vocabulary Mastery Platform
      </p>
    </div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
