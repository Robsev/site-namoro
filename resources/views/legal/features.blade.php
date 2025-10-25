<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.legal.features.title') }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="flex items-center">
                        <i class="fas fa-heart text-4xl text-pink-500 mr-3"></i>
                        <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name') }}</h1>
                    </a>
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
                                        <span class="mr-3">🇧🇷</span>Português
                                        @if(app()->getLocale() === 'pt_BR')
                                            <i class="fas fa-check ml-auto text-green-500"></i>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('language.change') }}" method="POST" class="block">
                                    @csrf
                                    <input type="hidden" name="locale" value="en">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <span class="mr-3">🇺🇸</span>English
                                        @if(app()->getLocale() === 'en')
                                            <i class="fas fa-check ml-auto text-green-500"></i>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('language.change') }}" method="POST" class="block">
                                    @csrf
                                    <input type="hidden" name="locale" value="es">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <span class="mr-3">🇪🇸</span>Español
                                        @if(app()->getLocale() === 'es')
                                            <i class="fas fa-check ml-auto text-green-500"></i>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-tachometer-alt mr-2"></i>{{ __('messages.nav.dashboard') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-sign-out-alt mr-2"></i>{{ __('messages.nav.logout') }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition duration-200">
                                <i class="fas fa-sign-in-alt mr-2"></i>{{ __('messages.auth.login') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition duration-200">
                                    <i class="fas fa-user-plus mr-2"></i>{{ __('messages.auth.register') }}
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-gray-900 mb-6">{{ __('messages.legal.features.title') }}</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">{{ __('messages.legal.features.subtitle') }}</p>
        </div>

        <!-- Core Features -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">{{ __('messages.legal.features.core.title') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Smart Matching -->
                <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="bg-pink-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-brain text-2xl text-pink-600"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.features.core.smart_matching.title') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('messages.legal.features.core.smart_matching.description') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.smart_matching.feature1') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.smart_matching.feature2') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.smart_matching.feature3') }}
                        </li>
                    </ul>
                </div>

                <!-- Profile Creation -->
                <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-user-edit text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.features.core.profile.title') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('messages.legal.features.core.profile.description') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.profile.feature1') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.profile.feature2') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.profile.feature3') }}
                        </li>
                    </ul>
                </div>

                <!-- Real-time Chat -->
                <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-comments text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.features.core.chat.title') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('messages.legal.features.core.chat.description') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.chat.feature1') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.chat.feature2') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ __('messages.legal.features.core.chat.feature3') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Advanced Features -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">{{ __('messages.legal.features.advanced.title') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Geographic Matching -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mr-6">
                            <i class="fas fa-map-marker-alt text-2xl text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.features.advanced.geographic.title') }}</h3>
                            <p class="text-gray-600">{{ __('messages.legal.features.advanced.geographic.description') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Photo Moderation -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mr-6">
                            <i class="fas fa-shield-alt text-2xl text-orange-600"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.features.advanced.moderation.title') }}</h3>
                            <p class="text-gray-600">{{ __('messages.legal.features.advanced.moderation.description') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mr-6">
                            <i class="fas fa-bell text-2xl text-indigo-600"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.features.advanced.notifications.title') }}</h3>
                            <p class="text-gray-600">{{ __('messages.legal.features.advanced.notifications.description') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Privacy Controls -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mr-6">
                            <i class="fas fa-lock text-2xl text-red-600"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.features.advanced.privacy.title') }}</h3>
                            <p class="text-gray-600">{{ __('messages.legal.features.advanced.privacy.description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Safety Features -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">{{ __('messages.legal.features.safety.title') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-yellow-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-shield text-3xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.features.safety.verification.title') }}</h3>
                    <p class="text-gray-600">{{ __('messages.legal.features.safety.verification.description') }}</p>
                </div>

                <div class="text-center">
                    <div class="bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-flag text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.features.safety.reporting.title') }}</h3>
                    <p class="text-gray-600">{{ __('messages.legal.features.safety.reporting.description') }}</p>
                </div>

                <div class="text-center">
                    <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-ban text-3xl text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.features.safety.blocking.title') }}</h3>
                    <p class="text-gray-600">{{ __('messages.legal.features.safety.blocking.description') }}</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl p-12 text-white text-center">
            <h2 class="text-4xl font-bold mb-6">{{ __('messages.legal.features.cta.title') }}</h2>
            <p class="text-xl mb-8">{{ __('messages.legal.features.cta.description') }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-pink-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition duration-200">
                    <i class="fas fa-user-plus mr-2"></i>
                    {{ __('messages.legal.features.cta.get_started') }}
                </a>
                <a href="{{ route('how-it-works') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-pink-600 transition duration-200">
                    <i class="fas fa-play mr-2"></i>
                    {{ __('messages.legal.features.cta.learn_more') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-heart text-pink-400 mr-2"></i>{{ config('app.name') }}
                    </h3>
                    <p class="text-gray-300 text-sm">
                        {{ __('messages.footer.description') }}
                    </p>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">{{ __('messages.footer.legal') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ route('privacy-policy') }}" class="text-gray-300 hover:text-pink-400 transition duration-200">
                                <i class="fas fa-shield-alt mr-1"></i>{{ __('messages.footer.privacy_policy') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('terms') }}" class="text-gray-300 hover:text-pink-400 transition duration-200">
                                <i class="fas fa-file-contract mr-1"></i>{{ __('messages.legal.terms.title') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cookies') }}" class="text-gray-300 hover:text-pink-400 transition duration-200">
                                <i class="fas fa-cookie-bite mr-1"></i>{{ __('messages.legal.cookies.title') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" class="text-gray-300 hover:text-pink-400 transition duration-200">
                                <i class="fas fa-envelope mr-1"></i>{{ __('messages.footer.contact') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">{{ __('messages.footer.support') }}</h4>
                    <div class="text-sm text-gray-300">
                        <p class="mb-2">
                            <i class="fas fa-envelope mr-2"></i>{{ __('messages.footer.support_email') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ __('messages.footer.response_time') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('messages.footer.copyright') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
