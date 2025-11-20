<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button
                            @click="exitSession"
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ settings.flashcard_type === 'standard' ? 'Standard' : 'Fill-in-the-Blank' }} Flashcards
                            </h1>
                            <p class="text-gray-600">{{ settings.mode }} mode</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Progress</div>
                        <div class="text-xl font-semibold text-gray-900">{{ currentIndex + 1 }} / {{ words.length }}</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="bg-gray-200 rounded-full h-2">
                        <div 
                            class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                            :style="{ width: progressPercentage + '%' }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Flashcard Content -->
            <div v-if="!sessionCompleted" class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Standard Flashcard Mode -->
                <div v-if="settings.flashcard_type === 'standard'" class="p-8">
                    <div class="text-center">
                        <!-- Word Display -->
                        <div class="mb-8">
                            <h2 class="text-4xl font-bold text-gray-900 mb-2">{{ currentWord.word }}</h2>
                            <p v-if="currentWord.pronunciation" class="text-lg text-gray-600 mb-2">
                                /{{ currentWord.pronunciation }}/
                            </p>
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                {{ currentWord.cefr_level }}
                            </span>
                        </div>

                        <!-- Show Definition (after first interaction) -->
                        <div v-if="showDefinition" class="mb-8 p-4 bg-gray-50 rounded-lg">
                            <p class="text-lg text-gray-800 mb-2">{{ currentWord.definition }}</p>
                            <p v-if="currentWord.example" class="text-sm text-gray-600 italic">
                                "{{ currentWord.example }}"
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div v-if="!showDefinition" class="space-x-4">
                            <button
                                @click="revealDefinition"
                                class="px-6 py-3 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors"
                            >
                                Show Definition
                            </button>
                            <button
                                @click="markForgotten"
                                class="px-6 py-3 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors"
                            >
                                I Don't Remember
                            </button>
                        </div>

                        <!-- Answer Buttons (after definition shown) -->
                        <div v-if="showDefinition && !answered" class="space-x-4">
                            <button
                                @click="submitAnswer(true)"
                                class="px-8 py-3 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition-colors"
                            >
                                ✓ I Knew It
                            </button>
                            <button
                                @click="submitAnswer(false)"
                                class="px-8 py-3 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors"
                            >
                                ✗ I Didn't Know
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Fill-in-the-Blank Mode -->
                <div v-if="settings.flashcard_type === 'fill_blank'" class="p-8">
                    <div class="text-center">
                        <!-- Definition Display -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">What word means:</h2>
                            <p class="text-xl text-gray-800 mb-2 p-4 bg-gray-50 rounded-lg">{{ currentWord.definition }}</p>
                            <p v-if="currentWord.example" class="text-sm text-gray-600 italic mb-4">
                                "{{ currentWord.example }}"
                            </p>
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                {{ currentWord.cefr_level }}
                            </span>
                        </div>

                        <!-- Hint Display -->
                        <div v-if="currentHint" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="text-sm text-yellow-700 mb-2">Hint:</div>
                            <div class="text-2xl font-mono font-bold text-yellow-800 tracking-widest">
                                {{ currentHint }}
                            </div>
                            <div class="text-xs text-yellow-600 mt-2">
                                {{ hintLevel }} hint{{ hintLevel > 1 ? 's' : '' }} used
                            </div>
                        </div>

                        <!-- Input Field -->
                        <div class="mb-6">
                            <input
                                ref="answerInput"
                                v-model="userAnswer"
                                @keyup.enter="submitFillBlankAnswer"
                                :disabled="answered"
                                type="text"
                                placeholder="Enter the word..."
                                class="w-full max-w-md px-4 py-3 text-xl text-center border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors"
                            />
                        </div>

                        <!-- Action Buttons -->
                        <div v-if="!answered" class="space-x-4 mb-4">
                            <button
                                @click="submitFillBlankAnswer"
                                :disabled="!userAnswer.trim()"
                                class="px-6 py-3 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                            >
                                Submit Answer
                            </button>
                            <button
                                @click="getHint"
                                :disabled="maxHintsReached"
                                class="px-6 py-3 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                            >
                                {{ maxHintsReached ? 'No More Hints' : 'Get Hint' }}
                            </button>
                            <button
                                @click="markForgotten"
                                class="px-6 py-3 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors"
                            >
                                I Don't Remember
                            </button>
                        </div>

                        <!-- Answer Feedback -->
                        <div v-if="answered" class="mb-6">
                            <div :class="[
                                'p-4 rounded-lg text-center',
                                lastAnswerCorrect ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'
                            ]">
                                <div :class="[
                                    'text-xl font-bold mb-2',
                                    lastAnswerCorrect ? 'text-green-800' : 'text-red-800'
                                ]">
                                    {{ lastAnswerCorrect ? '✓ Correct!' : '✗ Incorrect' }}
                                </div>
                                <div class="text-lg text-gray-800">
                                    The correct answer is: <strong>{{ currentWord.word }}</strong>
                                </div>
                                <div v-if="currentWord.pronunciation" class="text-sm text-gray-600">
                                    /{{ currentWord.pronunciation }}/
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Button -->
                <div v-if="answered" class="p-6 bg-gray-50 border-t">
                    <div class="text-center">
                        <button
                            @click="nextWord"
                            class="px-8 py-3 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            {{ currentIndex >= words.length - 1 ? 'Complete Session' : 'Next Word' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Session Completed -->
            <div v-if="sessionCompleted" class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Session Complete!</h2>
                    <p class="text-gray-600">Great job practicing your vocabulary!</p>
                </div>

                <!-- Session Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ words.length }}</div>
                        <div class="text-sm text-gray-600">Total Words</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ correctCount }}</div>
                        <div class="text-sm text-gray-600">Correct</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">{{ incorrectCount }}</div>
                        <div class="text-sm text-gray-600">Incorrect</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ totalHintsUsed }}</div>
                        <div class="text-sm text-gray-600">Hints Used</div>
                    </div>
                </div>

                <button
                    @click="returnToHome"
                    class="px-8 py-3 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors"
                >
                    Return to Dashboard
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    words: Array,
    settings: Object,
})

