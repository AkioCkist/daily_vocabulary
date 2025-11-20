<template>
  <Head title="Flashcard Practice - DailyVocab" />
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-50 to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950">
    <Header :user="$page.props.auth.user" />

    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Progress Bar with Stats -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
              {{ settings.flashcard_type === 'standard' ? 'Standard' : settings.flashcard_type === 'fill_blank' ? 'Fill-in-the-Blank' : 'Mixed' }} Flashcard Practice
            </h1>
            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
              <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                {{ correctCount }} correct
              </span>
              <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                {{ incorrectCount }} incorrect
              </span>
            </div>
          </div>
          <div class="text-right">
            <div class="text-sm text-gray-600 dark:text-gray-400">Progress</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ currentIndex + 1 }} / {{ words.length }}
            </div>
          </div>
        </div>
        <div class="relative w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
          <div 
            class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 rounded-full transition-all duration-500 ease-out"
            :style="{ width: `${((currentIndex + 1) / words.length) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Flashcard -->
      <div v-if="currentWord && !sessionCompleted" class="max-w-2xl mx-auto">
        <!-- Dynamic Mode Display -->
        <Transition name="mode-badge">
          <div v-if="settings.flashcard_type === 'mixed'" class="mb-4 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 text-purple-800 dark:text-purple-300 text-sm font-medium rounded-full shadow-sm">
              <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
              {{ currentMode === 'standard' ? 'Standard Mode' : 'Fill-in-the-Blank Mode' }}
            </span>
          </div>
        </Transition>

        <!-- Card with smooth transitions -->
        <Transition name="fade-slide" mode="out-in">
          <div :key="currentIndex" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden min-h-[450px] flex flex-col transition-shadow duration-300 hover:shadow-3xl relative">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 text-white p-6 relative">
              <div class="absolute inset-0 bg-white/10 animate-pulse-slow"></div>
              <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
                    {{ currentWord.cefr_level }}
                  </span>
                  <span class="text-sm opacity-90">{{ currentWord.topic }}</span>
                </div>
                <div class="flex items-center gap-3">
                  <!-- Add to Topic Button -->
                  <div class="relative z-50">
                    <button
                      @click.stop="showTopicDropdown = !showTopicDropdown"
                      class="flex items-center gap-2 px-3 py-1.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg text-sm font-medium transition-all duration-200"
                      title="Add to personal topic"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                      </svg>
                      <span class="hidden sm:inline">Save</span>
                    </button>
                    
                    <!-- Topic Dropdown -->
                    <Transition name="dropdown">
                      <div 
                        v-if="showTopicDropdown" 
                        class="absolute right-0 top-full mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 z-[100] max-h-80 overflow-y-auto"
                      >
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                          <div class="text-sm font-semibold text-gray-900 dark:text-white">Add to Topic</div>
                          <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Save word to your collections</div>
                        </div>
                        
                        <div v-if="userTopics && userTopics.length > 0" class="p-2">
                          <button
                            v-for="topic in userTopics"
                            :key="topic.id"
                            @click.stop="addToTopic(topic.id)"
                            :disabled="addingToTopic === topic.id"
                            class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                          >
                            <div class="flex items-center justify-between">
                              <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 dark:text-white truncate">{{ topic.name }}</div>
                                <div v-if="topic.description" class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ topic.description }}</div>
                              </div>
                              <svg v-if="addingToTopic === topic.id" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 animate-spin ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                              </svg>
                            </div>
                          </button>
                        </div>
                        
                        <div v-else class="p-6 text-center">
                          <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                          </svg>
                          <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">No custom topics yet</p>
                          <Link
                            :href="route('home')"
                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
                          >
                            Create topics in dashboard
                          </Link>
                        </div>
                      </div>
                    </Transition>
                  </div>
                  
                  <div v-if="currentMode === 'standard'" class="flex items-center gap-2 text-sm opacity-90">
                    <Transition name="fade" mode="out-in">
                      <span :key="showAnswer" class="flex items-center gap-1">
                        <span v-if="!showAnswer">💡 Click to Reveal</span>
                        <span v-else>👁️ Showing Definition</span>
                      </span>
                    </Transition>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card Content -->
            <div 
              class="flex-1 flex items-center justify-center p-8 relative"
              :class="{ 'cursor-pointer hover:bg-gradient-to-br hover:from-gray-50 hover:to-blue-50 dark:hover:from-gray-700/30 dark:hover:to-indigo-900/20 transition-all duration-300': currentMode === 'standard' && !showAnswer }"
              @click="currentMode === 'standard' && !showAnswer ? toggleCard() : null"
            >
              <!-- Tap indicator for standard mode -->
              <Transition name="bounce">
                <div v-if="currentMode === 'standard' && !showAnswer" class="absolute top-4 right-4 text-gray-400 dark:text-gray-500 animate-bounce-slow">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                  </svg>
                </div>
              </Transition>

              <div class="text-center w-full">
                <!-- Standard Mode -->
                <Transition name="fade-scale" mode="out-in">
                  <div v-if="currentMode === 'standard' && !showAnswer" :key="'word'" class="space-y-4">
                    <h2 class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 animate-fade-in">
                      {{ currentWord.word }}
                    </h2>
                    <div v-if="currentWord.pronunciation" class="text-xl text-gray-600 dark:text-gray-400 font-light">
                      /{{ currentWord.pronunciation }}/
                    </div>
                  </div>
                  
                  <div v-else-if="currentMode === 'standard' && showAnswer" :key="'definition'" class="space-y-6 animate-fade-in">
                    <h3 class="text-3xl font-semibold text-gray-900 dark:text-white leading-relaxed">
                      {{ currentWord.definition }}
                    </h3>
                    <div v-if="currentWord.example" class="max-w-lg mx-auto p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl">
                      <p class="text-lg text-gray-700 dark:text-gray-300 italic leading-relaxed">
                        "{{ currentWord.example }}"
                      </p>
                    </div>
                  </div>
                </Transition>

                <!-- Fill-in-the-Blank Mode -->
                <Transition name="fade-scale" mode="out-in">
                  <div v-if="currentMode === 'fill_blank'" :key="'fill-blank'" class="space-y-6">
                    <div class="space-y-4">
                      <div class="inline-block px-4 py-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                        <h2 class="text-lg font-bold text-indigo-900 dark:text-indigo-300">What word means:</h2>
                      </div>
                      <h3 class="text-2xl font-semibold text-gray-900 dark:text-white leading-relaxed max-w-xl mx-auto">
                        {{ currentWord.definition }}
                      </h3>
                      <div v-if="currentWord.example" class="max-w-lg mx-auto p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                        <p class="text-lg text-gray-700 dark:text-gray-300 italic leading-relaxed">
                          "{{ hideWordInExample(currentWord.example, currentWord.word) }}"
                        </p>
                      </div>
                    </div>

                    <!-- Hint Display -->
                    <Transition name="slide-down">
                      <div v-if="currentHint" class="p-5 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border-2 border-yellow-300 dark:border-yellow-700 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                          <span class="text-sm font-semibold text-yellow-700 dark:text-yellow-400 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Hint
                          </span>
                          <span class="text-xs px-2 py-1 bg-yellow-200 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-full font-medium">
                            {{ hintLevel }} of 3
                          </span>
                        </div>
                        <div class="text-3xl font-mono font-bold text-yellow-800 dark:text-yellow-300 tracking-[0.5em] text-center">
                          {{ currentHint }}
                        </div>
                      </div>
                    </Transition>

                    <!-- Input Field -->
                    <Transition name="fade-scale">
                      <div v-if="!answered" class="relative">
                        <input
                          ref="answerInput"
                          v-model="userAnswer"
                          @keyup.enter="submitFillBlankAnswer"
                          type="text"
                          placeholder="Type your answer here..."
                          class="w-full max-w-md px-6 py-4 text-xl text-center bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 border-3 border-indigo-300 dark:border-indigo-600 rounded-2xl focus:border-indigo-500 dark:focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-800/50 transition-all duration-300 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 shadow-lg font-medium"
                        />
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Press Enter to submit</div>
                      </div>
                    </Transition>

                    <!-- Answer Feedback -->
                    <Transition name="fade-scale">
                      <div v-if="answered" class="p-6 rounded-2xl text-center shadow-lg" :class="[
                        lastAnswerCorrect ? 'bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-300 dark:border-green-700' : 'bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-2 border-red-300 dark:border-red-700'
                      ]">
                        <div class="text-4xl mb-3">
                          {{ lastAnswerCorrect ? '🎉' : '💭' }}
                        </div>
                        <div class="text-2xl font-bold mb-3" :class="[
                          lastAnswerCorrect ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'
                        ]">
                          {{ lastAnswerCorrect ? '✓ Perfect!' : '✗ Not Quite' }}
                        </div>
                        <div class="text-lg text-gray-800 dark:text-gray-200">
                          The correct answer is: <strong class="text-xl text-indigo-600 dark:text-indigo-400">{{ currentWord.word }}</strong>
                        </div>
                        <div v-if="currentWord.pronunciation" class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                          /{{ currentWord.pronunciation }}/
                        </div>
                      </div>
                    </Transition>
                  </div>
                </Transition>
              </div>
            </div>

            <!-- Card Actions -->
            <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 border-t border-gray-200 dark:border-gray-600">
              <!-- Standard Mode Actions -->
              <Transition name="fade" mode="out-in">
                <div v-if="currentMode === 'standard' && showAnswer" :key="'standard-answered'" class="flex gap-3 justify-center">
                  <button
                    @click.stop="markAnswer(false)"
                    class="group flex-1 max-w-xs bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                  >
                    <svg class="w-5 h-5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    I Don't Remember
                  </button>
                  <button
                    @click.stop="markAnswer(true)"
                    class="group flex-1 max-w-xs bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                  >
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Got It Right
                  </button>
                </div>
                
                <div v-else-if="currentMode === 'standard' && !showAnswer" :key="'standard-unanswered'" class="text-center">
                  <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                    Click the card above to reveal the definition
                  </p>
                  <button
                    @click.stop="markAnswer(false)"
                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                  >
                    I Don't Remember
                  </button>
                </div>
              </Transition>

              <!-- Fill-in-the-Blank Mode Actions -->
              <Transition name="fade" mode="out-in">
                <div v-if="currentMode === 'fill_blank' && !answered" :key="'fill-actions'" class="flex gap-3 justify-center flex-wrap">
                  <button
                    @click.stop="submitFillBlankAnswer"
                    :disabled="!userAnswer.trim()"
                    class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 disabled:scale-100 disabled:shadow-none"
                  >
                    Submit Answer
                  </button>
                  <button
                    @click.stop="getHint"
                    :disabled="maxHintsReached"
                    class="bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 disabled:scale-100 disabled:shadow-none"
                  >
                    {{ maxHintsReached ? 'No More Hints' : 'Get Hint' }}
                  </button>
                  <button
                    @click.stop="skipWord"
                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                  >
                    Skip
                  </button>
                </div>

                <!-- Next Button (when answered) -->
                <div v-else-if="answered" :key="'next-button'" class="text-center">
                  <button
                    @click="nextCard"
                    class="group bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 hover:from-indigo-600 hover:via-purple-600 hover:to-purple-700 text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 flex items-center justify-center gap-2 mx-auto"
                  >
                    <span>{{ currentIndex >= words.length - 1 ? 'Complete Session' : 'Next Word' }}</span>
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                  </button>
                </div>
              </Transition>
            </div>
          </div>
        </Transition>

        <!-- Navigation -->
        <div class="flex justify-between items-center mt-6">
          <button
            @click="previousCard"
            :disabled="currentIndex === 0"
            class="group flex items-center gap-2 px-5 py-3 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg hover:bg-white dark:hover:bg-gray-800 disabled:hover:bg-transparent"
          >
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Session Complete -->
      <Transition name="scale-fade">
        <div v-if="sessionCompleted" class="max-w-2xl mx-auto text-center">
          <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-10 animate-fade-in">
            <div class="relative mb-8">
              <div class="w-24 h-24 bg-gradient-to-br from-green-400 via-emerald-500 to-green-600 rounded-full flex items-center justify-center mx-auto shadow-lg animate-bounce-slow">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 bg-green-400 rounded-full opacity-20 animate-ping-slow"></div>
              </div>
            </div>
            
            <h2 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 mb-3">
              Session Complete!
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-10 text-lg">
              Great job! You've completed {{ words.length }} flashcards.
            </p>
            
            <!-- Session Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
              <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ words.length }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">Total Words</div>
              </div>
              <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ correctCount }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">Correct</div>
              </div>
              <div class="bg-gradient-to-br from-red-50 to-rose-100 dark:from-red-900/20 dark:to-rose-900/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ incorrectCount }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">Incorrect</div>
              </div>
              <div class="bg-gradient-to-br from-yellow-50 to-amber-100 dark:from-yellow-900/20 dark:to-amber-900/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ totalHintsUsed }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">Hints Used</div>
              </div>
            </div>

            <div class="flex gap-4 justify-center">
              <Link
                :href="route('home')"
                class="group bg-gradient-to-r from-indigo-500 via-purple-500 to-purple-600 hover:from-indigo-600 hover:via-purple-600 hover:to-purple-700 text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 flex items-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Back to Dashboard</span>
              </Link>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';

