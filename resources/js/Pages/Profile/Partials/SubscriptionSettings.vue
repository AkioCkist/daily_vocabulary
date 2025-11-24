<template>
  <section>
    <header class="mb-4">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Email Preferences</h2>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose which learning digests and updates you want to receive.</p>
    </header>

    <form @submit.prevent="submit" class="space-y-6">
      
      <div class="rounded-lg border border-gray-300 dark:border-gray-700 p-3 bg-gray-100 dark:bg-gray-800/60">
        <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">Emails received this month:</span>
        <span class="ml-2 font-bold text-gray-900 dark:text-white">{{ metrics.monthly_email_count }}</span>
      </div>

      <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-800">
        <div>
          <label class="text-sm font-medium text-gray-900 dark:text-gray-100">Advertisement Emails</label>
          <p class="text-xs text-gray-500 dark:text-gray-400">Occasional product updates and promotions.</p>
        </div>
        <label class="inline-flex relative items-center cursor-pointer">
          <input type="checkbox" v-model="form.receive_ads" class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
        </label>
      </div>
      
      <div class="space-y-2">
        <label for="incorrect_words_frequency" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
          Incorrect Words Digest
        </label>
        <p class="text-xs text-gray-500 dark:text-gray-400">Receive a list of words you struggled with recently.</p>
        <select 
          id="incorrect_words_frequency" 
          v-model="form.incorrect_words_frequency"
          class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-lg shadow-sm text-sm dark:text-white"
        >
          <option value="none">Do not send</option>
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>

      <div class="space-y-2">
        <label for="topic_summary_frequency" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
          Topic Summary Digest
        </label>
        <p class="text-xs text-gray-500 dark:text-gray-400">A summary of your progress in a selected topic.</p>
        <select 
          id="topic_summary_frequency" 
          v-model="form.topic_summary_frequency"
          class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-lg shadow-sm text-sm dark:text-white"
        >
          <option value="none">Do not send</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>

      <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
        <button
            type="submit"
            :disabled="form.processing"
            class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg 
                    hover:bg-indigo-700 transition-colors duration-300 
                    shadow-md shadow-indigo-600/30 dark:shadow-indigo-500/50 disabled:opacity-50"
        >
            Save Preferences
        </button>
        <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
        >
            <span 
                v-if="saved" 
                class="text-sm font-medium text-green-600 dark:text-green-400"
            >
                Saved!
            </span>
        </Transition>
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

const submit = () => {
  saved.value = false;
  form.patch(route('profile.subscription.update'), {
    preserveScroll: true,
    onSuccess: () => {
      saved.value = true
      setTimeout(() => { saved.value = false }, 3000)
    }
  })
}

const fetchMetrics = async () => {
  try {
    const res = await fetch('/profile/subscription/metrics', { headers: { Accept: 'application/json' } })
    if (res.ok) {
      const data = await res.json()
      metrics.monthly_email_count = data.monthly_email_count
    }
  } catch (error) {
    console.error('Failed to fetch subscription metrics:', error)
  }
}

onMounted(() => {
  fetchMetrics()
})

</script>