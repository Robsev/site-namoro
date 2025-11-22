<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>{{ __('messages.auth.login') }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-pink-100 to-purple-100 min-h-screen flex items-center justify-center overflow-x-hidden px-3 py-4">
    <div class="bg-white rounded-2xl shadow-2xl p-4 sm:p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ config('app.name') }}</h1>
            <p class="text-gray-600">{{ __('messages.auth.login_subtitle') }}</p>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-4">
            <!-- Google Login -->
            <a href="{{ route('auth.google') }}" 
               class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-700 hover:bg-gray-50 transition duration-200">
                <i class="fab fa-google text-red-500 mr-3"></i>
                <span class="font-medium">{{ __('messages.auth.login_with_google') }}</span>
            </a>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">{{ __('messages.common.or') }}</span>
                </div>
            </div>

            <!-- Email/Password Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.auth.email') }}</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.auth.password') }}</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                        <span class="ml-2 text-sm text-gray-600">{{ __('messages.auth.remember_me') }}</span>
                    </label>
                    <a href="#" class="text-sm text-pink-600 hover:text-pink-500">{{ __('messages.auth.forgot_password') }}</a>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-lg font-medium hover:from-pink-600 hover:to-purple-700 transition duration-200">
                    {{ __('messages.auth.login') }}
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center">
                <p class="text-gray-600">
                    {{ __('messages.auth.dont_have_account') }} 
                    <a href="{{ route('register') }}" class="text-pink-600 hover:text-pink-500 font-medium">{{ __('messages.auth.create_account') }}</a>
                </p>
            </div>
        </div>

        <!-- Informative Message -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-gray-700 leading-relaxed">
                    <strong class="text-blue-800">Atenção:</strong> Estamos iniciando nossas atividades; portanto, ainda não há muitos usuários em nossa base. Esperamos que, em breve, já tenhamos uma base grande!
                </p>
                <p class="text-sm text-gray-700 leading-relaxed mt-2">
                    Nosso serviço sempre será gratuito e todos os recursos estarão liberados. Aproveite!
                </p>
            </div>
        </div>

        <!-- Language Selector -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-center space-x-4">
                <form action="{{ route('language.change') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="locale" value="pt_BR">
                    <button type="submit" class="text-sm {{ app()->getLocale() === 'pt_BR' ? 'text-pink-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        🇧🇷 {{ __('messages.language.portuguese') }}
                    </button>
                </form>
                <form action="{{ route('language.change') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit" class="text-sm {{ app()->getLocale() === 'en' ? 'text-pink-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        🇺🇸 {{ __('messages.language.english') }}
                    </button>
                </form>
                <form action="{{ route('language.change') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="locale" value="es">
                    <button type="submit" class="text-sm {{ app()->getLocale() === 'es' ? 'text-pink-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        🇪🇸 {{ __('messages.language.spanish') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
