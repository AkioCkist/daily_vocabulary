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

        <!-- Test Selection -->
        <div v-if="!currentTest" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Choose Your Test</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Daily Test -->
              <div class="border-2 border-indigo-200 dark:border-indigo-800 rounded-lg p-6 hover:border-indigo-500 transition-colors cursor-pointer" @click="startDailyTest">
                <div class="text-center">
                  <div class="text-4xl mb-4">📅</div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Daily Test</h3>
                  <p class="text-gray-600 dark:text-gray-400 mb-4">Take today's curated vocabulary test</p>
                  <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                    Start Daily Test
                  </button>
                </div>
              </div>

              <!-- Custom Test -->
              <div class="border-2 border-green-200 dark:border-green-800 rounded-lg p-6 hover:border-green-500 transition-colors cursor-pointer" @click="showCustomTestForm = true">
                <div class="text-center">
                  <div class="text-4xl mb-4">⚙️</div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Custom Test</h3>
                  <p class="text-gray-600 dark:text-gray-400 mb-4">Create a test with your preferences</p>
                  <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    Create Custom Test
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Custom Test Form -->
        <div v-if="showCustomTestForm" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Custom Test Settings</h2>
            
            <form @submit.prevent="generateCustomTest">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CEFR Level</label>
                  <select v-model="customTestForm.cefr_level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">All Levels</option>
                    <option value="A1">A1 - Beginner</option>
                    <option value="A2">A2 - Elementary</option>
                    <option value="B1">B1 - Intermediate</option>
                    <option value="B2">B2 - Upper Intermediate</option>
                    <option value="C1">C1 - Advanced</option>
                    <option value="C2">C2 - Proficient</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Topic</label>
                  <select v-model="customTestForm.topic" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">All Topics</option>
                    <option value="Technology">Technology</option>
                    <option value="Business">Business</option>
                    <option value="Travel">Travel</option>
                    <option value="Food">Food</option>
                    <option value="Health">Health</option>
                    <option value="Education">Education</option>
                    <option value="Sports">Sports</option>
                    <option value="Science">Science</option>
                    <option value="Nature">Nature</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Questions</label>
                  <select v-model="customTestForm.question_count" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="5">5 Questions</option>
                    <option value="10">10 Questions</option>
                    <option value="15">15 Questions</option>
                    <option value="20">20 Questions</option>
                  </select>
                </div>
              </div>
              
              <div class="flex gap-4">
                <button 
                  type="submit" 
                  :disabled="isGenerating"
                  class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50"
                >
                  <span v-if="!isGenerating">Generate Test</span>
                  <span v-else>Generating...</span>
                </button>
                <button 
                  type="button" 
                  @click="showCustomTestForm = false"
                  class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                >
                  Cancel
                </button>
              </div>
            </form>
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
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div v-if="currentTest && !testResults" class="mt-6">
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
  if (!currentTest.value || !currentTest.value.items) return 0;
  return ((currentQuestionIndex.value + 1) / currentTest.value.items.length) * 100;
});

// Methods
function startDailyTest() {
  const form = useForm({});
  
  form.post('/test/generate', {
    onSuccess: (response) => {
      if (response.props.test) {
        currentTest.value = response.props.test;
        currentQuestionIndex.value = 0;
        selectedAnswer.value = '';
        answers.value = {};
      }
    }
  });
}

function generateCustomTest() {
  isGenerating.value = true;
  
  const form = useForm(customTestForm);
  
  form.post('/test/generate', {
    onSuccess: (page) => {
      // The response will be a full page reload with new props
      // So we don't need to manually update currentTest here
    },
    onFinish: () => {
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
</script>