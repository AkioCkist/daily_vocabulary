<template>
  <footer class="py-16 text-center text-sm text-black dark:text-white/70">
    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
  </footer>
</template>
<script setup>
defineProps({ laravelVersion: String, phpVersion: String });
</script>
