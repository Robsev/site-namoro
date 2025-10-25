<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.legal.pricing.title') }} - {{ config('app.name') }}</title>
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
            <h1 class="text-5xl font-bold text-gray-900 mb-6">{{ __('messages.legal.pricing.title') }}</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">{{ __('messages.legal.pricing.subtitle') }}</p>
            
            <!-- Free Badge -->
            <div class="inline-flex items-center bg-green-100 text-green-800 px-6 py-3 rounded-full text-lg font-semibold mb-8">
                <i class="fas fa-gift mr-2"></i>
                {{ __('messages.legal.pricing.free_badge') }}
            </div>
        </div>

        <!-- Current Status -->
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl p-8 text-white text-center mb-16">
            <h2 class="text-3xl font-bold mb-4">{{ __('messages.legal.pricing.current_status.title') }}</h2>
            <p class="text-xl mb-6">{{ __('messages.legal.pricing.current_status.description') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <i class="fas fa-check-circle text-3xl mb-2"></i>
                    <h3 class="text-lg font-semibold">{{ __('messages.legal.pricing.current_status.feature1') }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <i class="fas fa-check-circle text-3xl mb-2"></i>
                    <h3 class="text-lg font-semibold">{{ __('messages.legal.pricing.current_status.feature2') }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <i class="fas fa-check-circle text-3xl mb-2"></i>
                    <h3 class="text-lg font-semibold">{{ __('messages.legal.pricing.current_status.feature3') }}</h3>
                </div>
            </div>
        </div>

        <!-- Free Plan Details -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">{{ __('messages.legal.pricing.free_plan.title') }}</h2>
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-2xl p-8 border-4 border-green-500 relative">
                    <!-- Popular Badge -->
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <div class="bg-green-500 text-white px-6 py-2 rounded-full text-sm font-semibold">
                            <i class="fas fa-star mr-1"></i>
                            {{ __('messages.legal.pricing.free_plan.popular') }}
                        </div>
                    </div>
                    
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ __('messages.legal.pricing.free_plan.name') }}</h3>
                        <div class="text-6xl font-bold text-green-500 mb-2">
                            {{ __('messages.legal.pricing.free_plan.price') }}
                        </div>
                        <p class="text-gray-600 text-lg">{{ __('messages.legal.pricing.free_plan.period') }}</p>
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-4">
                            <h4 class="text-xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.pricing.free_plan.core_features.title') }}</h4>
                            <ul class="space-y-3">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.core_features.item1') }}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.core_features.item2') }}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.core_features.item3') }}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.core_features.item4') }}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.core_features.item5') }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.pricing.free_plan.advanced_features.title') }}</h4>
                            <ul class="space-y-3">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.advanced_features.item1') }}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.advanced_features.item2') }}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.advanced_features.item3') }}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.advanced_features.item4') }}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ __('messages.legal.pricing.free_plan.advanced_features.item5') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="text-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center bg-green-500 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-600 transition duration-200">
                            <i class="fas fa-user-plus mr-2"></i>
                            {{ __('messages.legal.pricing.free_plan.cta') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Future Plans -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">{{ __('messages.legal.pricing.future_plans.title') }}</h2>
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
                <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lightbulb text-2xl text-yellow-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ __('messages.legal.pricing.future_plans.subtitle') }}</h3>
                <p class="text-lg text-gray-700 mb-6">{{ __('messages.legal.pricing.future_plans.description') }}</p>
                
                <!-- Planned Features -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <i class="fas fa-crown text-2xl text-yellow-600 mb-2"></i>
                        <h4 class="font-semibold text-gray-900 mb-2">{{ __('messages.legal.pricing.future_plans.premium.title') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('messages.legal.pricing.future_plans.premium.description') }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <i class="fas fa-rocket text-2xl text-blue-600 mb-2"></i>
                        <h4 class="font-semibold text-gray-900 mb-2">{{ __('messages.legal.pricing.future_plans.boost.title') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('messages.legal.pricing.future_plans.boost.description') }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <i class="fas fa-gem text-2xl text-purple-600 mb-2"></i>
                        <h4 class="font-semibold text-gray-900 mb-2">{{ __('messages.legal.pricing.future_plans.vip.title') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('messages.legal.pricing.future_plans.vip.description') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Free -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">{{ __('messages.legal.pricing.why_free.title') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="bg-pink-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-2xl text-pink-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.legal.pricing.why_free.reason1.title') }}</h3>
                    <p class="text-gray-600">{{ __('messages.legal.pricing.why_free.reason1.description') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.legal.pricing.why_free.reason2.title') }}</h3>
                    <p class="text-gray-600">{{ __('messages.legal.pricing.why_free.reason2.description') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('messages.legal.pricing.why_free.reason3.title') }}</h3>
                    <p class="text-gray-600">{{ __('messages.legal.pricing.why_free.reason3.description') }}</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl p-12 text-white text-center">
            <h2 class="text-4xl font-bold mb-6">{{ __('messages.legal.pricing.cta.title') }}</h2>
            <p class="text-xl mb-8">{{ __('messages.legal.pricing.cta.description') }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-pink-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition duration-200">
                    <i class="fas fa-user-plus mr-2"></i>
                    {{ __('messages.legal.pricing.cta.get_started') }}
                </a>
                <a href="{{ route('how-it-works') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-pink-600 transition duration-200">
                    <i class="fas fa-play mr-2"></i>
                    {{ __('messages.legal.pricing.cta.learn_more') }}
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
