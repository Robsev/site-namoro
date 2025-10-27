<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Página Não Encontrada - {{ config('app.name', 'Amigos Para Sempre') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Logo -->
            <div class="flex justify-center">
                <div class="bg-white rounded-full p-4 shadow-lg">
                    <i class="fas fa-heart text-6xl text-pink-500"></i>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="space-y-6">
                    <!-- Icon -->
                    <div class="flex justify-center">
                        <div class="bg-blue-100 rounded-full p-4">
                            <i class="fas fa-search text-3xl text-blue-600"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Página Não Encontrada
                        </h1>
                        <p class="text-gray-600">
                            A página que você está procurando não existe
                        </p>
                    </div>

                    <!-- Error Code -->
                    <div class="bg-gray-100 rounded-lg p-4">
                        <p class="text-2xl font-bold text-gray-800">404</p>
                        <p class="text-sm text-gray-600">Página Não Encontrada</p>
                    </div>

                    <!-- Description -->
                    <div class="text-left bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            O que aconteceu?
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            A página que você está tentando acessar pode ter sido movida, removida ou o endereço pode estar incorreto.
                        </p>
                        
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            O que fazer?
                        </h3>
                        <p class="text-sm text-gray-600">
                            Verifique o endereço digitado ou use os links abaixo para navegar pelo site.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div class="text-left">
                        <h3 class="font-semibold text-gray-900 mb-3 text-center">
                            <i class="fas fa-link text-purple-500 mr-2"></i>
                            Links Úteis
                        </h3>
                        <div class="grid grid-cols-1 gap-2">
                            <a href="{{ url('/') }}" class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition duration-200">
                                <i class="fas fa-home text-gray-400 mr-2"></i>
                                <span>Página Inicial</span>
                            </a>
                            @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition duration-200">
                                <i class="fas fa-sign-in-alt text-gray-400 mr-2"></i>
                                <span>Fazer Login</span>
                            </a>
                            @endif
                            @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition duration-200">
                                <i class="fas fa-user-plus text-gray-400 mr-2"></i>
                                <span>Criar Conta</span>
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <button onclick="window.history.back()" 
                                class="w-full bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition duration-200 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            {{ __('messages.common.back') }}
                        </button>
                        
                        <button onclick="window.location.href='{{ url('/') }}'" 
                                class="w-full bg-pink-600 text-white py-3 px-6 rounded-lg hover:bg-pink-700 transition duration-200 font-medium">
                            <i class="fas fa-home mr-2"></i>
                            Ir para o Início
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="bg-pink-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-search text-pink-500 mr-2"></i>
                            Procurar por algo?
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            Use nossa busca para encontrar o que você precisa:
                        </p>
                        <div class="flex">
                            <input type="text" 
                                   placeholder="Digite sua busca..." 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <button class="bg-pink-600 text-white px-4 py-2 rounded-r-lg hover:bg-pink-700 transition duration-200">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} Amigos Para Sempre. Todos os direitos reservados.</p>
                <p class="mt-1">Conectando pessoas, criando amizades para sempre ❤️</p>
            </div>
        </div>
    </div>
</body>
</html>
