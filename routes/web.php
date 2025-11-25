<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\WordSearchController;
use App\Http\Controllers\TokenManagerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Profile\SubscriptionSettingsController;
use Inertia\Inertia;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\WordFilterController;
use App\Http\Controllers\UserWordController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SavedSessionController;
use App\Http\Controllers\SavedSessionItemController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home page → Word of the Day (guest or auth)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Words list / search (guest or auth)
Route::get('/words', [WordController::class, 'index'])->name('words.index');

// Word filtering routes (guest or auth)
Route::get('/words/filter', [WordFilterController::class, 'index'])->name('words.filter');
Route::get('/words/search', [WordFilterController::class, 'search'])->name('words.search');

// Subscription (guest or logged in)
Route::post('/subscribe', [SubscriptionController::class, 'store'])->middleware('progressive_throttle:3,1')->name('subscribe.store');
Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->middleware('progressive_throttle:2,1')->name('subscribe.unsubscribe');
Route::post('/check-subscription-status', [SubscriptionController::class, 'checkStatus'])->middleware('progressive_throttle:5,1')->name('subscribe.checkStatus');
Route::get('/auth-subscription-status', [SubscriptionController::class, 'getAuthUserStatus'])->middleware('progressive_throttle:10,1')->name('subscribe.authStatus');

