<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WordController;
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

// Subscription (guest or logged in)
Route::post('/subscribe', [SubscriptionController::class, 'store'])->name('subscribe.store');

// Authenticated + Verified User routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

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
    return redirect()->route('dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification email sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Breeze auth routes
require __DIR__.'/auth.php';
