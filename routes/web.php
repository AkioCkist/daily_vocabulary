<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\WordSearchController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\WordFilterController;
use App\Http\Controllers\UserWordController;
use App\Http\Controllers\SubscriptionController;
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
Route::get('/api/words/filter', [WordFilterController::class, 'api'])->name('api.words.filter');
Route::get('/api/words/filter-options', [WordFilterController::class, 'filterOptions'])->name('api.words.filter-options');
Route::get('/api/words/search', [WordSearchController::class, 'search'])->name('api.words.search');

// Subscription (guest or logged in)
Route::post('/subscribe', [SubscriptionController::class, 'store'])->name('subscribe.store');
Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->name('subscribe.unsubscribe');
Route::post('/check-subscription-status', [SubscriptionController::class, 'checkStatus'])->name('subscribe.checkStatus');
Route::get('/auth-subscription-status', [SubscriptionController::class, 'getAuthUserStatus'])->name('subscribe.authStatus');

// Authenticated + Verified User routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Learning routes
    Route::prefix('learn')->name('learning.')->group(function () {
        Route::get('/', [LearningController::class, 'index'])->name('index');
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
        Route::post('/generate', [TestController::class, 'generate'])->name('generate');
        Route::post('/generate-daily', [TestController::class, 'generateDaily'])->name('generate-daily');
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
    Route::post('/user/words', [UserWordController::class, 'store'])->name('user.words.store');
    Route::put('/user/words/{id}', [UserWordController::class, 'update'])->name('user.words.update');
    Route::delete('/user/words/{id}', [UserWordController::class, 'destroy'])->name('user.words.destroy');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Breeze auth routes
require __DIR__.'/auth.php';
