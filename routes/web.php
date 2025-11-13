<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\UserWordController;
use App\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home page → Word of the Day
Route::get('/', [HomeController::class, 'index'])->name('home');

// Words list / search
Route::get('/words', [WordController::class, 'index'])->name('words.index');

// Authenticated user actions
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/user/words', [UserWordController::class, 'store'])->name('user.words.store');
    Route::delete('/user/words/{id}', [UserWordController::class, 'destroy'])->name('user.words.destroy');

    // Profile routes (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// Subscription (guest or logged in)
Route::post('/subscribe', [SubscriptionController::class, 'store'])->name('subscribe.store');

// Breeze auth routes
require __DIR__.'/auth.php';