// Simple route helper with fallback URLs
const route = (name, params) => {
  const routes = {
    'home': '/',
    'flashcards.answer': '/flashcards/answer',
    'flashcards.complete': '/flashcards/complete',
    'flashcards.words.add-to-topic': '/flashcards/words/add-to-topic'
  };
  
  return routes[name] || '#';
};

const props = defineProps({
  words: {
    type: Array,
    required: true
  },
  settings: {
    type: Object,
    required: true
  },
  userTopics: {
    type: Array,
    default: () => []
  }
});

// Reactive state
const currentIndex = ref(0);
const showAnswer = ref(false);
const answered = ref(false);
const sessionCompleted = ref(false);
const startTime = ref(null);

// Topic management
const showTopicDropdown = ref(false);
const addingToTopic = ref(null);

// Fill-in-the-blank specific
const userAnswer = ref('');
const currentHint = ref('');
const hintLevel = ref(0);
const maxHintsReached = ref(false);
const lastAnswerCorrect = ref(false);
const answerInput = ref(null);

// Session statistics
const correctCount = ref(0);
const incorrectCount = ref(0);
const totalHintsUsed = ref(0);

// Mixed mode tracking
const wordModes = ref(new Map()); // Track mode for each word

// Computed properties
const currentWord = computed(() => {
  return props.words[currentIndex.value] || null;
});

