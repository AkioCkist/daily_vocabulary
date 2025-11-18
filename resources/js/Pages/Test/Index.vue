<template>
  <Head title="Test - DailyVocab" />
  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Vocabulary Test</h1>
                <p class="text-gray-600 dark:text-gray-400">Test your vocabulary knowledge</p>
              </div>
              <div v-if="currentTest" class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">Question</div>
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ currentQuestionIndex + 1 }}/{{ currentTest.items.length }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error</h3>
              <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ error }}</p>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="message" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ message }}</p>
            </div>
          </div>
        </div>

        <!-- Test Filter Board (Always Visible) -->
        <div v-if="!currentTest" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Your Test</h2>
                <p class="text-gray-600 dark:text-gray-400">Customize your test with specific filters</p>
              </div>
            </div>
            
            <form @submit.prevent="generateCustomTest">
              <!-- Filter Categories -->
              <div class="space-y-8 mb-8">
                <!-- CEFR Level -->
                <div>
                  <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 CEFR Level</label>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
                    <button
                      type="button"
                      @click="customTestForm.cefr_level = ''"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200',
                        customTestForm.cefr_level === ''
                          ? 'border-gray-500 bg-gray-50 dark:bg-gray-700'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-center">
                        <div class="text-gray-600 dark:text-gray-400 font-bold text-sm">ALL</div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">All Levels</div>
                      </div>
                    </button>
                    
                    <button
                      type="button"
                      v-for="level in [
                        { value: 'A1', label: 'Beginner', color: 'green' },
                        { value: 'A2', label: 'Elementary', color: 'blue' },
                        { value: 'B1', label: 'Intermediate', color: 'yellow' },
                        { value: 'B2', label: 'Upper Int.', color: 'orange' },
                        { value: 'C1', label: 'Advanced', color: 'red' },
                        { value: 'C2', label: 'Proficient', color: 'purple' }
                      ]"
                      :key="level.value"
                      @click="customTestForm.cefr_level = customTestForm.cefr_level === level.value ? '' : level.value"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200',
                        customTestForm.cefr_level === level.value
                          ? `border-${level.color}-500 bg-${level.color}-50 dark:bg-${level.color}-900/20`
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-center">
                        <div :class="`text-${level.color}-600 dark:text-${level.color}-400 font-bold text-lg`">{{ level.value }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ level.label }}</div>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Topic -->
                <div>
                  <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">🏷️ Topic</label>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <button
                      type="button"
                      @click="customTestForm.topic = ''"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200 text-left',
                        customTestForm.topic === ''
                          ? 'border-gray-500 bg-gray-50 dark:bg-gray-700'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-2xl mb-2">🌍</div>
                      <div class="font-medium text-gray-900 dark:text-white text-sm">All Topics</div>
                    </button>
                    
                    <button
                      type="button"
                      v-for="topic in [
                        { value: 'Business', label: 'Business', icon: '💼' },
                        { value: 'Culture', label: 'Culture', icon: '🎭' },
                        { value: 'Education', label: 'Education', icon: '📚' },
                        { value: 'Environment', label: 'Environment', icon: '🌱' },
                        { value: 'Health', label: 'Health', icon: '🏥' },
                        { value: 'Politics', label: 'Politics', icon: '🏛️' },
                        { value: 'Science', label: 'Science', icon: '🔬' },
                        { value: 'Technology', label: 'Technology', icon: '💻' }
                      ]"
                      :key="topic.value"
                      @click="customTestForm.topic = customTestForm.topic === topic.value ? '' : topic.value"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all duration-200 text-left',
                        customTestForm.topic === topic.value
                          ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                      ]"
                    >
                      <div class="text-2xl mb-2">{{ topic.icon }}</div>
                      <div class="font-medium text-gray-900 dark:text-white text-sm">{{ topic.label }}</div>
                    </button>
                  </div>
                </div>

                <!-- Question Count -->
                <div>
                  <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">⚙️ Question Count</label>
                  <div class="grid grid-cols-4 gap-2 max-w-md">
                    <button
                      type="button"
                      v-for="count in [5, 10, 15, 20]"
                      :key="count"
                      @click="customTestForm.question_count = count.toString()"
                      :class="[
                        'py-3 px-4 rounded-lg border-2 font-medium transition-all duration-200',
                        customTestForm.question_count === count.toString()
                          ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 text-gray-700 dark:text-gray-300'
                      ]"
                    >
                      {{ count }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-600">
                <button 
                  type="button"
                  @click="startDailyTest"
                  :disabled="isGenerating"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2"
                >
                  <span>📅</span>
                  {{ isGenerating ? 'Starting...' : 'Daily Test (10 Random)' }}
                </button>
                
                <button 
                  type="submit"
                  :disabled="isGenerating"
                  class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2"
                >
                  <svg v-if="isGenerating" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isGenerating ? 'Generating...' : 'Generate Custom Test' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Custom Test Form is now integrated above -->

        <!-- No Questions Available Message -->
        <div v-if="currentTest && (!currentTest.items || currentTest.items.length === 0)" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-8 w-8 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-200 mb-2">No Questions Available</h3>
              <p class="text-yellow-700 dark:text-yellow-300 mb-4">
                Sorry, we couldn't find any words matching your criteria. Try adjusting your filters or check back later when more words are added to the database.
              </p>
              <button 
                @click="resetTest"
                class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
              >
                Try Different Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Test Question -->
        <div v-if="currentTest && currentQuestion" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <div class="mb-6">
              <!-- Multiple Choice Question (word_to_definition) -->
              <div v-if="currentQuestion.question_type === 'word_to_definition' && currentQuestion.options">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                  What does "<strong>{{ currentQuestion.word.word }}</strong>" mean?
                </h3>
                
                <div class="space-y-3">
                  <div 
                    v-for="(option, index) in currentQuestion.options" 
                    :key="index"
                    @click="selectAnswer(option)"
                    class="p-4 border-2 rounded-lg cursor-pointer transition-colors"
                    :class="[
                      selectedAnswer === option ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                    ]"
                  >
                    <div class="flex items-center">
                      <div class="w-6 h-6 rounded-full border-2 mr-3" :class="[
                        selectedAnswer === option ? 'border-indigo-500 bg-indigo-500' : 'border-gray-400'
                      ]">
                        <div v-if="selectedAnswer === option" class="w-2 h-2 bg-white rounded-full mx-auto mt-1"></div>
                      </div>
                      <span class="text-gray-700 dark:text-gray-300">{{ option }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Text Input Question (definition_to_word) -->
              <div v-else-if="currentQuestion.question_type === 'definition_to_word'">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                  What word matches this definition?
                </h3>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
                  <p class="text-gray-800 dark:text-gray-200 italic">
                    "{{ currentQuestion.word.definition }}"
                  </p>
                  <p v-if="currentQuestion.word.example" class="text-gray-600 dark:text-gray-400 text-sm mt-2">
                    Example: <em>{{ currentQuestion.word.example }}</em>
                  </p>
                </div>

                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Your answer:
                  </label>
                  <input
                    v-model="selectedAnswer" 
                    type="text"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Type the word here..."
                    @keyup.enter="nextQuestion"
                  />
                </div>
              </div>
            </div>

            <div class="flex justify-between">
              <button
                v-if="currentQuestionIndex > 0"
                @click="previousQuestion"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
              >
                ← Previous
              </button>
              <div v-else></div>

              <button
                @click="nextQuestion"
                :disabled="!selectedAnswer || !selectedAnswer.trim() || isSubmitting"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50"
              >
                <span v-if="currentQuestionIndex < currentTest.items.length - 1">Next →</span>
                <span v-else>{{ isSubmitting ? 'Submitting...' : 'Finish Test' }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Test Results -->
        <div v-if="testResults" class="space-y-6">
          <!-- Overall Score -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-8 text-center">
              <div class="text-6xl mb-4">
                {{ testResults.score >= 80 ? '🎉' : testResults.score >= 60 ? '👍' : '💪' }}
              </div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Test Complete!</h2>
              <div class="text-4xl font-bold mb-4" :class="[
                testResults.score >= 80 ? 'text-green-600' :
                testResults.score >= 60 ? 'text-yellow-600' : 'text-red-600'
              ]">
                {{ testResults.score }}%
              </div>
              <p class="text-gray-600 dark:text-gray-400 mb-6">
                You got {{ testResults.correct_answers }} out of {{ testResults.total_questions }} questions correct!
              </p>
              
              <div class="flex justify-center gap-4">
                <button 
                  @click="resetTest"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                >
                  Take Another Test
                </button>
                <Link href="/review" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                  Review Words
                </Link>
              </div>
            </div>
          </div>

          <!-- Detailed Results -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Detailed Results</h3>
              
              <div class="space-y-4">
                <div 
                  v-for="(result, index) in testResults.results" 
                  :key="index"
                  class="border rounded-lg p-4"
                  :class="[
                    result.is_correct 
                      ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' 
                      : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20'
                  ]"
                >
                  <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                      <span class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-2">
                        Question {{ index + 1 }}
                      </span>
                      <span 
                        :class="[
                          result.is_correct 
                            ? 'text-green-600 dark:text-green-400' 
                            : 'text-red-600 dark:text-red-400'
                        ]"
                      >
                        {{ result.is_correct ? '✓ Correct' : '✗ Incorrect' }}
                      </span>
                    </div>
                  </div>

                  <!-- Question Content -->
                  <div class="mb-3">
                    <div v-if="result.question_type === 'word_to_definition'" class="text-gray-700 dark:text-gray-300">
                      <strong>Word:</strong> {{ result.word }}
                    </div>
                    <div v-else class="text-gray-700 dark:text-gray-300">
                      <strong>Definition:</strong> {{ result.definition }}
                    </div>
                  </div>

                  <!-- Answer Comparison -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Your Answer:</div>
                      <div class="text-gray-900 dark:text-white font-medium">
                        {{ result.user_answer || '(No answer)' }}
                      </div>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Correct Answer:</div>
                      <div class="text-green-600 dark:text-green-400 font-medium">
                        {{ result.correct_answer }}
                      </div>
                    </div>
                  </div>

                  <!-- Multiple Choice Options (if applicable) -->
                  <div v-if="result.options && result.question_type === 'word_to_definition'" class="mt-3">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Options were:</div>
                    <div class="flex flex-wrap gap-2">
                      <span 
                        v-for="option in result.options" 
                        :key="option"
                        class="px-2 py-1 text-xs rounded"
                        :class="[
                          option === result.correct_answer 
                            ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                            : option === result.user_answer && !result.is_correct
                            ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
                        ]"
                      >
                        {{ option }}
                      </span>
                    </div>
                  </div>

                  <!-- Add to Vocabulary Button (for incorrect answers) -->
                  <div v-if="!result.is_correct" class="mt-4 flex justify-end">
                    <button
                      @click="addToVocabulary(result)"
                      :disabled="result.adding_to_vocab"
                      class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                      <svg v-if="result.adding_to_vocab" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <svg v-else-if="result.added_to_vocab" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                      <span v-if="result.added_to_vocab">Added to Vocabulary</span>
                      <span v-else-if="result.adding_to_vocab">Adding...</span>
                      <span v-else>Add to My Vocabulary</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div v-if="currentTest && !testResults && currentTest.items && currentTest.items.length > 0" class="mt-6">
          <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div 
              class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
              :style="{ width: progressPercentage + '%' }"
            ></div>
          </div>
          <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-2">
            {{ progressPercentage.toFixed(0) }}% Complete
          </p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  test: {
    type: Object,
    default: null
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  testResults: {
    type: Object,
    default: null
  },
  message: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  }
});

