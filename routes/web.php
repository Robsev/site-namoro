<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// OAuth Routes
Route::prefix('auth')->group(function () {
    // Google OAuth
    Route::get('/google', [OAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/google/callback', [OAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/basic', [App\Http\Controllers\ProfileController::class, 'updateBasic'])->name('profile.update.basic');
    Route::post('/profile/details', [App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update.details');
    Route::post('/profile/privacy', [App\Http\Controllers\ProfileController::class, 'updatePrivacy'])->name('profile.update.privacy');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.update.photo');

    // Photo routes
    Route::post('/photos', [App\Http\Controllers\PhotoController::class, 'store'])->name('photos.store');
    Route::post('/photos/{photo}/primary', [App\Http\Controllers\PhotoController::class, 'setPrimary'])->name('photos.primary');
    Route::post('/photos/order', [App\Http\Controllers\PhotoController::class, 'updateOrder'])->name('photos.order');
    Route::delete('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'destroy'])->name('photos.destroy');

    // Preferences routes
    Route::get('/preferences', [App\Http\Controllers\PreferencesController::class, 'edit'])->name('preferences.edit');
    Route::post('/preferences', [App\Http\Controllers\PreferencesController::class, 'update'])->name('preferences.update');
});