const currentMode = computed(() => {
  if (props.settings.flashcard_type === 'mixed') {
    // For mixed mode, determine mode for current word
    const wordId = currentWord.value?.id;
    if (wordId && !wordModes.value.has(wordId)) {
      // Randomly assign mode for this word (50/50 chance)
      const mode = Math.random() < 0.5 ? 'standard' : 'fill_blank';
      wordModes.value.set(wordId, mode);
    }
    return wordModes.value.get(wordId) || 'standard';
  }
  return props.settings.flashcard_type;
});

// Methods
function hideWordInExample(example, word) {
  if (!example || !word) return example;
  
  // Create regex to match the word (case-insensitive, whole word)
  const regex = new RegExp(`\\b${word}\\b`, 'gi');
  
  // Replace with underscores matching the word length
  return example.replace(regex, (match) => '_'.repeat(match.length));
}

function toggleCard() {
  if (!showAnswer.value) {
    startTime.value = Date.now();
  }
  showAnswer.value = !showAnswer.value;
}

function markAnswer(isCorrect) {
  console.log('Mark answer called:', isCorrect);
  if (!currentWord.value) {
    console.error('No current word available');
    return;
  }
  
  // For standard mode, ensure we mark as answered immediately
  if (currentMode.value === 'standard') {
    answered.value = true;
    lastAnswerCorrect.value = isCorrect;
    
    if (isCorrect) {
      correctCount.value++;
    } else {
      incorrectCount.value++;
    }
  }
  
  const responseTime = startTime.value ? Date.now() - startTime.value : null;
  
  try {
    // Submit answer using fetch instead of Inertia to avoid navigation issues
    fetch(route('flashcards.answer'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        word_id: currentWord.value.id,
        is_correct: isCorrect,
        response_time: responseTime,
        flashcard_type: currentMode.value
      })
    })
    .then(response => {
      console.log('Answer submission response status:', response.status);
      if (!response.ok) {
        console.error('Answer submission failed with status:', response.status);
      }
    })
    .catch(error => {
      console.error('Answer submission network error:', error);
    });
  } catch (error) {
    console.error('Exception in markAnswer:', error);
  }
}

