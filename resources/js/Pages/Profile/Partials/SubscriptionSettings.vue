<template>
  <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
    <header class="mb-4">
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Daily Subscription Settings</h2>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Choose what emails you want to receive and how often.</p>
    </header>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Monthly count -->
      <div class="rounded-md border border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-900">
        <span class="text-sm text-gray-600 dark:text-gray-300">Emails received this month:</span>
        <span class="ml-2 font-semibold text-gray-900 dark:text-white">{{ metrics.monthly_email_count }}</span>
      </div>

      <!-- Ads -->
      <div class="flex items-center justify-between">
        <div>
          <label class="text-sm font-medium text-gray-900 dark:text-gray-100">Advertisement Emails</label>
          <p class="text-xs text-gray-500 dark:text-gray-400">Occasional product updates and promotions.</p>
        </div>
        <label class="inline-flex items-center cursor-pointer">
          <input type="checkbox" v-model="form.receive_ads" class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:bg-indigo-600"></div>
        </label>
      </div>

      <!-- Incorrect words digest -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
        <div>
          <label class="text-sm font-medium text-gray-900 dark:text-gray-100">Frequently Incorrect Words</label>
          <p class="text-xs text-gray-500 dark:text-gray-400">Get a digest of words you miss most.</p>
        </div>
        <select v-model="form.incorrect_words_frequency" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
          <option value="none">Do not send</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>

      <!-- Topic summary digest -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
        <div>
          <label class="text-sm font-medium text-gray-900 dark:text-gray-100">Learning Topic Summary</label>
          <p class="text-xs text-gray-500 dark:text-gray-400">A summary of your learning by topic.</p>
        </div>
        <select v-model="form.topic_summary_frequency" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
          <option value="none">Do not send</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50">Save Preferences</button>
        <span v-if="saved" class="text-sm text-green-600">Saved!</span>
      </div>
    </form>
  </section>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  initial: { type: Object, default: () => ({
    receive_ads: false,
    incorrect_words_frequency: 'none',
    topic_summary_frequency: 'none',
  })}
})

const form = useForm({
  receive_ads: props.initial.receive_ads,
  incorrect_words_frequency: props.initial.incorrect_words_frequency,
  topic_summary_frequency: props.initial.topic_summary_frequency,
})

const metrics = reactive({ monthly_email_count: 0 })
const saved = ref(false)

const fetchMetrics = async () => {
  try {
    const res = await fetch('/profile/subscription/metrics', { headers: { Accept: 'application/json' } })
    if (res.ok) {
      const data = await res.json()
      metrics.monthly_email_count = data.monthly_email_count
    }
  } catch (e) { /* ignore */ }
}

const submit = () => {
  saved.value = false
  router.put(route('profile.subscription.update'), form.data(), {
    preserveScroll: true,
    onSuccess: () => { saved.value = true },
  })
}

onMounted(fetchMetrics)
</script>
