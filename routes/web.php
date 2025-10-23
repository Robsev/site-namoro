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
    
    // Microsoft OAuth
    Route::get('/microsoft', [OAuthController::class, 'redirectToMicrosoft'])->name('auth.microsoft');
    Route::get('/microsoft/callback', [OAuthController::class, 'handleMicrosoftCallback'])->name('auth.microsoft.callback');
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