function skipWord() {
  console.log('Skip word called');
  if (!currentWord.value) {
    console.error('No current word available');
    return;
  }
  
  // Mark as answered and incorrect for fill-blank mode
  answered.value = true;
  lastAnswerCorrect.value = false;
  incorrectCount.value++;
  
  const responseTime = startTime.value ? Date.now() - startTime.value : null;
  
  try {
    // Submit as incorrect with skip indication
    fetch(route('flashcards.answer'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        word_id: currentWord.value.id,
        is_correct: false,
        user_answer: '[SKIPPED]',
        hints_used: hintLevel.value,
        response_time: responseTime,
        flashcard_type: currentMode.value
      })
    })
    .then(response => {
      console.log('Skip submission response status:', response.status);
      if (!response.ok) {
        console.error('Skip submission failed with status:', response.status);
      }
    })
    .catch(error => {
      console.error('Skip submission network error:', error);
    });
  } catch (error) {
    console.error('Exception in skipWord:', error);
  }
}

const submitFillBlankAnswer = async () => {
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
      
      if (result.is_correct) {
        correctCount.value++;
      } else {
        incorrectCount.value++;
      }
      
      totalHintsUsed.value += hintLevel.value;
    }
  } catch (error) {
    console.error('Error submitting answer:', error);
  }
};

