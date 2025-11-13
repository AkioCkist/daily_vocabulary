<script setup>
defineProps({
    header: {
        type: String,
        default: '',
    },
    subHeader: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="bg-white shadow sm:rounded-lg dark:bg-gray-800">
        <div class="p-6 sm:p-8">
            <header v-if="header || subHeader" class="mb-6">
                <h2 v-if="header" class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ header }}
                </h2>
                <p v-if="subHeader" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ subHeader }}
                </p>
            </header>
            
            <slot />
        </div>
    </div>
</template>
