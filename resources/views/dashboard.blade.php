@php
use Illuminate\Support\Facades\Storage;
use App\Models\UserMatch;

$user = Auth::user();

// Calculate statistics
$matchesCount = UserMatch::where(function($query) use ($user) {
    $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
})->where('status', 'accepted')->count();

$likesSentCount = UserMatch::where('user1_id', $user->id)->where('status', 'pending')->count();
$likesReceivedCount = UserMatch::where('user2_id', $user->id)->where('status', 'pending')->count();

// For now, we'll use likes as a proxy for views (in a real app, you'd track profile views separately)
$viewsCount = $likesReceivedCount + $matchesCount;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.dashboard.title') }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name') }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Language Selector -->
                    <div class="relative group">
                        <button class="text-gray-600 hover:text-gray-800 px-2 py-2 rounded-md text-sm font-medium flex items-center">
                            <i class="fas fa-globe mr-1"></i>
                            @switch(app()->getLocale())
                                @case('pt_BR') 🇧🇷 @break
                                @case('en') 🇺🇸 @break
                                @case('es') 🇪🇸 @break
                                @default 🇧🇷
                            @endswitch
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <a href="{{ route('language.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>{{ __('messages.language.title') }}
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form action="{{ route('language.change') }}" method="POST" class="block">
                                    @csrf
                                    <input type="hidden" name="locale" value="pt_BR">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <span class="mr-3">🇧🇷</span>{{ __('messages.language.portuguese') }}
                                        @if(app()->getLocale() === 'pt_BR')
                                            <i class="fas fa-check ml-auto text-green-500"></i>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('language.change') }}" method="POST" class="block">
                                    @csrf
                                    <input type="hidden" name="locale" value="en">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <span class="mr-3">🇺🇸</span>{{ __('messages.language.english') }}
                                        @if(app()->getLocale() === 'en')
                                            <i class="fas fa-check ml-auto text-green-500"></i>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('language.change') }}" method="POST" class="block">
                                    @csrf
                                    <input type="hidden" name="locale" value="es">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <span class="mr-3">🇪🇸</span>{{ __('messages.language.spanish') }}
                                        @if(app()->getLocale() === 'es')
                                            <i class="fas fa-check ml-auto text-green-500"></i>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <span class="text-gray-600">{{ __('messages.dashboard.welcome', ['name' => Auth::user()->first_name ?? Auth::user()->name]) }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-sign-out-alt"></i> {{ __('messages.nav.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    @if(Auth::user()->profile_photo)
                        <img src="{{ Storage::url(Auth::user()->profile_photo) }}" 
                             alt="{{ __('messages.dashboard.profile_photo') }}" 
                             class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    @else
                        <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500 text-2xl"></i>
                        </div>
                    @endif
                    <h2 class="text-xl font-semibold text-gray-800">{{ Auth::user()->full_name }}</h2>
                    <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    <div class="mt-4">
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                            {{ Auth::user()->subscription_type === 'premium' ? __('messages.dashboard.premium') : __('messages.dashboard.free') }}
                        </span>
                        @if(Auth::user()->is_verified)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full ml-2">
                                <i class="fas fa-check"></i> {{ __('messages.dashboard.verified') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.dashboard.quick_actions') }}</h3>
                <div class="space-y-3">
        <a href="{{ route('matching.discover') }}" class="block w-full bg-pink-500 text-white py-2 px-4 rounded-lg text-center hover:bg-pink-600 transition duration-200">
            <i class="fas fa-search mr-2"></i>{{ __('messages.nav.discover') }}
        </a>
        <a href="{{ route('matching.matches') }}" class="block w-full bg-red-500 text-white py-2 px-4 rounded-lg text-center hover:bg-red-600 transition duration-200">
            <i class="fas fa-heart mr-2"></i>{{ __('messages.nav.matches') }}
        </a>
        <a href="{{ route('matching.likes-sent') }}" class="block w-full bg-blue-500 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-600 transition duration-200">
            <i class="fas fa-heart mr-2"></i>{{ __('messages.dashboard.likes_sent') }} ({{ $likesSentCount }})
        </a>
        <a href="{{ route('matching.likes-received') }}" class="block w-full bg-green-500 text-white py-2 px-4 rounded-lg text-center hover:bg-green-600 transition duration-200">
            <i class="fas fa-heart-broken mr-2"></i>{{ __('messages.dashboard.likes_received') }} ({{ $likesReceivedCount }})
        </a>
        <a href="{{ route('chat.conversations') }}" class="block w-full bg-green-500 text-white py-2 px-4 rounded-lg text-center hover:bg-green-600 transition duration-200">
            <i class="fas fa-comments mr-2"></i>{{ __('messages.nav.chat') }}
        </a>
        <a href="{{ route('notifications.index') }}" class="block w-full bg-yellow-500 text-white py-2 px-4 rounded-lg text-center hover:bg-yellow-600 transition duration-200 relative">
            <i class="fas fa-bell mr-2"></i>{{ __('messages.nav.notifications') }}
            @if(Auth::user()->unread_notifications_count > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center">
                    {{ Auth::user()->unread_notifications_count }}
                </span>
            @endif
        </a>
        <a href="{{ route('subscriptions.plans') }}" class="block w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-2 px-4 rounded-lg text-center hover:from-yellow-600 hover:to-orange-600 transition duration-200">
            <i class="fas fa-crown mr-2"></i>{{ __('messages.nav.subscriptions') }}
        </a>
        <a href="{{ route('language.index') }}" class="block w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white py-2 px-4 rounded-lg text-center hover:from-blue-600 hover:to-cyan-600 transition duration-200">
            <i class="fas fa-globe mr-2"></i>{{ __('messages.language.title') }}
        </a>
        <a href="{{ route('profile.edit') }}" class="block w-full bg-purple-500 text-white py-2 px-4 rounded-lg text-center hover:bg-purple-600 transition duration-200">
            <i class="fas fa-edit mr-2"></i>{{ __('messages.dashboard.edit_profile') }}
        </a>
        <a href="{{ route('preferences.edit') }}" class="block w-full bg-indigo-500 text-white py-2 px-4 rounded-lg text-center hover:bg-indigo-600 transition duration-200">
            <i class="fas fa-cog mr-2"></i>{{ __('messages.nav.preferences') }}
        </a>
        <a href="{{ route('profile.show') }}" class="block w-full bg-blue-500 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-600 transition duration-200">
            <i class="fas fa-user mr-2"></i>{{ __('messages.dashboard.view_profile') }}
        </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.dashboard.statistics') }}</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">{{ __('messages.dashboard.matches') }}</span>
                        <span class="font-semibold text-pink-600">{{ $matchesCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">{{ __('messages.dashboard.views') }}</span>
                        <span class="font-semibold text-purple-600">{{ $viewsCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">{{ __('messages.dashboard.likes_sent') }}</span>
                        <span class="font-semibold text-blue-600">{{ $likesSentCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">{{ __('messages.dashboard.likes_received') }}</span>
                        <span class="font-semibold text-green-600">{{ $likesReceivedCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.dashboard.recent_activity') }}</h3>
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-heart text-4xl mb-4"></i>
                <p>{{ __('messages.dashboard.no_recent_activity') }}</p>
                <p class="text-sm">{{ __('messages.dashboard.complete_profile_to_start') }}</p>
            </div>
        </div>
    </div>
</body>
</html>