// Reactive data
const currentIndex = ref(0)
const answered = ref(false)
const showDefinition = ref(false)
const userAnswer = ref('')
const currentHint = ref('')
const hintLevel = ref(0)
const maxHintsReached = ref(false)
const sessionCompleted = ref(false)
const lastAnswerCorrect = ref(false)
const answerInput = ref(null)

// Session statistics
const correctCount = ref(0)
const incorrectCount = ref(0)
const totalHintsUsed = ref(0)
const sessionStartTime = ref(Date.now())

// Computed properties
const currentWord = computed(() => props.words[currentIndex.value] || {})
const progressPercentage = computed(() => ((currentIndex.value + 1) / props.words.length) * 100)

// Methods
const revealDefinition = () => {
    showDefinition.value = true
}

const markForgotten = async () => {
    await submitAnswer(false, true)
}

const submitAnswer = async (isCorrect, forgotten = false) => {
    const responseTime = Date.now() - sessionStartTime.value
    
    try {
        const response = await fetch('/flashcards/answer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                word_id: currentWord.value.id,
                is_correct: isCorrect,
                forgotten: forgotten,
                hints_used: hintLevel.value,
                response_time: responseTime,
                flashcard_type: props.settings.flashcard_type,
            }),
        })

        if (response.ok) {
            answered.value = true
            lastAnswerCorrect.value = isCorrect && !forgotten
            
            if (isCorrect && !forgotten) {
                correctCount.value++
            } else {
                incorrectCount.value++
            }
            
            totalHintsUsed.value += hintLevel.value
        }
    } catch (error) {
        console.error('Error submitting answer:', error)
    }
}

const submitFillBlankAnswer = async () => {
    if (!userAnswer.value.trim()) return
    
    const responseTime = Date.now() - sessionStartTime.value
    
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
                flashcard_type: props.settings.flashcard_type,
            }),
        })

        if (response.ok) {
            const result = await response.json()
            answered.value = true
            lastAnswerCorrect.value = result.is_correct
            
            if (result.is_correct) {
                correctCount.value++
            } else {
                incorrectCount.value++
            }
            
            totalHintsUsed.value += hintLevel.value
        }
    } catch (error) {
        console.error('Error submitting answer:', error)
    }
}

const getHint = async () => {
    if (maxHintsReached.value) return
    
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
        })

        if (response.ok) {
            const result = await response.json()
            currentHint.value = result.hint
            hintLevel.value = result.hint_level
            maxHintsReached.value = result.max_hints_reached
        }
    } catch (error) {
        console.error('Error getting hint:', error)
    }
}

const nextWord = () => {
    if (currentIndex.value >= props.words.length - 1) {
        sessionCompleted.value = true
        return
    }

    // Reset for next word
    currentIndex.value++
    answered.value = false
    showDefinition.value = false
    userAnswer.value = ''
    currentHint.value = ''
    hintLevel.value = 0
    maxHintsReached.value = false
    sessionStartTime.value = Date.now()

    // Focus input for fill-in-the-blank mode
    if (props.settings.flashcard_type === 'fill_blank') {
        nextTick(() => {
            if (answerInput.value) {
                answerInput.value.focus()
            }
        })
    }
}

const exitSession = () => {
    if (confirm('Are you sure you want to exit this session? Your progress will be lost.')) {
        router.visit('/')
    }
}

const returnToHome = () => {
    router.visit('/')
}

// Initialize
onMounted(() => {
    if (props.settings.flashcard_type === 'fill_blank') {
        nextTick(() => {
            if (answerInput.value) {
                answerInput.value.focus()
            }
        })
    }
})
</script>