// State
const currentTest = ref(props.test);
const currentQuestionIndex = ref(0);
const selectedAnswer = ref('');
const answers = ref({});
const showCustomTestForm = ref(false);
const isGenerating = ref(false);
const isSubmitting = ref(false);
const testResults = ref(props.testResults);

const customTestForm = reactive({
  cefr_level: '',
  topic: '',
  question_count: '10'
});

// Computed
const currentQuestion = computed(() => {
  if (!currentTest.value || !currentTest.value.items) return null;
  return currentTest.value.items[currentQuestionIndex.value] || null;
});

const progressPercentage = computed(() => {
  if (!currentTest.value || !currentTest.value.items || currentTest.value.items.length === 0) return 0;
  return ((currentQuestionIndex.value + 1) / currentTest.value.items.length) * 100;
});

// Methods
function startDailyTest() {
  console.log('Starting daily test...');
  isGenerating.value = true;
  const form = useForm({});
  
  form.post('/test/generate-daily', {
    onSuccess: (page) => {
      console.log('Daily test success:', page);
      // Update the component state with the new test
      if (page.props.test) {
        currentTest.value = page.props.test;
        currentQuestionIndex.value = 0;
        selectedAnswer.value = '';
        answers.value = {};
        testResults.value = null;
        
        // Check if test has no items
        if (!page.props.test.items || page.props.test.items.length === 0) {
          console.warn('Generated test has no items');
        }
      }
    },
    onError: (errors) => {
      console.error('Daily test errors:', errors);
    },
    onFinish: () => {
      console.log('Daily test finished');
      isGenerating.value = false;
    }
  });
}

