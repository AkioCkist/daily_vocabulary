<template>
  <button
    @click.stop="speak"
    :aria-label="`Speak: ${text}`"
    class="tts-btn"
    :disabled="!text"
    title="Listen"
  >
    <SpeakerWaveIcon class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
  </button>
</template>

<script setup>
import { SpeakerWaveIcon } from '@heroicons/vue/24/outline';
const props = defineProps({
  text: { type: String, required: true },
  lang: { type: String, default: 'en-US' }
});

function speak() {
  if (!props.text) return;
  const utter = new window.SpeechSynthesisUtterance(props.text);
  utter.lang = props.lang;
  window.speechSynthesis.cancel();
  window.speechSynthesis.speak(utter);
}
</script>

<style scoped>
.tts-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.tts-btn:hover {
  background: #e0e7ff;
}
.tts-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