const getHint = async () => {
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
};

function nextCard() {
  console.log('nextCard called');
  
  if (currentIndex.value < props.words.length - 1) {
    console.log('Moving to next card');
    currentIndex.value++;
    showAnswer.value = false;
    answered.value = false;
    startTime.value = null;
    
    // Reset fill-blank specific state
    userAnswer.value = '';
    currentHint.value = '';
    hintLevel.value = 0;
    maxHintsReached.value = false;
    
    // Focus input for fill-in-the-blank mode
    if (currentMode.value === 'fill_blank') {
      nextTick(() => {
        if (answerInput.value) {
          answerInput.value.focus();
        }
      });
    }
  } else {
    console.log('Completing session');
    sessionCompleted.value = true;
  }
}

function previousCard() {
  if (currentIndex.value > 0) {
    currentIndex.value--;
    showAnswer.value = false;
    answered.value = false;
    startTime.value = null;
    
    // Reset fill-blank specific state
    userAnswer.value = '';
    currentHint.value = '';
    hintLevel.value = 0;
    maxHintsReached.value = false;
  }
}

// Initialize fill-blank mode
onMounted(() => {
  if (currentMode.value === 'fill_blank') {
    nextTick(() => {
      if (answerInput.value) {
        answerInput.value.focus();
      }
    });
  }
  
  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      showTopicDropdown.value = false;
    }
  });
})

// Topic management methods
const addToTopic = async (topicId) => {
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
      // Show success feedback
      showTopicDropdown.value = false;
      // You could add a toast notification here
      console.log('Word added to topic successfully');
    } else {
      // Handle error (e.g., already in topic)
      console.error('Failed to add word to topic:', result.message);
      alert(result.message || 'Failed to add word to topic');
    }
  } catch (error) {
    console.error('Error adding word to topic:', error);
    alert('Network error. Please try again.');
  } finally {
    addingToTopic.value = null;
  }
};
</script>

<style scoped>
/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.2s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

.fade-scale-enter-active, .fade-scale-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-scale-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(8px);
}
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-8px);
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

.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-15px);
  max-height: 0;
}
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-8px);
  max-height: 0;
}

.scale-bounce-enter-active {
  animation: scale-bounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.scale-bounce-leave-active {
  transition: all 0.3s ease;
}
.scale-bounce-leave-to {
  opacity: 0;
  transform: scale(0.8);
}

@keyframes scale-bounce {
  0% {
    opacity: 0;
    transform: scale(0.3);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.mode-badge-enter-active, .mode-badge-leave-active {
  transition: all 0.3s ease;
}
.mode-badge-enter-from, .mode-badge-leave-to {
  opacity: 0;
  transform: scale(0.8) translateY(-10px);
}

.bounce-enter-active {
  animation: bounce-in 0.5s ease;
}

@keyframes bounce-in {
  0% {
    opacity: 0;
    transform: scale(0.3);
  }
  50% {
    opacity: 1;
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
  }
}

.scale-fade-enter-active {
  animation: scale-fade-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scale-fade-in {
  0% {
    opacity: 0;
    transform: scale(0.8) translateY(20px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* Custom animations */
@keyframes pulse-slow {
  0%, 100% {
    opacity: 0.1;
  }
  50% {
    opacity: 0.2;
  }
}

.animate-pulse-slow {
  animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce-slow {
  animation: bounce-slow 2s ease-in-out infinite;
}

@keyframes ping-slow {
  0% {
    transform: scale(1);
    opacity: 0.5;
  }
  100% {
    transform: scale(2);
    opacity: 0;
  }
}

.animate-ping-slow {
  animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-in;
}

/* Dark mode shadow enhancements */
.dark .shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.dark .shadow-3xl {
  box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.6);
}

/* Glassmorphism effect for hints */
@supports (backdrop-filter: blur(10px)) {
  .backdrop-blur-sm {
    backdrop-filter: blur(4px);
  }
}
</style>