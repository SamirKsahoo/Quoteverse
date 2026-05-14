<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuoteController as AdminQuoteController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/map', function () {
    return view('map');
});

// ── Public Routes ──────────────────────────────────────────────────────────────
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/quotes', [PublicController::class, 'quotes'])->name('quotes.index');
Route::get('/quotes/{slug}', [PublicController::class, 'show'])->name('quotes.show');
Route::get('/category/{slug}', [PublicController::class, 'category'])->name('category.show');

// ── Admin Auth ─────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Categories
        Route::resource('categories', CategoryController::class);

        // Quotes
        Route::get('/quotes/{quote}/preview', [AdminQuoteController::class, 'preview'])->name('quotes.preview');
        Route::post('/quotes/{quote}/toggle', [AdminQuoteController::class, 'toggleStatus'])->name('quotes.toggle');
        Route::resource('quotes', AdminQuoteController::class);
    });
});
