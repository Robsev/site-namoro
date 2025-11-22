<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.legal.contact.title') }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Information -->
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ __('messages.legal.contact.title') }}</h1>
                <p class="text-gray-600 mb-8 text-lg">{{ __('messages.legal.contact.subtitle') }}</p>

                <!-- Contact Methods -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="bg-pink-100 p-3 rounded-full mr-4">
                                <i class="fas fa-envelope text-pink-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.legal.contact.email.title') }}</h3>
                                <p class="text-gray-600">{{ __('messages.legal.contact.email.description') }}</p>
                            </div>
                        </div>
                        <a href="mailto:suporte@sintoniadeamor.com.br" class="text-pink-600 hover:text-pink-500 font-medium">
                            suporte@sintoniadeamor.com.br
                        </a>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <i class="fas fa-clock text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.legal.contact.hours.title') }}</h3>
                                <p class="text-gray-600">{{ __('messages.legal.contact.hours.description') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-700">
                            <strong>{{ __('messages.legal.contact.hours.weekdays') }}:</strong> {{ __('messages.legal.contact.hours.weekdays_time') }}<br>
                            <strong>{{ __('messages.legal.contact.hours.weekend') }}:</strong> {{ __('messages.legal.contact.hours.weekend_time') }}
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-reply text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.legal.contact.response.title') }}</h3>
                                <p class="text-gray-600">{{ __('messages.legal.contact.response.description') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-700">{{ __('messages.legal.contact.response.time') }}</p>
                    </div>
                </div>

                <!-- FAQ Quick Links -->
                <div class="mt-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('messages.legal.contact.faq.title') }}</h3>
                    <p class="text-gray-600 mb-4">{{ __('messages.legal.contact.faq.description') }}</p>
                    <a href="{{ route('help') }}" class="inline-flex items-center text-pink-600 hover:text-pink-500 font-medium">
                        <i class="fas fa-question-circle mr-2"></i>
                        {{ __('messages.legal.contact.faq.link') }}
                    </a>
                </div>
            </div>

            <!-- Contact Form -->
            <div>
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">{{ __('messages.legal.contact.form.title') }}</h2>
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.legal.contact.form.name') }} *</label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                       required>
                            </div>
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.legal.contact.form.email') }} *</label>
                                <input type="email" 
                                       id="contact_email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.legal.contact.form.subject') }} *</label>
                            <select id="subject" 
                                    name="subject"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    required>
                                <option value="">{{ __('messages.legal.contact.form.subject_select') }}</option>
                                <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>{{ __('messages.legal.contact.form.subject_general') }}</option>
                                <option value="technical" {{ old('subject') == 'technical' ? 'selected' : '' }}>{{ __('messages.legal.contact.form.subject_technical') }}</option>
                                <option value="account" {{ old('subject') == 'account' ? 'selected' : '' }}>{{ __('messages.legal.contact.form.subject_account') }}</option>
                                <option value="billing" {{ old('subject') == 'billing' ? 'selected' : '' }}>{{ __('messages.legal.contact.form.subject_billing') }}</option>
                                <option value="privacy" {{ old('subject') == 'privacy' ? 'selected' : '' }}>{{ __('messages.legal.contact.form.subject_privacy') }}</option>
                                <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>{{ __('messages.legal.contact.form.subject_other') }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.legal.contact.form.message') }} *</label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                      placeholder="{{ __('messages.legal.contact.form.message_placeholder') }}"
                                      required>{{ old('message') }}</textarea>
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" 
                                   id="privacy" 
                                   name="privacy"
                                   class="mt-1 rounded border-gray-300 text-pink-600 focus:ring-pink-500"
                                   required>
                            <label for="privacy" class="ml-2 text-sm text-gray-600">
                                {{ __('messages.legal.contact.form.privacy_agreement') }}
                                <a href="{{ route('privacy-policy') }}" class="text-pink-600 hover:text-pink-500">
                                    {{ __('messages.footer.privacy_policy') }}
                                </a>
                            </label>
                        </div>

                        <!-- reCAPTCHA -->
                        @if(config('services.recaptcha.site_key'))
                            <div class="flex justify-center">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            </div>
                            @error('g-recaptcha-response')
                                <div class="text-red-600 text-sm mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        @endif

                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-lg font-medium hover:from-pink-600 hover:to-purple-700 transition duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>
                            {{ __('messages.legal.contact.form.submit') }}
                        </button>
                    </form>
                </div>
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
