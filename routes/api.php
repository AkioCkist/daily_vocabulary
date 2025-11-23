<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Sanctum protected API token endpoints
// Accept both session-based (web guard for SPA) and token-based auth
Route::middleware(['auth:web,sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API Token Management Routes
    Route::prefix('tokens')->name('api.tokens.')->group(function () {
        Route::get('/', [TokenController::class, 'index'])->name('index');
        Route::post('/', [TokenController::class, 'store'])->name('store');
        Route::delete('/{token_id}', [TokenController::class, 'destroy'])->name('destroy');
        Route::patch('/{token_id}/regenerate', [TokenController::class, 'regenerate'])->name('regenerate');
    });
});

// API Routes with auth:sanctum and throttling
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/words/filter', [App\Http\Controllers\WordFilterController::class, 'api']);
    Route::get('/words/filter-options', [App\Http\Controllers\WordFilterController::class, 'filterOptions']);
    Route::get('/words/search', [App\Http\Controllers\Api\WordSearchController::class, 'search']);
});
