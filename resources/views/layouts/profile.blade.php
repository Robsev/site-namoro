<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Perfil') - {{ config('app.name', 'Amigos Para Sempre') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/icons/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icons/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/icons/site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Maps API -->
    @if(config('services.google_maps.key'))
    <script>
        // Função global para inicializar Google Maps quando necessário
        window.initGoogleMaps = function() {
            // Esta função será sobrescrita nas páginas que precisam do Google Maps
            console.log('Google Maps carregado');
        };
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&loading=async&callback=initGoogleMaps"></script>
    @else
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl sm:text-2xl font-bold text-pink-600">
                            <i class="fas fa-heart mr-2"></i>
                            <span class="hidden sm:inline">Amigos Para Sempre</span>
                            <span class="sm:hidden">APS</span>
                        </a>
                    </div>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex items-center space-x-2">
                        <!-- Language Selector -->
                        <div class="relative group">
                            <button class="text-gray-700 hover:text-pink-600 px-2 py-2 rounded-md text-sm font-medium flex items-center">
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
                        
                        <a href="{{ route('profile.show') }}" class="text-gray-700 hover:text-pink-600 px-2 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-user mr-1"></i>{{ __('messages.nav.profile') }}
                        </a>
                        <a href="{{ route('matching.discover') }}" class="text-gray-700 hover:text-pink-600 px-2 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-search mr-1"></i>{{ __('messages.nav.discover') }}
                        </a>
                        <a href="{{ route('matching.matches') }}" class="text-gray-700 hover:text-pink-600 px-2 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-users mr-1"></i>{{ __('messages.nav.matches') }}
                        </a>
                        <a href="{{ route('chat.conversations') }}" class="text-gray-700 hover:text-pink-600 px-2 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-comments mr-1"></i>{{ __('messages.nav.chat') }}
                        </a>
                        <a href="{{ route('matching.likes-sent') }}" class="text-gray-700 hover:text-pink-600 px-2 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-heart mr-1"></i>{{ __('messages.nav.likes') }}
                        </a>
                        
                        <!-- Dropdown Menu -->
                        <div class="relative group">
                            <button class="text-gray-700 hover:text-pink-600 px-2 py-2 rounded-md text-sm font-medium flex items-center">
                                <i class="fas fa-cog mr-1"></i>{{ __('messages.nav.more') }}
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1">
                                    <a href="{{ route('location.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-map-marker-alt mr-2"></i>{{ __('messages.nav.location') }}
                                    </a>
                                    <a href="{{ route('interests.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-heart mr-2"></i>{{ __('messages.nav.interests') }}
                                    </a>
                                    <a href="{{ route('psychological-profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-brain mr-2"></i>{{ __('messages.nav.psychological_profile') }}
                                    </a>
                                    <a href="{{ route('preferences.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i>{{ __('messages.nav.preferences') }}
                                    </a>
                                    <a href="{{ route('email-preferences.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-envelope mr-2"></i>{{ __('messages.nav.email') }}
                                    </a>
                                    <a href="{{ route('language.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-globe mr-2"></i>{{ __('messages.language.title') }}
                                    </a>
                                    
                                    @if(auth()->user()->is_admin)
                                    <div class="border-t border-gray-100"></div>
                                    <a href="{{ route('admin.photos.statistics') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-shield-alt mr-2"></i>{{ __('messages.nav.photo_moderation') }}
                                    </a>
                                    @endif
                                    <a href="{{ route('matching.likes-received') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-heart-broken mr-2"></i>{{ __('messages.nav.likes_received') }}
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>{{ __('messages.nav.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div class="lg:hidden flex items-center">
                        <button id="mobile-menu-button" class="text-gray-700 hover:text-pink-600 p-2">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Menu -->
                <div id="mobile-menu" class="lg:hidden hidden bg-white border-t border-gray-200">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="{{ route('profile.show') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-user mr-2"></i>{{ __('messages.nav.my_profile') }}
                        </a>
                        <a href="{{ route('matching.discover') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-search mr-2"></i>{{ __('messages.nav.discover') }}
                        </a>
                        <a href="{{ route('matching.matches') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-users mr-2"></i>{{ __('messages.nav.matches') }}
                        </a>
                        <a href="{{ route('chat.conversations') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-comments mr-2"></i>{{ __('messages.nav.chat') }}
                        </a>
                        <a href="{{ route('matching.likes-sent') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-heart mr-2"></i>{{ __('messages.nav.likes') }}
                        </a>
                        <a href="{{ route('matching.likes-received') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-heart-broken mr-2"></i>{{ __('messages.nav.likes_received') }}
                        </a>
                        <a href="{{ route('location.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-map-marker-alt mr-2"></i>{{ __('messages.nav.location') }}
                        </a>
                        <a href="{{ route('interests.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-heart mr-2"></i>{{ __('messages.nav.interests') }}
                        </a>
                        <a href="{{ route('psychological-profile.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-brain mr-2"></i>{{ __('messages.nav.psychological_profile') }}
                        </a>
                        <a href="{{ route('preferences.edit') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-cog mr-2"></i>{{ __('messages.nav.preferences') }}
                        </a>
                        <a href="{{ route('email-preferences.edit') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-envelope mr-2"></i>{{ __('messages.nav.email') }}
                        </a>
                        <a href="{{ route('language.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-globe mr-2"></i>{{ __('messages.language.title') }}
                        </a>
                        
                        @if(auth()->user()->is_admin)
                        <div class="border-t border-gray-200"></div>
                        <a href="{{ route('admin.photos.statistics') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-shield-alt mr-2"></i>{{ __('messages.nav.photo_moderation') }}
                        </a>
                        @endif
                        
                        <div class="border-t border-gray-200 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">
                                    <i class="fas fa-sign-out-alt mr-2"></i>{{ __('messages.nav.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-heart text-pink-400 mr-2"></i>Amigos Para Sempre
                    </h3>
                    <p class="text-gray-300 text-sm">
                        {{ __('messages.footer.connecting_people') }}
                    </p>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">{{ __('messages.footer.legal_section') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ route('privacy-policy') }}" class="text-gray-300 hover:text-pink-400 transition duration-200">
                                <i class="fas fa-shield-alt mr-1"></i>{{ __('messages.footer.privacy_policy') }}
                            </a>
                        </li>
                        <li>
                            <a href="mailto:{{ __('messages.footer.support_email') }}" class="text-gray-300 hover:text-pink-400 transition duration-200">
                                <i class="fas fa-envelope mr-1"></i>{{ __('messages.footer.contact_email') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">{{ __('messages.footer.support_section') }}</h4>
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
                <p>{{ Str::replace(':year', date('Y'), __('messages.footer.copyright')) }}</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    
                    // Toggle icon
                    const icon = mobileMenuButton.querySelector('i');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.className = 'fas fa-bars text-xl';
                    } else {
                        icon.className = 'fas fa-times text-xl';
                    }
                });
                
                // Close mobile menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.add('hidden');
                        const icon = mobileMenuButton.querySelector('i');
                        icon.className = 'fas fa-bars text-xl';
                    }
                });
            }
        });
    </script>
</body>
</html>
