<template>
  <Head title="Flashcard Practice - DailyVocab" />
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-50 to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950">
    <Header :user="$page.props.auth.user" />

    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Progress Bar -->
      <ProgressBar
        :current-index="currentIndex"
        :total="words.length"
        :correct-count="correctCount"
        :incorrect-count="incorrectCount"
        :flashcard-type="settings.flashcard_type"
      />

      <!-- Flashcard Content -->
      <div v-if="currentWord && !sessionCompleted" class="max-w-2xl mx-auto">
        <!-- Mode Badge for Mixed Mode -->
        <transition name="mode-badge">
          <div v-if="settings.flashcard_type === 'mixed'" class="mb-4 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 text-purple-800 dark:text-purple-300 text-sm font-medium rounded-full shadow-sm">
              <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
              {{ currentMode === 'standard' ? 'Standard Mode' : 'Fill-in-the-Blank Mode' }}
            </span>
          </div>
        </transition>

        <!-- Card Transition Wrapper -->
        <transition name="fade-slide" mode="out-in">
          <div :key="currentIndex">
            <!-- Standard Flashcard -->
            <StandardFlashcard
              v-if="currentMode === 'standard'"
              :word="currentWord"
              :show-definition="showAnswer"
              @toggle="toggleCard"
              @answer="handleStandardAnswer"
            >
              <template #actions>
                <TopicManager
                  :word-id="currentWord.id"
                  :user-topics="userTopics"
                  :adding-to-topic="addingToTopic"
                  @add-to-topic="addToTopic"
                  @topic-created="handleTopicCreated"
                  @topic-deleted="handleTopicDeleted"
                />
              </template>
            </StandardFlashcard>

            <!-- Fill-in-the-Blank Flashcard -->
            <FillBlankFlashcard
              v-else-if="currentMode === 'fill_blank'"
              :word="currentWord"
              v-model:user-answer="userAnswer"
              :current-hint="currentHint"
              :max-hints-reached="maxHintsReached"
              :answered="answered"
              :is-correct="lastAnswerCorrect"
              @submit="submitFillBlankAnswer"
              @hint="getHint"
              @skip="skipWord"
              @next="nextCard"
              ref="fillBlankCard"
            >
              <template #actions>
                <TopicManager
                  :word-id="currentWord.id"
                  :user-topics="userTopics"
                  :adding-to-topic="addingToTopic"
                  @add-to-topic="addToTopic"
                  @topic-created="handleTopicCreated"
                  @topic-deleted="handleTopicDeleted"
                />
              </template>
            </FillBlankFlashcard>
          </div>
        </transition>

        <!-- Navigation -->
        <div class="flex justify-between items-center mt-6">
          <button
            @click="previousCard"
            :disabled="currentIndex === 0"
            class="group flex items-center gap-2 px-5 py-3 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg hover:bg-white dark:hover:bg-gray-800 disabled:hover:bg-transparent"
          >
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="font-medium">Previous</span>
          </button>

          <Link
            :href="route('home')"
            class="px-6 py-3 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all duration-300 font-medium rounded-lg hover:bg-white dark:hover:bg-gray-800"
          >
            Exit Practice
          </Link>

          <button
            @click="nextCard"
            :disabled="currentIndex >= words.length - 1"
            class="group flex items-center gap-2 px-5 py-3 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg hover:bg-white dark:hover:bg-gray-800 disabled:hover:bg-transparent"
          >
            <span class="font-medium">Next</span>
            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Session Complete -->
      <SessionComplete
        v-if="sessionCompleted"
        :total-words="words.length"
        :correct-count="correctCount"
        :incorrect-count="incorrectCount"
        :hints-used="totalHintsUsed"
        :route="route"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import ProgressBar from '@/Components/Flashcard/ProgressBar.vue';
import StandardFlashcard from '@/Components/Flashcard/StandardFlashcard.vue';
import FillBlankFlashcard from '@/Components/Flashcard/FillBlankFlashcard.vue';
import SessionComplete from '@/Components/Flashcard/SessionComplete.vue';
import TopicManager from '@/Components/Flashcard/TopicManager.vue';

// Route helper
const route = (name) => {
  const routes = { 
    'home': '/',
    'flashcards.complete': '/flashcards/complete'
  };
  return routes[name] || '#';
};

// Props
const props = defineProps({
  words: { type: Array, required: true },
  settings: { type: Object, required: true },
  userTopics: { type: Array, default: () => [] }
});

// Make userTopics reactive so it can be updated
const userTopics = ref([...props.userTopics]);

// State
const currentIndex = ref(0);
const showAnswer = ref(false);
const answered = ref(false);
const sessionCompleted = ref(false);
const startTime = ref(null);
const addingToTopic = ref(null);
const userAnswer = ref('');
const currentHint = ref('');
const hintLevel = ref(0);
const maxHintsReached = ref(false);
const lastAnswerCorrect = ref(false);
const correctCount = ref(0);
const incorrectCount = ref(0);
const totalHintsUsed = ref(0);
const wordModes = ref(new Map());
const fillBlankCard = ref(null);

// Computed
const currentWord = computed(() => props.words[currentIndex.value] || null);

