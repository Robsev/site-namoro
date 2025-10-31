<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Amigos Para Sempre') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 min-h-screen overflow-x-hidden">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-2 sm:px-4">
            <div class="flex justify-between items-center py-3 sm:py-4">
                <div class="flex items-center flex-shrink-0 min-w-0">
                    <i class="fas fa-heart text-2xl sm:text-4xl text-pink-500 mr-2 sm:mr-3 flex-shrink-0"></i>
                    <h1 class="text-lg sm:text-2xl font-bold text-gray-800 truncate">{{ config('app.name', 'Amigos Para Sempre') }}</h1>
                </div>
                <div class="flex items-center space-x-1 sm:space-x-4 flex-shrink-0 ml-2">
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
                            <a href="{{ route('login') }}" class="bg-pink-500 text-white px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg hover:bg-pink-600 transition duration-200 text-xs sm:text-base whitespace-nowrap">
                                <i class="fas fa-sign-in-alt mr-1 sm:mr-2"></i>{{ __('messages.auth.login') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-purple-500 text-white px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg hover:bg-purple-600 transition duration-200 text-xs sm:text-base whitespace-nowrap">
                                    <i class="fas fa-user-plus mr-1 sm:mr-2"></i>{{ __('messages.auth.register') }}
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-8 sm:py-16">
        <div class="text-center">
            <!-- Logo Grande e Central -->
            <div class="mb-8">
                <img src="{{ asset('images/logo/logo.png') }}" 
                     alt="{{ config('app.name') }}" 
                     class="mx-auto h-64 w-auto sm:h-80 md:h-96"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div style="display: none;" class="text-center">
                    <i class="fas fa-heart text-8xl text-pink-500 mb-4"></i>
                    <h1 class="text-4xl font-bold text-gray-800">{{ config('app.name', 'Amigos Para Sempre') }}</h1>
                </div>
            </div>
            
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 sm:mb-6 px-2">
                {{ __('messages.welcome.title') }}
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 mb-6 sm:mb-8 max-w-3xl mx-auto px-3">
                {{ __('messages.welcome.subtitle') }}
            </p>
            
            @if (Route::has('login'))
                @guest
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-3">
                        <a href="{{ route('register') }}" class="bg-pink-500 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold hover:bg-pink-600 transition duration-200 shadow-lg whitespace-nowrap">
                            <i class="fas fa-heart mr-2"></i>{{ __('messages.welcome.get_started') }}
                        </a>
                        <a href="{{ route('login') }}" class="bg-white text-pink-500 px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold border-2 border-pink-500 hover:bg-pink-50 transition duration-200 shadow-lg whitespace-nowrap">
                            <i class="fas fa-sign-in-alt mr-2"></i>{{ __('messages.welcome.have_account') }}
                        </a>
                    </div>
                @endguest
            @endif
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-3 sm:px-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-6 sm:mb-12">
                {{ __('messages.welcome.why_choose') }}
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-6">
                    <div class="bg-pink-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-brain text-2xl text-pink-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.welcome.feature1_title') }}</h3>
                    <p class="text-gray-600">
                        {{ __('messages.welcome.feature1_desc') }}
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center p-6">
                    <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-purple-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.welcome.feature2_title') }}</h3>
                    <p class="text-gray-600">
                        {{ __('messages.welcome.feature2_desc') }}
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center p-6">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.welcome.feature3_title') }}</h3>
                    <p class="text-gray-600">
                        {{ __('messages.welcome.feature3_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="bg-gradient-to-br from-pink-50 to-purple-50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-3 sm:px-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-6 sm:mb-12">
                {{ __('messages.welcome.how_it_works_title') }}
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="bg-pink-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('messages.welcome.step1_title') }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ __('messages.welcome.step1_desc') }}
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="bg-purple-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('messages.welcome.step2_title') }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ __('messages.welcome.step2_desc') }}
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="bg-indigo-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('messages.welcome.step3_title') }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ __('messages.welcome.step3_desc') }}
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        4
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('messages.welcome.step4_title') }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ __('messages.welcome.step4_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-pink-500 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3 sm:mb-4">
                {{ __('messages.welcome.cta_title') }}
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-pink-100 mb-6 sm:mb-8">
                {{ __('messages.welcome.cta_subtitle') }}
            </p>
            
            @if (Route::has('login'))
                @guest
                    <a href="{{ route('register') }}" class="bg-white text-pink-500 px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold hover:bg-gray-100 transition duration-200 shadow-lg inline-block whitespace-nowrap">
                        <i class="fas fa-heart mr-2"></i>{{ __('messages.welcome.start_free') }}
                    </a>
                @endguest
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-3 sm:px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-heart text-2xl text-pink-500 mr-2"></i>
                        <h3 class="text-xl font-bold">{{ config('app.name', 'Amigos Para Sempre') }}</h3>
                    </div>
                    <p class="text-gray-400">
                        {{ __('messages.footer.description') }}
                    </p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('messages.footer.product') }}</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('how-it-works') }}" class="hover:text-white">{{ __('messages.footer.how_it_works') }}</a></li>
                        <li><a href="{{ route('features') }}" class="hover:text-white">{{ __('messages.footer.features') }}</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-white">{{ __('messages.footer.pricing') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('messages.footer.support') }}</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('help') }}" class="hover:text-white">{{ __('messages.footer.help_center') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white">{{ __('messages.footer.contact') }}</a></li>
                        <li><a href="{{ route('community') }}" class="hover:text-white">{{ __('messages.footer.community') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('messages.footer.legal') }}</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('terms') }}" class="hover:text-white">{{ __('messages.legal.terms.title') }}</a></li>
                        <li><a href="{{ route('privacy-policy') }}" class="hover:text-white">{{ __('messages.footer.privacy_policy') }}</a></li>
                        <li><a href="{{ route('cookies') }}" class="hover:text-white">{{ __('messages.legal.cookies.title') }}</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Amigos Para Sempre') }}. {{ __('messages.footer.copyright') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>