function generateCustomTest() {
  console.log('Starting custom test with config:', customTestForm);
  isGenerating.value = true;
  
  const form = useForm(customTestForm);
  
  form.post('/test/generate', {
    onSuccess: (page) => {
      console.log('Custom test success:', page);
      // Update the component state with the new test
      if (page.props.test) {
        currentTest.value = page.props.test;
        currentQuestionIndex.value = 0;
        selectedAnswer.value = '';
        answers.value = {};
        testResults.value = null;
        
        // Check if test has no items
        if (!page.props.test.items || page.props.test.items.length === 0) {
          console.warn('Generated test has no items');
        }
      }
    },
    onError: (errors) => {
      console.error('Custom test errors:', errors);
    },
    onFinish: () => {
      console.log('Custom test finished');
      isGenerating.value = false;
    }
  });
}

function selectAnswer(answer) {
  selectedAnswer.value = answer;
}

function previousQuestion() {
  if (currentQuestionIndex.value > 0) {
    // Save current answer
    if (selectedAnswer.value) {
      answers.value[currentQuestionIndex.value] = selectedAnswer.value;
    }
    
    currentQuestionIndex.value--;
    selectedAnswer.value = answers.value[currentQuestionIndex.value] || '';
  }
}

function nextQuestion() {
  if (!selectedAnswer.value || !selectedAnswer.value.trim()) return;

  // Save current answer
  answers.value[currentQuestionIndex.value] = selectedAnswer.value.trim();

  if (currentQuestionIndex.value < currentTest.value.items.length - 1) {
    // Move to next question
    currentQuestionIndex.value++;
    selectedAnswer.value = answers.value[currentQuestionIndex.value] || '';
  } else {
    // Submit test
    submitTest();
  }
}