const currentMode = computed(() => {
  if (props.settings.flashcard_type === 'mixed') {
    const wordId = currentWord.value?.id;
    if (wordId && !wordModes.value.has(wordId)) {
      const mode = Math.random() < 0.5 ? 'standard' : 'fill_blank';
      wordModes.value.set(wordId, mode);
    }
    return wordModes.value.get(wordId) || 'standard';
  }
  return props.settings.flashcard_type;
});

// Methods
function toggleCard() {
  if (!showAnswer.value) startTime.value = Date.now();
  showAnswer.value = !showAnswer.value;
}

function handleStandardAnswer(isCorrect) {
  answered.value = true;
  lastAnswerCorrect.value = isCorrect;
  
  if (isCorrect) correctCount.value++;
  else incorrectCount.value++;
  
  submitAnswer(isCorrect, null);
  
  // Auto-advance after 1.5s
  setTimeout(() => nextCard(), 1500);
}

function submitAnswer(isCorrect, userAnswerText = null) {
  if (!currentWord.value) return;
  
  const responseTime = startTime.value ? Date.now() - startTime.value : null;
  
  fetch('/flashcards/answer', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      word_id: currentWord.value.id,
      is_correct: isCorrect,
      user_answer: userAnswerText,
      hints_used: hintLevel.value,
      response_time: responseTime,
      flashcard_type: currentMode.value
    })
  }).catch(error => console.error('Answer submission error:', error));
}

async function submitFillBlankAnswer() {
  if (!userAnswer.value.trim()) return;
  
  const responseTime = startTime.value ? Date.now() - startTime.value : null;
  
  try {
    const response = await fetch('/flashcards/answer', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({
        word_id: currentWord.value.id,
        user_answer: userAnswer.value,
        hints_used: hintLevel.value,
        response_time: responseTime,
        flashcard_type: currentMode.value,
      }),
    });

    if (response.ok) {
      const result = await response.json();
      answered.value = true;
      lastAnswerCorrect.value = result.is_correct;
      
      if (result.is_correct) correctCount.value++;
      else incorrectCount.value++;
      
      totalHintsUsed.value += hintLevel.value;
    }
  } catch (error) {
    console.error('Error submitting answer:', error);
  }
}

async function getHint() {
  if (maxHintsReached.value) return;
  
  try {
    const response = await fetch('/flashcards/hint', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({
        word_id: currentWord.value.id,
        current_hint_level: hintLevel.value,
      }),
    });

    if (response.ok) {
      const result = await response.json();
      currentHint.value = result.hint;
      hintLevel.value = result.hint_level;
      maxHintsReached.value = result.max_hints_reached;
    }
  } catch (error) {
    console.error('Error getting hint:', error);
  }
}

function skipWord() {
  answered.value = true;
  lastAnswerCorrect.value = false;
  incorrectCount.value++;
  
  submitAnswer(false, '[SKIPPED]');
}

function nextCard() {
  if (currentIndex.value < props.words.length - 1) {
    currentIndex.value++;
    resetCardState();
    
    if (currentMode.value === 'fill_blank') {
      nextTick(() => fillBlankCard.value?.focus());
    }
  } else {
    sessionCompleted.value = true;
  }
}

function previousCard() {
  if (currentIndex.value > 0) {
    currentIndex.value--;
    resetCardState();
  }
}

function resetCardState() {
  showAnswer.value = false;
  answered.value = false;
  startTime.value = null;
  userAnswer.value = '';
  currentHint.value = '';
  hintLevel.value = 0;
  maxHintsReached.value = false;
}

async function addToTopic(topicId) {
  if (!currentWord.value || addingToTopic.value) return;
  
  addingToTopic.value = topicId;
  
  try {
    const response = await fetch('/flashcards/words/add-to-topic', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        word_id: currentWord.value.id,
        topic_id: topicId,
      }),
    });

    const result = await response.json();
    
    if (response.ok && result.success) {
      console.log('Word added to topic successfully');
      // Emit event so TopicManager can update its UI immediately
      window.dispatchEvent(new CustomEvent('word-added-to-topic', { 
        detail: { wordId: currentWord.value.id, topicId } 
      }));
    } else {
      alert(result.message || 'Failed to add word to topic');
    }
  } catch (error) {
    console.error('Error adding word to topic:', error);
    alert('Network error. Please try again.');
  } finally {
    addingToTopic.value = null;
  }
}

function handleTopicCreated(newTopic) {
  // Add the newly created topic to the userTopics list
  userTopics.value.push(newTopic);
  console.log('New topic added to list:', newTopic);
}

function handleTopicDeleted(topicId) {
  // Remove the deleted topic from the userTopics list
  const index = userTopics.value.findIndex(topic => topic.id === topicId);
  if (index !== -1) {
    userTopics.value.splice(index, 1);
    console.log('Topic removed from list:', topicId);
  }
}
</script>

<style scoped>
.mode-badge-enter-active, .mode-badge-leave-active {
  transition: all 0.3s ease;
}
.mode-badge-enter-from, .mode-badge-leave-to {
  opacity: 0;
  transform: scale(0.8) translateY(-10px);
}

.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(15px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-15px);
}
</style>
