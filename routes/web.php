<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return view('welcome');
});

// Legal Pages
Route::get('/privacy-policy', function () {
    return view('legal.privacy-policy');
})->name('privacy-policy');

// Email Preferences
Route::middleware(['auth'])->group(function () {
    Route::get('/email-preferences', [App\Http\Controllers\EmailPreferencesController::class, 'edit'])->name('email-preferences.edit');
    Route::post('/email-preferences', [App\Http\Controllers\EmailPreferencesController::class, 'update'])->name('email-preferences.update');
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
    Route::get('/profile/view/{user}', [App\Http\Controllers\ProfileViewController::class, 'show'])->name('profile.view');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/basic', [App\Http\Controllers\ProfileController::class, 'updateBasic'])->name('profile.update.basic');
    Route::post('/profile/details', [App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update.details');
    Route::post('/profile/privacy', [App\Http\Controllers\ProfileController::class, 'updatePrivacy'])->name('profile.update.privacy');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
    Route::delete('/profile/photo', [App\Http\Controllers\ProfileController::class, 'removePhoto'])->name('profile.remove.photo');
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update.password');

    // Photo routes
    Route::post('/photos', [App\Http\Controllers\PhotoController::class, 'store'])->name('photos.store');
    Route::post('/photos/{photo}/primary', [App\Http\Controllers\PhotoController::class, 'setPrimary'])->name('photos.primary');
    Route::post('/photos/order', [App\Http\Controllers\PhotoController::class, 'updateOrder'])->name('photos.order');
    Route::delete('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'destroy'])->name('photos.destroy');

    // Preferences routes
    Route::get('/preferences', [App\Http\Controllers\PreferencesController::class, 'edit'])->name('preferences.edit');
    Route::post('/preferences', [App\Http\Controllers\PreferencesController::class, 'update'])->name('preferences.update');

    // Matching routes
    Route::get('/discover', [MatchingController::class, 'discover'])->name('matching.discover');
    Route::get('/matches', [MatchingController::class, 'matches'])->name('matching.matches');
    Route::get('/likes-sent', [MatchingController::class, 'likesSent'])->name('matching.likes-sent');
    Route::get('/likes-received', [MatchingController::class, 'likesReceived'])->name('matching.likes-received');
    Route::post('/matching/like/{user}', [MatchingController::class, 'like'])->name('matching.like');
    Route::post('/matching/undo-like/{user}', [MatchingController::class, 'undoLike'])->name('matching.undo-like');
    Route::post('/matching/pass/{user}', [MatchingController::class, 'pass'])->name('matching.pass');
    Route::post('/matching/super-like/{user}', [MatchingController::class, 'superLike'])->name('matching.super-like');
    Route::get('/matching/load-more', [MatchingController::class, 'loadMore'])->name('matching.load-more');

    // Chat routes
    Route::get('/chat', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send/{user}', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/messages/{user}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/read/{user}', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::delete('/chat/message/{message}', [ChatController::class, 'deleteMessage'])->name('chat.delete');
    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
    
    // Chat advanced options
    Route::post('/chat/clear/{user}', [ChatController::class, 'clearChat'])->name('chat.clear');
    Route::post('/chat/block/{user}', [ChatController::class, 'blockUser'])->name('chat.block');
    Route::post('/chat/report/{user}', [ChatController::class, 'reportUser'])->name('chat.report');
    Route::post('/chat/archive/{user}', [ChatController::class, 'archiveChat'])->name('chat.archive');
    
    // API routes for real-time updates
    Route::post('/api/update-last-seen', function() {
        $user = Auth::user();
        if ($user) {
            $user->update(['last_seen' => now()]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    })->name('api.update-last-seen');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('/notifications/type/{type}', [NotificationController::class, 'byType'])->name('notifications.by-type');

    // Subscription routes
    Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');
    Route::get('/subscriptions', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('/subscriptions', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::put('/subscriptions/{subscription}/payment-method', [SubscriptionController::class, 'updatePaymentMethod'])->name('subscriptions.update-payment');
    Route::get('/subscriptions/usage', [SubscriptionController::class, 'usage'])->name('subscriptions.usage');

        // Language routes
        Route::get('/language', [LanguageController::class, 'index'])->name('language.index');
        Route::post('/language/change', [LanguageController::class, 'change'])->name('language.change');
        Route::get('/language/current', [LanguageController::class, 'current'])->name('language.current');
        Route::get('/language/available', [LanguageController::class, 'available'])->name('language.available');
        Route::get('/language/detect', [LanguageController::class, 'detect'])->name('language.detect');

    // Geolocation routes
    Route::get('/location', [App\Http\Controllers\GeolocationController::class, 'index'])->name('location.index');
    Route::post('/location/update', [App\Http\Controllers\GeolocationController::class, 'update'])->name('location.update');
    Route::get('/location/search', [App\Http\Controllers\GeolocationController::class, 'search'])->name('location.search');
    Route::post('/location/current', [App\Http\Controllers\GeolocationController::class, 'getCurrentLocation'])->name('location.current');
    Route::post('/location/from-search', [App\Http\Controllers\GeolocationController::class, 'updateFromSearch'])->name('location.from-search');
    Route::post('/profile/location', [App\Http\Controllers\ProfileController::class, 'updateLocation'])->name('profile.update.location');

    // Conversation routes
    Route::get('/conversations', [App\Http\Controllers\ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [App\Http\Controllers\ConversationController::class, 'show'])->name('conversations.show');
    Route::get('/conversations/{conversation}/messages', [App\Http\Controllers\ConversationController::class, 'getMessages'])->name('conversations.messages');
    Route::post('/conversations/start', [App\Http\Controllers\ConversationController::class, 'start'])->name('conversations.start');
    Route::post('/conversations/{conversation}/send', [App\Http\Controllers\ConversationController::class, 'sendMessage'])->name('conversations.send-message');
    Route::post('/conversations/{conversation}/read', [App\Http\Controllers\ConversationController::class, 'markAsRead'])->name('conversations.mark-read');
    Route::get('/conversations/unread/count', [App\Http\Controllers\ConversationController::class, 'getUnreadCount'])->name('conversations.unread-count');
    Route::post('/conversations/{conversation}/archive', [App\Http\Controllers\ConversationController::class, 'archive'])->name('conversations.archive');
    Route::post('/conversations/{conversation}/unarchive', [App\Http\Controllers\ConversationController::class, 'unarchive'])->name('conversations.unarchive');

    // Interests routes
    Route::get('/interests', [App\Http\Controllers\InterestController::class, 'index'])->name('interests.index');
    Route::post('/interests', [App\Http\Controllers\InterestController::class, 'update'])->name('interests.update');
    Route::get('/api/interests/categories', [App\Http\Controllers\InterestController::class, 'getCategories'])->name('interests.categories');
    Route::get('/api/interests/user', [App\Http\Controllers\InterestController::class, 'getUserInterests'])->name('interests.user');

    // Psychological Profile routes
    Route::get('/psychological-profile', [App\Http\Controllers\PsychologicalProfileController::class, 'index'])->name('psychological-profile.index');
    Route::post('/psychological-profile', [App\Http\Controllers\PsychologicalProfileController::class, 'store'])->name('psychological-profile.store');
    Route::get('/psychological-profile/show', [App\Http\Controllers\PsychologicalProfileController::class, 'show'])->name('psychological-profile.show');
    Route::get('/api/psychological-profile', [App\Http\Controllers\PsychologicalProfileController::class, 'getProfile'])->name('psychological-profile.api');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Photo moderation routes
    Route::get('/photos', [App\Http\Controllers\AdminPhotoController::class, 'index'])->name('photos.index');
    Route::get('/photos/statistics', [App\Http\Controllers\AdminPhotoController::class, 'statistics'])->name('photos.statistics');
    Route::get('/photos/{photo}', [App\Http\Controllers\AdminPhotoController::class, 'show'])->name('photos.show');
    Route::post('/photos/{photo}/approve', [App\Http\Controllers\AdminPhotoController::class, 'approve'])->name('photos.approve');
    Route::post('/photos/{photo}/reject', [App\Http\Controllers\AdminPhotoController::class, 'reject'])->name('photos.reject');
    Route::post('/photos/bulk-approve', [App\Http\Controllers\AdminPhotoController::class, 'bulkApprove'])->name('photos.bulk-approve');
    Route::post('/photos/bulk-reject', [App\Http\Controllers\AdminPhotoController::class, 'bulkReject'])->name('photos.bulk-reject');
    Route::delete('/photos/{photo}', [App\Http\Controllers\AdminPhotoController::class, 'destroy'])->name('photos.destroy');
});
