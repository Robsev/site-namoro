<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.legal.cookies.title') }} - {{ config('app.name') }}</title>
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
    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ __('messages.legal.cookies.title') }}</h1>
            <p class="text-gray-600 mb-8">{{ __('messages.legal.cookies.last_updated') }}: {{ date('d/m/Y') }}</p>

            <!-- Introduction -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.cookies.introduction.title') }}</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('messages.legal.cookies.introduction.content') }}
                </p>
            </section>

            <!-- What are cookies -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.cookies.what_are.title') }}</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('messages.legal.cookies.what_are.content') }}
                </p>
            </section>

            <!-- Types of cookies -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.cookies.types.title') }}</h2>
                
                <div class="space-y-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.legal.cookies.types.essential.title') }}</h3>
                        <p class="text-gray-700 leading-relaxed mb-3">
                            {{ __('messages.legal.cookies.types.essential.content') }}
                        </p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>{{ __('messages.legal.cookies.types.essential.item1') }}</li>
                            <li>{{ __('messages.legal.cookies.types.essential.item2') }}</li>
                            <li>{{ __('messages.legal.cookies.types.essential.item3') }}</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.legal.cookies.types.analytics.title') }}</h3>
                        <p class="text-gray-700 leading-relaxed mb-3">
                            {{ __('messages.legal.cookies.types.analytics.content') }}
                        </p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>{{ __('messages.legal.cookies.types.analytics.item1') }}</li>
                            <li>{{ __('messages.legal.cookies.types.analytics.item2') }}</li>
                            <li>{{ __('messages.legal.cookies.types.analytics.item3') }}</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.legal.cookies.types.preferences.title') }}</h3>
                        <p class="text-gray-700 leading-relaxed mb-3">
                            {{ __('messages.legal.cookies.types.preferences.content') }}
                        </p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>{{ __('messages.legal.cookies.types.preferences.item1') }}</li>
                            <li>{{ __('messages.legal.cookies.types.preferences.item2') }}</li>
                            <li>{{ __('messages.legal.cookies.types.preferences.item3') }}</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- How we use cookies -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.cookies.how_we_use.title') }}</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('messages.legal.cookies.how_we_use.content') }}
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li>{{ __('messages.legal.cookies.how_we_use.item1') }}</li>
                    <li>{{ __('messages.legal.cookies.how_we_use.item2') }}</li>
                    <li>{{ __('messages.legal.cookies.how_we_use.item3') }}</li>
                    <li>{{ __('messages.legal.cookies.how_we_use.item4') }}</li>
                    <li>{{ __('messages.legal.cookies.how_we_use.item5') }}</li>
                </ul>
            </section>

            <!-- Managing cookies -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.cookies.managing.title') }}</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('messages.legal.cookies.managing.content') }}
                </p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">{{ __('messages.legal.cookies.managing.browser_settings.title') }}</h3>
                    <p class="text-blue-800 mb-3">{{ __('messages.legal.cookies.managing.browser_settings.content') }}</p>
                    <ul class="list-disc list-inside text-blue-800 space-y-1">
                        <li>{{ __('messages.legal.cookies.managing.browser_settings.item1') }}</li>
                        <li>{{ __('messages.legal.cookies.managing.browser_settings.item2') }}</li>
                        <li>{{ __('messages.legal.cookies.managing.browser_settings.item3') }}</li>
                    </ul>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-3">{{ __('messages.legal.cookies.managing.impact.title') }}</h3>
                    <p class="text-yellow-800">{{ __('messages.legal.cookies.managing.impact.content') }}</p>
                </div>
            </section>

            <!-- Third-party cookies -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.cookies.third_party.title') }}</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('messages.legal.cookies.third_party.content') }}
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li>{{ __('messages.legal.cookies.third_party.item1') }}</li>
                    <li>{{ __('messages.legal.cookies.third_party.item2') }}</li>
                    <li>{{ __('messages.legal.cookies.third_party.item3') }}</li>
                </ul>
            </section>

            <!-- Contact -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.cookies.contact.title') }}</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('messages.legal.cookies.contact.content') }}
                </p>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-gray-700">
                        <strong>{{ __('messages.legal.cookies.contact.email') }}:</strong> 
                        <a href="{{ route('contact') }}" class="text-pink-600 hover:text-pink-500">
                            suporte@sintoniadeamor.com.br
                        </a>
                    </p>
                </div>
            </section>

            <!-- Updates -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.cookies.updates.title') }}</h2>
                <p class="text-gray-700 leading-relaxed">
                    {{ __('messages.legal.cookies.updates.content') }}
                </p>
            </section>
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