// Authenticated + Verified User routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Dashboard stats by day range
    Route::get('/dashboard/stats/{days}', [HomeController::class, 'getStatsByDayRange'])->name('dashboard.stats');


    // Learning routes
    Route::prefix('learn')->name('learning.')->group(function () {
        Route::get('/', [LearningController::class, 'index'])->name('index');
        Route::post('/generate', [LearningController::class, 'generateSession'])->middleware('progressive_throttle:5,1')->name('generate');
        Route::post('/generate-quick', [LearningController::class, 'generateQuick'])->middleware('progressive_throttle:8,1')->name('generate-quick');
        Route::post('/start', [LearningController::class, 'startSession'])->name('start');
        Route::post('/next', [LearningController::class, 'next'])->name('next');
        Route::post('/mark-learned', [LearningController::class, 'markLearned'])->name('mark-learned');
        Route::post('/add-to-review', [LearningController::class, 'addToReview'])->name('add-to-review');
        Route::post('/update-progress', [LearningController::class, 'updateProgress'])->name('update-progress');
        Route::get('/session-words', [LearningController::class, 'getSessionWords'])->name('session-words');
    });

    // Daily Test routes
    Route::prefix('test')->name('test.')->group(function () {
        Route::get('/', [TestController::class, 'index'])->name('index');
        Route::post('/generate', [TestController::class, 'generate'])->middleware('progressive_throttle:5,1')->name('generate');
        Route::post('/generate-daily', [TestController::class, 'generateDaily'])->middleware('progressive_throttle:3,1')->name('generate-daily');
        Route::post('/answer', [TestController::class, 'submitAnswer'])->name('answer');
        Route::post('/complete', [TestController::class, 'complete'])->name('complete');
        Route::get('/results', [TestController::class, 'results'])->name('results');
        Route::get('/history', [TestController::class, 'history'])->name('history');
        Route::get('/{test}', [TestController::class, 'show'])->name('show');
    });

    // Review routes
    Route::prefix('review')->name('review.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/practice', [ReviewController::class, 'practice'])->name('practice');
        Route::post('/answer', [ReviewController::class, 'submitAnswer'])->name('answer');
        Route::get('/next', [ReviewController::class, 'nextWord'])->name('next');
        Route::get('/intensive', [ReviewController::class, 'intensive'])->name('intensive');
        Route::get('/spaced-repetition', [ReviewController::class, 'spacedRepetition'])->name('spaced-repetition');
        Route::post('/mark-mastered', [ReviewController::class, 'markMastered'])->name('mark-mastered');
        Route::post('/reset-to-review', [ReviewController::class, 'resetToReview'])->name('reset-to-review');
    });

    // User Vocabulary
    Route::get('/user/words', [UserWordController::class, 'index'])->name('user.words.index');
    Route::post('/user/words', [UserWordController::class, 'store'])->middleware('throttle:20,1')->name('user.words.store');
    Route::put('/user/words/{id}', [UserWordController::class, 'update'])->middleware('throttle:30,1')->name('user.words.update');
    Route::delete('/user/words/{id}', [UserWordController::class, 'destroy'])->middleware('throttle:20,1')->name('user.words.destroy');

    // Topic Management
    Route::prefix('topics')->name('topics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TopicController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\TopicController::class, 'store'])->middleware('throttle:15,1')->name('store');
        Route::put('/{id}', [\App\Http\Controllers\TopicController::class, 'update'])->middleware('throttle:20,1')->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\TopicController::class, 'destroy'])->middleware('throttle:10,1')->name('destroy');
        Route::get('/suggested', [\App\Http\Controllers\TopicController::class, 'suggested'])->name('suggested');
        Route::get('/search', [\App\Http\Controllers\TopicController::class, 'search'])->name('search');
    });

    // Flashcard System
    Route::prefix('flashcards')->name('flashcards.')->group(function () {
        Route::post('/start', [\App\Http\Controllers\FlashcardController::class, 'start'])->name('start');
        Route::get('/practice', function () {
            // Render the practice page using session data
            $session = session('flashcard_session');
            if (!$session) {
                return redirect()->route('dashboard')->with('error', 'No active flashcard session.');
            }
            $userTopics = \App\Models\Topic::where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->select(['id', 'name', 'description'])
                ->orderBy('name')
                ->get();
            return Inertia::render('Flashcards/Practice', [
                'words' => $session['words'],
                'settings' => $session['settings'],
                'userTopics' => $userTopics,
            ]);
        })->name('practice');
        Route::post('/next', [\App\Http\Controllers\FlashcardController::class, 'next'])->name('next');
        Route::post('/answer', [\App\Http\Controllers\FlashcardController::class, 'answer'])->name('answer');
        Route::post('/hint', [\App\Http\Controllers\FlashcardController::class, 'getHint'])->name('hint');
        Route::post('/complete', [\App\Http\Controllers\FlashcardController::class, 'complete'])->name('complete');

        // Word-to-topic management
        Route::post('/words/add-to-topic', [\App\Http\Controllers\FlashcardController::class, 'addToTopic'])->name('words.add-to-topic');
        Route::post('/words/remove-from-topic', [\App\Http\Controllers\FlashcardController::class, 'removeFromTopic'])->name('words.remove-from-topic');
        Route::get('/words/{wordId}/topics', [\App\Http\Controllers\FlashcardController::class, 'getWordTopics'])->name('words.topics');

        // Topic management
        Route::post('/topics/quick-create', [\App\Http\Controllers\FlashcardController::class, 'quickCreateTopic'])->name('topics.quick-create');
        Route::delete('/topics/{topicId}', [\App\Http\Controllers\FlashcardController::class, 'deleteTopic'])->name('topics.delete');

        // Template management
        Route::post('/templates', [\App\Http\Controllers\FlashcardController::class, 'saveTemplate'])->name('templates.save');
        Route::get('/templates', [\App\Http\Controllers\FlashcardController::class, 'listTemplates'])->name('templates.list');
        Route::get('/templates/{id}', [\App\Http\Controllers\FlashcardController::class, 'loadTemplate'])->name('templates.load');
        Route::delete('/templates/{id}', [\App\Http\Controllers\FlashcardController::class, 'deleteTemplate'])->name('templates.delete');
        Route::post('/templates/import', [\App\Http\Controllers\FlashcardController::class, 'importTemplate'])->name('templates.import');
        Route::get('/templates/{id}/export', [\App\Http\Controllers\FlashcardController::class, 'exportTemplate'])->name('templates.export');
    });

    // Saved Sessions
    Route::prefix('saved-sessions')->name('saved-sessions.')->group(function () {
        // Main CRUD operations
        Route::get('/', [\App\Http\Controllers\SavedSessionController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\SavedSessionController::class, 'store'])->name('store');
        Route::get('/{slug}', [\App\Http\Controllers\SavedSessionController::class, 'show'])->name('show');
        Route::put('/{slug}', [\App\Http\Controllers\SavedSessionController::class, 'update'])->name('update');
        Route::delete('/{slug}', [\App\Http\Controllers\SavedSessionController::class, 'destroy'])->name('destroy');

        // Review session (start study from saved session)
        Route::post('/{slug}/review', [\App\Http\Controllers\SavedSessionController::class, 'review'])->name('review');

        // Item management within sessions
        Route::prefix('{slug}/items')->name('items.')->group(function () {
            Route::post('/', [\App\Http\Controllers\SavedSessionItemController::class, 'store'])->name('store');
            Route::delete('/{itemId}', [\App\Http\Controllers\SavedSessionItemController::class, 'destroy'])->name('destroy');
            Route::put('/{itemId}/move', [\App\Http\Controllers\SavedSessionItemController::class, 'move'])->name('move');
            Route::put('/reorder', [\App\Http\Controllers\SavedSessionItemController::class, 'reorder'])->name('reorder');
        });
    });

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Subscription settings (Edit Profile)
    Route::put('/profile/subscription', [SubscriptionSettingsController::class, 'update'])->name('profile.subscription.update');
    Route::get('/profile/subscription/metrics', [SubscriptionSettingsController::class, 'metrics'])->name('profile.subscription.metrics');

    // API Token Manager
    Route::get('/tokens', [TokenManagerController::class, 'index'])->name('tokens.index');
});

// Email Verification Routes (double-opt-in)
Route::get('/email/verify', function () {
    return Inertia::render('Auth/VerifyEmail');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification email sent!');
})->middleware(['auth', 'progressive_throttle:2,1'])->name('verification.send');

// Admin routes for rate limit management
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/rate-limits', [\App\Http\Controllers\Admin\RateLimitController::class, 'index'])->name('rate-limits.index');
    Route::post('/rate-limits/unlock-user', [\App\Http\Controllers\Admin\RateLimitController::class, 'unlock'])->name('rate-limits.unlock-user');
    Route::post('/rate-limits/unlock-ip', [\App\Http\Controllers\Admin\RateLimitController::class, 'unlockIp'])->name('rate-limits.unlock-ip');
    Route::post('/rate-limits/unlock-auth-ip', [\App\Http\Controllers\Admin\RateLimitController::class, 'unlockAuthIp'])->name('rate-limits.unlock-auth-ip');
    Route::post('/rate-limits/unlock-auth-email', [\App\Http\Controllers\Admin\RateLimitController::class, 'unlockAuthEmail'])->name('rate-limits.unlock-auth-email');
});

// Breeze auth routes
require __DIR__ . '/auth.php';
