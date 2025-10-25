<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.legal.help.title') }} - {{ config('app.name') }}</title>
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
    <div class="max-w-6xl mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('messages.legal.help.title') }}</h1>
            <p class="text-xl text-gray-600 mb-8">{{ __('messages.legal.help.subtitle') }}</p>
            
            <!-- Search Box -->
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" 
                           id="faq-search" 
                           placeholder="{{ __('messages.legal.help.search_placeholder') }}"
                           class="w-full px-4 py-3 pl-12 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- FAQ Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Getting Started -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-pink-100 p-3 rounded-full mr-4">
                        <i class="fas fa-rocket text-pink-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.help.categories.getting_started.title') }}</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="faq-item" data-category="getting-started">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.getting_started.q1') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.getting_started.a1') }}</p>
                        </div>
                    </div>

                    <div class="faq-item" data-category="getting-started">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.getting_started.q2') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.getting_started.a2') }}</p>
                        </div>
                    </div>

                    <div class="faq-item" data-category="getting-started">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.getting_started.q3') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.getting_started.a3') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account & Profile -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.help.categories.account.title') }}</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="faq-item" data-category="account">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.account.q1') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.account.a1') }}</p>
                        </div>
                    </div>

                    <div class="faq-item" data-category="account">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.account.q2') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.account.a2') }}</p>
                        </div>
                    </div>

                    <div class="faq-item" data-category="account">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.account.q3') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.account.a3') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Matching & Discovery -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-heart text-green-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.help.categories.matching.title') }}</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="faq-item" data-category="matching">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.matching.q1') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.matching.a1') }}</p>
                        </div>
                    </div>

                    <div class="faq-item" data-category="matching">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.matching.q2') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.matching.a2') }}</p>
                        </div>
                    </div>

                    <div class="faq-item" data-category="matching">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.matching.q3') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.matching.a3') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional FAQ Sections -->
        <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Safety & Privacy -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                        <i class="fas fa-shield-alt text-purple-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.help.categories.safety.title') }}</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="faq-item" data-category="safety">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.safety.q1') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.safety.a1') }}</p>
                        </div>
                    </div>

                    <div class="faq-item" data-category="safety">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.safety.q2') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.safety.a2') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Support -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-orange-100 p-3 rounded-full mr-4">
                        <i class="fas fa-cog text-orange-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900">{{ __('messages.legal.help.categories.technical.title') }}</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="faq-item" data-category="technical">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.technical.q1') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.technical.a1') }}</p>
                        </div>
                    </div>

                    <div class="faq-item" data-category="technical">
                        <button class="faq-question w-full text-left py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ __('messages.legal.help.categories.technical.q2') }}</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <div class="faq-answer hidden mt-2 px-4 pb-3">
                            <p class="text-gray-700">{{ __('messages.legal.help.categories.technical.a2') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-12 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg p-8 text-white text-center">
            <h2 class="text-3xl font-bold mb-4">{{ __('messages.legal.help.contact_support.title') }}</h2>
            <p class="text-xl mb-6">{{ __('messages.legal.help.contact_support.description') }}</p>
            <a href="{{ route('contact') }}" class="inline-flex items-center bg-white text-pink-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-100 transition duration-200">
                <i class="fas fa-envelope mr-2"></i>
                {{ __('messages.legal.help.contact_support.button') }}
            </a>
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

    <!-- JavaScript for FAQ functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FAQ Toggle functionality
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const answer = this.nextElementSibling;
                    const icon = this.querySelector('i');
                    
                    // Toggle answer visibility
                    answer.classList.toggle('hidden');
                    
                    // Rotate icon
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                });
            });

            // Search functionality
            const searchInput = document.getElementById('faq-search');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const faqItems = document.querySelectorAll('.faq-item');
                
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
