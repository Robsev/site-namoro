<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Amigos Para Sempre') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full mb-4">
                <i class="fas fa-heart text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('messages.auth.verify_email_subject') }}</h1>
        </div>

        <!-- Message -->
        @if (session('resent'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ __('messages.auth.verification_link_sent') }}</span>
                </div>
            </div>
        @endif

        <div class="mb-6 text-center text-gray-700">
            <i class="fas fa-envelope-open-text text-5xl text-pink-500 mb-4"></i>
            <h2 class="text-xl font-semibold mb-3">{{ __('messages.auth.email_verification_needed') }}</h2>
            <p class="text-gray-600 mb-4">{{ __('messages.auth.email_verification_message') }}</p>
            
            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-left mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>{{ __('messages.auth.verification_instructions_title') }}
                </h3>
                <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                    <li>{{ __('messages.auth.verification_step_1') }}</li>
                    <li>{{ __('messages.auth.verification_step_2') }}</li>
                    <li>{{ __('messages.auth.verification_step_3') }}</li>
                </ol>
            </div>

            <p class="text-sm text-gray-500 mb-6">{{ __('messages.auth.email_not_received') }}</p>
        </div>

        <!-- Resend Button -->
        <form method="POST" action="{{ route('verification.resend') }}" class="mb-4">
            @csrf
            <button type="submit" class="w-full bg-pink-500 text-white py-3 px-4 rounded-lg hover:bg-pink-600 transition duration-200 font-medium">
                <i class="fas fa-redo mr-2"></i>{{ __('messages.auth.resend_verification_email') }}
            </button>
        </form>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm underline">
                {{ __('messages.auth.logout') }}
            </button>
        </form>
    </div>
</body>
</html>