function submitTest() {
  isSubmitting.value = true;
  
  const form = useForm({
    test_id: currentTest.value.id,
    answers: answers.value
  });

  form.post('/test/complete', {
    onSuccess: (page) => {
      // Results will be in page props
      if (page.props.testResults) {
        testResults.value = page.props.testResults;
      }
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
}

function resetTest() {
  currentTest.value = null;
  currentQuestionIndex.value = 0;
  selectedAnswer.value = '';
  answers.value = {};
  testResults.value = null;
  showCustomTestForm.value = false;
}

async function addToVocabulary(result) {
  // Set loading state
  result.adding_to_vocab = true;

  try {
    const response = await fetch('/user/words', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        word_id: result.word_id,
        difficulty_level: 'needs_practice',
        source: 'test_mistake'
      })
    });

    if (response.ok) {
      result.added_to_vocab = true;
      result.adding_to_vocab = false;
      
      // Create a simple toast notification
      showNotification(`"${result.word}" added to your vocabulary list!`, 'success');
    } else {
      throw new Error('Failed to add word to vocabulary');
    }
  } catch (error) {
    console.error('Error adding word to vocabulary:', error);
    result.adding_to_vocab = false;
    showNotification('Failed to add word to vocabulary. Please try again.', 'error');
  }
}

function showNotification(message, type = 'info') {
  // Simple notification using browser's built-in alert for now
  // You could replace this with a proper toast notification library
  if (type === 'success') {
    // Create a simple success message
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.remove();
    }, 3000);
  } else {
    alert(message);
  }
}
</script>